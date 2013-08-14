<?php
/**
* This file contains accesspoint views functionality
* @package System
*/

/**
* Extended by the accesspointclass
* @package System
*/
class AccesspointView extends Translation {

	/**
	* Get translation for file
	*/
	function __construct() {
		$this->addTranslation(__FILE__);
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

		$_ = '';
		$_ .= $HTML->head($this->translate("Edit point name"));
		$_ .= $HTML->block($this->translate("Point").":", $this->getQueryResult(0, "file"));
		$_ .= $HTML->input($this->varnames["name"], "name", $name);

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
		$_ .= $HTML->head($this->translate("Access Points"));
		$_ .= Generic::listItems($link, $validate, $items, $this->translate("Points"));
		return $_;
	}

}

?>