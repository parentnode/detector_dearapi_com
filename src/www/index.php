<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();

$page->bodyClass("devices");

// list
if($action && $action[0] == "restructure") {

	$page->header();
	$page->template("devices/restructure.php");
	$page->footer();

}
else {

	$page->header();
	$page->template("404.php");
	$page->footer();

}

?>
