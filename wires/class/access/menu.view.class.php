<?php
/**
* This file contains menu views functionality
* Extended by the menu class
*/
class MenuView extends Translation {

	/**
	* Get translation for file
	*/
	function __construct() {
		$this->addTranslation(__FILE__);
//		$this->translater = new Translation(__FILE__);
		
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
		$_ .= $HTML->head($this->translate("View menu item"));
		$_ .= $HTML->block($this->varnames["name"], $this->getQueryResult(0, "name"));
		$_ .= $HTML->block($this->varnames["url"], stringOr($this->getQueryResult(0, "url"), "-"));

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

		$name = stringOr($this->vars["name"], $this->getQueryResult(0, "name"));
		$url = stringOr($this->vars["url"], $this->getQueryResult(0, "url"));

		$_ = '';
		$_ .= $HTML->head($this->translate("Edit menu item"));
		$_ .= $HTML->input($this->varnames["name"], "name", $name, false, "item_name");
		$_ .= $HTML->input($this->varnames["url"], "url", $url, false, "item_url");
		$_ .= $HTML->select($this->varnames["page_list"], "page_list", array("id"=>$this->pageList("file"), "values"=>$this->pageList("values")), $this->vars["page_list"], array("", "-"), "Util.setValue('item_url', this.options[this.selectedIndex].value); document.getElementById('item_name').value ? '' : Util.setValue('item_name', this.options[this.selectedIndex].text);");

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
		$_ = '';
		$_ .= $HTML->head($this->translate("New menu item"));
		$_ .= $HTML->input($this->varnames["name"], "name", $this->vars["name"], false, "item_name");
		$_ .= $HTML->input($this->varnames["url"], "url", $this->vars["url"], false, "item_url");
		$_ .= $HTML->select($this->varnames["page_list"], "page_list", array("id"=>$this->pageList("file"), "values"=>$this->pageList("values")), $this->vars["page_list"], array("", "-"), "Util.setValue('item_url', this.options[this.selectedIndex].value); document.getElementById('item_name').value ? '' : Util.setValue('item_name', this.options[this.selectedIndex].text);");

		return $_;
	}

	/**
	* make table listing of items
	* row link if link is passed
	*
	* @param string $link Item link (function will append item id to link)
	* @return string HTML view
	*/
	function listItems($link, $validate) {
		global $HTML;
		$_ = '';
		$_ .= $HTML->head($this->translate("Menu layout"));

		// set initial values
		$this->item_indent = 0;
		$this->menu_layout = array();

		// get items
		$items = $this->getItems($this->item_indent);

		// no items
		if(!$items) {
			$table = $HTML->table(false);
			$table->setHeader(0, $this->translate("Menu structure"));
			$values[] = $this->translate("No menu available");
		}
		// items
		else{
			$table = $HTML->table("arrange");
			$table->setHeader(0, $this->translate("Menu structure"));
			foreach($items as $key) {
				$ids[] = $key->id;
				$values[] = $key->name;
				$links[] = $link.$key->id;
				$indents[] = $key->indent;
				$status[] = $link; 
			}
			if(!$validate || Session::getLogin()->validatePage($validate)) {
				$table->setRowStatus($status);
			}
			$table->setRowId($ids);

			$table->setColumnType(0, "indent");
			$table->setColumnIndent(0, $indents);
		}
		$table->setColumnValues($values);

		$_ .= $table->build();
		return $_;
	}

}

?>