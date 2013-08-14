<?php
error_reporting(E_ALL);

$access_item = array();
$access_item["select_language"] = "list";
$access_item["list"] = true;

//$access_item["view"] = true;
$access_item["edit"] = true;
$access_item["edit_single"] = "edit";

//$access_item["new"] = true;

$access_item["update"] = "edit";
$access_item["cancel"] = "edit";
//$access_item["delete"] = true;

$access_item["done"] = "list";

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/class/system/page.class.php");
$object = $page->addObject("translation/translation_tool.class.php");


/*
$access_item = array();
$access_item["list"] = true;
$access_item["view"] = true;
$access_item["edit"] = true;
$access_item["update"] = "edit";
$access_item["view_cancel"] = false;
$access_item["edit_cancel"] = false;
$access_item["edit_single"] = "edit";

//$_SESSION["view"] = "front";

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/class/system/page.class.php");

//$page->addObject("translation_tool", "TranslationTool", $object = "translationToolClass", "translation/");
$object = $page->addObject("translation/translation_tool.class.php");
*/

// default view
//if(!$page->getStatus() && $id) {$page->setStatus("page,view");}
if(!$page->getStatus()) {$page->setStatus("page,select_language");}


// header
if($page->getStatus("page")) {
	$page->header();
}

// excluding each other
if($page->getStatus("select_language")) {

	$page->getTemplate("translation/select_language.php", $object, "c300 border", "container:item");
}
else if($page->getStatus("list")) {

//	$page->getResetTargetScript("container:item_list");
	$page->getTemplate("translation/list_translations.php", $object, "c300 border", "container:item");
}



else if($page->getStatus("edit")) {

	$page->getTemplate("translation/edit.php", $object, "c300 border", "container:item");
}
else if($page->getStatus("edit_single")) {

	$page->getTemplate("translation/edit_single.php", $object, "c300 border", "container:item");
}
else if($page->getStatus("cancel")) {

//	$page->getResetTargetScript("container:item_list");
	$page->getTemplate("translation/list_translations.php", $object, "c300 border", "container:item");
}
else if($page->getStatus("update")) {

//	$page->getResetTargetScript("container:item_list");
	$page->getTemplate("translation/list_translations.php", $object, "c300 border", "container:item");
}



else if($page->getStatus() == "done") {

	$page->setUrlMarker();
//	$page->getLoadTargetScript("container:item_search", $page->url, "page_status=search_init");
//	$page->getObject($object)->getSearchItems();
	$page->getTemplate("translation/select_language.php", $object, "c300 border", "container:item");
}
// default required target
//else if($page->getStatus() == "page,list,search_init") {

//	$page->setUrlMarker();
//	$page->getTargetTemplate("c300 border", "container:item");
//}

// footer
if($page->getStatus("page")) {
	$page->footer();
}



/*
else if($page->getStatus() == "update") {

	// attempt to update
	if($page->getObject($object)->updateTranslation($id)) {
		$page->getTemplate("defaults/html.view.php", $object, "c300 border", "container:item", false, true);
	}
	exit();

}
else if($page->getStatus() == "view_cancel") {

	$page->getTargetTemplate("c300 border", "container:item");
	$page->getLoadTargetScript("container:item_list", $page->url.'?page_status=list');
	exit();

}
else if($page->getStatus() == "edit_cancel") {

	$page->getTemplate("defaults/html.view.php", $object, "c300 border", "container:item");
	exit();

}
else if($page->getStatus() == "edit_single") {

	$page->getTemplate("defaults/html.edit.php", $object, "c300 border", "container:item", false, true);
	exit();

}
else {

	$_SESSION["view"] = "all";
	$page->header();
	$page->getTemplate("defaults/html.list.php", $object, "c300 border", "container:item_list", "container:item");
	$page->getTargetTemplate("c150 border", "container:item");
	$page->footer();

}
*/

?>