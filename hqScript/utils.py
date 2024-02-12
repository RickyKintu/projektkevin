import os
import re
import unicodedata
from dotenv import load_dotenv
from openai import OpenAI

#Load environment variables
load_dotenv()

def to_friendly_filename(title, max_length=50):
    """Converts a title to a filesystem-friendly filename."""
    #Normalize unicode characters
    title = unicodedata.normalize('NFKD', title).encode('ASCII', 'ignore').decode('ASCII')
    
    #Replace spaces and unwanted characters
    title = re.sub(r'[^\w\s-]', '', title).strip().lower()
    title = re.sub(r'[\s_-]+', '-', title)
    
    #Truncate to max_length
    return title[:max_length]

def https_to_http(url):
    """Converts an HTTPS URL to HTTP."""
    return url.replace("https://", "http://", 1)

def generate_new_title(original_title):
    """Generates a new title using OpenAI's API."""
    OPENAI_API_KEY = os.environ.get("OPENAI_API_KEY")
    client = OpenAI(api_key=OPENAI_API_KEY)
    
    try:
        completion = client.chat.completions.create(
            model="gpt-3.5-turbo",
            messages=[
                {"role": "system", "content": "You are a creative video title creator"},
                {"role": "user", "content": f"Create a new title based on this: '{original_title}'. It should be creative and related to the subject. It should be a video title and perfect for SEO. Minimum 4 words. you are allowed to use click bait tactics. Just type out a related title!"}
            ]
        )
        return completion.choices[0].message.content
    except Exception as e:
        return f"An error occurred: {e}"
