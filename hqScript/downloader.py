import os
import requests
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from colorama import init, Fore
import time

init(autoreset=True)

class VideoDownloader:
    def __init__(self, url, filename):
        self.url = url
        self.filename = filename

    def download_video(self):
        """Downloads the video from the specified URL."""
        try:
            print(Fore.YELLOW + "Download time...")

            service = Service(executable_path='chromedriver.exe')
            options = webdriver.ChromeOptions()
            options.add_argument('--headless') 
            driver = webdriver.Chrome(service=service, options=options)
            driver.get(self.url)

            print(Fore.YELLOW + "Trying to get download link...")
            time.sleep(10)

            video_element = driver.find_element(By.ID, 'flvv')
            source_elements = video_element.find_elements(By.TAG_NAME, 'source')

            #Extract the video URL
            video_url = None
            for source in source_elements:
                if "1080.mp4" in source.get_attribute('src'):
                    video_url = source.get_attribute('src')
                    break

            if not video_url:
                for source in source_elements:                
                    if "720.mp4" in source.get_attribute('src'):
                        video_url = source.get_attribute('src')
                        break
                    
            if not video_url:
                for source in source_elements:
                    if "360.mp4" in source.get_attribute('src'):
                        video_url = source.get_attribute('src')
                        break
            if not video_url:
                print(Fore.RED + "video source not found")
                return False

            print(Fore.BLUE + f"Found video URL: {video_url}")

            if not video_url.startswith('http'):
                video_url = 'http:' + video_url

            driver.quit()

            #Start download
            print(Fore.YELLOW + "Starting download")
            print("This may take a while depending on your internet & hardware")
            response = requests.get(video_url, stream=True)

            if response.status_code == 200:
                total_length = response.headers.get('content-length')

                if total_length is None: 
                    print(Fore.RED + "Could not get the total file size.")
                    print("No progress can be shown :(")
                    with open(os.path.join("videos", self.filename), 'wb') as file:
                        file.write(response.content)
                else:
                    total_length = int(total_length)
                    downloaded = 0

                    with open(os.path.join("videos", self.filename), 'wb') as file:
                        for chunk in response.iter_content(chunk_size=1024):
                            if chunk:
                                downloaded += len(chunk)
                                file.write(chunk)
                                done = int(50 * downloaded / total_length)
                                percent = int(100 * downloaded / total_length)
                                print(f"\r[{'=' * done}{' ' * (50-done)}] {percent}%", end='')

                return Fore.GREEN + f"\nDownloaded {self.filename} to videos/{self.filename}"
            else:
                return Fore.RED + "Failed to retrieve video"
        except Exception as e:
            driver.quit()
            return f"An error occurred: {str(e)}"
