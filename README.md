# Documentation for `The Spotify Experience`

## Purpose
The `index.php` file is a PHP script that retrieves a user's playlists from the Spotify API and displays them on a webpage. The script handles errors if the access token cannot be retrieved or if there are no playlists found.

## Dependencies
The script requires the following dependencies:
- `dotenv` PHP package
- `jQuery` JavaScript library
- `Bootstrap` CSS framework
- `Masonry` JavaScript library

## Usage
The script is designed to be run on a web server that supports PHP. To use the script, you will need to do the following:
1. Set up a web server that supports PHP.
2. Copy the `index.php` file to the appropriate directory on your web server.
3. Set up environment variables for your Spotify API credentials (`SPOTIFY_CLIENT_ID` and `SPOTIFY_CLIENT_SECRET`).
4. Load the `index.php` file in a web browser.

When the `index.php` file is loaded in a web browser, it will retrieve the user's playlists from the Spotify API and display them on the webpage.

## Code Overview
The `index.php` file is divided into several sections, each of which serves a specific purpose. Here is an overview of each section:

### Section 1: Redirect if not logged in
This section includes an `include` statement that redirects the user to a login page if they are not logged in.

### Section 2: Debugging
This section sets up debugging options for the script.

### Section 3: Load environment variables
This section loads the `dotenv` PHP package and sets up environment variables for the Spotify API credentials.

### Section 4: Set up the API request to get the user's playlists
This section sets up the API request to get the user's playlists from the Spotify API.

### Section 5: Set up authentication options
This section sets up authentication options for the API request.

### Section 6: Create stream context for authentication
This section creates a stream context for authentication.

### Section 7: Get access token from Spotify API
This section retrieves an access token from the Spotify API.

### Section 8: Get the user's playlists with pages of 50 playlists
This section retrieves the user's playlists from the Spotify API.

### Section 9: Handle case where no playlists are found
This section handles the case where no playlists are found.

### Section 10: Get user's display name
This section retrieves the user's display name from the Spotify API.

### Section 11: Display playlists on webpage
This section displays the user's playlists on the webpage using HTML, CSS, and JavaScript.

## Conclusion
The `index.php` file is a PHP script that retrieves a user's playlists from the Spotify API and displays them on a webpage. The script is designed to be run on a web server that supports PHP and requires several dependencies, including the `dotenv` PHP package, `jQuery` JavaScript library, `Bootstrap` CSS framework, and `Masonry` JavaScript library. The script is divided into several sections, each of which serves a specific purpose, including setting up the API request, retrieving the user's playlists, and displaying them on the webpage.