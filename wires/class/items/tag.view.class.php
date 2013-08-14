<?php
/**
* @package regional.items
*/
/**
* This file contains tag views functionality
* Extended by the actions class
* @package regional
*/
class TagView extends Translation {

	/**
	* View item with item id
	* Item held in query result
	*
	* @return string HTML view
	*/
	function viewItem() {
		global $HTML;
		$_ = '';
		$_ .= $HTML->head("Tag: ".$this->getQueryResult(0, "name"));
		$_ .= $HTML->block($this->varnames["name"], $this->getQueryResult(0, "name"));
		
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
		
		$_ = '';
		$_ .= $HTML->head("Edit ".$this->getQueryResult(0, "name"));
		$_ .= $HTML->input($this->varnames["name"], "name", $this->vars["name"] ? $this->vars["name"] : $this->getQueryResult(0, "name"));
		
		return $_;
	}
	
	/**
	* New item form
	*
	* @return string HTML view
	*/
	function newItem() {
		global $HTML;
		global $page;
		
		$HTML->details(1);
		
		$_ = '';
		$_ .= $HTML->head("New tag");
		$_ .= $HTML->input($this->varnames["name"], "name", $this->vars["name"]);
		
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
		$_ = $HTML->head("Tags");
		$_ .= Generic::listItemsExtended($link, $validate, $items["id"], array($items["values"], array()), array("Tag name", "Search"), array("max", "search acenter"));
		return $_; 
	}

}

?>