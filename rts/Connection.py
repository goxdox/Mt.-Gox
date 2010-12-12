import tornado.httpserver
import tornado.ioloop
import tornado.web
import tornado.websocket
import SubscribePool

"""
ConnectHandler: Handles incoming websockets.

# should listen for events for this user
# should listen for global events
# if they have a marin account it should check that
"""

def hello():
        print "writing preamble"


class Connection(tornado.websocket.WebSocketHandler):
    mSeshID = ''
    mUserID = 0
    mUsername = ''
    #def open(self):
        #print "WebSocket opened"
       

    def on_message(self, message):
        #self.write_message(u"You said: " + message)
        #print "message: %s" % message
        if message.startswith("subscribe"):
            self.mSeshID=message[10:]
            #print "Subscribing: %s" % mSeshID
            self.mUserID=SubscribePool.thePool.getUserID(self.mSeshID)
            SubscribePool.thePool.add(self)
      
        
        

    def on_close(self):
        #print "WebSocket closed"
        SubscribePool.thePool.remove(self)

        
  