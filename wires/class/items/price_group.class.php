<?php
/**
* @package framework.items
*/
/**
* This file contains tags maintenance functionality
* TAG, extends tag views
*/

include_once("price_group.view.class.php");
include_once("class/system/generic.class.php");
include_once("class/system/validator.class.php");


/**
* @package regional
*/
class PriceGroup extends PriceGroupView {

	public $varnames;
	public $vars;
	private $validator;

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {
		$this->addTranslation(__FILE__);

		$this->db = UT_PRI;

		$this->validator = new Validator($this);

		$this->varnames["name"] = "Name:";
		$this->validator->rule("name", "txt");

		$this->varnames["uid"] = "UID:";
		$this->validator->rule("uid", "txt");

		$this->vars = getVars($this->varnames);
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
		return Generic::getItemName($id, $this->db);
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
		$this->sql("SELECT id, name, uid FROM ".$this->db. " ORDER BY uid");
		
		for($i = 0; $i < $this->getQueryCount(); $i++) {
			$items["id"][$i] = $this->getQueryResult($i, "id");
			$items["values"][$i] = $this->getQueryResult($i, "name");
			$items["uid"][$i] = $this->getQueryResult($i, "uid");
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
	* Save new item, based on submitted values
	*
	* @return bool
	* @uses Message
	*/
	function saveItem() {
		if($this->validator->validateAll()) {
			$vars = "DEFAULT";
			$vars .= ",'".$this->vars['name']."'";
			$vars .= ",'".$this->vars['uid']."'";
			
			if($this->sql("INSERT INTO ".$this->db." VALUES($vars)")) {
				messageHandler()->addStatusMessage("Item saved");
				return $this->getLastInsertId();
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
	* Update edited item
	*
	* @param int $id Item id
	* @return bool
	* @uses Message
	*/
	function updateItem($id) {
		if($this->validator->validateAll()){
			$vars = "name= '".$this->vars['name']."'";
			$vars .= ",uid= '".$this->vars['uid']."'";

			if($this->sql("UPDATE ".$this->db." SET $vars WHERE id = $id")) {
				messageHandler()->addStatusMessage("Item updated");
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
	* Delete selected item
	*
	* @param int $id Item id
	* @return bool
	* @uses Message
	*/
	function deleteItem($id) {
		if($this->sql("DELETE FROM ".$this->db." WHERE id = $id")){ 
			messageHandler()->addStatusMessage("Item deleted");
			return true;
		}
		else {
			messageHandler()->addErrorMessage($this->dbError());
			return false;
		}
	}
	
	/**
	* Checking usage of selected item
	*
	* We should allways be able to delete a tag, return false
	*
	* @param int $id Item id
	* @return bool
	*/
	function checkUsage($id) {
		return false;
	}
}
?>