<?php
/**
* @package global.basics
*/
/**
* includes
*/


/**
* This file contains contenttype maintenance functionality.
* Contenttype, extends Contentype views
*/
class ContenttypeCore extends Translation {

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {
		$this->addTranslation(__FILE__);
		
		$this->db = UT_BAS_CON;
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
	* @uses Generic::getItemName()
	*/
	function getItems($which=false, $order=false) {
		$items = array();
		$query = new Query();
		$query->sql("SELECT id, name, contenttype FROM ".$this->db." ORDER BY name, contenttype");
		
		for($i = 0; $i < $query->getQueryCount(); $i++) {
			$items["id"][$i] = $query->getQueryResult($i, "id");
			$items["values"][$i] = $query->getQueryResult($i, "name");
			$items["contenttype"][$i] = $query->getQueryResult($i, "contenttype");
		}

		if(!count($items)) {
			return false;
		}
		else if($which) {
			return $items[$which];
		}
		else {
			return $items;
		}
	}

	/**
	* Checking usage of selected item
	*
	* @param int $id Item id
	* @return bool
	*/
	function checkUsage($id) {
		return false;
		//Generic::checkUsage($id, "contenttype_id", UT_BAS_ITT_CON);
	}

}
?>