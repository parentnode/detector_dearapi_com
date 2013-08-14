<?php

$column = isset($column) ? $column : "c150";

// default view
if(!$page->getStatus()) {$page->setStatus("page,list");}

// header
if($page->getStatus("page")) {
	$page->header();
}

// views
if($page->getStatus("list")) {

	$page->getTemplate("html.list.php", $object, "$column border", "container:item_list", "container:item");
}

// excluding each other
if($page->getStatus("new")) {

	$page->setUrlMarker("list,new");
	$page->getTemplate("html.new.php", $object, "$column border", "container:item");
}
else if($page->getStatus("view")) {

	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("list,view,id=$id");
		$page->getTemplate("html.view.php", $object, "$column border", "container:item");
	}
	else {$page->reloadList("$column border");}
}
else if($page->getStatus("edit")) {

	if($page->getObject($object)->getItem($id)) {
		$page->setUrlMarker("list,edit,id=$id");
		$page->getTemplate("html.edit.php", $object, "$column border", "container:item");
	}
	else {$page->reloadList("$column border");}
}

// default required target
else if($page->getStatus() == "page,list" || $page->getStatus() == "done") {
	$page->setUrlMarker();
	$page->getTargetTemplate("$column border", "container:item");
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
		$page->reloadListView("$column border", $id, $object);
	}
	else {
		$page->getTemplate("html.new.php", $object, "$column border", "container:item");
	}
	exit();
}
else if($page->getStatus() == "update") {

	// attempt to update
	if($page->getObject($object)->updateItem($id)) {
		$page->reloadListView("$column border", $id, $object);
	}
	else {
		$page->getTemplate("html.edit.php", $object, "$column border", "container:item");
	}
	exit();
}
else if($page->getStatus() == "delete") {

	// attempt to delete
	$page->getObject($object)->deleteItem($id);
	$page->reloadList("$column border");
	exit();
}

?>