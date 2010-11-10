import tornado.httpserver
import tornado.ioloop
import tornado.web
import tornado.websocket
import Connection
import PhpUpdateHandler

class MainHandler(tornado.web.RequestHandler):
    def get(self):
        self.write("Running...")
        

application = tornado.web.Application([
    (r"/", MainHandler),
    (r"/connect", Connection.Connection),
    (r"/php/update", PhpUpdateHandler.PhpUpdateHandler),

])

if __name__ == "__main__":
    http_server = tornado.httpserver.HTTPServer(application)
    http_server.listen(8080)
    tornado.ioloop.IOLoop.instance().start()
