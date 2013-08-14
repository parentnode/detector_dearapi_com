<?php

$db_hostname = "localhost";
$db_username = "wires";
$db_password = "1saladilla";

@mysql_pconnect($db_hostname, $db_username, $db_password) or die;

// correct the database connection setting
mysql_query("SET NAMES utf8");
mysql_query("SET CHARACTER SET utf8");

$headers = apache_request_headers();
$useragent = "";
$header = "";
$site_id = "";

foreach($headers as $key => $value) {
	if(str_replace("-", "", strtolower($key)) == "useragent") {
		$useragent = $value;
	}
	if(str_replace("-", "", strtolower($key)) == "host") {
		$site_id = $value;
	}
	$header .= "$key: $value\n";
}

// check if device exists

//$result = mysql_query("SELECT id, requests FROM wires.devices WHERE user_agent = '$user_agent'");

//if($result && mysql_num_rows($result)) {
//	$id = mysql_result($result, 0, "id");
//	$requests = mysql_result($result, 0, "requests") + 1;
//	mysql_query("UPDATE wires.devices SET requests = $requests WHERE id = $id");
//}
//else {

	mysql_query("INSERT INTO wires.devices_unidentified VALUES(DEFAULT, '$useragent', '$header', '$site_id', DEFAULT)");
	
?>