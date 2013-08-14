<?php
//session_start();
$access_item = false;if(isset($read_access) && $read_access){return;}
error_reporting(E_ALL);
set_time_limit(0);

include_once($_SERVER["FRAMEWORK_PATH"]."/class/system/page.class.php");

include_once($_SERVER["FRAMEWORK_PATH"]."/include/functions_wurfl.php");
require_once($_SERVER["FRAMEWORK_PATH"]."/include/wurfl/WURFL/WURFLManagerProvider.php");

$wurflConfigFile = $_SERVER["FRAMEWORK_PATH"]."/include/wurfl/resources/wurfl-config.xml";
$wurflManager = WURFL_WURFLManagerProvider::getWURFLManager($wurflConfigFile);

$devices = $wurflManager->getAllDevicesID();
$brands = array();

foreach($devices as $key => $device_id) {

	$device = $wurflManager->getDevice($device_id);
	$brand = $device->getCapability("brand_name");

	if($brand == "") {
		print "Unknown phone: ".$device->userAgent."<br>";
	}

	if($device->actualDeviceRoot && $brand != "") {

		// model (required to look up existing devices)
		$model_name = $device->getCapability("model_name");
		$model_extra_info = $device->getCapability("model_extra_info");
		$brands[$brand][$device->id]["model"] = $model_name.($model_extra_info ? ", $model_extra_info" : "");


		// contenttypes
		// ADD NEW types here

		// display:WxH
		$resolution_width = $device->getCapability("resolution_width");
		$resolution_height = $device->getCapability("resolution_height");
		$brands[$brand][$device->id]["display"] = "display/".$resolution_width."x".$resolution_height;

		// browser:name version
		$browser = $device->getCapability("mobile_browser");
		$browser_version = $device->getCapability("mobile_browser_version");
		$brands[$brand][$device->id]["browser"] = ($browser != "" ? "browser/".$browser.($browser_version ? "/".$browser_version : "") : "");

		// pointing:method (can be blank)
		$pointing_method = $device->getCapability("pointing_method");
		$brands[$brand][$device->id]["pointing"] = ($pointing_method != "" ? "pointing/".$pointing_method : false);

		// markup
		$xhtml_support_level = $device->getCapability("xhtml_support_level");
		$brands[$brand][$device->id]["xhtml"] = "xhtml_level/".$xhtml_support_level;

		// mp3
		$mp3 = $device->getCapability("mp3");
		$brands[$brand][$device->id]["mp3"] = ($mp3 == "true" ? "audio/mp3" : false);

		// jpg
		$jpg = $device->getCapability("jpg");
		$brands[$brand][$device->id]["jpg"] = ($jpg == "true" ? "image/jpg" : false);

		// release_date
		$released = $device->getCapability("release_date");
		$brands[$brand][$device->id]["released"] = ($released != "" ? "released/$released" : false);

		// useragents
		$brands[$brand][$device->id]["useragents"][] = $device->userAgent;


	}
	else if(isset($brands[$brand][$device->fallBack]) && $brand != ""){
		$brands[$brand][$device->fallBack]["useragents"][] = $device->userAgent;
	}

}

foreach($brands as $brand => $devices) {

	print "create brand:" . $brand . "<br>";
	$brand_id = createBrand($brand);

	foreach($devices as $device) {

		$device_id = createDevice($device["model"], $brand_id);

		addContenttype($device_id, $device["display"]);
		addContenttype($device_id, $device["browser"]);
		addContenttype($device_id, $device["pointing"]);
		addContenttype($device_id, $device["xhtml"]);
		addContenttype($device_id, $device["mp3"]);
		addContenttype($device_id, $device["jpg"]);
		addContenttype($device_id, $device["released"]);


		foreach($device["useragents"] as $useragent) {

			addUseragent($device_id, $useragent);
//			print $useragent."<br>";
		}
	}
}

?>






	?>