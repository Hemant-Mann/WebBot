<?php
// Define Directory Separator
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

// Site Root i.e. root directory of the project
defined('SITE_ROOT') ? null : 
	define('SITE_ROOT', '/opt/lampp/htdocs/WebBot/');

define('LIB', SITE_ROOT.DS.'lib'.DS);

// Working directories
$dir_http = LIB.'HTTP'.DS;
$dir_webBot = LIB.'WebBot'.DS;
$dir_tmp = SITE_ROOT.'tmp'.DS;
?>
