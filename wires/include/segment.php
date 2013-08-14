<?php

	session_start();
	if(isset($_GET["segment"])) {
		$_SESSION["segment"] = $_GET["segment"];
	}
	if(!isset($_SESSION["segment"])) {

		// print_r($_SERVER);
		// print_r($_POST);
		// print_r($_GET);
		// print __FILE__;


		$device_id = file_get_contents("http://devices.dearapi.com/xml?ua=".urlencode($_SERVER["HTTP_USER_AGENT"])."&site=".urlencode($_SERVER["HTTP_HOST"])."&file=".urlencode($_SERVER["SCRIPT_NAME"]));
//		$device_id = file_get_contents("http://devices.local/xml?ua=".urlencode($_SERVER["HTTP_USER_AGENT"])."&site=".urlencode($_SERVER["HTTP_HOST"])."&file=".urlencode($_SERVER["SCRIPT_NAME"]));
		$device = simplexml_load_string($device_id);
		if($device) {
			$_SESSION["segment"] = (string) $device->segment;
		}
		else {
			$_SESSION["segment"] = "desktop";
		}
	}

?>