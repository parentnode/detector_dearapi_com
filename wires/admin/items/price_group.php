<?php
$access_item = array();
$access_item["list"] = true;
$access_item["view"] = true;
$access_item["edit"] = true;
$access_item["new"] = true;

$access_item["update"] = "edit";
$access_item["save"] = "new";
$access_item["delete"] = true;

$access_item["done"] = "list";

if(isset($read_access) && $read_access){
	return;
}
include_once($_SERVER["FRAMEWORK_PATH"]."/class/system/page.class.php");

$object = $page->addObject("items/price_group.class.php");

include_once("templates/defaults/generic.controller.php");

?>