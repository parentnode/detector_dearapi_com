<?php
/**
* @package global.basics
*/
/**
*
*/

//include_once("contenttype.core.class.php");
include_once("class/system/generic.class.php");


/**
* This file contains itemtype maintenance functionality.
* Itemtype, extends Itemtype views
*
*/
class ItemtypeCore extends Translation {

	//public $mimetypes;
	public $varnames;
	public $vars;
	private $validator;

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {
//		this->translate
		$this->addTranslation(__FILE__);
		$this->db = UT_BAS_ITT;
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
		$query = new Query();
		if($query->sql("SELECT name FROM ".$this->db." WHERE id = $id")) {
			return $query->getQueryResult(0, "name");
		}
		else {
			return false;
		}
	}

	/**
	* Get all items
	*
	* @param string $which Optional limitation of returned result. ("id" or "values")
	* @return array|false Item array or false on error
	*/
	function getItems($which=false) {
		return Generic::getItems($this->db, $which);
	}


	/**
	* Checking usage of selected item
	*
	* @param int $id Item id
	* @return bool
	*/
	function checkUsage($id) {
		return false;
	}
}
?>