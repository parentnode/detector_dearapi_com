<?php
$access_item = false;

if(isset($read_access) && $read_access) {
	return;
}

include_once("translation_tool.php");
$_SESSION["view"] = "front";
?>