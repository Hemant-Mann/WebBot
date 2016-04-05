<?php
namespace WebBot\Core\WebBot;
use WebBot\Core\Bot as Bot;

// set unlimited execution time
set_time_limit(0);

// set the default timeout to 1 seconds
Bot::$conf_default_timeout = 1;

// set delay between consecutive fetches (in milli seconds)
Bot::$conf_delay_between_fetches = 1000;

// do not use HTTPS protocol
Bot::$conf_force_https = false;

// don't include document field raw values
Bot::$conf_include_document_field_raw_values = false;

// set the directory for storing information
// $dir = your/custom/path;
if (defined('LIB_PATH')) {
	$dir = LIB_PATH . '/WebBot/tmp/';
} else {
	$dir = '../tmp/';
}
Bot::$conf_store_dir = $dir;