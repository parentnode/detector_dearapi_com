<?php
/**
* @package framework
*/

/**
* typeLog
*
* UT_ITT_LOG
*
*/
class TypeLogCore extends ItemCore {
	
	public $itemtype;

	/**
	* Default settings
	*/
	function __construct() {
		$this->addTranslation(__FILE__);

		$this->db = UT_ITT_LOG;

		$this->itemtype = "log";

		$this->type_name = $this->translate("Log");
		$this->types_name = $this->translate("Logs");
	}

	/**
	* Get type specific info of item with $item_id
	* Fills out values in Item->item for item with Item->item["id"] == $item_id
	*
	* @param integer $item_id Item id
	*/
	function getTypeItem($item_id) {
		global $page;
		
		$item = $page->getObject("Item");
		$key = array_search($item_id, $item->item["id"]);

		$query = new Query();
		$query->sql("SELECT name, html, latitude, longitude, UNIX_TIMESTAMP(timestamp) as timestamp, language_id FROM ".$this->db." WHERE item_id = ".$item_id);
		$item->item["name"][$key] = $query->getQueryResult(0, "name");
		$item->item["html"][$key] = str_replace("&quot;", '"', $query->getQueryResult(0, "html"));

		$item->item["latitude"][$key] = $query->getQueryResult(0, "latitude");
		$item->item["longitude"][$key] = $query->getQueryResult(0, "longitude");

		$item->item["timestamp"][$key] = date("d-m-Y H:i", $query->getQueryResult(0, "timestamp"));
		$item->item["language_id"][$key] = $query->getQueryResult(0, "language_id");
	}

	/**
	* Get type specific name of item with $item_id
	* Returns string value with whatever appropriate
	*
	* @param integer $item_id Item id
	*/
	function getTypeName($item_id) {
		global $page;

		$item = $page->getObject("Item");

		$query = new Query();
		$query->sql("SELECT name FROM ".$this->db." WHERE item_id = ".$item_id);
		$name = $query->getQueryResult(0, "name");

		// is the item in current item-collection
		$key = array_search($item_id, $item->item["id"]);
		if($key !== false) {
			$item->item["name"][$key] = $name;
		}

		return $name;
	}

	/**
	* Core Save, based on parameter values
	*
	* @param Integer $item_id Item id
	* @param Array $content array
	* @return bool
	*/
	function save($item_id, $content) {
		$query = new Query();
		$query->dbExistsElseCreate($this->db);

		// default HTML wrapper if no HTML is used
		$html = $content["html"];
		if($html == strip_tags($html)) {
			$html = "<p>$html</p>";
		}
		$name = stringOr($content['name'], cutString(strip_tags($html), 50));

		$vars = "''";
		$vars .= ", '".$name."'";
		$vars .= ", '".$html."'";

		// latitude/longitude definition with minutes
		if($content["latitude_minutes"]) {
			list($degrees) = explode(".", $content["latitude"]);
			$content["latitude"] = $degrees + ($content["latitude_minutes"]/60);
		}
		if($content["longitude_minutes"]) {
			list($degrees) = explode(".", $content["longitude"]);
			$content["longitude"] = $degrees + ($content["longitude_minutes"]/60);
		}

		$vars .= ", '".$content["latitude"]."'";
		$vars .= ", '".$content["longitude"]."'";

		$vars .= ", '".mTimestamp($content["timestamp"])."'";

		$vars .= ", '$item_id'";
		$vars .= ", '".$content["language_id"]."'";

//		print "INSERT INTO ".$this->db." VALUES($vars)";

		if($this->sql("INSERT INTO ".$this->db." VALUES($vars)")) {
			Item::addTag($item_id, "type:" . $this->itemtype);
			return $item_id;
		}

		return false;
	}

}

?>