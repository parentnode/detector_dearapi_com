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

$ua = getVar("ua");
$values = explode(",", getVar("values"));

if($ua) {

	$device = $wurflManager->getDeviceForUserAgent($ua);

	foreach($values as $capability) {
		print '<div class="ok">'.$capability.': '.$device->getCapability($capability).'</div>';
	}

}



?>
</body>
</html>