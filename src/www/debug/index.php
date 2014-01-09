<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}


include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");
include_once("class/identify.class.php");

$action = $page->actions();

$ua = stringOr(getVar("ua"), $_SERVER["HTTP_USER_AGENT"]);


$Identify = new Identify();
$device = $Identify->identifyDevice($ua, false);



// prepare content for email
$message = ($ua ? $ua : $_SERVER["HTTP_USER_AGENT"])."\n".print_r($device, true)."\n\n";
$headers = apache_request_headers();
foreach($headers as $key => $value) {
	$message .= $key.": ".$value."\n";
}

$page->mail(array("subject" => "DEBUG USERAGENT: ".$ua, "message" => $message, "template" => "system"));

header("Content-type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html>
<body>

<div id="device">
	<div id="useragent" class="proporty">UserAgent: <?= $ua ?></div>
	<div id="segment" class="proporty">Segment: <?= $device["segment"] ?></div>
	<div id="model" class="proporty">Model: <?= $device["name"] ?></div>
	<div id="description" class="proporty">Description: <?= $device["description"] ?></div>
	<div id="published_at" class="proporty">Published at: <?= $device["published_at"] ?></div>
<? if($device["tags"]): ?>
<?	foreach($device["tags"] as $tag): ?>
	<div id="<?= $tag["context"] ?>" class="proporty"><?= $tag["context"] ?>:<?= $tag["value"] ?></div>
<?	endforeach; ?>
<? endif; ?>
</div>

</body>
</html>