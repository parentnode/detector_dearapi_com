<?php
$access_item = array();
$access_item["new"] = true;
$access_item["save"] = "new";

$access_item["done"] = "new";

//$access_item["upload"] = true;
//$access_item["upload_save"] = "upload";
//$access_item["upload_view"] = "upload";
//$access_item["upload_cancel"] = "upload";
//$access_item["new_cancel"] = "new";


if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/class/system/page.class.php");
$object = $page->addObject("items/item.class.php");
//$page->addObject("items/item", "Item", $object = "itemClass");


// default view
if(!$page->getStatus()) {$page->setStatus("page,new");}

// header
if($page->getStatus("page")) {
	$page->header();
}

// views
// excluding each other
if($page->getStatus("new")) {

	$page->setUrlMarker("new");
	$page->getTemplate("items/item.new.php", $object, "c300 border", "container:item");
}

else if($page->getStatus() == "done") {

	$page->getObject($object)->vars["itemtype_id"] = "";
	$page->setUrlMarker();
	$page->getTemplate("items/item.new.php", $object, "c300 border", "container:item");
}
// default required target
else if($page->getStatus() == "page,new") {

	$page->setUrlMarker();
	$page->getTargetTemplate("c150 border", "container:item");
}

// actions
if($page->getStatus("save")) {

	// attempt to save
	if($id = $page->getObject($object)->saveItem()) {

		$page->getLocationHrefScript("items.php?id=$id");
	}
	else {

		$page->getTemplate("items/item.new.php", $object, "c300 border", "container:item");
	}
	exit();
}

// footer
if($page->getStatus("page")) {
	$page->footer();
	exit();
}

?>