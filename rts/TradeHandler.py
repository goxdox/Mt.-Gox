import tornado.web

class TradeHandler(tornado.web.RequestHandler):
    def get(self):
        self.write("TradeHandler")
       