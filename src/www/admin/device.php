<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

// include the output class for output method support
include_once("class/system/output.class.php");

// include device identifier class
include_once("class/identify.class.php");

$action = $page->actions();
$IC = new Item();
$itemtype = "device";
$model = $IC->typeObject($itemtype);

$output = new Output();


$page->bodyClass($itemtype);
$page->pageTitle("Devices");


if(is_array($action) && count($action)) {

	if(preg_match("/[a-zA-Z]+/", $action[0])) {

		// check if custom function exists on User class
		if($model && method_exists($model, $action[0])) {

			$output->screen($model->$action[0]($action));
			exit();
		}
	}

	// LIST ITEM
	// Requires exactly two parameters /enable/#item_id#
	if(count($action) == 1 && $action[0] == "list") {

		$page->header(array("type" => "admin"));
		$page->template("admin/".$itemtype."/list.php");
		$page->footer(array("type" => "admin"));
		exit();

	}
	// NEW ITEM
	else if(count($action) == 1 && $action[0] == "new") {

		$page->header(array("type" => "admin"));
		$page->template("admin/".$itemtype."/new.php");
		$page->footer(array("type" => "admin"));
		exit();

	}
	// EDIT ITEM
	else if(count($action) == 2 && $action[0] == "edit") {
	
		$page->header(array("type" => "admin"));
		$page->template("admin/".$itemtype."/edit.php");
		$page->footer(array("type" => "admin"));
		exit();
	
	}
	// UNIDENTIFIED
	else if(count($action) == 1 && $action[0] == "unidentified") {
	
		$page->header(array("type" => "admin", "body_class" => "unidentified"));
		$page->template("admin/".$itemtype."/unidentified.php");
		$page->footer(array("type" => "admin"));
		exit();
	
	}

}

$page->header();
$page->template("404.php");
$page->footer();

?>
