<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}


include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");
include_once("classes/helpers/identify.class.php");

$action = $page->actions();

$ua = stringOr(getVar("ua"), isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "");


$Identify = new Identify();
$device = $Identify->identifyDevice($ua, false);



// prepare content for email
$message = ($ua ? $ua : $_SERVER["HTTP_USER_AGENT"])."<br><pre>".print_r($device, true)."</pre><br>";
$headers = apache_request_headers();
foreach($headers as $key => $value) {
	$message .= $key.": ".$value."<br>";
}

mailer()->send(array(
	"subject" => "DEBUG USERAGENT: ".$ua, 
	"message" => $message, 
	"template" => "system",
	"tracking" => false
));

header("Content-type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html>
<body>

<h1>Thank you for submitting your information</h1>
<p>Your browser details has been emailed to the API administrator.</p>

<div id="device">
	<div id="useragent" class="proporty">UserAgent: <?= $ua ?></div>
	<div id="segment" class="proporty">Segment: <?= $device["segment"] ?></div>
	<div id="model" class="proporty">Name: <?= $device["name"] ?></div>
	<div id="description" class="proporty">Description: <?= isset($device["description"]) ? $device["description"] : "N/A" ?></div>
	<div id="published_at" class="proporty">Published at: <?= isset($device["published_at"]) ? date("Y-m", strtotime($device["published_at"])) : "N/A" ?></div>
</div>

</body>
</html>