<?php
$access_item["/purgeUseragentRegex"] = true;
$access_item["/deleteDupletUseragents"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


include_once("classes/helpers/identify.class.php");

$action = $page->actions();
$IC = new Items();


if(is_array($action) && count($action)) {

	if(preg_match("/^(purgeUseragentRegex|deleteDupletUseragents)$/", $action[0])) {

		$itemtype = "device";
		$model = $IC->typeObject($itemtype);


		// check if custom function exists on User class
		if($model && method_exists($model, $action[0])) {

			// More readable in log files
			debug([$model->{$action[0]}($action)]);

			// $output = new Output();
			// $output->screen($model->{$action[0]}($action));
			exit();
		}
	}

}

exit();
