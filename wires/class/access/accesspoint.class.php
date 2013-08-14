<?php
/**
* This file contains access point functionality
* @package System
*/

/**
* Include view and additional classes
*/
include_once("accesspoint.view.class.php");
include_once("class/system/generic.class.php");
include_once("class/system/validator.class.php");
include_once("class/system/filesystem.class.php");

/**
* Accesspoint, extends accesspoint views
* @package System
*/
class Accesspoint extends AccesspointView {

	public $varnames;
	public $vars;
	private $validator;

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {
		// initiate helpers before calling View construct
		$this->addTranslation(__FILE__);
		$this->validator = new Validator($this);
		parent::__construct();
		$this->db = UT_ACC_POI;

		$this->varnames["name"] = $this->translate("Name");
		$this->validator->rule("name", "txt");

		$this->vars = getVars($this->varnames);
	}

	/**
	* Updates available points (Automatic procedure)
	*/
	function updatePoints() {
		$points = array();
		$exclude_dirs = array("css", "js", "img", "error", "temp", "config", "include", "class", "library", "templates", "reset", "cron", "docs", "wurfl");
		$extensions = array(".php");

		// Verify existing points
		$this->sql("SELECT id, file FROM ".$this->db);
		for($i = 0; $i < $this->getQueryCount(); $i++) {
			$point = $this->getQueryResult($i, "file");
//			print matchKnownPaths($point).":".$point."<br>";
			if(!file_exists($point) || !matchKnownPaths($point) || array_search($point, $points) !== false || !FileSystem::validFolder($point, $exclude_dirs, $extensions)) {
				$this->deleteItem($this->getQueryResult($i, "id"));
			}
			$points[] = $point;
		}

		$global_path = FileSystem::folderIterator(GLOBAL_PATH, "", $exclude_dirs, $extensions);
		$framework_path = FileSystem::folderIterator(FRAMEWORK_PATH, "", $exclude_dirs, $extensions);
		$files = array_merge_recursive($global_path, $framework_path);
		foreach($files as $value) {
			if(array_search($value, $points) === false) {
				$this->addItem($value);
			}
		}
	}

	/**
	* Add new point to accesspoints
	* Only used internally
	*
	* @param string $file Point based file
	* @param integer $type Type of point (Container = 0, Controller = 1, View template = 2) (default = 0)
	* @param integer $relation Point relation (default = 0, rootlevel)
	* @return integer Id of new accesspoint
	*/
	function addItem($point) {
		$query = new Query();
		$query->sql("INSERT INTO ".$this->db." values(DEFAULT, '', '$point')");
		return $query->getLastInsertId();
	}

	/**
	* Delete selected item from points and level_points
	* Only used internally
	*
	* @param int $id Item id
	*/
	function deleteItem($id) {
		$query = new Query();
		$query->sql("DELETE FROM ".$this->db." WHERE id = $id");
		$query->sql("DELETE FROM ".UT_ACC_LEV_POI." WHERE point_id = $id");
	}

	/**
	* Get id for item
	* Used for relations
	*
	* @param String $point Point to find id for
	*/
	function getItemId($point) {
		if($this->sql("SELECT id FROM ".$this->db." WHERE file = '$point'")) {
			return $this->getQueryResult(0, "id");
		}
		else {
			return 0;
		}
	}

	/**
	* Get file
	*
	* @param Integer $id Id to find file for
	*/
	function getItemFile($id) {
		if($this->sql("SELECT file FROM ".$this->db." WHERE id = $id")) {
			return $this->getQueryResult(0, "file");
		}
		else {
			return false;
		}
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
	*/
	function getItems($which=false) {
		$this->updatePoints();

		$items = array();
		$dirs = new Query();

		$dirs->sql("SELECT id, name, file FROM ".$this->db." ORDER BY file");

		for($i = 0; $i < $dirs->getQueryCount(); $i++) {
			$items["id"][] = $dirs->getQueryResult($i, "id");
			$items["values"][] = $dirs->getQueryResult($i, "name") ? $dirs->getQueryResult($i, "name") : preg_replace('/\/www|\/projects/', "", $dirs->getQueryResult($i, "file"));
			$items["file"][] = $dirs->getQueryResult($i, "file");
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
	* Update edited item
	*
	* @param int $id Item id
	* @return bool
	* @uses Message
	*/
	function updateItem($id) {
		if($this->validator->validateAll()){
			$vars = "name='".$this->vars['name']."'";

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

}

?>