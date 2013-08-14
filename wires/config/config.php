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
define("SITE_UID", "FRW");
define("SITE_NAME", "FRAMEWORK");
define("SITE_DB", "framework");

define("DEFAULT_LANGUAGE_ISO", "DA"); // Reginal language Danish
define("DEFAULT_COUNTRY_ISO", "DK"); // Regional country Denmark

include_once($_SERVER["FRAMEWORK_PATH"]."/config/file_paths.php");
include_once("config/databases.php");
include_once("config/connect.php");


?>
