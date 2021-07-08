<?php
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$page->pageTitle("the Janitor @ ".SITE_URL);



$page->page(array(
	"type" => "janitor",
	"templates" => "janitor/front/index.php"
));
exit();

