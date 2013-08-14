<?php
/**
* @package framework
*/
include_once("class/system/image_tools.class.php");

/**
* typePhotoCore
*
* UT_ITT_PHO
*
*/
class TypePhotoCore extends ItemCore {
	
//	public $limits;
	public $itemtype;
//	public $item_name;
//	public $items_name;

	/**
	* Default settings
	*/
	function __construct() {
		$this->addTranslation(__FILE__);

		$this->db = UT_ITT_PHO;

//		$this->image_path = LOCAL_PATH."/library/public/";
//		$this->backup_path = LOCAL_PATH."/library/private/";

		$this->itemtype = "photo";

		$this->type_name = $this->translate("Photo");
		$this->types_name = $this->translate("Photos");
	}

	/**
	* Get type specific info of item with $id
	* Fills out values in Item->item for item with Item->item["id"] == $id
	*
	* @param integer $id Item id
	*/
	function getTypeItem($id) {
		global $page;
		
		$item = $page->getObject("Item");
		$key = array_search($id, $item->item["id"]);

		$query = new Query();
		$query->sql("SELECT name FROM ".UT_ITT_PHO." WHERE item_id = ".$id);
		$item->item["name"][$key] = $query->getQueryResult(0, "name");
	}

	/**
	* Get type specific name of item with $id
	* Returns string value with whatever appropriate
	*
	* @param integer $id Item id
	*/
	function getTypeName($id) {
		global $page;

		$item = $page->getObject("Item");
		$key = array_search($id, $item->item["id"]);

		$query = new Query();
		$query->sql("SELECT name FROM ".UT_ITT_PHO." WHERE item_id = ".$id);
		$name = $query->getQueryResult(0, "name");
		$item->item["name"][$key] = $name;
		return $name;
	}

	/**
	* Core Save, based on parameter values
	*
	* @param Integer item id
	* @param String Filepath
	* @return bool
	*/
	function save($item_id, $filepath, $name = false) {
		$query = new Query();
		$query->dbExistsElseCreate($this->db);

		if($this->updateFiles($item_id, $filepath)) {

			$vars = "DEFAULT";
			$vars .= ", '$name'";
			$vars .= ", '$item_id'";

			if($query->sql("INSERT INTO ".$this->db." VALUES($vars)")) {
				Item::addTag($item_id, "type:" . $this->itemtype);
				return $item_id;
			}
			else {
				// delete files
				$this->updateFiles($item_id, false);
				return false;
			}
		}
		else {
			return false;
		}

	}

}

?>