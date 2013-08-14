<?php
$access_item = false;

if(isset($read_access) && $read_access) {
	return;
}

header("Content-type: text/html; charset=UTF-8");

include_once($_SERVER["FRAMEWORK_PATH"]."/config/file_paths.php");

include_once("config/config.php");
include_once("config/connect.php");
include_once("config/databases.php");
include_once("class/system/query.class.php");

$select_query = new Query();
$update_query = new Query();
$string = "";

$select_query->sql("SET NAMES utf8");
$select_query->sql("SET CHARACTER SET utf8");

$string .= "<h2>".UT_ACC_POI."</h2>";

$result = $select_query->sql("SELECT * FROM ".UT_ACC_POI."");
for($i = 0; $i < $select_query->getQueryCount(); $i++) {
	$id = $select_query->getQueryResult($i, "id");
	$file = $select_query->getQueryResult($i, "file");

	$new_file = str_replace($old_framework, $new_framework, $file);
	
	if($update_query->sql("UPDATE ".UT_ACC_POI." SET file = '$new_file' WHERE id = $id")) {
		$string .= '<div class="ok">' . $file . " -> " . $new_file . '</div>';
	}
	else {
		$string .= '<div class="bad">' . $file . " -> " . $new_file . '</div>';
	}
}

$string .= "<h2>".UT_MEN."</h2>";

$result = $select_query->sql("SELECT * FROM ".UT_MEN."");
for($i = 0; $i < $select_query->getQueryCount(); $i++) {
	$id = $select_query->getQueryResult($i, "id");
	$file = $select_query->getQueryResult($i, "url");

	$new_file = str_replace($old_framework, $new_framework, $file);
	
	if($update_query->sql("UPDATE ".UT_MEN." SET url = '$new_file' WHERE id = $id")) {
		$string .= '<div class="ok">' . $file . " -> " . $new_file . '</div>';
	}
	else {
		$string .= '<div class="bad">' . $file . " -> " . $new_file . '</div>';
	}
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Reseting databases...</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<style type="text/css">
	body {
		margin: 0;
		font-family: monaco, sans-serif;
		font-size: 10px;
		color: #000;
	}

	
	.ok {
		border: 1px solid #22A84B;
		color: #22A84B;
		background-color: #D6F7D6;
		padding: 2px 2px 0 2px;
		margin-bottom: 4px;
	}
	
	.bad {
		border: 1px solid #CC0000;
		color: #CC0000;
		background-color: #ECD8D8;
		padding: 2px 2px 0 2px;
		margin-bottom: 4px;
	}
	</style>
</head>
<body>
<div id="page">

	<?= $string ?>

</div>
</body>
</html>