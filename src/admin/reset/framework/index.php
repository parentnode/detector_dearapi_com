<?php
$access_item = false;

if(isset($read_access) && $read_access) {
	return;
}

// this script changes paths in DB to work after changing framework in apache conf

// updates point paths in db to:

// $old_framework = "/projects/www/framework/v3/";
// $new_framework = "/srv/sites/hvadhedderde/devices_dearapi_com/wires/";

$old_framework = "/projects/www/apis/devices/";
$new_framework = "/srv/sites/hvadhedderde/devices_dearapi_com/src/";

include_once($_SERVER["FRAMEWORK_PATH"]."/admin/reset/framework/change_framework.php");
