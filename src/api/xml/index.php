<?php
header("Content-type: text/xml; charset=UTF-8");
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


print '<?xml version="1.0" encoding="UTF-8"?>';
?>
<device>
    <brand><?= $device["brand"] ?></brand>
    <model><?= $device["model"] ?></model>
    <segment><?= $device["segment"] ?></segment>
    <display_width><?= $device["display_width"] ?></display_width>
    <display_height><?= $device["display_height"] ?></display_height>
</device>

