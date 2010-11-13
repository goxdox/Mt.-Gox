import Connection
import MySQLdb
import json

"""
SubscribePool is a list of all the subscribed WebSockets

"""

class SubscribePool():
    mList = [] 
    mData = {}
    
    def __init__(self):
        self.mData['ticker']={}
        self.mData['depth']={}
        
        try:
            self.mDatabase = MySQLdb.connect("localhost", "land", "-island-", "btcx")
            self.mDatabase.autocommit(True)
            self.mCursor = self.mDatabase.cursor(MySQLdb.cursors.DictCursor)
            self.updateTrade()
        except MySQLdb.Error, e:
             print "Error %d: %s" % (e.args[0], e.args[1])
             sys.exit(1)
        
    def add(self,connection):
        self.mList.append(connection)
        self.sendData(connection)
        
    def remove(self,connection):
        self.mList.remove(connection)
        
    def sendData(self,connection):
        connection.write_message(json.dumps(self.mData))
    
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
    def calcDepth(self):
        try:
            ask=self.getDepth("SELECT amount,price from Asks where status=1 order by price","sell")
            bid=self.getDepth("SELECT amount,price from Bids where status=1 order by price desc","buy")
            self.mData['depth']['ask1000']=ask
            self.mData['depth']['bid1000']=bid
            #print "Depth %f(%f)  :  %f(%f)" % (self.mData['depth']['ask1000'],ask, self.mData['depth']['bid1000'],bid)
        except MySQLdb.Error, e:
             print "Error %d: %s" % (e.args[0], e.args[1])
             
    def updateDepth(self):
        beforeAsk=self.mData['depth']['ask1000']
        beforeBid=self.mData['depth']['bid1000']
        self.calcDepth();
        
        if (((beforeAsk-self.mData['depth']['ask1000'])>.00001) or ((beforeBid-self.mData['depth']['bid1000'])>.00001)) :       
            for connection in self.mList:
                connection.write_message(json.dumps(self.mData))
            
             
    # send a market update to all the subscribed connections    
    def updateTrade(self):
        try:
            self.mCursor.execute("SELECT * from Ticker")
            row = self.mCursor.fetchone()
            
            self.mData['ticker']['high']=round( row['High'],4)
            self.mData['ticker']['low']=round( row['Low'],4)
            self.mData['ticker']['vol']=round( row['Volume']/1000,0)
            self.mData['ticker']['buy']=round( row['HighBuy'],4)
            self.mData['ticker']['sell']=round( row['LowSell'],4)
            self.mData['ticker']['last']=round( row['LastPrice'],4)
            
            self.calcDepth();
            
            
            for connection in self.mList:
                connection.write_message(json.dumps(self.mData))
        
        except MySQLdb.Error, e:
             print "Error %d: %s" % (e.args[0], e.args[1])    
           
            

thePool = SubscribePool()
        