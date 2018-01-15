<?php
$access_item["/"] = true;
$access_item["/purge-useragent-regex"] = true;
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


$page->bodyClass("maintenance");
$page->pageTitle("Maintenance");


if(is_array($action) && count($action)) {

	// Allowed templates
	if(preg_match("/^(lost-useragents|lost-devices|devices-without-useragents|devices-without-tags|devices-without-brand|purge-useragent-regex|find-markers-on-unidentified|delete-duplet-useragents)$/", $action[0])) {

		$page->page(array(
			"type" => "janitor",
			"templates" => "janitor/maintenance/".$action[0].".php"
		));
		exit();
	}

	// Class interface
	else if($page->validateCsrfToken() && preg_match("/[a-zA-Z]+/", $action[0])) {

		// check if custom function exists on User class
		if($model && method_exists($model, $action[0])) {

			$output = new Output();
			$output->screen($model->{$action[0]}($action));
			exit();
		}
	}

}

// STATISTICS FRONT
$page->page(array(
	"type" => "janitor",
	"templates" => "janitor/maintenance/index.php"
));

?>
