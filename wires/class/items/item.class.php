<?php
/**
* @package wires
*/
/**
*
*/

/**
* includes
*/

include_once($_SERVER["FRAMEWORK_PATH"]."/class/system/page.class.php");

include_once("class/items/item.view.class.php");
include_once("class/system/validator.class.php");
include_once("class/system/filesystem.class.php");


/**
* This class holds Item functionallity.
*
*/
class Item extends ItemView {

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {
		// initiate helpers before calling View construct
//		this->translate
		$this->addTranslation(__FILE__);

		$this->validator = new Validator($this);
		parent::__construct();

		/*
		$this->itemtypeClass = new Itemtype();
		$this->itemtypes = $this->itemtypeClass->getItems();
		$this->country_id = Session::getCountryIso();
		$this->language_id = Session::getLanguageIso();

		$this->itemtype = false;
		$this->itemtype_id = false;
		
		$this->db = UT_ITE;
*/

		$this->varnames["itemtype_id"] = $this->translate("Itemtype");
		$this->validator->rule("itemtype_id", "txt");

		$this->varnames["status"] = $this->translate("Status");
		$this->varnames["sindex"] = $this->translate("sIndex");

		$this->varnames["tags"] = $this->translate("Appears in");

		$this->varnames["title"] = $this->translate("Title");
		$this->validator->rule("title", "txt");

		$this->varnames["description_short"] = $this->translate("Short description");
		$this->varnames["description_long"] = $this->translate("Long description");
		$this->varnames["description_language_id"] = $this->translate("Description language");

		$this->varnames["prices"] = $this->translate("Prices");

		$this->vars = getVars($this->varnames);
	}


	/**
	* Save item
	*/
	function saveItem() {
		if($this->validator->validateList("itemtype_id")) {
			$itemtype = $this->vars["itemtype_id"];

			$this->setItemtype($itemtype, true);

			// save core item
			$item_id = $this->save($this->itemtype_id, Session::getLogin()->getUserId(), 1);
			if($item_id) {

				// attempt to save type item
				if($this->getTypeObject()->saveItem($item_id)) {
					return $item_id;
				}
				// or clean up
				else {
					$this->sql("DELETE FROM ".$this->db." WHERE id = ".$item_id,true);
				}
			}
			else {
				messageHandler()->addErrorMessage($this->dbError());
				return false;
			}
		}
		else {
			messageHandler()->addErrorMessage($this->translate("Please complete missing information"));
			return false;
		}
	}

	/**
	* Delete selected item
	*
	* @param int $id Item id
	* @return bool
	* @uses Message
	*/
	function updateSIndex() {
		global $id;

		if($this->sIndex($id, $this->vars["sindex"])) {
			messageHandler()->addStatusMessage($this->translate("sIndex updated"));
			return true;
		}
		else {
			messageHandler()->addErrorMessage($this->translate("Wierd!! It didn't work."));
			return false;
		}
	}

	function updateItemtype() {
		return $this->getTypeObject()->updateItem();
	}
	function updateItemtypeFile() {
		return $this->getTypeObject()->updateItemFile();
	}

	/**
	* Delete selected item
	*
	* @param int $id Item id
	* @return bool
	* @uses Message
	*/
	function deleteItem($id) {
		if($this->sql("DELETE FROM ".$this->db." WHERE id = $id")) {
			// cleanup filesystem
			FileSystem::rmdirr(PUBLIC_FILE_PATH.$id);
			FileSystem::rmdirr(BACKUP_FILE_PATH.$id);
			messageHandler()->addStatusMessage($this->translate("Item deleted"));
			return true;
		}
		else {
			messageHandler()->addErrorMessage($this->dbError());
			return false;
		}
	}


	/**
	* Toggle enable/disable
	*/
	function enableDisable($item_id) {
		$query = new Query();
		$query->sql("SELECT status FROM " . UT_ITE . " WHERE id = $item_id");
		$status = $query->getQueryResult(0, "status");
		$query->sql("UPDATE " . UT_ITE . " SET status = '" . ($status ? 0 : 1) . "' WHERE id = '$item_id'");
		messageHandler()->addStatusMessage($this->itemtype . " " . ($status ? $this->translate("Disabled") : $this->translate("Enabled")));
	}



	/**
	* update item description in a given language for a given item
	*
	* @param int $item_id Item id
	* @return bool true on success otherwise false
	*/
	function updateItemDescription($item_id) {
		if($this->validator->validateList("title")) {
			$vars = "title='".$this->vars['title']."'";
			$vars .= ",description_short='".$this->vars['description_short']."'";
			$vars .= ",description_long='".$this->vars['description_long']."'";

			// if entry does not already exist, create empty dummy entry, to be updated
			if(!$this->sql("SELECT * FROM ".UT_ITE_DES." WHERE item_id = $item_id AND language_id = '".$this->vars['description_language_id']."'")) {
				$this->sql("INSERT INTO ".UT_ITE_DES." values('', '', '', '', $item_id, '".$this->vars['description_language_id']."')");
			}

			// update entry
			if($this->sql("UPDATE ".UT_ITE_DES." SET $vars WHERE item_id = $item_id AND language_id = '".$this->vars['description_language_id']."'")) {
				messageHandler()->addStatusMessage("Item description updated");
				return true;
			}
			else {
				messageHandler()->addErrorMessage($this->dbError());
				return false;
			}
		}
		else {
			messageHandler()->addErrorMessage("Please complete missing information");
			return false;
		}
	}

	/**
	* update item description in a given language for a given item
	*
	* @param int $item_id Item id
	* @return bool true on success otherwise false
	*/
	function updatePrices() {
		global $id;

		$query = new Query();

		// delete all prices for this item
		$query->sql("DELETE FROM ".UT_ITE_PRI." WHERE item_id = $id");

		foreach($this->vars['prices'] as $country => $prices) {
			foreach($prices as $price_group_uid => $price) {

				// insert price
				if($price) {
					$query->sql("INSERT INTO ".UT_ITE_PRI." values(DEFAULT, '$price', '$price_group_uid', $id, '$country')");
				}
			}
		}

		messageHandler()->addStatusMessage("Item description updated");
		return true;

	}






}

?>