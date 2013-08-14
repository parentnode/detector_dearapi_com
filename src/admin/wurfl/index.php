<?php
$access_item = false;if(isset($read_access) && $read_access){return;}

set_time_limit(0);

include_once($_SERVER["FRAMEWORK_PATH"]."/class/system/page.class.php");
include_once($_SERVER["FRAMEWORK_PATH"]."/include/functions_wurfl.php");

require_once($_SERVER["FRAMEWORK_PATH"]."/include/wurfl/WURFL/WURFLManagerProvider.php");


$wurflConfigFile = $_SERVER["FRAMEWORK_PATH"]."/include/wurfl/resources/wurfl-config.xml";
$wurflManager = WURFL_WURFLManagerProvider::getWURFLManager($wurflConfigFile);

?>
<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		.ok,
		.ok .details {
			border: 1px solid #22A84B;
			color: #22A84B;
			background-color: #D6F7D6;
			padding: 2px 2px 0;
			margin-bottom: 4px;
		}
		.ok a {font-weight: bold; color: #22A84B;}
		.error,
		.error .details {
			border: 1px solid #CC0000;
			color: #CC0000;
			background-color: #ECD8D8;
			padding: 2px 2px 0;
			margin-bottom: 4px;
		}
		.error a {font-weight: bold; color: #CC0000;}

		.details {display: none;}
		div:hover .details {display: block;}
	</style>
	<script type="text/javascript">
		var U = new Object();
		U.addUA = function() {
		
		}
	</script>
</head>
<body>
<?

$devices = $wurflManager->getAllDevicesID();
$brands = array();

$query = new Query();
$crossquery = new Query();

// loop through all devices
foreach($devices as $key => $device_id) {

	$device = $wurflManager->getDevice($device_id);
	$brand = $device->getCapability("brand_name");

	// if no brand, look for UA and handle situation appropriately
	if($brand == "") {

//		print "Unknown phone: ".$device->userAgent."<br>";

	}
	// Brand attribute is set
	else {

		$model_name = $device->getCapability("model_name");
		$brand_id = createBrand($brand);

		// device has no model name, is it really a device then?
		// I have looked through the matches and conclude it is not devices - DO Nothing
		if($model_name == "") {

			// print '<div class="error">Device has no model name - '.$device->userAgent.'</div>';

		}
		// device has model name and brand
		else {

			// look for useragent in database
			if($query->sql("SELECT device_id FROM ".UT_DEV_USE." WHERE useragent = '".$device->userAgent."'")) {
				
				$device_id = $query->getQueryResult(0, "device_id");

				// more than one occurrence of useragent
				if($query->getQueryCount() > 1) {
					print '<div class="error">Duplicate UA, check manually - '.$device->userAgent.'</div>';
				}
				// UA exists
				else {


		//			print "SELECT id FROM ".UT_DEV." WHERE model = '$model_name' AND brand_id = $brand_id<br>";

					if(!$crossquery->sql("SELECT id FROM ".UT_DEV." WHERE model = '$model_name' AND brand_id = $brand_id")) {
//						$xdevice_id = $crossquery->getQueryResult(0, "id");
						
						
						$crossquery->sql("SELECT model, name FROM ".UT_DEV.", ".UT_BAS_BRA." WHERE ".UT_DEV.".id = $device_id AND ".UT_DEV.".brand_id = ".UT_BAS_BRA.".id");
						$x_model = $crossquery->getQueryResult(0, "model");
						$x_brand = $crossquery->getQueryResult(0, "name");

						$crossquery->sql("DELETE FROM ".UT_DEV." WHERE id = $device_id");
						print '<div class="error">Delete device id: '.$x_brand.', '.$x_model.' ('. $device_id .')</div>';

						$x_device_id = createDevice($model_name, $brand_id);

						$resolution_width = $device->getCapability("resolution_width");
						$resolution_height = $device->getCapability("resolution_height");
						addContenttype($x_device_id, "display/".$resolution_width."x".$resolution_height);

						$browser = $device->getCapability("mobile_browser");
						$browser_version = $device->getCapability("mobile_browser_version");
						addContenttype($x_device_id, ($browser != "" ? "browser/".$browser.($browser_version ? "/".$browser_version : "") : ""));

						$pointing_method = $device->getCapability("pointing_method");
						addContenttype($x_device_id, ($pointing_method != "" ? "pointing/".$pointing_method : false));

						$mp3 = $device->getCapability("mp3");
						addContenttype($x_device_id, ($mp3 == "true" ? "audio/mp3" : false));

						$jpg = $device->getCapability("jpg");
						addContenttype($x_device_id, ($jpg == "true" ? "image/jpg" : false));

						$released = $device->getCapability("release_date");
						addContenttype($x_device_id, ($released != "" ? "released/$released" : false));

						addUseragent($x_device_id, $device->userAgent);

						print '<div class="ok">Create device: '. $brand.', '.$model_name .'('.$x_device_id.')</div>';

/*
						if($device->actualDeviceRoot) {
							print '<div class="error">';
								print '<div>DeviceRoot error, UA mapped to wrong device - '.$device->userAgent.'</div>';
								print '<div class="details">';
									print '<div>Belong to: '.$brand.', '.$model_name.'</div>';
									print '<div>Is mapped to: '.$x_brand.', '.$x_model.'</div>';
								print '</div>';
							print '</div>';
						}
						else {
							print '<div class="ok">';
								print '<div>Device error, UA mapped to wrong device - '.$device->userAgent.'</div>';
								print '<div class="details">';
									print '<div>Belong to: '.$brand.', '.$model_name.'</div>';
									print '<div>Is mapped to: '.$x_brand.', '.$x_model.'</div>';
								print '</div>';
							print '</div>';
						}
*/
					}

				}
				
			}
			// no occurrence of useragent
			else {

				// check for partial match due to MYSQL field length error
				if($crossquery->sql("SELECT id FROM ".UT_DEV_USE." WHERE useragent = '".substr($device->userAgent, 0, 150)."'")) {

					$ua_id = $crossquery->getQueryResult(0, "id");

					// Update UA
					$crossquery->sql("UPDATE ".UT_DEV_USE." set useragent = '$device->userAgent' WHERE id = $ua_id");

					print '<div class="error">';
						print '<div>Length error, UA ('.$ua_id.') replaced - '.$device->userAgent.'</div>';
						print '<div class="details">';
							print '<div>'.$brand.', '.$model_name.' ('.$device_id.')</div>';
						print '</div>';
					print '</div>';
				}
				// it is a new useragent
				else {

					// add new UA
					$device_id = createDevice($model_name, $brand_id);

					$resolution_width = $device->getCapability("resolution_width");
					$resolution_height = $device->getCapability("resolution_height");
					addContenttype($device_id, "display/".$resolution_width."x".$resolution_height);

					$browser = $device->getCapability("mobile_browser");
					$browser_version = $device->getCapability("mobile_browser_version");
					addContenttype($device_id, ($browser != "" ? "browser/".$browser.($browser_version ? "/".$browser_version : "") : ""));

					$pointing_method = $device->getCapability("pointing_method");
					addContenttype($device_id, ($pointing_method != "" ? "pointing/".$pointing_method : false));

					$mp3 = $device->getCapability("mp3");
					addContenttype($device_id, ($mp3 == "true" ? "audio/mp3" : false));

					$jpg = $device->getCapability("jpg");
					addContenttype($device_id, ($jpg == "true" ? "image/jpg" : false));

					$released = $device->getCapability("release_date");
					addContenttype($device_id, ($released != "" ? "released/$released" : false));

					addUseragent($device_id, $device->userAgent);

					print '<div class="ok">';
						print '<div>New UA - '.$device->userAgent.'</div>';
						print '<div class="details">';
							print '<div>'.$brand.', '.$model_name.' ('.$device_id.')</div>';
						print '</div>';
					print '</div>';
				}

			}


		}
		

	}

}


?>
</body>
</html>