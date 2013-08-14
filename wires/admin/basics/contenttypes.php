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
$object = $page->addObject("basics/contenttype.class.php");


// default view
if(!$page->getStatus()) {$page->setStatus("page,list");}

// header
if($page->getStatus("page")) {
	$page->header();
}

// views
if($page->getStatus("list")) {

	$page->getTemplate("basics/contenttype.list.php", $object, "c300 border", "container:item");
}

// excluding each other
if($page->getStatus("new")) {

	$page->setUrlMarker("new");
	$page->getTemplate("basics/contenttype.new.php", $object, "c300 border", "container:item");
}
else if($page->getStatus("view")) {

	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("view,id=$id");
		$page->getTemplate("basics/contenttype.view.php", $object, "c300 border", "container:item");
	}
	else {
		$page->getTemplate("basics/contenttype.list.php", $object, "c300 border", "container:item");	}
}
else if($page->getStatus("edit")) {

	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("edit,id=$id");
		$page->getTemplate("basics/contenttype.edit.php", $object, "c300 border", "container:item");
	}
	else {
		$page->getTemplate("basics/contenttype.list.php", $object, "c300 border", "container:item");
	}
}

// default required target
else if($page->getStatus() == "done") {
	$page->setUrlMarker();
	$page->getTemplate("basics/contenttype.list.php", $object, "c300 border", "container:item");
}

// footer
if($page->getStatus("page")) {
	$page->footer();
	exit();
}


// actions
if($page->getStatus() == "save") {

	// attempt to save
	$id = $page->getObject($object)->saveItem();
	if($id && $page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("view,id=$id");
		$page->getTemplate("basics/contenttype.view.php", $object, "c300 border", "container:item");
	}
	else {
		$page->getTemplate("basics/contenttype.new.php", $object, "c300 border", "container:item");
	}
	exit();
}
else if($page->getStatus() == "update") {

	// attempt to update
	if($page->getObject($object)->updateItem($id)) {
		$page->getObject($object)->getItem($id);
		$page->setUrlMarker("view,id=$id");
		$page->getTemplate("basics/contenttype.view.php", $object, "c300 border", "container:item");
	}
	else {
		$page->getTemplate("basics/contenttype.edit.php", $object, "c300 border", "container:item");
	}
	exit();
}
else if($page->getStatus() == "delete") {

	// attempt to delete
	$page->getObject($object)->deleteItem($id);
	$page->setUrlMarker();
	$page->getTemplate("basics/contenttype.list.php", $object, "c300 border", "container:item");
	exit();
}

?>
