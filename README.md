# WebBot #
It is a library which makes request to webpages and extract the required data just like a bot.
The library makes use of PHP's built-in CURL library to make requests.

## Usage ##
For usage see examples folder.
- First, we include the autoloader
- Then, we pass in the list of URLs to the bot and it executes the request
- The resultant documents are returned and required data can be extracted and saved in file or database

### Notes ###
Linux/Mac users you may need to make the directory 'tmp' writable for logging purposes. You can disable logging in the bot by commenting out the lines which write to the 'log.txt' file
