<?php
$access_item = array();

$access_item["logs"] = true;

$access_item["search_init"] = "list";
$access_item["search"] = "list";
$access_item["search_reset"] = "list";

$access_item["list"] = true;
$access_item["view"] = true;

$access_item["done"] = "list";

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/class/system/page.class.php");
$object = $page->addObject("log/log.class.php");


// default view
//if(!$page->getStatus() && $id) {$page->setStatus("page,list,search_init");}
if(!$page->getStatus()) {$page->setStatus("page,logs");}

// header
if($page->getStatus("page")) {
	$page->header();
}

// views
if($page->getStatus("logs")) {

	$page->setUrlMarker();
	$page->getTemplate("log/logs.php", $object, "c300 border", "container:item");
}

if($page->getStatus("search_init")) {

	$page->getTemplate("html.search.php", $object, "c300 border", "container:item_search", false, true);
}

if($page->getStatus("list")) {

	if(getVar("log")) {
		$page->setUrlMarker("list");
		$page->getTemplate("log/log.list.php", $object, "c300 border", "container:item");
	}
	else {
		$page->setUrlMarker();
		$page->getTemplate("log/logs.php", $object, "c300 border", "container:item");
	}
}


// excluding each other
if($page->getStatus("view")) {

	$page->setUrlMarker("view");
	$page->getTemplate("log/".getVar("log").".view.php", $object, "c300 border", "container:item");
//	$page->getTemplate("log/log.view.php", $object, "c300 border", "container:item");
}

// default required target
else if($page->getStatus() == "done") {
	$page->setUrlMarker();
	$page->getTemplate("log/log.list.php", $object, "c300 border", "container:item");
}

// footer
if($page->getStatus("page")) {
	$page->footer();
	exit();
}

?>
