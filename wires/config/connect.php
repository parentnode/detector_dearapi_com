<?php

/**
* This file contains generel database connection information and creates a permanent connection to the specified database
* SQL: GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY '##serverpassword##' WITH GRANT OPTION;
*
* @package Config
*/

$db_hostname = "localhost";
$db_username = "";
$db_password = isset($_SERVER["DB_PASS"]) ? $_SERVER["DB_PASS"] : "";

@mysql_pconnect($db_hostname, $db_username, $db_password) or die; //header(ADMIN_PATH."/errors/db.php");

// correct the database connection setting
mysql_query("SET NAMES utf8");
mysql_query("SET CHARACTER SET utf8");

?>