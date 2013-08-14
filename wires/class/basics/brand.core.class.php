<?php
/**
* @package framework.devices
*/


/**
* This file contains brand core maintenance functionality.
* Brand Core, extends Translation
*/
class BrandCore extends Translation {

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {
		$this->addTranslation(__FILE__);
		$this->db = UT_BAS_BRA;
		
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
	* Get all items
	*
	* @param string $which Optional limitation of returned result. ("id" or "values")
	* @return array|false Item array or false on error
	* @uses Generic::getItemName()
	*/
	function getItems($which=false) {
		return Generic::getItems($this->db, $which, "name");
	}
	
	/**
	* Checking usage of selected item
	*
	* Checking if item id is in use in database tables:
	*
	* @param int $id Item id
	* @return bool
	*/
	function checkUsage($id) {
		return false;
		//return Generic::checkUsage($id, "brand_id", UT_DEV);
	}
}

?>