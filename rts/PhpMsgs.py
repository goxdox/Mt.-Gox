import tornado.web
import SubscribePool


class PhpTradeHandler(tornado.web.RequestHandler):
    def get(self):
        print "PhpTradeHandler"
        SubscribePool.thePool.updateTrade()
        
        
class PhpOrderHandler(tornado.web.RequestHandler):
    def get(self):
        print "PhpOrderHandler"
        SubscribePool.thePool.updateDepth()
       