<?php
/**
* @package framework
*/
/**
*
*/

include_once("class/items/item.core.class.php");

/**
* This file contains site item maintenance functionality
* Item
* @package local
*/
class ItemWCore extends ItemCore {

	function __construct() {
		$this->addTranslation(__FILE__);
		parent::__construct();
	}

	/**
	* Get matching type object (frontend version)
	* Object type based on $this->itemtype (sat via getItems)
	*
	* @param $itemtype Specific itemtype to get (optional)
	* @return return instance of type object
	*/
	function getTypeObject($itemtype=false) {

		$itemtype = $itemtype ? $itemtype : $this->itemtype;

		// include generic type (for mixed itemtypes)
		if($itemtype == "mixed" || !$itemtype) {
			$itemtype = "mixed";
			$class = "TypeMixed";
		}
		else {
			$class = "Type".ucfirst($itemtype);
		}

		if(!isset($this->itemtypes["class"][$itemtype])) {
			include_once("class/www/type.$itemtype.class.php");
			$this->itemtypes["class"][$itemtype] = new $class();

		}
		return $this->itemtypes["class"][$itemtype];
	}


	function getItemtype($sindex) {
		$this->getItem($sindex);
		return $this->item["itemtype"][0];
	}

	/**
	* Get search items
	*
	* @uses Item::getItems()
	*/
	function getSearchItem() {
		// getSearch / getValue
		$item_sindex = Session::getValue("item_sindex");
		$this->getItem($item_sindex);
	}

	/**
	* Get search items
	*
	* @uses Item::getItems()
	*/
	function getSearchItems() {
		// getSearch / getValue

		$itemtype = Session::getSearch("itemtype_id");
		$tags = Session::getValue("tags");
		$nav_sindex = Session::getValue("nav_sindex");
		$order = Session::getValue("order");
		$this->getItems($itemtype, 1, $tags, $nav_sindex, $order);
	}

}