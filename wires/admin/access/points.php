<?php
$access_item = array();
$access_item["list"] = true;
$access_item["edit"] = true;
$access_item["update"] = "edit";
$access_item["done"] = "edit";

//$access_item = false;

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/class/system/page.class.php");
$object = $page->addObject("access/accesspoint.class.php");


// default view
if(!$page->getStatus()) {$page->setStatus("page,list");}

// header
if($page->getStatus("page")) {
	$page->header();
}

// views
if($page->getStatus("list")) {

	$page->getTemplate("html.list_no_new_to_edit.php", $object, "c300 border", "container:item");
}
if($page->getStatus("edit")) {

	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("list,edit,id=$id");
		$page->getTemplate("html.edit.php", $object, "c300 border", "container:item");
	}
	else {$page->reloadList("c150 border");}
}
// default required target
else if($page->getStatus() == "page,list" || $page->getStatus() == "done") {
	$page->setUrlMarker();
	$page->getTargetTemplate("c300 border", "container:item");
}

// footer
if($page->getStatus("page")) {
	$page->footer();
}


// actions
if($page->getStatus() == "update") {

	// attempt to update
	if($page->getObject($object)->updateItem($id)) {

		$page->reloadList("c300 border");
	}
	else {
		$page->getTemplate("html.edit.php", $object, "c300 border", "container:item");
	}
	exit();
}

?>