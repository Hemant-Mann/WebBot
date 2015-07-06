<?php
require_once '../autoloader.php';
use WebBot\lib\WebBot\Bot as Bot;

// URLs to fetch data from
$urls = [
	'bazm' => 'http://bazm.in/',
	'project' => 'http://drcmanjari15.info/',
];

// set WebBot object
$webbot = new Bot($urls);

// Execute fetch data from URLs
$webbot->execute();

// Only documents which returned status code '200' which will be returned. For failed documents
// see /tmp/log.txt which list each URL to which request has been made
$documents = $webbot->getDocuments();

// Set the filename using date-time stamp
$filename = date('F d, Y').' at '.date('g.ia'). '.dat';

// Parse each document. 
foreach ($documents as $document) {
	echo '<b>Document:</b> '. $document->id. '<br />';
	echo '<b>URL:</b> <i>'. $document->uri .'</i><br />';
	
	$save_data = "\nURL: ". $document->uri;
	// Find required pattern from the page
	$data = $document->find("/<a.*?href\s*=\s*[\"'](.*?)[\"'].*?>(.*?)<\/a>/");
	
	if($data){
		$save_data .= "\n". print_r($data[0], true);
		// Store the data of fetched documents in .dat file
		if($webbot->store("{$filename}", $save_data)) {
			echo 'Data saved <br />';
		} else {
			echo 'Failed to save data: '. $webbot->error . '<br />';
		}
	} else {
		// Pattern not found
		echo 'Requested pattern could not be found for: '. $document->uri. '<br />';
	}
}
?>