<?php
/**
* @package framework
*/
include_once("class/system/image_tools.class.php");

/**
* typeBundleCore
*
* UT_ITT_BUN
* UT_ITT_BUN_ITE
*
*/
class TypeBundleCore extends ItemCore {
	
	public $itemtype;

	/**
	* Default settings
	*/
	function __construct() {
		$this->addTranslation(__FILE__);

		//$this->db = UT_ITT_BUN;
		//$this->db_rel = UT_ITT_BUN_PRO;

		$this->itemtype = "bundle";

		$this->type_name = $this->translate("Bundle");
		$this->types_name = $this->translate("Bundles");
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
		$query->sql("SELECT name FROM ".UT_ITT_BUN." WHERE item_id = ".$id);
		$item->item["name"][$key] = $query->getQueryResult(0, "name");

		if($query->sql("SELECT value_pct, item_id FROM ".UT_ITT_BUN_ITE." WHERE bundle_item_id = ".$id)) {

			$bundle_item = new Item();

			for($i = 0; $i < $query->getQueryCount(); $i++) {
				$item_id = $query->getQueryResult($i, "item_id");

				$bundle_item->getItem($item_id);
				$item->item["items"][$key][$i] = $bundle_item->item;
				$item->item["items"][$key][$i]["value_pct"][0] = $query->getQueryResult($i, "value_pct");
				$item->item["items"][$key][$i]["name"][0] = $bundle_item->getTypeName();

//				$item->item["items"][$key]["item_id"][] = $item_id;

//				$item->item["items"][$key]["itemtype"][] = $new_item->item[]
			}
		}
		else {
			$item->item["items"][$key] = false;
		}
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
		$query->sql("SELECT name FROM ".UT_ITT_BUN." WHERE item_id = ".$id);
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

		$query->dbExistsElseCreate(UT_ITT_BUN);
		$query->dbExistsElseCreate(UT_ITT_BUN_ITE);

		$filepath = $filepath ? $filepath : WWW_PATH."/img/defaults/bundle.jpg";

		if($this->updateFiles($item_id, $filepath)) {

			$vars = "DEFAULT";
			$vars .= ", '$name'";
			$vars .= ", ''"; // classname
			$vars .= ", '$item_id'";

			if($query->sql("INSERT INTO ".UT_ITT_BUN." VALUES($vars)")) {
				Item::addTag($item_id, "type:" . $this->itemtype);
				return $item_id;
			}
		}

		return false;
	}

}

?>