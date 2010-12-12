import tornado.web
import SubscribePool


class PhpTradeHandler(tornado.web.RequestHandler):
    def get(self):
        #print "Trade"
        SubscribePool.thePool.updateTrade()
        
        
class PhpOrderHandler(tornado.web.RequestHandler):
    def get(self):
        #print "Order"
        SubscribePool.thePool.updateDepth()
        
# change in:
#    funds
#    Orders
class PhpUserHandler(tornado.web.RequestHandler):
    def get(self,userID):
        #print "Order"
        SubscribePool.thePool.updateUser(userID)
       