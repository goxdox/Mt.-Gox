import tornado.web
import SubscribePool


class PhpUpdateHandler(tornado.web.RequestHandler):
    def get(self):
        print "PhpUpdateHandler"
        SubscribePool.thePool.update()
       