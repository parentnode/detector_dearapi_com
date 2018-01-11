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
$useragent = 'Mozilla/5.0 (Mobile; $LYF/$F30C/$LYF_F30C-000-09-05-131117; rv:48.0) Gecko/48.0 Firefox/48.0 KAIOS/2.0';

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