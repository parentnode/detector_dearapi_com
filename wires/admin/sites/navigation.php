<?php
$access_item = array();
$access_item["list"] = true;
$access_item["link"] = "list";
$access_item["view"] = true;
$access_item["edit"] = true;
$access_item["new"] = true;

$access_item["update"] = "edit";
$access_item["save"] = "new";
$access_item["delete"] = true;

$access_item["structure_update"] = "edit";
$access_item["enable_disable"] = "edit";

$access_item["list_matches"] = "view";
$access_item["navigation_items_update"] = "view";


$access_item["done"] = "list";

if(isset($read_access) && $read_access){
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/class/system/page.class.php");
$object = $page->addObject("sites/navigation.class.php");
$page->addObject("items/item.class.php");

// default view
if(!$page->getStatus()) {$page->setStatus("page,list");}

// header
if($page->getStatus("page")) {
	$page->header();
}

// views
if($page->getStatus("list")) {

	$page->getTemplate("sites/html.list.php", $object, "c300 border", "container:item_list");
}
else if($page->getStatus("list_matches")) {

	$page->getTemplate("sites/html.list_matches.php", $object, "c300 border", "container:item");
	exit();
}

// excluding each other
if($page->getStatus("new")) {

	$page->setUrlMarker("list,new");
	$page->getTemplate("sites/html.new.php", $object, "c300 border", "container:item_list");
}
else if($page->getStatus("view")) {

	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("list,view,id=$id");
		$page->getTemplate("sites/html.view.php", $object, "c300 border", "container:item_list");
		$page->getLoadTargetScript("container:item", $page->url, "page_status=list_matches&id=$id");
	}
	else {$page->reloadList("c300 border");}
}
else if($page->getStatus("edit")) {

	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("list,edit,id=$id");
		$page->getTemplate("sites/html.edit.php", $object, "c300 border", "container:item_list");
		$page->getResetTargetScript("container:item");
	}
	else {$page->reloadList("c300 border");}
}

else if($page->getStatus() == "done") {

	$page->setUrlMarker();
//	$page->getLoadTargetScript("container:item_search", $page->url, "page_status=search_init");
//	$page->getObject($object)->getSearchItems();
	$page->getTemplate("sites/html.list.php", $object, "c300 border", "container:item_list");
//	$page->getLoadTargetScript("container:item", $page->url, "page_status=list_matches&id=$id");
	$page->getResetTargetScript("container:item");
}
// default required target
else if($page->getStatus() == "page,list") {
	$page->setUrlMarker();
	$page->getTargetTemplate("c150 border", "container:item");
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
	if($id) {
		$page->getObject($object)->getItem($id);
		$page->setUrlMarker("list,view,id=$id");
		$page->getTemplate("sites/html.view.php", $object, "c300 border", "container:item_list");
		$page->getLoadTargetScript("container:item", $page->url, "page_status=list_matches&id=$id");
	}
	else {
		$page->getTemplate("sites/html.new.php", $object, "c300 border", "container:item_list");
	}
	exit();
}
else if($page->getStatus() == "update") {

	// attempt to update
	if($page->getObject($object)->updateItem($id)) {
		$page->getObject($object)->getItem($id);
		$page->setUrlMarker("list,view,id=$id");
		$page->getTemplate("sites/html.view.php", $object, "c300 border", "container:item_list");
		$page->getLoadTargetScript("container:item", $page->url, "page_status=list_matches&id=$id");
	}
	else {
		$page->getTemplate("sites/html.edit.php", $object, "c300 border", "container:item_list");
	}
	exit();
}
else if($page->getStatus() == "delete") {

	// attempt to delete
	$page->getObject($object)->deleteItem($id);
	$page->getTemplate("sites/html.list.php", $object, "c300 border", "container:item_list");
	exit();
}

else if($page->getStatus() == "structure_update") {

	// attempt to update structure
	$page->getObject($object)->updateStructure($id);
	$page->getTemplate("sites/html.list.php", $object, "c300 border", "container:item_list");
	exit();
}

else if($page->getStatus() == "enable_disable") {

	$page->getObject($object)->enableDisable($id);
	$page->getObject($object)->getItem($id);
	$page->getTemplate("sites/html.view.php", $object, "c300 border", "container:item_list");
}

else if($page->getStatus() == "navigation_items_update") {

	// attempt to update structure
	$page->getObject($object)->updateNavigationItems($id);
	$page->getTemplate("sites/html.list_matches.php", $object, "c300 border", "container:item");
	exit();
}


?>