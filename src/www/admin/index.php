<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");

$action = $page->actions();
?>
<? $page->header(array("type" => "admin")) ?>

<div class="scene front">

	<h1>Devices Admin</h1>

</div>

<? $page->footer(array("type" => "admin")) ?>