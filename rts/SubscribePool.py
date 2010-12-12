import Connection
import MySQLdb
import json
import threading
from time import time
from decimal import *

"""
SubscribePool is a list of all the subscribed WebSockets

"""

class DecimalEncoder(json.JSONEncoder):
    def _iterencode(self, o, markers=None):
        if isinstance(o, Decimal):
            return (str(o) for o in [o])
        return super(DecimalEncoder, self)._iterencode(o, markers)

class SubscribePool():
    mList = [] 
    mData = {}
   
    
    def __init__(self):
        self.mData['ticker']={}
        self.mData['asks']={}
        self.mData['bids']={}
        self.mData['plot']={}
        self.mData['date']=0
        self.mData['now']=0
        getcontext().prec = 4
        
        #self.mTimer = threading.Timer(60,self.timer)
        #self.mTimer.start()
        try:
            self.mDatabase = MySQLdb.connect("localhost", "land", "-island-", "btcx")
            self.mDatabase.autocommit(True)
            self.mCursor = self.mDatabase.cursor(MySQLdb.cursors.DictCursor)
            self.updateTrade()
        except MySQLdb.Error, e:
             print "Error %d: %s" % (e.args[0], e.args[1])
             sys.exit(1)
    """      
    def timer(self):
        print('stay')
        for connection in self.mList:
            connection.write_message('.')
        self.mTimer.start()
    """   
    def add(self,connection):
        self.mData['now']=int(time())
        self.mList.append(connection)
        self.sendData(connection)
        if connection.mUserID:
            self.updateUser(connection.mUserID)
        print "Adding %d (%d)" % (connection.mUserID,len(self.mList))
        
    def remove(self,connection):
        try:
            self.mList.remove(connection)
        except ValueError, e:
            print "Connection not in list"
        
    def sendData(self,connection):
        connection.write_message(json.dumps(self.mData,cls=DecimalEncoder))
    
    def getDepth(self,sql,ticker):
        self.mCursor.execute(sql)
        rows = self.mCursor.fetchall()
        
        sum=0
        totalPrice=0
        for row in rows:
            price=row['price']
            if sum==0: self.mData['ticker'][ticker]=price
            amount=row['amount']
            if sum+amount >= 1000000:
                totalPrice=totalPrice+(1000000-sum)*price
                break
            else:
                totalPrice=totalPrice+amount*price
                sum += amount
        
        
        return round(totalPrice/1000000,4)
   
    # see what the avergae price you would get for filling 1000BTC at market
    def calc1000Depth(self):
        try:
            ask=self.getDepth("SELECT amount,price from Asks where status=1 and darkstatus=0 order by price","sell")
            bid=self.getDepth("SELECT amount,price from Bids where status=1 and darkstatus=0 order by price desc","buy")
            self.mData['depth']['ask1000']=ask
            self.mData['depth']['bid1000']=bid
            #print "Depth %f(%f)  :  %f(%f)" % (self.mData['depth']['ask1000'],ask, self.mData['depth']['bid1000'],bid)
        except MySQLdb.Error, e:
             print "Error %d: %s" % (e.args[0], e.args[1])
             
    def update1000Depth(self):
        beforeAsk=self.mData['depth']['ask1000']
        beforeBid=self.mData['depth']['bid1000']
        self.calc1000Depth();
        
        if (((beforeAsk-self.mData['depth']['ask1000'])>.00001) or ((beforeBid-self.mData['depth']['bid1000'])>.00001)) :       
            for connection in self.mList:
                connection.write_message(json.dumps(self.mData,cls=DecimalEncoder))
                
    def calcDepth(self):
        self.mData['asks']=[]
        self.mData['bids']=[]
        try:
            
            sql="SELECT amount,price From Asks where status=1 and darkStatus=0 order by Price";
            self.mCursor.execute(sql)
            rows = self.mCursor.fetchall()
            for row in rows:
                self.mData['asks'].append( (Decimal(str(row['price'])),int(row['amount']/1000)) )
                
            sql="SELECT amount,price From Bids where status=1 and darkStatus=0 order by Price desc";
            self.mCursor.execute(sql)
            rows = self.mCursor.fetchall() 
            for row in rows:
                self.mData['bids'].append( ( Decimal(str(row['price'])),int(row['amount']/1000)) )
                   
        except MySQLdb.Error, e:
             print "Error %d: %s" % (e.args[0], e.args[1])    
                
    def updateDepth(self):
        self.calcDepth()
        self.mData['now']=int(time())    
        for connection in self.mList:
            connection.write_message(json.dumps(self.mData,cls=DecimalEncoder))
            
    def addPlot(self):
        try:
            self.mData['plot']=[]
            
            startTime=int(time()-24*60*60)
                
            self.mData['date']=int(time())
                
            sql="SELECT price,amount,date From Trades where Date>%d order by Date" % (startTime);
            self.mCursor.execute(sql)
            rows = self.mCursor.fetchall()
           
            for row in rows:
                self.mData['plot'].append( ( Decimal(str(row['price'])),0,0,0, int(row['amount']/1000), row['date'] ) )
               
        except MySQLdb.Error, e:
             print "Error %d: %s" % (e.args[0], e.args[1])
        

    def getUserID(self,seshID):
        if seshID:
            try:
                sql="SELECT SessionData from sessions.Sessions where uniqueID='%s'" % (seshID)
                self.mCursor.execute(sql)
                row = self.mCursor.fetchone()
                if row:
                    seshStr=row['SessionData']
                    #print seshStr
                    index=seshStr.find('UserID')
                    if index>=0 :
                        # UserID|s:1:"1";
                        endIndex=seshStr.find(';',index)
                        startIndex=seshStr.find('"',index)
                        userID=seshStr[startIndex+1:endIndex-1]
                        return(int(userID))
                    
            except MySQLdb.Error, e:
                print "Error %d: %s" % (e.args[0], e.args[1])
        return 0
                
      
    def updateUser(self,userID):
        print "update User %d" % userID
        for connection in self.mList:
            if connection.mUserID==userID:
                try:
                    data={}
                    data['orders']=[]
                    
                    
                    sql="SELECT * from Asks where userid=%d" % userID
                    self.mCursor.execute(sql)
                    rows = self.mCursor.fetchall()
                    for row in rows:
                        id=row['OrderID']
                        amount=Decimal(row['Amount'])/1000
                        price=Decimal(str(row['Price']))
                        status=row['Status']
                        darkStatus=row['DarkStatus']
                        date=row['Date']

                        order={'oid' : id, 'type' : 1, 'amount' : amount, 'price' : price, 'status' : status, 'dark' : darkStatus, 'date' : date}
            
                        data['orders'].append( order )
                        
                    sql="SELECT * from Bids where userid=%d" % userID
                    self.mCursor.execute(sql)
                    rows = self.mCursor.fetchall()
                    for row in rows:
                        id=row['OrderID']
                        amount=Decimal(row['Amount'])/1000
                        price=Decimal(str(row['Price']))
                        status=row['Status']
                        darkStatus=row['DarkStatus']
                        date=row['Date']

                        order={'oid' : id, 'type' : 2, 'amount' : amount, 'price' : price, 'status' : status, 'dark' : darkStatus, 'date' : date}
            
                        data['orders'].append( order )
                    
                    sql="SELECT usd,btc from Users where userid=%d" % userID
                    self.mCursor.execute(sql)
                    row = self.mCursor.fetchone()
                    
                    btc=Decimal(row['btc'])/1000
                    usd=Decimal(row['usd'])/1000
                    if usd<0: usd=0
                    if btc<0: btc=0
                    
                    data['usds']= usd
                    data['btcs']= btc
                    
                    connection.write_message(json.dumps(data,cls=DecimalEncoder))
                    
                except MySQLdb.Error, e:
                    print "Error %d: %s" % (e.args[0], e.args[1])    
             
    # send a market update to all the subscribed connections    
    def updateTrade(self):
        try:
            self.mCursor.execute("SELECT * from Ticker")
            row = self.mCursor.fetchone()
            
            self.mData['ticker']['high']=Decimal(str( row['High']))
            self.mData['ticker']['low']=Decimal(str( row['Low']))
            self.mData['ticker']['vol']=int( row['Volume']/1000)
            self.mData['ticker']['buy']=Decimal(str( row['HighBuy']))
            self.mData['ticker']['sell']=Decimal(str( row['LowSell']))
            self.mData['ticker']['last']=Decimal(str( row['LastPrice']))
            
            self.calcDepth();
            self.addPlot();
            
            self.mData['now']=int(time())
            for connection in self.mList:
                connection.write_message(json.dumps(self.mData,cls=DecimalEncoder))
        
        except MySQLdb.Error, e:
             print "Error %d: %s" % (e.args[0], e.args[1])    
           

thePool = SubscribePool()
        