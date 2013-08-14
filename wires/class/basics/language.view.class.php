<?php
/**
* @package global.basics
* This file contains language views functionality
* Extended by the actions class
*/
class LanguageView extends Translation {

	/**
	* Get translation for file
	*/
	function __construct() {
		//$this->translater->__construct(__FILE__);
		$this->addTranslation(__FILE__);
	}

	/**
	* View item with item id
	* Item held in query result
	*
	* @return string HTML view
	*/
	function viewItem() {
		global $HTML;

		$_ = '';
		$_ .= $HTML->head($this->translate("Language").": ".$this->getQueryResult(0, "name"));
		$_ .= $HTML->block($this->varnames["name"], $this->getQueryResult(0, "name"));
		$_ .= $HTML->block($this->varnames["id"], $this->getQueryResult(0 ,"id"));

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

		$default_value = array("", $this->translate("Select"));

		$_ = '';
		$_ .= $HTML->head($this->translate("New language"));
		$_ .= $HTML->select($this->varnames["name"], "id", $this->getAllItems(), $this->vars["id"], $default_value);

		return $_;
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
		global $HTML;
		// get items
		$items = $this->getItems();

		$_ = $HTML->head($this->translate("Languages"));
		$_ .= Generic::listItemsExtended($link, $validate, $items["id"], array($items["values"], $items["id"]), array($this->translate("Language name"), $this->translate("search")), array("max", "search acenter"));

		return $_;
	}

}

?>