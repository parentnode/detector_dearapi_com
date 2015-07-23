<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

$useragent = "Mozilla/5.0 (Linux; Android 4.4.3; Nexus 5 Build/KTU84M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.138 Mobile Safari/537.36";

$starttime = microtime();

include(PUBLIC_FILE_PATH."/detection_script.php");


if($device_segment && $device_name) {
	print "passed as ".$device_segment.", ".$device_name." in ".($starttime-microtime())."ms";
	
}
else {
	print "failed";
}
?>
