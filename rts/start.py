import tornado.httpserver
import tornado.ioloop
import tornado.web
import tornado.websocket
import Connection
import PhpMsgs

class MainHandler(tornado.web.RequestHandler):
    def get(self):
        self.write("Running...")
        

application = tornado.web.Application([
    (r"/", MainHandler),
    (r"/connect", Connection.Connection),
    (r"/php/trade", PhpMsgs.PhpTradeHandler),
    (r"/php/order", PhpMsgs.PhpOrderHandler),

])

if __name__ == "__main__":
    print "Starting Tornado"
    http_server = tornado.httpserver.HTTPServer(application)
    http_server.listen(8080)
    tornado.ioloop.IOLoop.instance().start()
    
