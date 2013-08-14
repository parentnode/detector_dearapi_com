<?php
/**
* @package wires
*/
/**
*
*/

/**
* includes
*/

//include_once("class/basics/itemtype.core.class.php");

/**
* This class holds Item functionallity.
*
*/
class ItemCore extends Translation {

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {
		// initiate helpers before calling View construct
//		this->translate
		$this->addTranslation(__FILE__);


		// initialize related classes
//		$this->itemtypeClass = new ItemtypeCore();
//		$this->itemtypes = $this->itemtypeClass->getItems();

//		$this->country_id = Session::getCountryIso();
//		$this->language_id = Session::getLanguageIso();
//		$this->site_id = Session::getSiteId();
//		$this->country_id = Session::getCountryId();
//		$this->language_id = Session::getLanguageId();
		$this->itemtypes = Page::getItems(UT_BAS_ITT);

		$this->itemtype = false;
		$this->itemtype_id = false;
		
		$this->db = UT_ITE;

	}
	
	/**
	* Set itemtype
	* Sets both this->itemtype and this->itemtype_id
	*
	* @param $index Itemtype or itemtype_id (both will work)
	* @param $update Optional parameter to indicate resetting of itemtype (not part of an iteration, thus it cannot result in mixed type)
	*/
	function setItemtype($index, $update = false) {

		// no index
		if(!$index) {
			$this->itemtype = false;
			$this->itemtype_id = false;
		}
		// multiple itemtypes
		else if(!$update && $this->itemtype && $this->itemtype_id != $index && $this->itemtype != $index) {
			$this->itemtype = "mixed";
			$this->itemtype_id = false;
		}
		// numeric index
		else if(is_numeric($index)) {
			$this->itemtype = $this->itemtypes["values"][array_search($index, $this->itemtypes["id"])];
			$this->itemtype_id = $index;
		}
		// itemname index
		else {
			$this->itemtype = $index;
			$this->itemtype_id = $this->itemtypes["id"][array_search($index, $this->itemtypes["values"])];
		}

	}

	/**
	* Get matching type object
	* Object type based on $this->itemtype (sat via getItems)
	*
	* @return return instance of type object
	*/
	function getTypeObject($itemtype=false) {

		$itemtype = $itemtype ? $itemtype : $this->itemtype;

		// include generic type (for mixed itemtypes)
		if($itemtype == "mixed" || !$itemtype) {
			$itemtype = "mixed";
			$class = "TypeMixed";
		}
		else {
			$class = "Type".ucfirst($itemtype);
		}

		if(!isset($this->itemtypes["class"][$itemtype])) {
			include_once("class/items/type.$itemtype.class.php");
			$this->itemtypes["class"][$itemtype] = new $class();

		}
		return $this->itemtypes["class"][$itemtype];
	}

	/**
	* Do we have item(s)
	*/
	function item() {
		return isset($this->item["id"]) && $this->item["id"] ? true : false;
	}

	/**
	* Global getItem
	* Only gets minimum set of data
	*
	* @param $id Item id to get
	*/
	function getItem($id) {

		$query = new Query();
		$this->item["id"] = array();

		if($query->sql("SELECT id, itemtype_id, user_id, status, sindex FROM ".UT_ITE." WHERE sindex = '$id' OR id = '$id'")) {

			$itemtype = $this->itemtypes["values"][array_search($query->getQueryResult(0, "itemtype_id"), $this->itemtypes["id"])];
			$item_sindex = $query->getQueryResult(0, "sindex");

			$this->item["id"][0] = $query->getQueryResult(0, "id");
			// create sindex value if it doesn't exist (backwards compatibility)
			$this->item["sindex"][0] = $item_sindex ? $item_sindex : $this->sindex($id, $this->getTypeObject($itemtype)->getTypeName($id));
//			$this->item["sindex"][0] = $query->getQueryResult(0, "sindex");

			$this->item["status"][0] = $query->getQueryResult(0, "status");
			$this->item["status_text"][0] = $query->getQueryResult(0, "status") ? $this->translate("enabled") : $this->translate("disabled");

			$this->item["itemtype_id"][0] = $query->getQueryResult(0, "itemtype_id");
			$this->item["itemtype"][0] = $itemtype;

			$this->item["user_id"][0] = $query->getQueryResult(0, "user_id");
                                                                                                                                                                                                              
			$this->setItemtype($this->item["itemtype_id"][0], true);
			return true;
		}
		return false;
	}

	/*
	function getItemId($sindex) {
		$query = new Query();
		if($query->sql("SELECT id FROM ".UT_ITE." WHERE sindex = '$sindex'")) {
			return $query->getQueryResult(0, "id");
		}
		return false;
	}
	*/

	/**
	* Get all matching items
	*
	* @param String $itemtype Item type name
	* @param String $order 
	* @param String $sindex Optional navigation index - s(earch)index
	*
	* @return Array [id][] + [itemtype][]
	*/
	function getItems($itemtype=false, $status=false, $tags=false, $sindex=false, $order=false, $limit=false) {

		$this->item = array();

		//if itemtype get itemtype_id
		if($itemtype) {
			$this->setItemtype($itemtype, true);
		}
		else {
			$this->setItemtype(false);
		}


		$query = new Query();

		$SELECT = array();
		$FROM = array();
		$WHERE = array();
		$GROUP_BY = "";
		$ORDER = array();


		$SELECT[] = "items.id";
		$SELECT[] = "items.sindex";
		$SELECT[] = "items.status";
		$SELECT[] = "items.itemtype_id";

		if($sindex) {
			$SELECT[] = UT_NAV_ITE.".sequence";
			$FROM[] = UT_ITE." as items LEFT JOIN ".UT_NAV_ITE." ON items.id = ".UT_NAV_ITE.".item_id  AND ".UT_NAV_ITE.".sindex = '$sindex'";
			$ORDER[] = UT_NAV_ITE.".sequence";

			$tags .= ",".$this->getNavTags($sindex);
		}
		else {
			$FROM[] = UT_ITE." as items";
		}

		if($status) {
			$WHERE[] = "items.status = $status";
		}
		$GROUP_BY = "items.id";

		if($this->itemtype_id) {
			$WHERE[] = "items.itemtype_id = $this->itemtype_id";
		}

		if($tags) {
			$FROM[] = UT_ITE_TAG . " as item_tags";
			$FROM[] = UT_TAG . " as tags";
			$tag_array = explode(",", $tags);
			foreach($tag_array as $tag) {
//				$exclude = false;
				// tag id
				if($tag) {
					if(substr($tag, 0, 1) == "!") {
						$tag = substr($tag, 1);
						$WHERE[] = "items.id NOT IN (SELECT item_id FROM ".UT_ITE_TAG." as item_tags, ".UT_TAG." as tags WHERE item_tags.tag_id = '$tag' OR (item_tags.tag_id = tags.id AND tags.name = '$tag'))";

	//					$exclude = true;
					}
					else {
						$WHERE[] = "items.id IN (SELECT item_id FROM ".UT_ITE_TAG." as item_tags, ".UT_TAG." as tags WHERE item_tags.tag_id = '$tag' OR (item_tags.tag_id = tags.id AND tags.name = '$tag'))";

					}
				}
			}

//			$FROM[] = UT_NAV_ITE . " as nav_items";
//			$WHERE[] = "((nav_items.item_id = items.id AND nav_items.navigation_tags = '$tags') OR items.id NOT IN (SELECT item_id FROM ".UT_NAV_ITE." WHERE navigation_tags = '$tags'))";
		}
		/*
		if($tags) {
			$FROM[] = UT_ITE_TAG . " as item_tags";
			$WHERE[] = "item_tags.item_id = items.id";
			$WHERE[] = "item_tags.tag_id = $tags";

			$SELECT[] = "nav_items.sequence";
			$FROM[] = UT_NAV_ITE . " as nav_items";
			$WHERE[] = "(nav_items.item_id = items.id AND nav_items.navigation_tags = '$tags' OR items.id NOT IN (SELECT item_id FROM ".UT_NAV_ITE." WHERE navigation_tags = '$tags'))";
			$ORDER[] = "nav_items.sequence";
		}
		*/


		// add item-order specific SQL
		if($order) {
			$ORDER[] = $order;
		}

		$ORDER[] = "items.timestamp DESC";

		if($limit) {
			$limit = " LIMIT $limit";
		}

//		print $query->makeQuery($SELECT, $FROM, $WHERE, $GROUP_BY, $ORDER) . $limit;
		$query->sql($query->makeQuery($SELECT, $FROM, $WHERE, $GROUP_BY, $ORDER) . $limit);

		for($i = 0; $i < $query->getQueryCount(); $i++){
			$item_id = $query->getQueryResult($i, "items.id");
			$item_sindex = $query->getQueryResult($i, "items.sindex");
			$item_status = $query->getQueryResult($i, "items.status");

			$itemtype_id = $query->getQueryResult($i, "items.itemtype_id");
			$itemtype = $this->itemtypes["values"][array_search($itemtype_id, $this->itemtypes["id"])];

			$this->setItemtype($itemtype_id);

			$this->item["id"][] = $item_id;
			// create sindex value if it doesn't exist (backwards compatibility)
			$this->item["sindex"][] = $item_sindex ? $item_sindex : $this->sindex($item_id, $this->getTypeObject($itemtype)->getTypeName($item_id));

			$this->item["status"][] = $item_status;
			$this->item["status_text"][] = $item_status ? $this->translate("enabled") : $this->translate("disabled");

			$this->item["itemtype_id"][] = $itemtype_id;
			$this->item["itemtype"][] = $itemtype;
		}

	}
	
	/**
	* Get search items
	*
	* @uses Item::getItems()
	*/
	function getSearchItems() {
		$itemtype = Session::getSearch("itemtype_id");
		$tags = Session::getSearch("tags");
		$sindex = Session::getSearch("sindex");
		$order = Session::getSearch("order");
		$this->getItems($itemtype, false, $tags, $sindex, $order);
	}
	
	
	function getNavTags($sindex) {
		$query = new Query();
		if($query->sql("SELECT tags FROM ".UT_NAV." WHERE sindex = '$sindex'")) {
			return array_list(explode(",", $query->getQueryResult(0, "tags")));
		}
		return '';

	}

	/**
	* Get status for selected item (1: enabled / 0: disabled)
	*
	* @param int $item_id Item id
	* @return int Status
	*/
	function getStatus($item_id)	{
		$query = new Query();
		$query->sql("SELECT status FROM ".UT_ITE." WHERE id = $item_id");
		return $query->getQueryResult(0, "status"); 
	}

	/**
	* Core Save item, based on parameters
	* Creates new item of itemtype
	*
	* @param String/Integer Itemtype identifier or itemtype id
	* @param Integer user_id
	* @param Integer status, default 0 = disabled
	* @param Optional content container, contenttype defined by type class, most frequently array, string or imagepath
	*/
	function save($itemtype, $user_id, $status=1, $content=false) {

		$this->setItemtype($itemtype, true);
		if($this->itemtype_id) {
			$query = new Query();

			$vars = "DEFAULT";
			$vars .= ", ''";
			$vars .= ", '$status'";
			$vars .= ", CURRENT_TIMESTAMP";
			$vars .= ", ".$user_id;
			$vars .= ", ".$this->itemtype_id;

			if($query->sql("INSERT INTO ".$this->db." VALUES($vars)")) {
				$item_id = $query->getLastInsertId();

				if($content) {
					if($this->getTypeObject()->save($item_id, $content)) {
						// set sindex value
						//$this->sIndex($item_id, $this->getTypeObject()->getTypeName($item_id));
						return $item_id;
					}
					else {
						$query->sql("DELETE FROM ".$this->db." WHERE id = ".$item_id, true);
					}
				}
				else {
					return $item_id;
				}
			}
		}
		return false;
	}

	/**
	* set sIndex value for item
	*
	* @param string $item_id Item id
	* @param string $sindex
	* @return String final/valid sindex
	*/
	function sIndex($item_id, $sindex) {
		$sindex = superNormalize(substr($sindex, 0, 40));
		$query = new Query();

		// check for existance
		if($sindex && !$query->sql("SELECT sindex FROM ".UT_ITE." WHERE sindex = '$sindex' AND id != $item_id")) {
			$query->sql("UPDATE ".UT_ITE." SET sindex = '$sindex' WHERE id = $item_id");
		}
		// try with timestamped variation
		else {
			$query->sql("SELECT timestamp FROM ".UT_ITE." WHERE id = $item_id");
			$sindex = $this->sIndex($item_id, $query->getQueryResult(0, "timestamp")."_".$sindex);
		}
		return $sindex;
	}

	/**
	* Update files - clean up and create new source jpg
	*/
	function updateFiles($item_id, $file_tmp) {

		if($file_tmp && file_exists($file_tmp)) {

			// clean up old files
			FileSystem::rmdirr(PUBLIC_FILE_PATH."$item_id");
			FileSystem::mkdirr(PUBLIC_FILE_PATH."$item_id");

			FileSystem::rmdirr(BACKUP_FILE_PATH."$item_id");
			FileSystem::mkdirr(BACKUP_FILE_PATH."$item_id");

			if(copy($file_tmp, BACKUP_FILE_PATH.$item_id."/jpg")) {
				return true;
			}
		}

		return false;
	}


	/**
	* Get item description
	* Goes through $this->item and adds description to any indexes found
	* Descriptions indexes,
	* - ["title"][$index][]
	* - ["description_short"][$index][]
	* - ["description_long"][$index][]
	* - ["description_language_id"][$index][]
	*
	* If no descriptions, ["description_language_id"][$index] = false
	*
	* @param String $language_id Language iso, to get only specific language
	*/
	function getDescription($language_id = false) {
		$query = new Query();

		foreach($this->item["id"] as $key => $value) {
			$query_string = "
				SELECT 
					title,
					description_short,
					description_long,
					language_id
				FROM ".UT_ITE_DES."
				WHERE 
					item_id = $value
				" . ($language_id ? " AND language_id = '$language_id'" : "");

			if($query->sql($query_string)) {
				for($i = 0; $i < $query->getQueryCount(); $i++) {
					$this->item["title"][$key][] = $query->getQueryResult($i, "title");
					$this->item["description_short"][$key][] = $query->getQueryResult($i, "description_short");
					$this->item["description_long"][$key][] = $query->getQueryResult($i, "description_long");
					$this->item["description_language_id"][$key][] = $query->getQueryResult($i, "language_id");
				}
			}
			else {
				$this->item["description_language_id"][$key] = false;
			}
		}
	}


	/**
	* Get prices
	* Goes through $this->item and adds prices to any indexes found
	* Prices indexes,
	* - ["price"][ITEM_INDEX][COUNTRY_ISO][PRICE_ID]
	*
	* If no prices, ["price"][ITEM_INDEX] = false
	*/
	function getPrices() {
		$query = new Query();


		foreach($this->item["id"] as $key => $value) {
			if($query->sql("SELECT item_prices.price, price_groups.uid, item_prices.country_id FROM ".UT_ITE_PRI." AS item_prices, ".UT_PRI." AS price_groups WHERE price_groups.uid = item_prices.price_group_uid AND item_prices.item_id = '".$value."'")) {

				for($i = 0; $i < $query->getQueryCount(); $i++) {
					$this->item["price"][$key][$query->getQueryResult($i, "country_id")][$query->getQueryResult($i, "uid")] = $query->getQueryResult($i, "price");
//					$this->item["price_id"][$key][] = $query->getQueryResult($i, "id");
				}
			}
			else {
				$this->item["price"][$key] = false;
			}

		}
	}

	function getReviews() {return "";}
	function getRating() {return "";}


	/**
	* Checking usage of selected item
	*
	*
	* @param int $item_id Item id
	* @return bool
	*/
	function checkUsage($item_id) {
		return false;
		
//		return Session::getLogin()->getUserId() == $this->item["user_id"][0];
//		return Generic::checkUsage($item_id, "item_id", UT_SIT_ITE, UT_ORD, UT_ORD_ARC);
	}
}

?>