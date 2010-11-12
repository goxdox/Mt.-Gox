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
    
    def getDepth(self,sql):
        self.mCursor.execute(sql)
        rows = self.mCursor.fetchall()
        
        sum=0
        totalPrice=0
        for row in rows:
            price=row['price']
            amount=row['amount']
            if sum+amount >= 1000000:
                totalPrice=totalPrice+(1000000-sum)*price
                break
            else:
                totalPrice=totalPrice+amount*price
                sum += amount
        
        
        return totalPrice/1000000
   
    # see what the avergae price you would get for filling 1000BTC at market
    def calcDepth(self):
        try:

            self.mData['depth']['ask1000']=self.getDepth("SELECT amount,price from Asks where status=1 order by price")
            self.mData['depth']['bid1000']=self.getDepth("SELECT amount,price from Bids where status=1 order by price desc")
            
        except MySQLdb.Error, e:
             print "Error %d: %s" % (e.args[0], e.args[1])
             
    def updateDepth(self):
        self.calcDepth();
                
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
        