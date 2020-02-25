<?php
global $action;
global $IC;
global $itemtype;
global $model;

//print_r($all_items);
?>
<div class="scene defaultList tests">
	<h1>Testing specific useragent against script</h1>

<?
$useragent = 'Mozilla/5.0 (Mobile; $LYF/$F30C/$LYF_F30C-000-09-05-131117; rv:48.0) Gecko/48.0 Firefox/48.0 KAIOS/2.0';
$useragent = 'Mozilla/5.0 (Linux; Android 7.0; SM-G930F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.116 Mobile Safari/537.36';

$starttime = microtime(true);

include(PUBLIC_FILE_PATH."/detection_script.php");


if($device_segment && $device_name) {
	print "<p>passed as ".$device_segment.", ".$device_name." in ".(microtime(true)-$starttime)."ms</p>";
	
}
else {
	print "<p>failed</p>";
}
?>

</div>