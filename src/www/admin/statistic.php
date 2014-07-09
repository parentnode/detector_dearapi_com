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


$page->bodyClass("statistic");
$page->pageTitle("Statistics");


if(is_array($action) && count($action)) {

	if(preg_match("/[a-zA-Z]+/", $action[0])) {

		// check if custom function exists on User class
		if($model && method_exists($model, $action[0])) {

			$output->screen($model->$action[0]($action));
			exit();
		}
	}

	// List devices with unique potential
	if(count($action) == 1 && $action[0] == "uniquePotential") {

		$page->header(array("type" => "admin"));
		$page->template("admin/statistic/unique_potential.php");
		$page->footer(array("type" => "admin"));
		exit();

	}
	// eliminate devices with unique match
	else if(count($action) == 1 && $action[0] == "uniqueMatch") {

		$page->header(array("type" => "admin"));
		$page->template("admin/statistic/unique_match.php");
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

// STATISTICS FRONT
$page->header(array("type" => "admin"));
$page->template("admin/statistic/index.php");
$page->footer(array("type" => "admin"));

?>
