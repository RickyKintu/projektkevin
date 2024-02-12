from scraper import WebpageScraper
from downloader import VideoDownloader
from utils import to_friendly_filename, generate_new_title
from streams.VideoDownloader import VideoUploader
from generate_thumbnails import generate_thumbnails
from dbManager import dbManager
import threading
from datetime import datetime



def upload_video(uploader, file_path, file_title, fld_id, tags, file_public, results, key):
    result = uploader.upload_file(file_path, file_title, fld_id, tags, file_public)
    results[key] = result

def main():
    success = 0
    failed = 0
    already = 0
    start_time = datetime.now().strftime("%H:%M:%S")
    print("Script started: ", start_time)
    



    
    file_path = 'hqScript/links.txt'

    scrape_list = []

    try:
        with open(file_path, 'r') as file:
            for line in file:
                #Strip whitespace and add to the list if the line is not empty
                stripped_line = line.strip()
                if stripped_line:
                    scrape_list.append(stripped_line)
    except FileNotFoundError:
        print(f"File not found: {file_path}")

    print(scrape_list)



    for url in scrape_list:
        try:
            scraper = WebpageScraper(url)
            source = url

            db = dbManager()
            if db.source_exists(source):
                print(f"The source '{source}' already exists in the database.")
                already = already + 1
                continue

            original_title = scraper.get_title()

            if(original_title == False):
                failed = failed + 1
                continue

            video_url = scraper.get_video_url()
                    

            title = generate_new_title(original_title)
            filename = to_friendly_filename(title)
            videofile = filename + ".mp4"
            tags = scraper.get_tags()
            duration = scraper.get_duration()
            cast = scraper.get_actresses()


            print("Original title: ",original_title)
            print("New Title: ", title)
            print("Tags: ", tags)
            print("Cast: ", cast)
            print("Duration(s): ", duration)

            downloader = VideoDownloader(video_url, videofile)
            download_message = downloader.download_video()
            print(download_message)



            file_path = "videos/" + videofile
            file_title = original_title
            fld_id = 71511  # 27632 
            
            
            file_public = 1
            file_adult = 1

            thumbnails = generate_thumbnails(filename, file_path)
            upload_start_time = current_time = datetime.now().strftime("%H:%M:%S")
            print("Time starting upload: ", upload_start_time)
            
            a_uploader = VideoUploader("1")

            b_uploader = VideoUploader("2")


            #Shared dictionary to store results from threads
            upload_results = {}

            #Creating threads for uploading
            streamwish_thread = threading.Thread(target=upload_video, args=(a_uploader, file_path, file_title, 71511, tags, file_public, file_adult, upload_results, 'video1'))
            vidhide_thread = threading.Thread(target=upload_video, args=(b_uploader, file_path, file_title, 27632, tags, file_public, file_adult, upload_results, 'video2'))

            streamwish_thread.start()
            vidhide_thread.start()

            #Waiting for both threads to finish
            streamwish_thread.join()
            vidhide_thread.join()

            #Retrieving results
            video1 = upload_results.get('video1')
            print("Streamwish EmbedLink:", video1)
            video2 = upload_results.get('video2')
            print("VidHide EmbedLink:", video2)
            upload_end_time = current_time = datetime.now().strftime("%H:%M:%S")
            print("Time upload end: ", upload_end_time)
            
            # # Convert to datetime objects
            # format = '%H:%M:%S'
            # UST = datetime.strptime(upload_start_time, format)
            # UET = datetime.strptime(upload_end_time, format)

            # # Calculate the difference
            # difference = UST - UET

            # # Calculating the total difference in hours, minutes, and seconds
            # total_seconds = int(difference.total_seconds())
            # hours = total_seconds // 3600
            # minutes = (total_seconds % 3600) // 60
            # seconds = total_seconds % 60

            # time_difference_str = f"It took {hours} hours, {minutes} minutes, and {seconds} seconds."
            # print(time_difference_str)

            # print("Upload took: ", difference)

            tags = ', '.join(str(item) for item in tags)

            db = dbManager()
            if db.source_exists(source):
                print(f"The source '{source}' already exists in the database. Skipping data processing and upload.")
                return
            else:
                print(f"Title: {title}, Tags: {tags}, Cast: {cast}, Thumbnails: {thumbnails}, Duration: {duration}, Video1: {video1}, Video2: {video2}, Source: {source}")
                db.add_to_db(title, tags, cast, thumbnails, duration, video1, video2, source)
                success = success + 1



        except FileNotFoundError:
            print(f"File not found for URL: {url}. Skipping to the next URL.")
            failed = failed + 1
            continue
        except Exception as e:
            print(f"An error occurred for URL: {url}. Error: {e}. Skipping to the next URL.")
            failed = failed + 1
            continue
        finally:
            print("Sucess: ", success)
            print("Failed: ", failed)
            print("Already: ", already)


if __name__ == "__main__":
    main()

    
