<?php
/**
* @package framework
*/

/**
* typeBlog
*
* UT_ITT_BLO
*
*/
//class TypeBlogCore extends Translation {
class TypeBlogCore extends ItemCore {
	
//	public $limits;
	public $itemtype;
//	public $type_name;
//	public $types_name;

	/**
	* Default settings
	*/
	function __construct() {
		$this->addTranslation(__FILE__);

		$this->db = UT_ITT_BLO;

		$this->itemtype = "blog";

		$this->type_name = $this->translate("Blog");
		$this->types_name = $this->translate("Blogs");
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
		$query->sql("SELECT name, html, UNIX_TIMESTAMP(timestamp) as timestamp, language_id FROM ".UT_ITT_BLO." WHERE item_id = ".$id);
		$item->item["name"][$key] = $query->getQueryResult(0, "name");
		$item->item["html"][$key] = $query->getQueryResult(0, "html");
		$item->item["timestamp"][$key] = date("d-m-Y H:i", $query->getQueryResult(0, "timestamp"));
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
		$query->sql("SELECT name FROM ".UT_ITT_BLO." WHERE item_id = ".$id);
		$name = $query->getQueryResult(0, "name");
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

		$html = $content["html"];
		// default p-wrapper if no HTML
		if($html == strip_tags($html)) {
			$html = "<p>$html</p>";
		}

		$name = stringOr($content['name'], cutString($content["html"], 50));

		$vars = "DEFAULT";
		$vars .= ", '$name'";
		$vars .= ", '".$html."'";
		$vars .= ", '".mTimestamp($content["timestamp"])."'";
		$vars .= ", '$item_id'";
		$vars .= ", '".$content["language_id"]."'";
		
		if($query->sql("INSERT INTO ".$this->db." VALUES($vars)")) {
			Item::addTag($item_id, "type:" . $this->itemtype);
			return $item_id;
		}

		return false;
	}

}

?>