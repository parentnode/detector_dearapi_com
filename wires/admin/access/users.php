<?php
$access_item = array();
$access_item["list"] = true;
$access_item["view"] = true;

$access_item["done"] = "list";

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/class/system/page.class.php");
$object = $page->addObject("access/user.class.php");


// default view
if(!$page->getStatus()) {$page->setStatus("page,list");}

// header
if($page->getStatus("page")) {
	$page->header();
}

// views
if($page->getStatus("list")) {

	$page->getTemplate("access/user.list.php", $object, "c300 border", "container:item");
}

// excluding each other
if($page->getStatus("view")) {

	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("list,view,id=$id");
		$page->getTemplate("access/user.view.php", $object, "c300 border", "container:item");
	}
	else {$page->reloadList("c300 border");}
}

else if($page->getStatus() == "done") {

	$page->setUrlMarker();
	$page->getTemplate("access/user.list.php", $object, "c300 border", "container:item");
}

// default required target
else if($page->getStatus() == "page,list") {
	$page->setUrlMarker();
	$page->getTargetTemplate("c300 border", "container:item");
}

// footer
if($page->getStatus("page")) {
	$page->footer();
}



?>