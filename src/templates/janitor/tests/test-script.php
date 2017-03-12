<?php
global $action;
global $IC;
global $itemtype;
global $model;

//print_r($all_items);
?>
<div class="scene defaultList tests">
	<h1>Testing specific regexp</h1>

<?
$useragent = "Mozilla/5.0 (Linux; Android 4.4.3; Nexus 5 Build/KTU84M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.138 Mobile Safari/537.36";

$starttime = microtime();

include(PUBLIC_FILE_PATH."/detection_script.php");


if($device_segment && $device_name) {
	print "<p>passed as ".$device_segment.", ".$device_name." in ".($starttime-microtime())."ms</p>";
	
}
else {
	print "<p>failed</p>";
}
?>

</div>