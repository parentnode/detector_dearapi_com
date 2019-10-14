<?php
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


// include device identifier class
include_once("classes/identify.class.php");


$action = $page->actions();
$IC = new Items();
$itemtype = "device";
$model = $IC->typeObject($itemtype);


$page->bodyClass("generate");
$page->pageTitle("Generate detection script");


if(is_array($action) && count($action)) {

	// Class interface
	if($page->validateCsrfToken() && preg_match("/[a-zA-Z]+/", $action[0])) {

		// check if custom function exists on User class
		if($model && method_exists($model, $action[0])) {

			$output = new Output();
			$output->screen($model->{$action[0]}($action));
			exit();
		}
	}

}

// GENERATE FRONT
$page->page(array(
	"type" => "janitor",
	"templates" => "janitor/generate/index.php"
));

?>
