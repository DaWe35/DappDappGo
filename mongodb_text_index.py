# create text index
import pymongo
client = pymongo.MongoClient('mongodb://localhost:27017/')
db = client['dappdappgo']
collection = db['skydata']
collection.create_index([('title', pymongo.TEXT),('text', pymongo.TEXT)], name='text_index', default_language='english')
