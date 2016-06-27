# WebBot #
It is a library which makes request to webpages and extract the required data just like a bot.
The library makes use of PHP's built-in CURL library to make requests.

## Usage ##
For usage see examples folder.
- First, we include the autoloader
- Then, we pass in the list of URLs to the bot and it executes the request
- The resultant documents are returned and required data can be extracted and saved in file or database


Basic Example
```php
<?php
require 'autoloader.php';
use WebBot\Core\Bot as Bot;

$bot = new Bot(array(
			'url' => 'http://www.youtube.com/'
		), array(
			'logging' => true, // or false (Default: false)
			'log_dir' => null // (Default: "./tmp")
		));

// execute
$bot->execute();

$documents = $bot->getDocuments();
foreach ($documents as $doc) {
	$body = $doc->getBody(); // will return whole html as string
	
	$pattern = "/<a.*?href\s*=\s*(.*?).*?>(.*?)<\/a>/";
	$find = $doc->find($pattern);
	
	$query = $doc->query('//xpath');
	..
}

?>
```

### Notes ###
- Log Directory must be writable if logging is enabled
- You can enable logging in the bot by setting it in constructor or chaning in "Core/bootstrap.php" 

### Installation
```bash
git clone https://github.com/Hemant-Mann/WebBot.git
cd WebBot
chmod 777 tmp
```