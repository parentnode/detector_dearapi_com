<?php
header("Content-type: text/javascript; charset=UTF-8");
$access_item = array();
$access_default = "page";

$access_item = false;

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["PAGE_PATH"]."/page.class.php");

include_once("class/devices/device.core.class.php");

$ua = getVar("ua");

// include _include in path - for development
$dev = getVar("dev");

// general path
$path = getVar("path");

// specific paths
$css_path = getVar("css_path");
$js_path = getVar("js_path");

// specific params
$css_param = getVar("css_param");
$js_param = getVar("js_param");


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

#<script type="text/javascript" src="http://devices.local/js-include/"></script>
#<script type="text/javascript" src="http://devices.dearapi.com/js-include/"></script>
#<script type="text/javascript" src="http://devices.dearapi.com/js-include/?test=true"></script>

// what file to include?
$file = ($dev ? "lib/" : "")."seg_".$device["segment"].($dev ? "_include" : "");

?>

<? if($css_path): ?>
document.write('<link type="text/css" rel="stylesheet" media="all" href="<?= $css_path ?>/<?= $file ?>.css<?= $css_param ? "?$css_param" : "" ?>" />');
<? else: ?>
document.write('<link type="text/css" rel="stylesheet" media="all" href="<?= $path ? $path : "" ?>/css/<?= $file ?>.css<?= $css_param ? "?$css_param" : "" ?>" />');
<? endif; ?>

<? if($js_path): ?>
document.write('<script type="text/javascript" src="<?= $js_path ?>/<?= $file ?>.js<?= $js_param ? "?$js_param" : "" ?>"></script>');
<? else: ?>
document.write('<script type="text/javascript" src="<?= $path ? $path : "" ?>/js/<?= $file ?>.js<?= $js_param ? "?$js_param" : "" ?>"></script>');
<? endif; ?>

document.cookie = "segment=<?= $device["segment"] ?>";