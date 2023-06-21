<?php
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

// // include the output class for output method support
// include_once("classes/system/output.class.php");

// include device identifier class
include_once("classes/identify.class.php");

$action = $page->actions();
$IC = new Items();
$itemtype = "device";
$model = $IC->typeObject($itemtype);

//$output = new Output();


$page->bodyClass($itemtype);
$page->pageTitle("Devices");


if(is_array($action) && count($action)) {


	// LIST/EDIT/NEW ITEM
	if(preg_match("/^(list|edit|new)$/", $action[0])) {

		$page->page(array(
			"type" => "janitor",
			"templates" => "janitor/".$itemtype."/".$action[0].".php"
		));
		exit();
	}

	// UNIDENTIFIED
	else if(count($action) == 1 && $action[0] == "unidentified") {
	
		$page->page(array(
			"type" => "janitor",
			"body_class" => "unidentified",
			"templates" => "janitor/".$itemtype."/".$action[0].".php"
		));

		// $page->header(array("type" => "janitor", "body_class" => "unidentified"));
		// $page->template("janitor/".$itemtype."/unidentified.php");
		// $page->footer(array("type" => "janitor"));
		exit();
	
	}

	// Class interface
	else if(security()->validateCsrfToken() && preg_match("/[a-zA-Z]+/", $action[0])) {

		// check if custom function exists on User class
		if($model && method_exists($model, $action[0])) {

			$output = new Output();
			$output->screen($model->{$action[0]}($action));
			exit();
		}
	}

	// if(preg_match("/[a-zA-Z]+/", $action[0])) {
	//
	// 	// check if custom function exists on User class
	// 	if($model && method_exists($model, $action[0])) {
	//
	// 		$output->screen($model->{$action[0]}($action));
	// 		exit();
	// 	}
	// }
	//
	// // LIST ITEM
	// // Requires exactly two parameters /enable/#item_id#
	// if(count($action) == 1 && $action[0] == "list") {
	//
	// 	$page->header(array("type" => "janitor"));
	// 	$page->template("janitor/".$itemtype."/list.php");
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
	// // UNIDENTIFIED
	// else if(count($action) == 1 && $action[0] == "unidentified") {
	//
	// 	$page->header(array("type" => "janitor", "body_class" => "unidentified"));
	// 	$page->template("janitor/".$itemtype."/unidentified.php");
	// 	$page->footer(array("type" => "janitor"));
	// 	exit();
	//
	// }

}

$page->page(array(
	"templates" => "pages/404.php"
));

// $page->header();
// $page->template("404.php");
// $page->footer();

?>
