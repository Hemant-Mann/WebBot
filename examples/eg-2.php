<?php
require '../autoloader.php';
use WebBot\lib\HTTP\Request as Request;

$pageHeaders = Request::head("http://drcmanjari15.info/");

echo "<pre>". print_r($pageHeaders, true). "</pre>";

?>
