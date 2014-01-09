<?php
header("Content-type: text/html; charset=UTF-8");

$access_item = array();
$access_default = "page";

$access_item = false;

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["PAGE_PATH"]."/page.class.php");

include_once("class/devices/device.core.class.php");

$ua = getVar("ua");

$deviceClass = new DeviceCore();
$identification = $deviceClass->identifyDevice($ua ? $ua : $_SERVER["HTTP_USER_AGENT"]);
if($identification && isset($identification["device_id"])) {
	$device = $deviceClass->getDeviceBase($identification["device_id"]);
}
else {
	$device = array("brand" => "unknown", "model" => "unknown", "display_width" => 0, "display_height" => 0, "segment" => "desktop_light");
}

include_once("include/notifier.php");
$message = ($ua ? $ua : $_SERVER["HTTP_USER_AGENT"])."\n".$device["brand"].", ".$device["model"]."\n".$device["segment"]."\n\n".$_SERVER["HTTP_HOST"]."\n".(getenv("HTTP_X_FORWARDED_FOR") ? getenv("HTTP_X_FORWARDED_FOR") : getenv("REMOTE_ADDR"))."\n\n";

$headers = apache_request_headers();
foreach($headers as $key => $value) {
	$message .= $key.": ".$value."\n";
}


notifier("DEBUG USERAGENT: ".($ua ? $ua : $_SERVER["HTTP_USER_AGENT"]), $message);

?>
<!DOCTYPE html>
<html>
<head></head>
<body>

<div id="device">
	<div id="useragent" class="proporty">UserAgent: <?= ($ua ? $ua : $_SERVER["HTTP_USER_AGENT"]) ?></div>
	<div id="brand" class="proporty">Brand: <?= $device["brand"] ?></div>
	<div id="model" class="proporty">Model: <?= $device["model"] ?></div>
	<div id="display_width" class="proporty">Display width: <?= $device["display_width"] ?></div>
	<div id="display_height" class="proporty">Display height: <?= $device["display_height"] ?></div>
	<div id="segment" class="proporty">Segment: <?= $device["segment"] ?></div>
</div>

</body>
</html>