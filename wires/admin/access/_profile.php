<?php
$access_item = array();
$access_item["view"] = true;
$access_item["edit"] = true;

$access_item["update"] = "edit";

$access_item["password"] = "edit";
$access_item["password_update"] = "edit";

$access_item["done"] = "view";

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/class/system/page.class.php");
$object = $page->addObject("users/user.class.php");


// default view
if(!$page->getStatus()) {$page->setStatus("page,view");}

// header
if($page->getStatus("page")) {
	$page->header();
}

// views

// excluding each other
if($page->getStatus("view")) {
	$id = Session::getLogin()->getUserId();
//	print $id;
//	exit();
	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("view,id=$id");
		$page->getTemplate("users/user.profile.php", $object, "c300 border", "container:item");
	}
	else {$page->reloadList("c300 border");}
}
else if($page->getStatus("edit")) {

	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("edit,id=$id");
		$page->getTemplate("users/user.profile_edit.php", $object, "c300 border", "container:item");
	}
	else {$page->reloadList("c300 border");}
}
else if($page->getStatus("password")) {

	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("password,id=$id");
		$page->getTemplate("users/user.password.php", $object, "c300 border", "container:item");
	}
	else {$page->reloadList("c300 border");}
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
	if($page->getObject($object)->updateProfile($id)) {
		$page->setUrlMarker("view,id=$id");
		$page->getObject($object)->getItem($id);
		$page->getTemplate("users/user.profile.php", $object, "c300 border", "container:item");
//		$page->getTemplate("users/user.view.php", $object, "c300 border", "container:item");
	}
	else {
		$page->getTemplate("html.edit.php", $object, "c300 border", "container:item");
	}
	exit();
}
else if($page->getStatus() == "password_update") {

	// attempt to update password
	if($page->getObject($object)->updatePassword($id)) {
		$page->setUrlMarker("view,id=$id");
		$page->getObject($object)->getItem($id);
		$page->getTemplate("users/user.profile.php", $object, "c300 border", "container:item");
//		$page->getTemplate("users/user.view.php", $object, "c300 border", "container:item");
	}
	else {
		$page->getTemplate("users/user.password.php", $object, "c300 border", "container:item");
	}
	exit();
}

?>