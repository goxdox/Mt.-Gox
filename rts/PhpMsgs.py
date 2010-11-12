import tornado.web
import SubscribePool


class PhpTradeHandler(tornado.web.RequestHandler):
    def get(self):
        print "Trade"
        SubscribePool.thePool.updateTrade()
        
        
class PhpOrderHandler(tornado.web.RequestHandler):
    def get(self):
        print "Order"
        SubscribePool.thePool.updateDepth()
       