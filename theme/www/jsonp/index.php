<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");
include_once("classes/helpers/identify.class.php");

$action = $page->actions();

$ua = stringOr(getVar("ua"), isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "");
$callback = stringOr(getVar("callback"), "callback");

$Identify = new Identify();
$device = $Identify->identifyDevice($ua);


header("Content-type: text/javascript; charset=UTF-8");
?>
<?= $callback ?>({"segment":"<?= $device["segment"] ?>"});
