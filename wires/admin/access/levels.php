<?php
$access_item = array();
$access_item["list"] = true;
$access_item["view"] = true;
$access_item["edit"] = true;
$access_item["new"] = true;

$access_item["update"] = "edit";
$access_item["save"] = "new";
$access_item["delete"] = true;

$access_item["select_point"] = "edit";
$access_item["edit_access"] = "select_point";
$access_item["edit_access_update"] = "select_point";

$access_item["done"] = "list";

//$access_item = false;

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/class/system/page.class.php");
$object = $page->addObject("access/accesslevel.class.php");


// default view
if(!$page->getStatus()) {$page->setStatus("page,list");}

// header
if($page->getStatus("page")) {
	$page->header();
}

// views
if($page->getStatus("list")) {

	$page->getTemplate("html.list.php", $object, "c150 border", "container:item_list", "container:item");
}

// excluding each other
if($page->getStatus("new")) {

	$page->setUrlMarker("list,new");
	$page->getTemplate("html.new.php", $object, "c150 border", "container:item");
}
else if($page->getStatus("view")) {

	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("list,view,id=$id");
		$page->getTemplate("access/level.view.php", $object, "c150 border", "container:item");
	}
	else {$page->reloadList("c150 border");}
}
else if($page->getStatus("edit")) {

	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("list,edit,id=$id");
		$page->getTemplate("html.edit.php", $object, "c150 border", "container:item");
	}
	else {$page->reloadList("c150 border");}
}

else if($page->getStatus("select_point")) {
	
	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("list,select_point,id=$id");
		$page->getTemplate("access/level.selectpoint.php", $object, "c150 border", "container:item");
	}
	else {$page->reloadList("c150 border");}
}
else if($page->getStatus("edit_access")) {

	if($page->getObject($object)->getaccessPoint($id)) {
		$page->setUrlMarker("list,edit_access,id=$id");
		$page->getTemplate("access/level.edit_access.php", $object, "c150 border", "container:item");
	}
	else {
		$page->getTargetTemplate("c150 border", "container:item");
		$page->getLoadTargetScript("container:item", $page->url, 'page_status=access');
	}
	exit();

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
	if($id) {
		$page->reloadListView("c150 border", $id, $object, "users/level.view.php");
	}
	else {
		$page->getTemplate("html.new.php", $object, "c150 border", "container:item");
	}
	exit();
}
else if($page->getStatus() == "update") {

	// attempt to update
	if($page->getObject($object)->updateItem($id)) {
		$page->reloadListView("c150 border", $id, $object, "users/level.view.php");
	}
	else {
		$page->getTemplate("html.edit.php", $object, "c150 border", "container:item");
	}
	exit();
}
else if($page->getStatus() == "delete") {

	// attempt to delete
	$page->getObject($object)->deleteItem($id);
	$page->reloadList("c150 border");
	exit();
}

else if($page->getStatus() == "edit_access_update") {

	// attempt to update
	if($page->getObject($object)->updateItemAccess($id, getVar("point_id"))) {
		$page->getTemplate("access/level.selectpoint.php", $object, "c150 border", "container:item");
	}
	else {
		$page->getTemplate("access/level.setaccess.php", $object, "c150 border", "container:item");
	}
	exit();
}

?>