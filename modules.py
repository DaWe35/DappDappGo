import config
import requests
import time
import os
import datetime
from dateutil import parser
import json
from bs4 import BeautifulSoup
from bs4.element import Comment
import mysql.connector
from PIL import Image
from io import BytesIO
from urllib.parse import urlparse
from urlextract import URLExtract
extractor = URLExtract()

# MySQL config (edit in config.py)
mydb = mysql.connector.connect(
  host = config.host,
  user = config.user,
  passwd = config.passwd,
  database = config.database
)

mycursor = mydb.cursor(dictionary=True)


def create_thumbnail(skypath, dl_portal):
	response = requests.get(dl_portal + skypath)
	im = Image.open(BytesIO(response.content))
	# convert to thumbnail image
	im.thumbnail((200, 200), Image.ANTIALIAS)
	script_dir = os.path.dirname(os.path.abspath(__file__))
	imgpath = os.path.join(script_dir, 'public_html', 'thumbnails', skypath + ".jpg")
	im = im.convert("RGB")
	im.save(imgpath, "JPEG")


def tag_visible(element):
	if element.parent.name in ['style', 'script', 'head', 'title', 'meta', '[document]']:
		return False
	if isinstance(element, Comment):
		return False
	return True


def text_from_html(body):
	soup = BeautifulSoup(body, 'html.parser')
	texts = soup.findAll(text=True)
	visible_texts = filter(tag_visible, texts)  
	return u" ".join(t.strip() for t in visible_texts)

def title_from_html(body):
	soup = BeautifulSoup(body, 'html.parser')
	title = soup.find('title', text=True)
	if title:
		return title.text
	else:
		return False

def description_from_html(body):
	soup = BeautifulSoup(body, 'html.parser')
	metas = soup.find_all('meta')
	if metas:
		try:
			return [ meta.attrs['content'] for meta in metas if 'name' in meta.attrs and meta.attrs['name'] == 'description' ][0]
		except IndexError:
			return False
	else:
		return False

def exact_urls_from_page(page_url):
	req = requests.get(page_url)
	content = req.text
	return extractor.find_urls(content)

def exact_urls_from_google():
	google_start = 0
	urls = []
	while True:
		req = requests.get("https://www.google.com/search?&q=site%3Asiasky.net&start=" + str(google_start))
		content = req.text
		url = extractor.find_urls(content)
		urls += url
		print('Collected',google_start,'from Google')
		google_start += 10
		time.sleep(1)
		if len(url) < 10:
			return urls



def get_skynet_portals():
	req = requests.get('https://siastats.info/dbs/skynet_current.json')
	req_json = req.json()
	portal_urls = []
	for portal in req_json:
		portal_urls.append(portal['link'])
	return portal_urls

def get_skynet_path(url, skynet_portals):
	if url.startswith(tuple(skynet_portals)):
		path =urlparse(url).path
		if path != '/' and path != '':
			if path.startswith('/'):
				path = path[1:]
			if path.endswith('&amp'):
				path = path[:-4]
			if len(path) >= 46:
				return path
	return False

def insert_url_to_db(skypath):
	now = int(time.time())

	sql = "INSERT IGNORE INTO skylinks (skypath, insertion_date) VALUES (%s, %s)"
	val = (skypath, now)
	mycursor.execute(sql, val)

	mydb.commit()
	return mycursor.rowcount


def update_headers_in_db(skypath, headers):
	try:
		filename = json.loads(headers['Skynet-File-Metadata'])['filename']
		filedate = parser.parse(headers['Date'])
		filedate = datetime.datetime.timestamp(filedate)
	except KeyError:
		return False

	sql = "UPDATE skylinks SET filename=%s, filedate=%s, `content-type`=%s, `content-length`=%s WHERE skypath = %s"
	val = (filename, filedate, headers['Content-Type'], headers['Content-Length'], skypath)
	mycursor.execute(sql, val)
	mydb.commit()
	return mycursor.rowcount

def update_text_in_db(skypath, text, title, description):
	now = int(time.time())

	sql = "UPDATE skylinks SET title=%s, content=%s, description=%s, lastupdate=%s WHERE skypath = %s"
	val = (title[0:255], text[0:65533], description[0:255], now, skypath)
	mycursor.execute(sql, val)
	mydb.commit()
	return mycursor.rowcount

def get_skylinks_to_update():
	old_doc_time = int(time.time()) - 6000

	sql = "SELECT * FROM skylinks WHERE lastupdate < " + str(old_doc_time) + " OR lastupdate IS NULL"
	mycursor.execute(sql)

	return mycursor.fetchall()

def get_headers(url):
	headers = {
		'User-Agent': 'DappDappGo header crawler',
		'Accept-Encoding': None
	}
	response = requests.head(url, headers=headers)
	return response.headers


def get_text_from_file(url, header):
	if int(header['Content-Length']) > 100000000:
		print('Too big file:', url)
		return False

	headers = {
		'User-Agent': 'DappDappGo header crawler'
	}
	req = requests.get(url, headers=headers)

	if header['Content-Type'].startswith('text/html'):
		html_text = text_from_html(req.text)
		title = title_from_html(req.text)
		if not title or title == '':
			title = json.loads(header['Skynet-File-Metadata'])['filename']

		description = description_from_html(req.text)
		if not description or description == '':
			description = html_text[0:255]
		return { "text": html_text, "title": title, "description": description, "contenttype": header['Content-Type'] }
	else:
		filename = json.loads(header['Skynet-File-Metadata'])['filename']
		return { "text": filename, "title": filename, "description": filename, "contenttype": header['Content-Type'] }



def signal_handler(sig, frame):
	browser.quit()
	exit(0)

def get_data_from_duckduckgo():
	from selenium import webdriver
	from webdriver_manager.chrome import ChromeDriverManager
	import signal

	signal.signal(signal.SIGINT, signal_handler)

	options = webdriver.ChromeOptions()
	options.add_argument('allow-elevated-browser')
	options.add_argument("window-size=800,600")
	options.add_argument("--mute-audio")
	options.add_argument('--no-sandbox')
	#if (len(argv) <= 1 or argv[1] != 'display'):
	options.headless = True
	browser = webdriver.Chrome(executable_path=ChromeDriverManager().install(), options=options)

	browser.get('https://duckduckgo.com/?q=site%3Asiasky.net&t=h_&ia=web')

	while True:
		try:
			browser.find_elements_by_class_name('result--more__btn')[0].click()
			time.sleep(1)
		except:
			break

	results = browser.find_elements_by_class_name("result__url")

	urls = []
	for elem in results:
		urls.append(elem.text)
		
	browser.quit()
	return urls
