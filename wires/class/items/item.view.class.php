<?php
/**
* @package wires
*/
include_once("class/items/item.tags.class.php");
include_once("class/basics/country.class.php");
include_once("class/basics/language.class.php");
include_once("class/items/price_group.class.php");

/**
* This file contains item views functionality
* Extended by the actions class
*
* TEMPLATES
*
* item.view (wrapper) -> delete
*
* description.list
* description.edit -> update
*
* itemtype.list -> itemtype class
* itemtype.view -> itemtype class
* itemtype.edit -> itemtype class
*
*/
class ItemView extends ItemTags {

	function __construct() {
//		$this->translater->__construct(__FILE__);
		$this->addTranslation(__FILE__);

		// initialize related classes
		$this->countryClass = new Country();
		$this->languageClass = new Language();

		$this->priceGroupClass = new PriceGroup();

		parent::__construct();
	}

	/**
	*
	*/
	function enableDisableSmartButton($id) {
		global $page;
		global $HTML;

		$status = $this->getStatus($id);
		$class = $status ? " disable" : " enable";
		$label = $status ? $this->translate("Disable") : $this->translate("Enable");

		$_ = '';
		$_ .= $HTML->smartButton($label, "enable_disable", "enable_disable", "fright".$class);
		return $_;
	}

	/**
	* New item, select item type and move on the type class
	*/
	function newItem() {
		global $HTML;
		global $page;
		$HTML->details(1);

		if($this->validator->validateList("itemtype_id")) {

			$this->setItemtype($this->vars["itemtype_id"]);
			return $this->getTypeObject()->newItem();
		}
		// only one itemtype? skip selection
		else if(count($this->itemtypes["id"]) == 1) {

			$this->setItemtype($this->itemtypes["id"][0]);
			return $this->getTypeObject()->newItem();
		}
		else {
			$default_value = array("0", $this->translate("Select"));
			$_ = '';
			$_ .= $this->head($this->translate("Choose Itemtype for new item"));
			$_ .= $this->p($this->translate("Select an itemtype and click create!"), "info");
			$_ .= $HTML->select($this->varnames["itemtype_id"], "itemtype_id", $this->itemtypes, $this->vars["itemtype_id"], $default_value);
			$_ .= HTML::smartButton($this->translate("Create"), "new", "new", "fright key:s");

			return $_;
		}
		
	}


	function listItems($link=false, $validate=false, $classname=false) {
		return $this->getTypeObject()->listItems($link, $validate, $classname);
	}

	/**
	* View itemtype specific values
	*
	* @return string HTML-view 
	* @uses itemtype->viewItem()
	*/
	function viewItemType() {
//		global $HTML;
		global $page;

		$_ = '';
		$_ .= '<div class="c init:form form:action:'.$page->url.'" id="container:itemtype">';
			$_ .= $this->getTypeObject()->viewItem();
		$_ .= '</div>';

		return $_;
	}

	/**
	* Edit itemtype specific values
	*
	* @return string HTML-view 
	* @uses itemtype->editItem()
	*/
	function editItemType() {
		global $page;
		global $HTML;
		$HTML->details(1);

		$_ = '';
		$_ .= '<div class="c init:form form:action:'.$page->url.'" id="container:itemtype">';
			$_ .= $this->getTypeObject()->editItem();
		$_ .= '</div>';

		return $_;
	}

	/**
	* List item tags and unvalidated tags
	* 
	* @return string HTML-view
	*/
	function viewSIndex() {
		global $page;
		global $id;
		$this->vars["sindex"] = stringOr($this->vars["sindex"], $this->item["sindex"][0]);

		$_ = '';
		$_ .= '<div class="ci33 init:form form:action:'.$page->url.'" id="container:sindex">';
		$_ .= '<div class="init:expandable id:sindex">';
		$_ .= $this->head("sIndex", "2");
		$_ .= '<fieldset>';

		$_ .= $this->p("Used as link alias to achieve search engine optimization. Only a-z and 0-9 allowed. And it must be unique - if already used it will be prepended a unique identifier.", "info");

		if(Session::getLogin()->validatePage("sindex_update")) {
			$_ .= $this->inputHidden("id", $id);
			$_ .= $this->inputHidden("page_status", "sindex_update");
			$_ .= $this->input("sindex");
			$_ .= $this->smartButton($this->translate("Update"), false, "sindex_update", "fright");
		}
		else {
			$_ .= $this->block($this->varnames["sindex"], $this->vars["sindex"]);
		}

		$_ .= '</fieldset>';
		$_ .= '</div>';
		$_ .= '</div>';
		return $_;
	}

	/**
	* Lists languages and description
	* 
	* @return string HTML-view
	* @uses Item::getDescription()
	*/
	function listDescriptions() {
		global $HTML;
		global $page;
		global $id;

		$this->getDescription();

		$languages = $this->languageClass->getItems();

		$table = $HTML->table();
		$table->setTableId("descriptions");
		$table->setHeader(0, $this->translate("Language"), "sortby");
		$table->setHeader(1, $this->translate("Title"));
		$table->setHeader(2, $this->translate("Search"), "max search");
		$column_1 = false;
		$column_2 = false;
		$column_3 = false;
		$ids = false;
		$status = false;
		$count = isset($this->item["title"]) ? (" (" . count($this->item["title"][0]) .")") : "";
		foreach($languages["id"] as $language_key => $language_id) {
			if($this->item["description_language_id"][0] && array_search($language_id, $this->item["description_language_id"][0]) !== false) {
				$column_1[] = $this->languageClass->getItemName($language_id);
				$column_2[] = $this->item["title"][0][array_search($language_id, $this->item["description_language_id"][0])];
				$column_3[] = $this->item["description_short"][0][array_search($language_id, $this->item["description_language_id"][0])];
			}
			else {
				$column_1[] = $this->languageClass->getItemName($language_id);
				$column_2[] = "-";
				$column_3[] = "-";
			}
			$ids[] = $language_id; 
			$status[] = "description_edit";
		}

		$table->setRowStatus($status);
		$table->setRowId($ids);
		$table->setColumnValues($column_1, $column_2, $column_3);

		$_ = '';
		$_ .= '<div class="c init:form form:action:'.$page->url.'" id="container:description">';
		$_ .= '<div class="c init:expandable id:descriptions">';
		$_ .= $HTML->head($this->translate("Descriptions") . $count, "2");
		$_ .= '<fieldset>';

		$_ .= $HTML->inputHidden("id", $id);
		$_ .= $HTML->inputHidden("item_id", $id);
		$_ .= $HTML->inputHidden("page_status", "description_edit");

		$_ .= $table->build();
		$_ .= '</fieldset>';
		$_ .= '</div>';
		$_ .= '</div>';
		return $_;
	}

	/**
	* Edit a description in a given language for this item 
	* 
	* @param String $language_id ISO language code
	* @return string HTML-view
	* @uses Item::getDescription()
	*/
	function editItemDescription($language_id) {
		global $HTML;
		global $page;
		$HTML->details(1);

		$this->getDescription();

		// index for selected language_id
		$index = $this->item["description_language_id"][0] ? array_search($language_id, $this->item["description_language_id"][0]) : false;

		$title = stringOr($this->vars["title"], $index !== false ? $this->item["title"][0][$index] : "");
		$description_short = stringOr($this->vars["description_short"], $index !== false ? $this->item["description_short"][0][$index] : "");
		$description_long = stringOr($this->vars["description_long"], $index !== false ? $this->item["description_long"][0][$index] : "");

		$_ = '';
		$_ .= '<div class="c init:form form:action:'.$page->url.'" id="container:description">';
		$_ .= $HTML->head($this->translate("Description"), 2);

		$_ .= $HTML->inputHidden("id", $this->item["id"][0]);
		$_ .= $HTML->inputHidden("description_language_id", $language_id);
		$_ .= $HTML->inputHidden("page_status", "description_update");

		$_ .= '<div class="c">';

		$_ .= '<div class="ci50">';
		$_ .= $HTML->block($this->varnames["description_language_id"], $this->languageClass->getItemName($language_id));
		$_ .= $HTML->input($this->varnames["title"], "title", $title);
		$_ .= $HTML->input($this->varnames["description_short"], "description_short", $description_short);

		$_ .= '</div>';
		$_ .= '<div class="ci50">';

		$_ .= $HTML->textarea($this->varnames["description_long"], "description_long", $description_long);

		$_ .= '</div>';
		$_ .= '</div>';
		
		$_ .= $HTML->smartButton($this->translate("Cancel"), false, "description_cancel", "fleft key:esc");
		$_ .= $HTML->smartButton($this->translate("Save"), false, "description_update", "fright key:s");
		$_ .= $HTML->separator();

		$_ .= '</div>';

		return $_;
	}



	/**
	* List item prices
	* 
	* @return string HTML-view
	*/
	function listPrices() {
		global $HTML;
		global $page;
		global $id;
		$this->getPrices();

		$price_groups = $this->priceGroupClass->getItems();
		$countries = $this->countryClass->getItems();

		$_ = '';
		$_ .= '<div class="c init:form form:action:'.$page->url.'" id="container:prices">';
		$_ .= '<div class="init:expandable id:prices">';

		$_ .= $HTML->head("Prices", "2");
		$_ .= '<fieldset>';

		if(!$price_groups) {
			$_ .= $HTML->p("No price groups");
		}
		else {
			
			$_ .= $HTML->inputHidden("id", $id);
			$_ .= $HTML->inputHidden("item_id", $id);
			if(Session::getLogin()->validatePage("prices_edit")) {
				$_ .= $HTML->inputHidden("page_status", "prices_edit");
			}

			if($this->item() && count($this->item["id"]) == 1) {

				foreach($countries["id"] as $country_key => $country_id) {

					if(Session::getLogin()->validatePage("prices_edit")) {
						$_ .= '<div class="ci33 init:button status:prices_edit">';
					}
					else {
						$_ .= '<div class="ci33">';
					}

					$_ .= $HTML->head($countries["values"][$country_key], 3);

					foreach($price_groups["uid"] as $index => $price_group_uid) {
						$price = ($this->item["price"][0] && isset($this->item["price"][0][$country_id][$price_group_uid])) ? $this->item["price"][0][$country_id][$price_group_uid] : "";
						$_ .= $HTML->p($price_groups["values"][$index].": $price ".$this->countryClass->getCurrency($country_id, "abbreviation"));
					}

					$_ .= $HTML->separator();
					$_ .= '</div>';

				}
			}
		}


		$_ .= '</fieldset>';
		$_ .= '</div>';
		$_ .= '</div>';
		return $_;
	}

	
	/**
	* Edit prices for this item 
	* 
	* @return string HTML-view
	* @uses Item::getPrices()
	*/
	function editPrices() {
		global $HTML;
		global $page;
		global $id;
		$HTML->details(1);

		$this->getPrices();

		$price_groups = $this->priceGroupClass->getItems();
		$countries = $this->countryClass->getItems();

		$_ = '';
		$_ .= '<div class="c init:form form:action:'.$page->url.'" id="container:prices">';
		$_ .= '<div class="c">';

		$_ .= $HTML->inputHidden("id", $id);
		$_ .= $HTML->inputHidden("item_id", $id);

		$_ .= $HTML->head("Prices", "2");

		if(Session::getLogin()->validatePage("prices_update")) {
			$_ .= $HTML->inputHidden("page_status", "prices_update");
		}

		if($this->item() && count($this->item["id"]) == 1) {

			foreach($countries["id"] as $country_key => $country_id) {

				$_ .= '<div class="ci33">';

					$_ .= $HTML->head($countries["values"][$country_key], 3);

					foreach($price_groups["uid"] as $index => $price_group_uid) {
						$price = ($this->item["price"][0] && isset($this->item["price"][0][$country_id][$price_group_uid])) ? $this->item["price"][0][$country_id][$price_group_uid] : "";
						$_ .= $HTML->input($price_groups["values"][$index], "prices[$country_id][$price_group_uid]", $price);
					}

					$_ .= $HTML->separator();
				$_ .= '</div>';

			}
		}

		$_ .= '</div>';

		$_ .= $HTML->smartButton($this->translate("Cancel"), false, "prices_cancel", "fleft key:esc");
		$_ .= $HTML->smartButton($this->translate("Save"), false, "prices_update", "fright key:s");

		$_ .= '</div>';

		return $_;
	}

}

?>