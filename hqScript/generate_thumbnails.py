import imageio
import os
import random
import ftplib

def generate_thumbnails(filename, video_path, count=5):
    print("Trying to create thumbnails")
    output_folder = "thumbnails"
    thumbnail_filenames = []

    if not os.path.exists(output_folder):
        os.makedirs(output_folder)

    reader = imageio.get_reader(video_path)

    #Get video metadata
    meta_data = reader.get_meta_data()
    fps = meta_data.get('fps', 30)  #Default fps to 30 if not found
    duration = meta_data.get('duration')  
    if duration is None:
        print("Unable to determine video duration.")
        return

    #Calculate intervals for screenshots in seconds
    intervals = [duration / (count + 1) * i for i in range(1, count + 1)]

    #Generate and save thumbnails with random prefixes
    for i, timepoint in enumerate(intervals):
        random_prefix = random.randint(10000, 99999)
        frame_number = int(timepoint * fps)
        frame = reader.get_data(frame_number)
        output_filename = os.path.join(output_folder, f"{random_prefix}_{filename}_{i + 1}.png")
        imageio.imwrite(output_filename, frame)
        thumbnail_filenames.append(output_filename)

        print(f"Thumbnail {i + 1} generated at time {timepoint:.2f}s, frame {frame_number}.")

    #Return comma-separated string of thumbnail filenames
    result = ', '.join(thumbnail_filenames)
    upload_files(result)

    return result.replace("\\", "/")

def upload_files(thumbnail_filenames, passive_mode=True):
    ftp_host = ''
    ftp_user = ''
    ftp_pass = ''

    print("Trying to upload thumbnails online")
    thumbnail_filenames = thumbnail_filenames.split(', ')

    try:
        with ftplib.FTP(ftp_host) as ftp:
            ftp.login(ftp_user, ftp_pass)

            if passive_mode:
                ftp.set_pasv(True)

            print(f"Current FTP directory: {ftp.pwd()}")

            for filename in thumbnail_filenames:
                try:
                    with open(filename, 'rb') as file:
                        dest_name = os.path.basename(filename)
                        ftp.storbinary('STOR ' + dest_name, file)
                        print(f"Uploaded {filename}")
                except FileNotFoundError:
                    print(f"File not found: {filename}")
                except Exception as e:
                    print(f"Error uploading {filename}: {e}")
    except ftplib.all_errors as e:
        print(f"FTP error: {e}")

generate_thumbnails("", "", count=5)