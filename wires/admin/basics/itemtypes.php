<?php
$access_item = array();
$access_item["list"] = true;
$access_item["view"] = true;
$access_item["edit"] = true;
$access_item["new"] = true;

$access_item["update"] = "edit";
$access_item["save"] = "new";
$access_item["delete"] = true;

$access_item["done"] = "list";

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/class/system/page.class.php");
$object = $page->addObject("basics/itemtype.class.php");


// default view
if(!$page->getStatus()) {$page->setStatus("page,list");}

// header
if($page->getStatus("page")) {
	$page->header();
}

// views
if($page->getStatus("list")) {

	$page->getTemplate("basics/itemtype.list.php", $object, "c300 border", "container:item_list", "container:item");
}

// excluding each other
if($page->getStatus("new")) {

	$page->setUrlMarker("list,new");
	$page->getTemplate("basics/itemtype.new.php", $object, "c300 border", "container:item");
}
else if($page->getStatus("view")) {

	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("list,view,id=$id");
		$page->getTemplate("basics/itemtype.view.php", $object, "c300 border", "container:item");
	}
	else {
		$page->getTargetTemplate("c300 border", "container:item");
		$page->getLoadTargetScript("container:item_list", $page->url.'?page_status=list');
	}
}
else if($page->getStatus("edit")) {

	$id = getVar("itemtype_id");
	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("list,edit,id=$id");
		$page->getTemplate("basics/itemtype.edit.php", $object, "c300 border", "container:item");
	}
	else {
		$page->getTargetTemplate("c300 border", "container:item");
		$page->getLoadTargetScript("container:item_list", $page->url.'?page_status=list');
	}
}

// default required target
else if($page->getStatus() == "page,list" || $page->getStatus() == "done") {
	$page->setUrlMarker();
	$page->getTargetTemplate("c150 border", "container:item");
}

// footer
if($page->getStatus("page")) {
	$page->footer();
}


// actions
if($page->getStatus() == "save") {

	// attempt to save
	$id = $page->getObject($object)->saveItem();
	if($id && $page->getObject($object)->getItem($id)) {
		$page->getTemplate("basics/itemtype.view.php", $object, "c300 border", "container:item", false, true);
		$page->getLoadTargetScript("container:item_list", $page->url.'?page_status=list');
	}
	else {
		$page->getTemplate("basics/itemtype.new.php", $object, "c300 border", "container:item");
	}
	exit();
}
else if($page->getStatus() == "update") {

	// attempt to update
	if($page->getObject($object)->updateItem($id)) {
		$page->getObject($object)->getItem($id);
		$page->getTemplate("basics/itemtype.view.php", $object, "c300 border", "container:item", false, true);
		$page->getLoadTargetScript("container:item_list", $page->url.'?page_status=list');
	}
	else {
		$page->getTemplate("basics/itemtype.edit.php", $object, "c300 border", "container:item");
	}
	exit();
}
else if($page->getStatus() == "delete") {

	// attempt to delete
	$page->getObject($object)->deleteItem($id);
	// update related view
	$page->getTargetTemplate("c300 border", "container:item");
	$page->getLoadTargetScript("container:item_list", $page->url.'?page_status=list');
	exit();
}
?>