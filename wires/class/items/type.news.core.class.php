<?php
/**
* @package framework
*/

/**
* typeBlog
*
* UT_ITT_NEW
*
*/
class TypeNewsCore extends ItemCore {
	
//	public $limits;
	public $itemtype;
//	public $item_name;
//	public $items_name;

	/**
	* Default settings
	*/
	function __construct() {
		$this->addTranslation(__FILE__);

		$this->db = UT_ITT_NEW;

		$this->itemtype = "news";

		$this->type_name = $this->translate("News");
		$this->types_name = $this->translate("News");
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
		$query->sql("SELECT name, teaser, text, UNIX_TIMESTAMP(timestamp) as timestamp, language_id FROM ".UT_ITT_NEW." WHERE item_id = ".$id);
		$item->item["name"][$key] = $query->getQueryResult(0, "name");
		$item->item["teaser"][$key] = $query->getQueryResult(0, "teaser");
		$item->item["text"][$key] = str_replace('&quot;', '"', $query->getQueryResult(0, "text"));
		$item->item["timestamp"][$key] = date("d-m-Y H:i", $query->getQueryResult(0, "timestamp"));
//		$item->item["release"][$key] = date("d-m-Y H:i", $query->getQueryResult(0, "release"));
//		$item->item["expire"][$key] = date("d-m-Y H:i", $query->getQueryResult(0, "expire"));
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
		$query->sql("SELECT name FROM ".UT_ITT_NEW." WHERE item_id = ".$id);
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

		$name = stringOr($content['name'], cutString($content["text"], 50));
		$teaser = stringOr($content['teaser'], cutString($content["text"], 100));

		$vars = "DEFAULT";
		$vars .= ", '$name'";
		$vars .= ", '$teaser'";
		$vars .= ", '".$content["text"]."'";
		$vars .= ", '".mTimestamp($content["timestamp"])."'";
//		$vars .= ", '".mTimestamp($content["release"])."'";
//		$vars .= ", '".($content["expire"] ? mTimestamp($content["expire"]) : "")."'";
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