<?php
/**
* @package global.basics
*/
/**
*
*/
include_once("itemtype.core.class.php");
include_once("class/system/validator.class.php");


/**
* This file contains itemtype maintenance functionality.
* Itemtype, extends Itemtype views
*
*/
class Itemtype extends ItemtypeCore {

	//public $mimetypes;
	public $varnames;
	public $vars;
	private $validator;

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {
//		this->translate
		$this->addTranslation(__FILE__);
		parent::__construct();

		$this->validator = new Validator($this);
		$this->varnames["name"] = $this->translate("Name");
		$this->validator->rule("name", "unik", $this->db);
		
		$this->vars = getVars($this->varnames);
	}


	/**
	* Save new item, based on submitted values
	*
	* @return bool
	* @uses Message
	*/
	function saveItem() {
		if($this->validator->validateAll()) {
			$contenttypes = $this->vars["contenttype"];

			$vars = "DEFAULT";
			$vars .= ",'".$this->vars['name']."'";

			if($this->sql("INSERT INTO ".$this->db." VALUES($vars)")) {
				$item_id = $this->getLastInsertId();
				$this->addTypeTag($this->vars['name']);
				return $item_id;
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

	function addTypeTag($itemtype) {
		$tag = "type:".$itemtype;

		$query = new Query();
		if(!$query->sql("SELECT id FROM ".UT_TAG." WHERE name = '$tag'")) {
			$query->sql("INSERT INTO ".UT_TAG." VALUES(DEFAULT, '$tag')");
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
		if($this->validator->validateList("name")) {

			$query = new Query();
			$query->sql("SELECT name FROM ".$this->db." WHERE id = '$id'");
			$old_name = $query->getQueryResult("name", 0);

			$vars = "name='".$this->vars['name']."'";
			if($this->sql("UPDATE ".$this->db." SET $vars WHERE id = $id")) {
				messageHandler()->addStatusMessage($this->translate("Item updated"));
				$this->updateTypeTag($this->vars['name'], $old_name);
				return true;
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

	function updateTypeTag($new_itemtype, $old_itemtype) {
		$old_tag = "type:".$old_itemtype;
		$new_tag = "type:".$new_itemtype;

		$query = new Query();
		if($query->sql("SELECT id FROM ".UT_TAG." WHERE name = '$old_tag'")) {
			$id = $query->getQueryResult("id", 0);
			$query->sql("UPDATE ".UT_TAG." SET name = '$new_tag' WHERE id = $id");
		}
		else {
			$query->sql("INSERT INTO ".UT_TAG." VALUES(DEFAULT, '$new_tag')");
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

		$query = new Query();
		$query->sql("SELECT name FROM ".$this->db." WHERE id = $id");
		$name = $query->getQueryResult("name", 0);

		if($this->sql("DELETE FROM ".$this->db." WHERE id = $id", true)) {
			messageHandler()->addStatusMessage($this->translate("Item deleted"));
			$query->sql("DELETE FROM ".UT_TAG." WHERE name = 'type:$name'");
			return true;
		} 
		else {
			messageHandler()->addErrorMessage($this->dbError());
			return false;
		}
	}

}
?>