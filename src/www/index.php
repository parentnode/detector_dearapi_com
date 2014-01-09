<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}


include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");
include_once("class/identify.class.php");

$action = $page->actions();

$ua = stringOr(getVar("ua"), $_SERVER["HTTP_USER_AGENT"]);

$Identify = new Identify();
$device = $Identify->identifyDevice($ua);

?>
<!DOCTYPE html>
<html>
<body>
<div id="device">
	<span id="segment" class="proporty"><?= $device["segment"] ?></span>
</div>
</body>
</html>