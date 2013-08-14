<?php
$access_item = array();
$access_item["list"] = true;

$access_item["view_unidentified"] = true;
$access_item["add_to_device"] = true;
$access_item["add_to_other_device"] = "add_to_device";
$access_item["add_to_device_quick"] = "add_to_device";
$access_item["find_devices_by_pattern"] = "add_to_device";

$access_item["pattern"] = true;
$access_item["uniquely_identified"] = "pattern";
$access_item["uniquely_test_identified"] = "pattern";
$access_item["add_by_pattern"] = "pattern";

$access_item["xref"] = true;

$access_item["view"] = true;
$access_item["edit"] = true;
$access_item["update"] = "edit";

$access_item["new_device"] = true;
$access_item["save"] = "new_device";

$access_item["clone_device"] = "new_device";

$access_item["useragent_delete"] = "edit";

$access_item["delete_useragent"] = true;

$access_item["done"] = "view";


//$access_item["select_device"] = true;
/*$access_item["link"] = "view";*/
//$access_item["delete"] = true;			// delete device disabled


if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/class/system/page.class.php");
$object = $page->addObject("devices/device.class.php");


// default view
if(!$page->getStatus() && $id) {$page->setStatus("page,view_unidentified");}
if(!$page->getStatus()) {$page->setStatus("page,list");}

// header
if($page->getStatus("page")) {
	$page->header();
}

// views
if($page->getStatus("list")) {

	$page->setUrlMarker();
	$page->getTemplate("devices/device_unidentified.list.php", $object, "c300 border", "container:item");
}
else if($page->getStatus("view_unidentified")) {

	if($page->getObject($object)->getUnidentifiedDevice($id)) {
		$page->setUrlMarker("view_unidentified,id=$id");
		$page->getTemplate("devices/device_unidentified.view.php", $object, "c300 border", "container:item");
	}
	else {
		messageHandler()->addErrorMessage("Item was not found! The list has been updated.");
		$page->getLoadTargetScript("container:item", $page->url.'?page_status=list');
	}
}
else if($page->getStatus("pattern")) {

	$page->setUrlMarker("pattern");
	$page->getTemplate("devices/device_unidentified.pattern.php", $object, "c300 border", "container:item");
	exit();

}
else if($page->getStatus("uniquely_identified")) {

	$page->setUrlMarker("uniquely_identified");
	$page->getTemplate("devices/device_unidentified.uniquely_identified.php", $object, "c300 border", "container:item");
	exit();

}
else if($page->getStatus("uniquely_test_identified")) {

	$page->setUrlMarker("uniquely_test_identified");
	$page->getTemplate("devices/device_unidentified.uniquely_test_identified.php", $object, "c300 border", "container:item");
	exit();

}
else if($page->getStatus("xref")) {

	$page->setUrlMarker("xref");

	$page->getObject($object)->xRefUnidentifiedDevices();
	$page->getTemplate("devices/device_unidentified.list.php", $object, "c300 border", "container:item");
	exit();

}
else if($page->getStatus() == "view") {

	if($page->getObject($object)->getItem($id)) {
		
		$page->setUrlMarker("view,id=$id");
		$page->getTemplate("devices/device.view.php", $object, "c300 border", "container:item");
	}
	else {
		messageHandler()->addErrorMessage("Item was not found! The list has been updated.");
		$page->getLoadTargetScript("container:item", $page->url.'?page_status=list');
	}
	exit();
}
else if($page->getStatus() == "done") {

	$page->setUrlMarker();
	$page->getTemplate("devices/device_unidentified.list.php", $object, "c300 border", "container:item");
}

// footer
if($page->getStatus("page")) {
	$page->footer();
}



// actions
if($page->getStatus() == "add_to_device") {

	$page->getObject($object)->addToDevice(getVar("useragent_id"), $id);

	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("view,id=$id");
		$page->getTemplate("devices/device.view.php", $object, "c300 border", "container:item");
	}
	else {
		messageHandler()->addErrorMessage("Item was not found! The list has been updated.");
		$page->getLoadTargetScript("container:item", $page->url.'?page_status=list');
	}
	exit();
}
else if($page->getStatus() == "add_to_device_quick") {

	$page->getObject($object)->addToDevice(getVar("useragent_id"), $id);

	$page->setUrlMarker();
	$page->getTemplate("devices/device_unidentified.list.php", $object, "c300 border", "container:item");

}
else if($page->getStatus() == "add_to_other_device") {

	$id = getVar("device_id");

	$page->getObject($object)->addToDevice(getVar("useragent_id"), $id);

	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("view,id=$id");
		$page->getTemplate("devices/device.view.php", $object, "c300 border", "container:item");
	}
	else {
		messageHandler()->addErrorMessage("Item was not found! The list has been updated.");
		$page->getLoadTargetScript("container:item", $page->url.'?page_status=list');
	}

	exit();
}
else if($page->getStatus() == "clone_device") {

	$id = $page->getObject($object)->cloneDevice(getVar("id"), getVar("useragent"));

//	$id = $page->getObject($object)->cloneDeviceAndAdd(getVar("useragent_id"), getVar("device_id"));
	
	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("view,id=$id");
		$page->getTemplate("devices/device.view.php", $object, "c300 border", "container:item");
	}
	else {
		messageHandler()->addErrorMessage("Item was not found! The list has been updated.");
		$page->getLoadTargetScript("container:item", $page->url.'?page_status=list');
	}
	
	exit();
}
else if($page->getStatus() == "add_by_pattern") {

	$id = getVar("device_id");
	$page->getObject($object)->addGroupToDevice(getVar("useragent"), $id);
	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("view,id=$id");
		$page->getTemplate("devices/device.view.php", $object, "c300 border", "container:item");
	}
	else {
		messageHandler()->addErrorMessage("Item was not found! The list has been updated.");
		$page->getLoadTargetScript("container:item", $page->url.'?page_status=list');
	}

	exit();
}
else if($page->getStatus("new_device")) {

	$items = $page->getObject($object)->getUnidentifiedDevice($id);
	$page->getTemplate("devices/device.new.php", $object, "c300 border", "container:item");
	exit();
}
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

/*
else if($page->getStatus("select_device")) {

	$page->setUrlMarker("select,id=$id");
	Session::setSearch("brand_id", getVar("brand_id"));
	Session::setSearch("device_id", getVar("device_id"));
	$page->getTemplate("devices/device.select.php", $object, "ci100 border", "select:device");
	exit();
}
*/
else if($page->getStatus("edit")) {

	$page->setUrlMarker("edit,id=$id");
	$page->getObject($object)->getItem($id);
	$page->getTemplate("devices/device.edit.php", $object, "c300 border", "container:item");
	exit();
}
else if($page->getStatus() == "update") {

	if($page->getObject($object)->updateItem($id)) {
		$page->setUrlMarker("view,id=$id");
		$page->getObject($object)->getItem($id);
		$page->getTemplate("devices/device.view.php", $object, "c300 border", "container:item");
	}
	else {
		$page->getTemplate("devices/device.edit.php", $object, "c300 border", "container:item");
	}
	exit();
}
// unidentified
else if($page->getStatus() == "delete_useragent") {

	$page->getObject($object)->deleteUnidentifiedUseragent($id);
	$page->getTemplate("devices/device_unidentified.list.php", $object, "c300 border", "container:item");
	exit();
}
// from identified device
else if($page->getStatus() == "useragent_delete") {

	$page->getObject($object)->deleteUseragent($id);
	$id = getVar("device_id");
	$page->setUrlMarker("view,id=$id");
	$page->getObject($object)->getItem($id);
	$page->getTemplate("devices/device.view.php", $object, "c300 border", "container:item");
	exit();
}
// from identified device
else if($page->getStatus() == "find_devices_by_pattern") {
	
	Session::setSearch("pattern", getVar("pattern"));
	$page->getTemplate("devices/devices.autosearch.php", $object);
	exit();
}


?>