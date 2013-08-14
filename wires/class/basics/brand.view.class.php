<?php
/**
* @package framework.devices.
*/
include_once("brand.core.class.php");

/**
* This file contains brand views functionality
* Extended by the actions class
*/
class BrandView extends BrandCore {

	function __construct() {
		$this->addTranslation(__FILE__);
		parent::__construct();
	}

	/**
	* View item with item id
	* Item held in query result
	*
	* @return string HTML view
	*/
	function viewItem() {
		global $HTML;
		global $id;
		
		$name = $this->getQueryResult(0, "name");
		$note = $this->getQueryResult(0, "note");
		
		$_ = '';
		$_ .= $this->head("$name");
		$_ .= $this->block($this->varnames["name"], $name);
		$_ .= $this->block($this->varnames["note"], stringOr($note, "-"));
		
		return $_;
	}

	/**
	* Edit item
	* Item held in query result
	*
	* @return string HTML view
	*/
	function editItem() {
		global $id;

		$this->details(1);
		
		$name = $this->getQueryResult(0, "name");
		$note = $this->getQueryResult(0, "note");
		$this->vars["name"] = stringOr($this->vars["name"], $name);
		
		$_ = '';
		$_ .= $this->head($this->translate("Edit ###$name###"));
		$_ .= $this->input("name");


		$_ .= $this->textarea($this->varnames["note"], "note", stringOr($this->vars["note"], $note));
		
		return $_;
	}
	
	/**
	* New item form
	*
	* @return string HTML view
	*/
	function newItem() {
		$this->details(1);
		
		$_ = '';
		$_ .= $this->head($this->translate("New brand"));
		$_ .= $this->input("name");
		$_ .= $this->textarea($this->varnames["note"], "note", $this->vars["note"]);
				
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
		// get items
		$items = $this->getItems();

		$_ = $this->head($this->translate("Brands"));
		$_ .= Generic::listItemsExtended($link, $validate, $items["id"], array($items["values"], array()), array($this->varnames["name"], "search"), array("max", "search"));
		return $_;
	}

}

?>