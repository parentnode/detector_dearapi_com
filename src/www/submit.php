<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}


include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


include_once("classes/identify.class.php");

$action = $page->actions();

$ua = stringOr(getVar("ua"), isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "UA not available");


// Add useragent to unidentified without doing any identification
$query = new Query();

$site = getVar("site");
$file = getVar("file");

$comment = "Submitted from: ";
if($site) {
	$comment .= preg_replace("/\/$/", "", $site)."/".$file . "\n";
}
else {
	$comment .= "Unknown\n";
}

$headers = apache_request_headers();
foreach($headers as $key => $value) {
	$comment .= "$key: $value\n";
}

$query->sql("INSERT INTO ".SITE_DB.".unidentified_useragents VALUES(DEFAULT, '$ua', '$comment', '', DEFAULT)");

?>
success