<?php
/**
* @package framwork.devices
*/
include_once("class/devices/device.core.class.php");
include_once("class/basics/contenttype.core.class.php");

/**
* This file contains device views functionality
* Extended by the actions class
*/
class DeviceView extends DeviceCore {

	/**
	* Get translation for file
	*/
	function __construct() {
		$this->addTranslation(__FILE__);
		parent::__construct();

		$this->contenttypeClass = new ContenttypeCore();
	}


	/**
	* make table listing of items
	* row link if link is passed
	*
	* @param string $link Item link (function will append item id to link)
	* @return string HTML view
	* @uses Generic::listItems()
	*/
	function listItems($link=false, $validate=false) {
		$items = $this->getSearchItems();
		$_ = $this->head("Devices");
		$_ .= Generic::listItemsExtended($link, $validate, $items["id"], array($items["values"], $items["display"], $items["segment"], $items["browser"]), array("Brand and model", "Display", "Segment", "search"), array("max", "", "", "search"));
		return $_;
	}

	/**
	* Search
	* Sets search values in session
	*/
	function search() {
		Session::setSearch("brand_id", getVar("brand_id"));
		Session::setSearch("contenttype_id", getVar("contenttype_id"));
		Session::setSearch("pattern", getVar("pattern"));
	}
	
	/**
	* Reset Search 
	* Resets search values in session
	*/
	function searchReset() {
		Session::resetSearch("brand_id");
		Session::resetSearch("contenttype_id");
		Session::resetSearch("pattern");
	}

	/**
	* Search form
	*
	* @return string HTML view
	*/
	function searchOptions() {
		global $HTML;

		$_ = '';
		$_ .= $HTML->head($this->translate("Search devices"));
		$_ .= $HTML->select($this->translate("Select brand"), "brand_id", Generic::getItems(UT_BAS_BRA, false, "name"), stringOr(Session::getSearch("brand_id")), array("", "-"), "Util.Ajax.submitContainer('container:item_search');");
		$_ .= $HTML->select($this->translate("Select contenttype"), "contenttype_id", Generic::getItems(UT_BAS_CON, false, "name"), stringOr(Session::getSearch("contenttype_id")), array("", "-"), "Util.Ajax.submitContainer('container:item_search');");
		$_ .= $HTML->input($this->varnames["pattern"], "pattern", stringOr(Session::getSearch("pattern")));

		return $_;
	}

}

?>