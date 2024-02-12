import requests
import os

class VideoUploader:
    def __init__(self, server_name):
        self.servers = {
            "stream1": {
                "key": "",
                "url": "",
                "embed": ""
            },
            "stream2": {
                "key": "",
                "url": "",
                "embed": ""
            }
        }

        print("Trying to upload to: ", server_name)

        if server_name not in self.servers:
            raise ValueError("Invalid server name provided")

        self.api_key = self.servers[server_name]["key"]
        self.server_url = self.servers[server_name]["url"]
        self.embed = self.servers[server_name]["embed"]

    def get_upload_server(self):
        """Fetch the URL of the upload server."""
        params = {"key": self.api_key}
        response = requests.get(self.server_url, params=params)
        if response.status_code == 200:
            data = response.json()
            if data["status"] == 200:
                return data["result"]
            else:
                raise Exception("Failed to get upload server: " + data.get("msg", ""))
        else:
            raise Exception("Failed to connect to the API")

    def upload_file(self, file_path, file_title, fld_id, tags, file_public):
        """Uploads a file to the specified server."""
        upload_url = self.get_upload_server()
        full_path = os.path.abspath(file_path)        
        files = {'file': open(full_path, 'rb')}
        data = {
            'key': self.api_key,
            'file_title': file_title,
            'fld_id': fld_id,
            'tags': tags,
            'file_public': file_public,
        }
        response = requests.post(upload_url, files=files, data=data)
        if response.status_code == 200:
            response_data = response.json()
            if 'files' in response_data and response_data['files']:
                return self.embed + response_data['files'][0].get('filecode')
            else:
                raise Exception("File upload succeeded but no filecode was returned.")
        else:
            raise Exception("Failed to upload file")

