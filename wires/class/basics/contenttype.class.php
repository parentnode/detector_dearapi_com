<?php
/**
* @package global.basics
*/
/**
* includes
*/
include_once("class/basics/contenttype.core.class.php");
include_once("class/system/validator.class.php");


/**
* This file contains contenttype maintenance functionality.
* Contenttype, extends Contentype views
*/
class Contenttype extends ContenttypeCore {

	public $varnames;
	public $vars;
	private $validator;

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {
		$this->addTranslation(__FILE__);
		
		$this->validator = new Validator($this);
		parent::__construct();

		$this->varnames["name"] = $this->translate("Contenttype name");
		$this->validator->rule("name", "txt");
		$this->varnames["contenttype"] = $this->translate("Contenttype/mimetype");

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
			$vars = "''";
			$vars .= ",'".$this->vars['name']."'";
			$vars .= ",'".$this->vars['contenttype']."'";

			if($this->sql("INSERT INTO ".$this->db." VALUES($vars)")) {
				messageHandler()->addStatusMessage($this->translate("Item saved"));
				return $this->getLastInsertId();
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
	* Update edited item
	*
	* @param int $id Item id
	* @return bool
	* @uses Message
	*/
	function updateItem($id) {
		if($this->validator->validateAll()) {
			$vars = "name= '".$this->vars['name']."'";
			$vars .= ", contenttype = '".$this->vars['contenttype']."'";

			if($this->sql("UPDATE ".$this->db." SET $vars WHERE id = '$id'")) {
				messageHandler()->addStatusMessage($this->translate("Item updated"));
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

	/**
	* Delete selected item
	*
	* @param int $id Item id
	* @return bool
	* @uses Message
	*/
	function deleteItem($id) {
		if($this->sql("DELETE FROM ".$this->db." WHERE id = $id")) {
			messageHandler()->addStatusMessage($this->translate("Item deleted"));
			return true;
		}
		else {
			messageHandler()->addErrorMessage($this->dbError());
			return false;
		}
	}

}
?>