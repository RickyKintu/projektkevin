import mysql.connector
from mysql.connector import Error
import traceback
from datetime import datetime



class dbManager:
    #def __init__(self, host='', database='', user='', password=''):
    def __init__(self, host='', database='', user='', password=''):
        self.host = host
        self.database = database
        self.user = user
        self.password = password
        self.connection = None
        self.connect()

    def connect(self):
        try:
            self.connection = mysql.connector.connect(
                host=self.host,
                database=self.database,
                user=self.user,
                password=self.password
            )
            print("Database connection successful.")
        except Error as e:
            print(f"Error connecting to MySQL: {e}")
            traceback.print_exc()  # Print full traceback

    def close_connection(self):
        if self.connection and self.connection.is_connected():
            self.connection.close()

    def add_to_db(self, title, tags, cast, thumbnails, duration, video1, video2, source):
        if self.connection is None or not self.connection.is_connected():
            print("No database connection available.")
            return False

        try:
            cursor = self.connection.cursor()
            sql_insert_query = """
                INSERT INTO videos (title, tags, cast, thumbnails, duration, video1, video2, source)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
            """
            values_to_insert = (title, tags, cast, thumbnails, duration, video1, video2, source)
            cursor.execute(sql_insert_query, values_to_insert)
            self.connection.commit()
            print("Video data inserted successfully into the database.")
        except Error as e:
            print("Error while executing to MySQL", e)
            return False
        finally:
            if cursor is not None:
                cursor.close()

    def source_exists(self, source):
        if self.connection is None or not self.connection.is_connected():
            print("No database connection available.")
            return False

        try:
            cursor = self.connection.cursor()
            query = "SELECT COUNT(*) FROM videos WHERE source = %s"
            cursor.execute(query, (source,))
            result = cursor.fetchone()
            return result[0] > 0
        except Error as e:
            print("Error while executing to MySQL", e)
            return False
        finally:
            if cursor is not None:
                cursor.close()


    def article_to_db(self, title, article, tags, source):
        """Inserts the generated article into the MySQL database."""
        if self.connection is None or not self.connection.is_connected():
                print("No database connection available.")
                return False
        
        if self.article_source_exists(source):
            print(f"Source '{source}' already exists. Article not inserted.")
            return False
        try:

            cursor = self.connection.cursor()  

            insert_query = """
            INSERT INTO article (title, content, tags, source, views, created_at)
            VALUES (%s, %s, %s, %s, %s, %s)
            """
            values = (title, article, tags, source, 0, datetime.now())
            cursor.execute(insert_query, values)
            self.connection.commit()
            print(f"Article titled '{title}' inserted into database.")

        except mysql.connector.Error as e:
            print(f"Error: {e}")
        finally:
            cursor.close()
            self.close_connection()


    def article_source_exists(self, source):
        """Check if a source already exists in the database."""
        if self.connection is None or not self.connection.is_connected():
            print("No database connection available.")
            return False

        try:
            cursor = self.connection.cursor()
            query = "SELECT COUNT(*) FROM article WHERE source = %s"
            cursor.execute(query, (source,))
            result = cursor.fetchone()
            return result[0] > 0
        except mysql.connector.Error as e:
            print(f"Error: {e}")
            return False
        finally:
            cursor.close()
        
    def update_video_link(self, video_id, link):
        if self.connection is None or not self.connection.is_connected():
            print("No database connection available.")
            return False

        try:
            cursor = self.connection.cursor()
            sql_update_query = "UPDATE videos SET video4 = %s WHERE id = %s"
            values_to_update = (link, video_id)
            cursor.execute(sql_update_query, values_to_update)
            self.connection.commit()
            print(f"------- Updated video link for ID {video_id} to {link} -----------")
            return True
        except Error as e:
            print("Error while executing to MySQL", e)
            return False
        finally:
            if cursor is not None:
                cursor.close()
                

    def __del__(self):
        self.close_connection()


