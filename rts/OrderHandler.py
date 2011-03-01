import tornado.web

class OrderHandler(tornado.web.RequestHandler):
    def get(self):
        self.write("TradeHandler")
       