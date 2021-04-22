<?php
$access_item["/"] = true;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$page->pageTitle("the Janitor @ ".SITE_URL);

$query = new Query();
$IC = new Items();
$model = $IC->typeObject("device");


$query->checkDBExistence($model->db);
$query->checkDBExistence($model->db_useragents);
$query->checkDBExistence($model->db_markers);
$query->checkDBExistence($model->db_exceptions);
$query->checkDBExistence($model->db_unidentified);


?>
<? $page->header(array("type" => "janitor")) ?>

<div class="scene front">
	<h1><?= SITE_NAME ?></h1>

</div>

<? $page->footer(array("type" => "janitor")) ?>