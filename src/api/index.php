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

// $device_id = $deviceClass->identifyDevice($ua ? $ua : $_SERVER["HTTP_USER_AGENT"]);
// $device = $deviceClass->getDeviceBase($device_id);

//print $deviceClass->perf->result();
?>
<!DOCTYPE html>
<div id="device">
	<span id="brand" class="proporty"><?= $device["brand"] ?></span>
	<span id="model" class="proporty"><?= $device["model"] ?></span>
	<span id="display_width" class="proporty"><?= $device["display_width"] ?></span>
	<span id="display_height" class="proporty"><?= $device["display_height"] ?></span>
	<span id="segment" class="proporty"><?= $device["segment"] ?></span>
</div>
