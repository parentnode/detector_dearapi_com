<?php
$access_item = array();
$access_item["list"] = true;
$access_item["link"] = "list";
$access_item["view"] = true;
$access_item["edit"] = true;

$access_item["sindex_update"] = false;

$access_item["update"] = "edit";
$access_item["edit_cancel"] = "edit";

$access_item["file"] = true;
$access_item["delete"] = true;

$access_item["enable_disable"] = "edit";


$access_item["search_init"] = "list";
$access_item["search"] = "list";
$access_item["search_reset"] = "list";

$access_item["done"] = "view";
$access_item["next"] = "view";
$access_item["prev"] = "view";

$access_item["tags"] = true;

$access_item["tags_get"] = "tags";
$access_item["tags_add"] = "tags";

$access_item["tags_a_delete"] = "tags";
$access_item["tags_b_delete"] = "tags";
$access_item["tags_c_delete"] = "tags";

$access_item["davs"] = true;

$access_item["davs_add"] = "davs";
$access_item["davs_delete"] = "davs";


$access_item["description_edit"] = true;
$access_item["description_update"] = "description_edit";
$access_item["description_cancel"] = "description_edit";

$access_item["prices_edit"] = true;
$access_item["prices_update"] = "prices_edit";
$access_item["prices_cancel"] = "prices_edit";


//$access_item["description_new"] = true;
//$access_item["description_save"] = "description_new";
//$access_item["description_delete"] = true;




//user input
//$access_item["view_user_input"] = false;

//paging
/*
$access_item["next"] = "view";
$access_item["previous"] = "view";
$access_item["update_items_per_page"] = "view";
*/

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/class/system/page.class.php");
$object = $page->addObject("items/item.class.php");

// default view
if(!$page->getStatus() && $id) {$page->setStatus("page,view,search_init");}
if(!$page->getStatus()) {$page->setStatus("page,list,search_init");}

// header
if($page->getStatus("page")) {
	$page->header();
}

// views
if($page->getStatus("search_init")) {

	$page->getTemplate("html.search.php", $object, "c300 border", "container:item_search", false, true);
}

// excluding each other
if($page->getStatus("list")) {

	$page->getLoadTargetScript("container:item_search", $page->url, "page_status=search_init");
	$page->getObject($object)->getSearchItems();
	$page->getTemplate("html.list_no_new.php", $object, "c300 border", "container:item");
}
else if($page->getStatus("view")) {

	if($page->getObject($object)->getItem($id)) {
		$page->getResetTargetScript("container:item_search");
		$page->setUrlMarker("view,id=$id");
		$page->getTemplate("items/item.view.php", $object, "c300 border", "container:item");
	}
	else {
		messageHandler()->addErrorMessage("Item was not found! The list has been updated.");
		$page->getLoadTargetScript("container:item", $page->url.'?page_status=list');
	}
}
else if($page->getStatus("file")) {

	$page->getObject($object)->getItem($id);
	if($page->getObject($object)->updateItemtypeFile()) {
		$page->getLocationHrefScript("items.php?id=$id");
	}
	else {
		$page->getTemplate("items/item.view.php", $object, "c300 border", "container:item");
	}
}
else if($page->getStatus() == "done") {

	$page->setUrlMarker();
	$page->getLoadTargetScript("container:item_search", $page->url, "page_status=search_init");
	$page->getObject($object)->getSearchItems();
	$page->getTemplate("html.list_no_new.php", $object, "c300 border", "container:item");
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
if($page->getStatus("sindex_update")) {

	$page->getObject($object)->updateSIndex();
	$page->getObject($object)->getItem($id);

	$page->getSnippet($page->getObject($object)->viewSIndex());
	exit();
}
else if($page->getStatus("edit")) {

	$page->getObject($object)->getItem($id);
	$page->getSnippet($page->getObject($object)->editItemType());
	exit();
}
else if($page->getStatus() == "update") {

	$page->getObject($object)->getItem($id);
	if($page->getObject($object)->updateItemtype()) {
		$page->setUrlMarker("view,id=$id");
		$page->getSnippet($page->getObject($object)->viewItemType());
	}
	else {
		$page->getSnippet($page->getObject($object)->editItemType());
	}
	exit();
}
else if($page->getStatus() == "edit_cancel") {

	$page->getObject($object)->getItem($id);
	$page->setUrlMarker("view,id=$id");
	$page->getSnippet($page->getObject($object)->viewItemType());
	exit();
}
else if($page->getStatus() == "delete") {

	$page->getObject($object)->deleteItem($id);
	$page->setUrlMarker();
	$page->getLoadTargetScript("container:item_search", $page->url, "page_status=search_init");
	$page->getObject($object)->getSearchItems();
	$page->getTemplate("html.list_no_new.php", $object, "c300 border", "container:item");
	exit();
}

else if($page->getStatus() == "enable_disable") {

	$page->getObject($object)->getItem($id);
	$page->getObject($object)->enableDisable($id);
	$page->getLoadTargetScript("container:item", $page->url.'?page_status=view&id='.$id);
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


else if($page->getStatus() == "tags_add") {

	$page->getObject($object)->getItem($id);
	$page->getObject($object)->addTag($id, getVar("tag"));
	$page->getSnippet($page->getObject($object)->listTags());
	exit();
}
else if($page->getStatus() == "tags_a_delete") {

	// id = tag_id
	$item_id = getVar("item_id");
	$page->getObject($object)->deleteATag($id, $item_id);
	$id = getVar("item_id");
	$page->getObject($object)->getItem($item_id);
	$page->getSnippet($page->getObject($object)->listTags());
	exit();
}
else if($page->getStatus() == "tags_b_delete") {

	// id = tag_id
	$item_id = getVar("item_id");
	$page->getObject($object)->deleteBTag($id, $item_id);
	$id = getVar("item_id");
	$page->getObject($object)->getItem($item_id);
	$page->getSnippet($page->getObject($object)->listTags());
	exit();
}
else if($page->getStatus() == "tags_c_delete") {

	// id = tag_id
	$item_id = getVar("item_id");
	$page->getObject($object)->deleteCTag($id, $item_id);
	$id = getVar("item_id");
	$page->getObject($object)->getItem($item_id);
	$page->getSnippet($page->getObject($object)->listTags());
	exit();
}


else if($page->getStatus() == "tags_get") {
	print $page->getObject($object)->getAutoCompleteTags();
	exit();
}

/*
if($page->getStatus() == "davs_add") {

	$page->getObject($object)->getItem($id);
	$page->getObject($object)->addDav($id, getVar("dav"));
	$page->getSnippet($page->getObject($object)->listDavs());
	exit();
}
else if($page->getStatus() == "davs_delete") {

	// id = tag_id
	$page->getObject($object)->deleteDav($id);
	$id = getVar("item_id");
	$page->getObject($object)->getItem($id);
	$page->getSnippet($page->getObject($object)->listDavs());
	exit();
}
*/

else if($page->getStatus("description_edit")) {

	// id = language_id
	$page->getObject($object)->getItem(getVar("item_id"));
	$page->getSnippet($page->getObject($object)->editItemDescription($id));
	exit();
}
else if($page->getStatus() == "description_update") {

	$page->getObject($object)->getItem($id);
	$page->getObject($object)->updateItemDescription($id);
	$page->getSnippet($page->getObject($object)->listDescriptions());
	exit();
}
else if($page->getStatus() == "description_cancel") {

	$page->getObject($object)->getItem($id);
	$page->getSnippet($page->getObject($object)->listDescriptions());
	exit();
}

else if($page->getStatus("prices_edit")) {

	$page->getObject($object)->getItem($id);
	$page->getSnippet($page->getObject($object)->editPrices());
	exit();
}
else if($page->getStatus() == "prices_update") {

	$page->getObject($object)->getItem($id);
	$page->getObject($object)->updatePrices();
	$page->getSnippet($page->getObject($object)->listPrices());
	exit();
}
else if($page->getStatus() == "prices_cancel") {

	$page->getObject($object)->getItem($id);
	$page->getSnippet($page->getObject($object)->listPrices());
	exit();
}
?>