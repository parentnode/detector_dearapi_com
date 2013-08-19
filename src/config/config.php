<?php

/**
* This file contains definitions
*
* @package Config
*/
error_reporting(E_ALL);

/**
* Site name
*/
define("SITE_UID", "DVCS");
define("SITE_NAME", "devices");
define("SITE_DB", "devices");
define("SITE_URL", "devices.dearapi.com");

define("DEFAULT_LANGUAGE_ISO", "DA"); // Regional language Danish
define("DEFAULT_COUNTRY_ISO", "DK"); // Regional country Denmark
define("ADMIN_FRONT", "/devices/devices.php");

include_once($_SERVER["FRAMEWORK_PATH"]."/config/file_paths.php");
include_once("config/databases.php");
include_once("config/connect.php");


?>
