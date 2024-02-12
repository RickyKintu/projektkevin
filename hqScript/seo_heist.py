import xml.etree.ElementTree as ET
import os
from dotenv import load_dotenv
from openai import OpenAI
from dbManager import dbManager


def generate_article_title(text):
    """Generates a new title using OpenAI's API."""
    OPENAI_API_KEY = os.environ.get("OPENAI_API_KEY")
    client = OpenAI(api_key=OPENAI_API_KEY)
    
    try:
        completion = client.chat.completions.create(
            model="gpt-3.5-turbo",
            messages=[
                {"role": "system", "content": "You are a creative creator assistant"},
                {"role": "user", "content": f"Create a SEO optimized article title based on this: '{text}'. It should be creative... Click bait."}
            ]
        )
        return completion.choices[0].message.content
    except Exception as e:
        return f"An error occurred: {e}"
    

def generate_article(title):
    """Generates a new title using OpenAI's API."""
    OPENAI_API_KEY = os.environ.get("OPENAI_API_KEY")
    client = OpenAI(api_key=OPENAI_API_KEY)
    
    try:
        completion = client.chat.completions.create(
            model="gpt-3.5-turbo",
            messages=[
                {"role": "system", "content": "Roletext..."},
                {"role": "user", "content": f"Create a SEO optimized article, the title is'{title}'. It should be 150 words. and only write out the article not the title."}
            ]
        )
        return completion.choices[0].message.content
    except Exception as e:
        return f"An error occurred: {e}"
    
def generate_tags(title,article):
    """Generates a new title using OpenAI's API."""
    OPENAI_API_KEY = os.environ.get("OPENAI_API_KEY")
    client = OpenAI(api_key=OPENAI_API_KEY)
    
    try:
        completion = client.chat.completions.create(
            model="gpt-3.5-turbo",
            messages=[
                {"role": "system", "content": "Roletext"},
                {"role": "user", "content": f"Create a SEO optimized tags for given title and content return a comma seperated string with 5 tags, title ({title}) content ({article}),  remeber only to return a string like this tags,tags,tags,tags,tags ITS very important!"}
            ]
        )
        return completion.choices[0].message.content
    except Exception as e:
        return f"An error occurred: {e}"

# Path to the downloaded XML file
xml_file = 'hqScript/lpv.xml'

# Parse the XML file
tree = ET.parse(xml_file)
root = tree.getroot()

# Define the namespace
namespace = {'ns': 'http://www.sitemaps.org/schemas/sitemap/0.9'}

# Extract and print the first 50 URLs
extracted_urls = [url.text for url in root.findall('ns:url/ns:loc', namespace)[:20]]
last_keys = [url.split('/')[-1] for url in extracted_urls]
for source in last_keys:
    db = dbManager()

    if db.article_source_exists(source):
        print(f"Source '{source}' already exists. Article not created.")
        continue    
        

    title = generate_article_title(source).replace('"', '')
    print("title:")
    print(title)
    print("article:")
    article = generate_article(title)
    print(article)
    print("tags:")
    tags = generate_tags(title,article)
    print(tags)

    

    db.article_to_db(title, article, tags, source)
    
    


    

