<?php
/**
* @package global.basics
* This file contains language maintenance functionality
*/
include_once("language.view.class.php");
include_once("class/system/generic.class.php");
include_once("class/system/validator.class.php");

/**
* Language
*/
class Language extends LanguageView {

	public $varnames;
	public $vars;
	private $validator;

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {
		// initiate helpers before calling View construct
//		$this->translater = new Translation(__FILE__);
		$this->addTranslation(__FILE__);

		$this->validator = new Validator($this);
		parent::__construct();

		$this->file = FRAMEWORK_PATH."/library/basics/languages.xml";

		$this->db = UT_BAS_LAN;

		$this->varnames["name"] = $this->translate("Language");

		$this->varnames["id"] = $this->translate("ISO-2");
		$this->validator->rule("id", "txt", false, 2, 2);
		$this->validator->rule("id", "unik", $this->translate("Langauge exists!"), $this->db);

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
	* @param string $which Optional limitation of returned result. ("id", "values")
	* @return array|false Item array or false on error
	* @uses Generic::getItems()
	*/
	function getItems($which=false, $order="name") {
		return Generic::getItems($this->db, $which, $order);
	}

	/**
	* Save new item, based on submitted values
	*
	* @return bool
	* @uses Message
	*/
	function saveItem($id = false) {
		if($this->validator->validateAll()) {
			if(!$id) {
				global $id;
			}

			$languages = $this->getAllItems();
			$name = $languages["values"][array_search($id, $languages["id"])];

			$vars = "'$id'";
			$vars .= ",'$name'";

			if($this->sql("INSERT INTO ".$this->db." VALUES($vars)")) {
				messageHandler()->addStatusMessage($this->translate("Item ###$name### saved"));
				return $id;
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
		if($this->sql("DELETE FROM ".$this->db." WHERE id = '$id'")) {
			messageHandler()->addStatusMessage($this->translate("Item deleted"));
			return true;
		}
		else {
			messageHandler()->addErrorMessage($this->dbError());
			return false;
		}
	}

	function checkUsage($id) {
		$return = false;
	}

	/**
	* Returns a array of countries, used when customer is to select home country (we can not be certain if we have the country in that region) 
	* @return array Countries 
	*/
	function getAllItems() {
		$xml = simplexml_load_file($this->file);
		$allItem = array();

		foreach($xml->language as $key => $value) {
			$allItems["id"][] = $value->iso;
			$allItems["values"][] = $value->name;
		}
		return $allItems;
	}

}

?>