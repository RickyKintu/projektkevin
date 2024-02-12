import requests
from dbManager import dbManager

# List of URLs to check
urls = [
    "https://127.0.0.1/uploads/00001.mp4",
    "https://127.0.0.1/uploads/00002.mp4",
    "https://127.0.0.1/uploads/00003.mp4",
    "https://127.0.0.1/uploads/00004.mp4",
]

def check_url(url):
    try:
        response = requests.get(url)
        if response.status_code == 200:
            # URL is valid and returns a video

            #db = dbManager()
            #db.update_video_link()

            return "Valid Video"
        elif response.status_code == 404:
            #URL returns a 404 error
            return "404 Error Page"
        else:
            return "Other Status Code: " + str(response.status_code)
    except Exception as e:
        # An exception occurred while checking the URL
        return "Exception: " + str(e)

# Check each URL and print the result
for url in urls:
    result = check_url(url)
    print(f"URL: {url} - Result: {result}")
