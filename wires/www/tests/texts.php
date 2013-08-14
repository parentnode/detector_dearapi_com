<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/class/www/page.class.php");
//include_once($_SERVER["GLOBAL_PATH"]."/class/www/page.class.php");
?>
<?php $page->header() ?>

<? $page->getTemplate("tests/texts.php", false, "c300 border"); ?>

<?php $page->footer() ?>