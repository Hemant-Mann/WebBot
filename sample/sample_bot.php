<?php
require_once $libpath = substr(str_replace('\\', '/', __dir__), 0, -6).'config/initialize.php'; 

// URLs to fetch data from
$urls = [
	'bazm' => 'http://bazm.in/',
	'project' => 'http://s2dentportal.com/olx/',
];

// set WebBot object
$webbot = new \WebBot\WebBot($urls);

// Execute fetch data from URLs
$webbot->execute();

// Get all the documents resulted from execute call
$documents = $webbot->getDocuments();

$filename = date('F d, Y').' at '.date('g.ia'). '.dat';

// Parse each document
foreach ($documents as $document) {
	echo '<b>Document:</b> '. $document->id. '<br />';
	echo '<b>URL:</b> <i>'. $document->url .'</i><br />';
	
	// Document was fetched
	if($document->success) {
		$save_data = "\nURL: ". $document->url;
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
			echo 'Requested pattern could not be found for: '. $document->url. '<br />';
		}
	} else {
		echo $document->error.'<br />';
	}
}

?>