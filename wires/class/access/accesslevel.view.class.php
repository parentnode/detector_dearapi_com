<?php
/**
* This file contains accesslevel views functionality
* @package System
*/

/**
* Extended by the accesslevelclass
* @package System
*/
class AccesslevelView extends Translation {

	/**
	* Get translation for file
	*/
	function __construct() {
		$this->addTranslation(__FILE__);
	}

	/**
	* View item with item id
	* Item held in query result
	*
	* @global Object $HTML
	* @return string HTML view
	*/
	function viewItem() {
		global $HTML;
		$_ = '';
		$_ .= $HTML->head($this->translate("View level"));
		$_ .= $HTML->block($this->varnames["name"], $this->getQueryResult(0, "name"));
		$_ .= $HTML->block($this->varnames["notes"], stringOr($this->getQueryResult(0, "notes"), "-"));

		return $_;
	}

	/**
	* Edit item
	* Item held in query result
	*
	* @return string HTML view
	*/
	function editItem() {
		global $HTML;
		$HTML->details(1);
		$name = stringOr($this->vars["name"], $this->getQueryResult(0, "name"));
		$notes = stringOr($this->vars["notes"], $this->getQueryResult(0, "notes"));

		$_ = '';
		$_ .= $HTML->head($this->translate("Edit level"));
		$_ .= $HTML->input($this->varnames["name"], "name", $name);
		$_ .= $HTML->textArea($this->varnames["notes"], "notes", $notes);

		return $_;
	}

	/**
	* New item form
	*
	* @return string HTML view
	*/
	function newItem() {
		global $HTML;
		$HTML->details(1);
		$_ = '';
		$_ .= $HTML->head($this->translate("New level"));
		$_ .= $HTML->input($this->varnames["name"], "name", $this->vars["name"]);
		$_ .= $HTML->textarea($this->varnames["notes"], "notes", $this->vars["notes"], "30");

		return $_;
	}

	/**
	* Make table listing of items
	*
	* @param string $link Optional item link (function will append item id to link)
	* @param array $validate Optional Validation information
	* @return string HTML view
	* @uses Generic::listItems()
	*/
	function listItems($link=false, $validate=false) {
		global $HTML;
		$items = $this->getItems();
		$_ = '';
		$_ .= $HTML->head($this->translate("Access levels"));
		$_ .= Generic::listItems($link, $validate, $items, $this->translate("Access Levels"));
		return $_;
	}

	/**
	* Make table listing of controllers
	*
	* @param string $link Point link
	* @return string HTML view
	*/
	function listPointItems($id, $link, $validate) {
		global $HTML;
		$ids = array();
		$points = array();
		$actions = array();
		$accesspointClass = new Accesspoint();
		$items = $accesspointClass->getItems();
		foreach($items["id"] as $key => $point_id) {
			$file = $this->getAccessPoint($point_id);
			$read_access = true;
			// do not include reset scripts
			//if(!stristr($file, "/reset/") && !stristr($file, "/error/")) {
				
//				print $file;
				if(file_exists($file)) {
					include($file);
				}
				$access = $this->getItemAccess($id, $point_id);
				if($access_item) {
					$access_items = 0;
					foreach($access_item as $base_action) {
						$access_items += ($base_action === true ? 1 : 0);
					}
					$ids[] = $point_id;
					$points[] = $items["values"][$key];
					$actions[] = count($access)."/".$access_items;
				}
			//}
		}
		$_ = '';
		$_ .= $HTML->head($this->translate("Access points"), 2);
		$_ .= Generic::listItemsExtended($link, $validate, $ids, array($points, $actions), array($this->translate("Point"), $this->translate("Allowed/Actions")), array("max", "acenter"));
		return $_;
	}

	/**
	* Make table listing of controller actions
	* Reads access_item array from controller
	*
	* @param Integer $id Access level id
	* @return string HTML view
	*/
	function listPointActions($access_level_id, $id) {
		global $HTML;

		$file = $this->getAccessPoint($id);
		$read_access = true;
	
		if(file_exists($file)) {
			include($file);
		}
		$access = $this->getItemAccess($access_level_id, $id);

		// no items
		if(!$access_item) {
			$table = $HTML->table(false);
			$table->setHeader(0, $this->translate("Point actions"));
			$values[] = $this->translate("No actions available");
			$table->setColumnValues($values);
		}
		// items
		else {
			$table = $HTML->table();
			$table->setHeader(1, $this->translate("Point actions"), "max");

			$values = array();
			foreach($access_item as $key => $value) {
				if($value === true) {
					$values[] = $key;
					$checks[0][] = "access[".$key."]";
					$checks[1][] = array_search($key, $access) !== false ? 1 : 0;
				}
			}
			$table->setColumnType(0, "checkbox");
			$table->setColumnValues($checks, $values);
		}

		return $table->build();
	}

}

?>