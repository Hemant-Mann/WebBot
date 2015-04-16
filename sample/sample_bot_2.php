<?php
require_once $libpath = substr(str_replace('\\', '/', __dir__), 0, -6).'config/initialize.php'; 

// URLs to fetch data from
$urls = [
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
		// Find required pattern from the page
		$data = $document->find("/<a.*?href\s*=\s*[\"'](.*?)[\"'].*?>(.*?)<\/a>/");
		
		// If pattern/patterns is/are found
		if($data){
			// Create a string variable to store the data
			$save_data = "\nURL: ". $document->url. "\n".print_r($data, true);

			// Now call the spider function passing in the hyperlinks array
			if($spider_docs = $document->spider($data[1])) { // If documents objects are returned
				// Loop over each document which is returned
				foreach ($spider_docs as $new_doc) {
					// Find any pattern from each document, let it be the <img> tag
					$new_data = $new_doc->find("/<img.*?src\s*=\s*[\"'](.*?)[\"'].*?>/");
					if($new_data) { // If found pattern
						// Append to the save-data element
						$save_data .= "\nURL: ". $new_doc->url. "\n".print_r($new_data[0], true);
					} else {
						echo 'Failed to find the pattern for URL: '. $new_doc->url. '<br />';
					}
				}
			}

			// Store the data of each document in .dat file
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