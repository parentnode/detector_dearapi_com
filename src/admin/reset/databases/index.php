<?php
$access_item = false;

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["LOCAL_PATH"]."/config/connect.php");
include_once($_SERVER["FRAMEWORK_PATH"]."/config/file_paths.php");


// Database names
$global_db_name = 'api_devices';
$regional_db_names = array('api_devices');
$local_db_name = 'api_devices';

// Always list tables in IMPORT order
$global_db = array();
$regional_db = array();
$local_db = array();


// import default table-sets
//include_once($_SERVER["FRAMEWORK_PATH"]."/config/db/basics_contenttypes.php");
//include_once($_SERVER["FRAMEWORK_PATH"]."/config/db/basics.php");

//include_once($_SERVER["FRAMEWORK_PATH"]."/config/db/devices.php");

$global_db[] = 'basics_languages';
$global_db[] = 'basics_countries';
$global_db[] = 'basics_contenttypes';
$global_db[] = 'basics_brands';

//$global_db[] = 'basics_feedback';


$global_db[] = 'devices';
$global_db[] = 'devices_unidentified';
$global_db[] = 'device_useragents';
$global_db[] = 'device_contenttypes';


include_once($_SERVER["FRAMEWORK_PATH"]."/config/db/users.php");


// include default loop
include_once($_SERVER["FRAMEWORK_PATH"]."/admin/reset/databases/default_loop.php");

?>