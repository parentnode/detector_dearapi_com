<?php
/**
* @package framework
*/

/**
* typeHtml
*
* UT_ITT_HTM
*
*/
class TypeHtmlCore extends ItemCore {
	
//	public $limits;
	public $itemtype;
//	public $item_name;
//	public $items_name;

	/**
	* Default settings
	*/
	function __construct() {
		$this->addTranslation(__FILE__);

		$this->db = UT_ITT_HTM;

		$this->itemtype = "html";

		$this->type_name = $this->translate("HTML");
		$this->types_name = $this->translate("HTML");
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
		$query->sql("SELECT html, language_id FROM ".UT_ITT_HTM." WHERE item_id = ".$id);
		$item->item["name"][$key] = str_replace("&quot;", '"', cutString($query->getQueryResult(0, "html"), 50));
		$item->item["html"][$key] = str_replace("&quot;", '"', $query->getQueryResult(0, "html"));
		$item->item["language_id"][$key] = $query->getQueryResult(0, "language_id");
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
		$query->sql("SELECT html FROM ".UT_ITT_HTM." WHERE item_id = ".$id);
		$name = str_replace("&quot;", '"', cutString($query->getQueryResult(0, "html"), 50));
		$item->item["name"][$key] = $name;
		return $name;
	}

	/**
	* Core Save, based on parameter values
	*
	* @param Integer item id
	* @param Array content array
	* @return bool
	*/
	function save($item_id, $content) {
		$query = new Query();
		$query->dbExistsElseCreate($this->db);

		$vars = "''";
		$vars .= ", '".$content["html"]."'";
		$vars .= ", '$item_id'";
		$vars .= ", '".$content["language_id"]."'";

		if($this->sql("INSERT INTO ".$this->db." VALUES($vars)")) {
			Item::addTag($item_id, "type:" . $this->itemtype);
			return $item_id;
		}

		return false;
	}

}

?>