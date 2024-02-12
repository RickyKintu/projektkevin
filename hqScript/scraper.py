import requests
from bs4 import BeautifulSoup

class WebpageScraper:
    def __init__(self, url):
        self.url = url
        self.soup = None
        self._fetch_page()
        self.title = ""

    def _fetch_page(self):
        """Fetches the webpage content and initializes BeautifulSoup object."""
        try:
            response = requests.get(self.url)
            response.raise_for_status()
            self.soup = BeautifulSoup(response.text, 'html.parser')
        except requests.HTTPError as e:
            print(f"HTTP error: {e}")
        except requests.RequestException as e:
            print(f"Error fetching page: {e}")
        except Exception as e:
            print(f"An error occurred: {e}")

    def get_video_url(self):
        """Finds and returns the video URL from the page."""
        try:
            video_wrapper = self.soup.find('div', class_='videoWrapper')
            if not video_wrapper:
                return "No video wrapper found."

            iframe_tag = video_wrapper.find('iframe')
            if not iframe_tag or 'src' not in iframe_tag.attrs:
                return "No valid iframe tag found in video wrapper."

            iframe_src = iframe_tag['src']

            if iframe_src.startswith('//'):
                video_url = "https:" + iframe_src
            else:
                video_url = iframe_src

            print("Found video URL:", video_url)
            return video_url
        except Exception as e:
            return f"An error occurred in get_video_url: {e}"



    def get_title(self):
        """Extracts and returns the title from the page."""
        try:
            h1_tag = self.soup.find('h1').get_text(strip=True)
            self.title = h1_tag
            return h1_tag if h1_tag else "No <h1> tag found"
        except Exception as e:
            print(f"An error occurred in get_title: {e}")
            return False

    def get_tags(self):
        """Extracts and returns the tags from the page."""
        try:
            sections = self.soup.find_all('section')
            tags_section = None
            for section in sections:
                if section.find('h3', string="This video belongs to the following categories"):
                    tags_section = section
                    break

            if not tags_section:
                return "Tags section not found."

            anchors = tags_section.find_all('a', class_='tag-link')
            tags = [anchor.get_text(strip=True) for anchor in anchors]
            return tags
        except Exception as e:
            return f"An error occurred in get_tags: {e}"

    def get_duration(self):
        """Extracts and returns the duration of the video in seconds."""
        try:
            duration_li = self.soup.find('li', class_='icon fa-clock-o')
            if duration_li:
                duration_text = duration_li.get_text().strip()
                time_parts = duration_text.split(' ')

                hours = 0
                minutes = 0
                seconds = 0

                for part in time_parts:
                    if 'h' in part:
                        hours = int(part[:-1])
                    elif 'm' in part:
                        minutes = int(part[:-1])
                    elif 's' in part:
                        seconds = int(part[:-1])

                return hours * 3600 + minutes * 60 + seconds
            return "Duration not found"
        except Exception as e:
            return f"An error occurred in get_duration: {e}"


    def get_cast(self):
        try:
            cast_li = self.soup.find('li', class_='icon fa-star-o')
            if cast_li:
                cast = [a.get_text(strip=True) for a in cast_li.find_all('a')]
                return ', '.join(cast)
            return "cast not found"
        except Exception as e:
            return f"An error occurred in get_cast: {e}"

    @staticmethod
    def fetch_links(url):
        """Fetches and returns all video links from a given URL."""
        links = []
        response = requests.get(url)
        if response.status_code == 200:
            soup = BeautifulSoup(response.text, 'html.parser')
            divs_9u = soup.find_all('div', class_='9u')
            for div_9u in divs_9u:
                divs_6u = div_9u.find_all('div', class_='6u')
                for div_6u in divs_6u:
                    a_tag = div_6u.find('a', href=True)
                    if a_tag:
                        full_link = f"https://adress.com{a_tag['href']}"
                        links.append(full_link)
        return links

    @staticmethod
    def scrape_pages(base_url, query, limit=30, page=1):
        """Scrapes multiple pages for video links based on a query."""
        while page <= limit:
            url = f"{base_url}/?q={query}&p={page}"
            print("///////////////////////////////////////////")
            print(f"Fetching links from: {url}")
            print("//////////////////////////////////////////")
            links = WebpageScraper.fetch_links(url)
            if not links:
                print("No more links found or reached end of the results.")
                break
            for link in links:
                print(link)
            page += 1
        print("Completed scraping pages up to the limit or available data.")
        return links
