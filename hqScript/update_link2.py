import requests
from dbManager import dbManager


def check_url(url):
    try:
        response = requests.get(url)
        if response.status_code == 200:

            return "Valid Video"
        elif response.status_code == 404:
            # URL returns a 404 error
            return False
        else:
            # Other status code, not sure about the response
            return False
    except Exception as e:
        # An exception occurred while checking the URL
        return "Exception: " + str(e)

def extract_ids_and_links_from_file(file_path):
    try:
        with open(file_path, 'r') as file:
            lines = file.readlines()

        ids_and_links = []
        for line in lines:
            parts = line.strip().split('|')
            if len(parts) == 2:
                video_id = int(parts[0].strip())
                video_link = parts[1].strip()
                ids_and_links.append((video_id, video_link))

        return ids_and_links
    except Exception as e:
        print(f"Error while reading from the file: {e}")
        return False

#List of URLs to check
file_path_to_extract_data = "hqScript/links.txt"

#Extract video IDs and links from the text file
result_data = extract_ids_and_links_from_file(file_path_to_extract_data)

if result_data:

    for video_id, video_link in result_data:
        result = check_url(video_link)
        print(f"Video ID: {video_id} - Link: {video_link} - Result: {result}")
        
        if(result):
            db = dbManager()
            print("Updating database")
            db.update_video_link(video_id, video_link)
else:
    print("Failed to extract data from the file.")
