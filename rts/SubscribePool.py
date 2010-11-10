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
            self.mCursor = self.mDatabase.cursor()
            self.update()
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
    
    # send a market update to all the subscribed connections    
    def update(self):
        try:
            self.mCursor.execute ("SELECT * from Ticker")
            row = self.mCursor.fetchone()
            
            self.mData['ticker']['high']=round( row['High'],4)
            self.mData['ticker']['low']=round( row['Low'],4)
            self.mData['ticker']['vol']=round( row['Volume']/1000,0)
            self.mData['ticker']['buy']=round( row['HighBuy'],4)
            self.mData['ticker']['sell']=round( row['LowSell'],4)
            self.mData['ticker']['last']=round( row['LastPrice'],4)
            
            for connection in self.mList:
                connection.write_message(json.dumps(self.mData))
        
        except MySQLdb.Error, e:
             print "Error %d: %s" % (e.args[0], e.args[1])    
           
            

thePool = SubscribePool()
        