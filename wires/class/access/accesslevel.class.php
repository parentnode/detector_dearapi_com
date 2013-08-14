<?php
/**
* This file contains access level functionality
* @package System
*/

/**
* Include view and additional classes
*/
include_once("accesslevel.view.class.php");
include_once("accesspoint.class.php");
include_once("class/system/generic.class.php");
include_once("class/system/validator.class.php");

/**
* Accesslevel, extends accesslevel views
* @package System
*/
class Accesslevel extends AccesslevelView {

	public $varnames;
	public $vars;
	private $validator;

	/**
	* init, set varnames, validation rules
	*/
	function __construct() {
		// initiate helpers before calling View construct
		$this->addTranslation(__FILE__);
		$this->validator = new Validator($this);
		parent::__construct();
		$this->db = UT_ACC_LEV;

		$this->varnames["name"] = $this->translate("Name");
		$this->validator->rule("name", "unik", false, $this->db);

		$this->varnames["notes"] = $this->translate("Notes");

		$this->varnames["point_id"] = "";

		$this->varnames["access"] = "";
		$this->validator->rule("access", "arr");

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
	function getItems($which=false) {
		return Generic::getItems($this->db, $which);
	}

	/**
	* Get access settings for item with id
	*
	* @param Integer $id Id og item to get access settings for
	* @return array|false Item array or false on error
	*/
	function getItemAccess($id, $point_id) {
		$items = array();
		if($this->sql("SELECT action FROM ".UT_ACC_LEV_POI." WHERE level_id = $id AND point_id = ".$point_id)) {
			for($i = 0; $i < $this->getQueryCount(); $i++) {
				$items[] = $this->getQueryResult($i, "action");
			}
		}
		return $items;
	}

	/**
	* Check if we can find access point file based on point id
	* 
	* @param Integer $id Point Id
	* @return string File name
	*/
	function getAccessPoint($id) {
		$accesspointClass = new Accesspoint();
		return $accesspointClass->getItemFile($id);
	}

	/**
	* Get point name for point item (point_id is passed through $vars)
	*
	* @return array|false Item array or false on error
	*/
	function getPointName($id) {
		if($this->sql("SELECT name, file FROM ".UT_ACC_POI." WHERE id = ".$id)) {
			return $this->getQueryResult(0, "name") ? $this->getQueryResult(0, "name") : preg_replace('/\/www|\/projects/', "", $this->getQueryResult(0, "file"));
		}
		else {
			return false;
		}
	}

	/**
	* Save new item, based on submitted values
	*
	* @return bool
	* @uses Message
	*/
	function saveItem() {
		if($this->validator->validateAll("access")) {
			$vars = "DEFAULT";
			$vars .= ",'".$this->vars['name']."'";
			$vars .= ",'".$this->vars['notes']."'";

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
		if($this->validator->validateAll("access")) {
			$vars = "name='".$this->vars['name']."'";
			$vars .= ",notes='".$this->vars['notes']."'";

			if($this->sql("UPDATE ".$this->db." SET $vars WHERE id = $id")) {
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
	*/
	function deleteItem($id) {
		if(!$this->checkUsage($id)) {
			if($this->sql("DELETE FROM ".$this->db." WHERE id = $id")) {
				messageHandler()->addStatusMessage($this->translate("Item deleted"));
				return true;
			}
			else {
				messageHandler()->addErrorMessage($this->dbError());
				return false;
			}
		}
		else {
			messageHandler()->addErrorMessage($this->translate("Item is currently being used"));
		}
	}

	/**
	* Update access for item
	*
	* @param int $id Item id
	* @param int $point_id Point item id
	* @return bool
	* @uses Message
	*/
	function updateItemAccess($level_id, $point_id) {
		$this->deleteItemAccess($level_id, $point_id);
		$inserts = 0;

		foreach($this->vars['access'] as $action => $value) {
			$vars = "DEFAULT";
			$vars .= ",".$level_id;
			$vars .= ",".$point_id;
			$vars .= ",'".$action."'";
			if($this->sql("INSERT INTO ".UT_ACC_LEV_POI." VALUES($vars)")) {
				$inserts++;
			}
		}
		if(!$this->vars['access'] || $inserts == count($this->vars['access'])) {
			messageHandler()->addStatusMessage($this->translate("Item access updated"));
			return true;
		}
		else {
			messageHandler()->addErrorMessage($this->dbError());
			return false;
		}
	}

	/**
	* Delete existing access points for access level id and point_id (combined with updating)
	*
	* @param Integer $id Id of item to delete access items for
	* @param Integer $point_id Id of point item to delete access items for
	*/
	function deleteItemAccess($id, $point_id) {
		$this->sql("DELETE FROM ".UT_ACC_LEV_POI." WHERE level_id = $id AND point_id = $point_id");
	}

	/**
	* Check if the given item is being used
	* (if user is associated with level)
	*
	* @param Integer $id id
	* @return bool True if level is used by user, false if not
	*/
	function checkUsage($id) {
		if($this->sql("SELECT id FROM ".UT_USE." WHERE access_level_id = $id")) {
			return true;
		}
		else {
			return false;
		}
	}

}

?>