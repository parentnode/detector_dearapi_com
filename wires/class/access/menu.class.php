<?php
/**
* This file contains menu maintenance functionality
*/
include_once("menu.view.class.php");
include_once("accesspoint.class.php");
include_once("class/system/generic.class.php");
include_once("class/system/validator.class.php");

/**
* Menu, extends menu views
*/
class Menu extends MenuView {

	// used as menu structure container
	public $menu_layout;
	public $item_indent;

	public $varnames;
	public $vars;
	private $validator;

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {
		// initiate helpers before calling View construct
		$this->addTranslation(__FILE__);
		$this->validator = new Validator($this);
		parent::__construct();
		$this->db = UT_MEN;

		$this->varnames["name"] = $this->translate("Item name");
		$this->validator->rule("name", "txt");

		$this->varnames["url"] = $this->translate("Url");
		$this->varnames["page_list"] = $this->translate("Local pages");

		$this->varnames["relation"] = "";

		$this->vars = getVars($this->varnames);
	}

	/**
	* Get selected item
	* Makes query result available
	*
	* @param int $id Item id
	* @return bool
	* @uses Generic::getItem()
	*/
	function getItem($id) {
		return Generic::getItem($id, $this->db);
	}

	/**
	* Get selected item name
	*
	* @param int $id Item id
	* @return string|false Item name or false on error
	* @uses Generic::getItemName()
	*/
	function getItemName($id) {
		return Generic::getItemName($id, $this->db);
	}

	/**
	* Get all items by iterating based on relations
	*
	* @param Integer $relation Idetifier for iteration
	* @return array|false Item array or false on error
	*/
	function getItems($relation) {
		$query = new Query();
		$query->sql("SELECT * FROM ".$this->db." WHERE relation = $relation ORDER BY sequence ASC");

		// preliminary sort of menu items
		for($i = 0; $i < $query->getQueryCount(); $i++) {
			$item = null;
			$item->id = $query->getQueryResult($i,"id");
			$item->name = $query->getQueryResult($i,"name");
			$item->url = $query->getQueryResult($i,"url");
			$item->relation = $query->getQueryResult($i,"relation");
			$item->sequence = $query->getQueryResult($i,"sequence");
			$item->indent = $this->item_indent;
			$this->menu_layout[] = $item;

			$this->item_indent++;
			$this->getItems($item->id);
			$this->item_indent--;
		}
		return $this->menu_layout;
	}

	/**
	* Returns array of possible local files
	*/
	function pageList($which) {
		$accesspointClass = new AccessPoint();
		$points = $accesspointClass->getItems();
		return $points[$which];
	}

	/**
	* Save new item, based on submitted values
	*
	* @return bool
	* @uses Message
	*/
	function saveItem() {
		if($this->validator->validateAll()) {
			$vars = "DEFAULT";
			$vars .= ",'".$this->vars['name']."'";
			$vars .= ",'".$this->vars['url']."'";
			$vars .= ",DEFAULT";
			$vars .= ",DEFAULT";

			if($this->sql("INSERT INTO ".$this->db." VALUES($vars)")) {
				messageHandler()->addStatusMessage($this->translate("Item saved"));
				$this->makeNavigationForTranslation();
				return $this->getLastInsertId();
			}
			else {
				messageHandler()->addErrorMessage($this->dbError());
				return false;
			}
		}
		else {
			messageHandler()->addErrorMessage($this->translate("Please complete missing information"));
			return false;
		}
	}

	/**
	* Update edited item
	*
	* @param int $id Item id
	* @return bool
	* @uses Message
	*/
	function updateItem($id) {
		if($this->validator->validateAll()) {
			$vars = "name='".$this->vars['name']."'";
			$vars .= ",url='".$this->vars['url']."'";

			if($this->sql("UPDATE ".$this->db." SET $vars WHERE id = $id")) {
				messageHandler()->addStatusMessage($this->translate("Item updated"));
				$this->makeNavigationForTranslation();
				return true;
			}
			else {
				messageHandler()->addErrorMessage($this->dbError());
				return false;
			}
		}
		else {
			messageHandler()->addErrorMessage($this->translate("Please complete missing information"));
			return false;
		}
	}

	/**
	* Delete selected item
	*
	* @param int $id Item id
	* @return bool
	* @uses Message
	*/
	function deleteItem($id) {
		if(!$this->checkUsage($id)) {
			if($this->sql("DELETE FROM ".$this->db." WHERE id = $id")) {
				messageHandler()->addStatusMessage($this->translate("Item deleted"));
				$this->makeNavigationForTranslation();
				return true;
			}
			else {
				messageHandler()->addErrorMessage($this->dbError());
				return false;
			}
		}
	}

	/**
	* Update structure
	*
	* @param int $id Item id
	* @return bool
	* @uses getVar
	* @uses Message
	*/
	function updateStructure($id) {
		$relation = $this->vars['relation'];
		$sequence = array();
		$updates = 0;

		for($i = 0; $i < count($id); $i++) {
			$sequence[$relation[$i]] = isset($sequence[$relation[$i]]) ? $sequence[$relation[$i]]+1 : 0;
			if($this->sql("UPDATE ".$this->db." SET relation = ".$relation[$i].", sequence = ".$sequence[$relation[$i]]." WHERE id = ".$id[$i])) {
				$updates++;
			}
		}
		if($updates == count($id)) {
			messageHandler()->addStatusMessage($this->translate("Structure updated"));
			$this->makeNavigationForTranslation();
			return true;
		}
		else {
			messageHandler()->addErrorMessage($this->dbError());
			return false;
		}
	}

	/**
	* Check if item has children
	*
	* @param Integer $id Item id
	* @return bool
	*/
	function checkUsage($id) {
		if($this->sql("SELECT id FROM ".$this->db." WHERE relation = $id")) {
			return true;
		}
		else {
			return false;
		}
	}


	/**
	* Recursive function, looping through children
	*
	* @param Array $items Nested array containing menu structure
	* @param Integer $indent Current level of structure indenting
	* @param Resource $file Filepointer to write to
	*/
	function makeNavigationForTranslation() {
		$filename = LOCAL_PATH."/templates/menu.summary.php";

		$file = fopen($filename, "w+");
		fwrite($file, '<?xml version="1.0" encoding="UTF-8"?>'."\n");

		$query = new Query();
		$query->sql("SELECT name FROM ".UT_MEN);
		for($i = 0; $i < $query->getQueryCount(); $i++) {
			$name = $query->getQueryResult($i,"name");
			if($name != "----") {
				fwrite($file, '<element><?= $this->translate'."".'("'.$name.'")?></element>'."\n");
			}
		}
		fclose($file);
	}


}

?>