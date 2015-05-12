<?php
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

// include the output class for output method support
//include_once("classes/system/output.class.php");

// include device identifier class
include_once("classes/identify.class.php");


$action = $page->actions();
$IC = new Items();
$itemtype = "device";
$model = $IC->typeObject($itemtype);

//$output = new Output();


$page->bodyClass("statistic");
$page->pageTitle("Statistics");


if(is_array($action) && count($action)) {

	// if(preg_match("/[a-zA-Z]+/", $action[0])) {
	//
	// 	// check if custom function exists on User class
	// 	if($model && method_exists($model, $action[0])) {
	//
	// 		$output->screen($model->$action[0]($action));
	// 		exit();
	// 	}
	// }

	// LIST/EDIT/NEW ITEM
	if(preg_match("/^(unique_potential|unique_match)$/", $action[0])) {

		$page->page(array(
			"type" => "janitor",
			"templates" => "janitor/statistic/".$action[0].".php"
		));
		exit();
	}

	// // List devices with unique potential
	// if(count($action) == 1 && $action[0] == "uniquePotential") {
	//
	// 	$page->header(array("type" => "janitor"));
	// 	$page->template("admin/statistic/unique_potential.php");
	// 	$page->footer(array("type" => "janitor"));
	// 	exit();
	//
	// }
	// // eliminate devices with unique match
	// else if(count($action) == 1 && $action[0] == "uniqueMatch") {
	//
	// 	$page->header(array("type" => "janitor"));
	// 	$page->template("admin/statistic/unique_match.php");
	// 	$page->footer(array("type" => "janitor"));
	// 	exit();
	//
	// }
	// // NEW ITEM
	// else if(count($action) == 1 && $action[0] == "new") {
	//
	// 	$page->header(array("type" => "janitor"));
	// 	$page->template("janitor/".$itemtype."/new.php");
	// 	$page->footer(array("type" => "janitor"));
	// 	exit();
	//
	// }
	// // EDIT ITEM
	// else if(count($action) == 2 && $action[0] == "edit") {
	//
	// 	$page->header(array("type" => "janitor"));
	// 	$page->template("janitor/".$itemtype."/edit.php");
	// 	$page->footer(array("type" => "janitor"));
	// 	exit();
	//
	// }
	// UNIDENTIFIED
	// else if(count($action) == 1 && $action[0] == "unidentified") {
	//
	// 	$page->header(array("type" => "janitor", "body_class" => "unidentified"));
	// 	$page->template("janitor/".$itemtype."/unidentified.php");
	// 	$page->footer(array("type" => "janitor"));
	// 	exit();
	//
	// }

	// Class interface
	else if($page->validateCsrfToken() && preg_match("/[a-zA-Z]+/", $action[0])) {

		// check if custom function exists on User class
		if($model && method_exists($model, $action[0])) {

			$output = new Output();
			$output->screen($model->$action[0]($action));
			exit();
		}
	}

}

// STATISTICS FRONT
$page->page(array(
	"type" => "janitor",
	"templates" => "janitor/statistic/index.php"
));

// $page->header(array("type" => "janitor"));
// $page->template("admin/statistic/index.php");
// $page->footer(array("type" => "janitor"));

?>
