import modules
dl_portal = 'https://skyportal.xyz/'

skynet_portals = modules.get_skynet_portals()

urls = []
urls += modules.exact_urls_from_page("https://skynethub-api.herokuapp.com/skapps?limit=100")
print('collected', len(urls), 'urls')
urls += modules.get_data_from_duckduckgo()
print('collected', len(urls), 'urls')

urls += modules.exact_urls_from_google()
	
print('collected', len(urls), 'urls')
print(urls)

for url in urls:
	skynet_path = modules.get_skynet_path(url, skynet_portals)
	if skynet_path != False:
		modules.insert_url_to_db(skynet_path)

		
text_update_success = 0
text_update_fail = 0
text_update_other = 0
invalid_file = 0

skylink_for_update = modules.get_skylinks_to_update()
for skylink in skylink_for_update:
	skypath = skylink['skypath'].decode()
	print('\nUpdating header for', skypath)
	header = modules.get_headers(dl_portal + skypath)
	valid_file = modules.update_headers_in_db(skypath, header)

	if valid_file:
		print('Updating text for', skypath)
		text_and_meta = modules.get_text_from_file(dl_portal + skypath, header)

		if text_and_meta:
			file_text = text_and_meta['text']
			file_title = text_and_meta['title']
			file_description = text_and_meta['description']
			print('UPDATED:', skypath)
			text_update_success += 1
			if  text_and_meta['contenttype'].startswith('image'):
				print('Creating  thumbnail for', skypath)
				modules.create_thumbnail(skypath, dl_portal)
		else:
			file_text = ''
			file_title = ''
			file_description = ''
			print('Empty page:', dl_portal + skypath)
			text_update_fail += 1
			
		modules.update_text_in_db(skypath, file_text, file_title, file_description)
	else:
		invalid_file += 1


print('\n', text_update_success, 'row updated,', text_update_fail, 'is too big and', invalid_file, 'url is invalid')