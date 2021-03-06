<?php
require_once '../autoloader.php';
use WebBot\Core\Bot as Bot;
use WebBot\Exceptions\Document as Doc;

$bot = new Bot(array('test' => 'http://swiftintern.com'));
$bot->execute();

$document = array_shift($bot->getDocuments());

try {
	$query = '/html/body/div';
	$el = $document->query($query);
	var_dump($el);
} catch (Doc $e) {
	echo $e->getMessage();
}
