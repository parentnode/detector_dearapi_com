<?php
$access_item = array();
$access_item["list"] = true;
//$access_item["link"] = "list";

$access_item["view"] = true;
$access_item["edit"] = true;
$access_item["new"] = true;

$access_item["manual_task"] = true;

$access_item["update"] = "edit";
$access_item["save"] = "new";
$access_item["delete"] = true;

$access_item["useragent_delete"] = "edit";


$access_item["search_init"] = "list";
$access_item["search"] = "list";
$access_item["search_reset"] = "list";

$access_item["done"] = "view";
//$access_item["next"] = "view";
//$access_item["prev"] = "view";


if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/class/system/page.class.php");
$object = $page->addObject("devices/device.cleanup.class.php");

// default view
if(!$page->getStatus() && $id) {$page->setStatus("page,view,search_init");}
if(!$page->getStatus()) {$page->setStatus("page,list,search_init");}

// header
if($page->getStatus("page")) {
	$page->header();
}

if($page->getStatus("manual_task")) {

	$page->getObject($object)->manualTask();
	exit();

}


// views
if($page->getStatus("search_init")) {

	$page->getTemplate("html.search.php", $object, "c300 border", "container:item_search", false, true);
}

// excluding each other
if($page->getStatus("list")) {

	$page->getLoadTargetScript("container:item_search", $page->url, "page_status=search_init");
	$page->setUrlMarker();
	$page->getTemplate("devices/device_cleanup.list.php", $object, "c300 border", "container:item");
}
else if($page->getStatus("new")) {

	$page->getResetTargetScript("container:item_search");
	$page->setUrlMarker("new");
	$page->getTemplate("devices/device.new.php", $object, "c150 border", "container:item");
}
else if($page->getStatus("view")) {

	if($page->getObject($object)->getItem($id)) {
		$page->getResetTargetScript("container:item_search");
		$page->setUrlMarker("view,id=$id");
		$page->getTemplate("devices/device.view.php", $object, "c300 border", "container:item");
	}
	else {
		messageHandler()->addErrorMessage("Item was not found! The list has been updated.");
		$page->getLoadTargetScript("container:item", $page->url.'?page_status=list');
	}
}

else if($page->getStatus() == "done") {

	$page->setUrlMarker();
	$page->getLoadTargetScript("container:item_search", $page->url, "page_status=search_init");
	$page->getObject($object)->getSearchItems();
	$page->getTemplate("devices/device_cleanup.list.php", $object, "c300 border", "container:item");
}
// default required target
else if($page->getStatus() == "page,list,search_init") {

	$page->setUrlMarker();
	$page->getTargetTemplate("c300 border", "container:item");
}

// footer
if($page->getStatus("page")) {
	$page->footer();
}



// actions
if($page->getStatus() == "save") {

	// attempt to save
	$id = $page->getObject($object)->saveItem();

	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("view,id=$id");
		$page->getTemplate("devices/device.view.php", $object, "c300 border", "container:item");
	}
	else {
		$page->getTemplate("devices/device.new.php", $object, "c150 border", "container:item");
	}
	exit();
}
else if($page->getStatus("edit")) {

	$page->getObject($object)->getItem($id);
	$page->setUrlMarker("edit,id=$id");
	$page->getTemplate("devices/device.edit.php", $object, "c300 border", "container:item");
	exit();
}
else if($page->getStatus() == "update") {

	if($page->getObject($object)->updateItem($id)) {
		$page->getResetTargetScript("container:item_search");
		$page->setUrlMarker("view,id=$id");
		$page->getObject($object)->getItem($id);
		$page->getTemplate("devices/device.view.php", $object, "c300 border", "container:item");
	}
	else {
		$page->getTemplate("devices/device.edit.php", $object, "c300 border", "container:item");
	}
	exit();
}

else if($page->getStatus() == "delete") {

	$page->getObject($object)->deleteItem($id);
	$page->setUrlMarker();
	$page->getLoadTargetScript("container:item_search", $page->url, "page_status=search_init");
	$page->getObject($object)->getSearchItems();
	$page->getTemplate("devices/device_cleanup.list.php", $object, "c300 border", "container:item");
	exit();
}

else if($page->getStatus() == "search") {

	$page->getObject($object)->search();
	$page->getTemplate("html.search.php", $object, "c300 border", "container:item_search", false, true);
	$page->getLoadTargetScript("container:item", $page->url, "page_status=list");
	exit();
}
else if($page->getStatus() == "search_reset") {

	$page->getObject($object)->searchReset();
	$page->getTemplate("html.search.php", $object, "c300 border", "container:item_search");
	$page->getLoadTargetScript("container:item", $page->url, "page_status=list");
	exit();
}

else if($page->getStatus() == "useragent_delete") {

	$page->getObject($object)->deleteUseragent($id);
	$id = getVar("device_id");
	$page->getObject($object)->getItem($id);
	$page->getTemplate("devices/device.edit.php", $object, "c300 border", "container:item");
	exit();
}




?>