<?php
include_once("class/items/item.core.class.php");


/**
* This class holds Item functionallity.
*
*/
class ItemTags extends ItemCore {


	private $exclude_tags = "";

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {
		// initiate helpers before calling View construct
		$this->addTranslation(__FILE__);
		parent::__construct();

		$this->exclude_tags .= " AND name NOT LIKE 'type:%'";
//		$this->exclude_tags .= " AND name NOT LIKE 'related:%'";
//		$this->exclude_tags .= " AND name != 'frontpage'";
	}

	/**
	* Get tags
	* Goes through $this->item and adds tags to any indexes found
	* Tags indexes,
	* - ["tags_id"][$index][]
	* - ["tags"][$index][]
	* - ["tags_b_id"][$index][]
	* - ["tags_b"][$index][]
	* - ["tags_c_id"][$index][]
	* - ["tags_c"][$index][]
	*
	* If no a-tags, ["tags"][$index] = false
	* If no b-tags, ["tags_b"][$index] = false
	* If no c-tags, ["tags_c"][$index] = false
	*/
	function getTags() {
		$query = new Query();

		foreach($this->item["id"] as $key => $value) {
			// A-tags
			if($query->sql("SELECT tags.id, tags.name FROM ".UT_ITE_TAG." AS item_tags, ".UT_TAG." AS tags WHERE tags.id = item_tags.tag_id AND item_tags.item_id = '".$value."'" . $this->exclude_tags)) {
				for($i = 0; $i < $query->getQueryCount(); $i++) {
					$this->item["tags_id"][$key][] = $query->getQueryResult($i, "id");
					$this->item["tags"][$key][] = $query->getQueryResult($i, "name");
				}
			}
			else {
				$this->item["tags"][$key] = false;
			}

			// B-tags
			if($query->sql("SELECT id, name FROM ".UT_TAG_BLI." WHERE item_id = '".$value."'")) {
				for($i = 0; $i < $query->getQueryCount(); $i++) {
					$this->item["tags_b_id"][$key][] = $query->getQueryResult($i, "id");
					$this->item["tags_b"][$key][] = $query->getQueryResult($i, "name");
				}
			}
			else {
				$this->item["tags_b"][$key] = false;
			}

			// C-tags
			if($query->sql("SELECT id, name FROM ".UT_TAG_CLI." WHERE item_id = '".$value."'")) {
				for($i = 0; $i < $query->getQueryCount(); $i++) {
					$this->item["tags_c_id"][$key][] = $query->getQueryResult($i, "id");
					$this->item["tags_c"][$key][] = $query->getQueryResult($i, "name");
				}
			}
			else {
				$this->item["tags_c"][$key] = false;
			}
		}
	}

	/**
	* List item tags and unvalidated tags
	* 
	* @return string HTML-view
	*/
	function listTags() {
		global $HTML;
		global $page;
		global $id;
		$this->getTags();

		$_ = '';
		$_ .= '<div class="ci33 init:form form:action:'.$page->url.'" id="container:tags">';
		$_ .= '<div class="init:expandable id:tags">';
		//$_ .= '<div class="init:expandable">';

		$_ .= $HTML->head("Tags", "2");
		$_ .= '<fieldset>';
		$_ .= $HTML->inputHidden("id", $id);
		$_ .= $HTML->inputHidden("item_id", $id);

		if(Session::getLogin()->validatePage("tags_add")) {
			$_ .= $HTML->inputHidden("page_status", "tags_add");
			$_ .= $HTML->input("Add tag", "tag", false, "init:autocomplete autocomplete:tags_get:".$page->url, false, 'autocomplete="off"');
			$_ .= $HTML->smartButton($this->translate("Add"), false, "tags_add", "fright");
			$_ .= $HTML->separator();
		}

		// A tags
		$table = $HTML->table();
		$table->setHeader(0, "A-tags", "");
		$column = false;
		$ids = false;
		$status = false;

		foreach($this->item["tags"] as $item_key => $value) {
			if($value) {
				foreach($value as $tag_key => $tag) {
					$column[] = $tag;
					$ids[] = $this->item["tags_id"][$item_key][$tag_key];
					$status[] = "tags_a_delete";
				}
			}
		}
		if(!$column) {
			$column[] = "No A tags";
			$table->setColumnValues($column);
		}
		else {
			$table->setRowId($ids);
			$table->setColumnValues($column);
			if(Session::getLogin()->validatePage("tags_a_delete")) {
				$table->setRowStatus($status);
				$table->setRowClass("delete");
			}
		}
		$_ .= $table->build();


		// B tags
		$table = $HTML->table();
		$table->setHeader(0, "B-tags", "");
		$column = false;
		$ids = false;
		$status = false;

		foreach($this->item["tags_b"] as $item_key => $value) {
			if($value) {
				foreach($value as $tag_key => $tag) {
					$column[] = $tag;
					$ids[] = $this->item["tags_b_id"][$item_key][$tag_key]; 
					$status[] = "tags_b_delete";
				}
			}
		}
		if(!$column) {
			$column[] = "No B tags";
			$table->setColumnValues($column);
		}
		else {
			$table->setRowId($ids);
			$table->setColumnValues($column);
			if(Session::getLogin()->validatePage("tags_b_delete")) {
				$table->setRowStatus($status);
				$table->setRowClass("delete");
			}
		}
		$_ .= $table->build();

		/*
		// C tags
		$table = $HTML->table();
		$table->setHeader(0, "C-tags", "");
		$column = false;
		$ids = false;
		$status = false;

		foreach($this->item["tags_c"] as $item_key => $value) {
			if($value) {
				foreach($value as $tag_key => $tag) {
					$column[] = $tag;
					$ids[] = $this->item["tags_c_id"][$item_key][$tag_key]; 
					$status[] = "tags_c_delete";
				}
			}
		}
		if(!$column) {
			$column[] = "No C tags";
			$table->setColumnValues($column);
		}
		else {
			$table->setRowId($ids);
			$table->setColumnValues($column);
			if(Session::getLogin()->validatePage("tags_c_delete")) {
				$table->setRowStatus($status);
				$table->setRowClass("delete");
			}
		}
		$_ .= $table->build();
		*/

		//$_ .= '</div>';
		$_ .= '<fieldset>';
		$_ .= '</div>';
		$_ .= '</div>';
		return $_;
	}




	/**
	* Get tags
	* Goes through $this->item and adds tags to any indexes found
	* Taglist indexes,
	* - ["tag_list"][$index]
	*
	*/
	function getTagList() {
		$query = new Query();
		foreach($this->item["id"] as $key => $value) {
			$this->item["tag_list"][$key] = array();
			// A-tags
			if($query->sql("SELECT tags.id, tags.name FROM ".UT_ITE_TAG." AS item_tags, ".UT_TAG." AS tags WHERE tags.id = item_tags.tag_id AND item_tags.item_id = '".$value."'")) {
				for($i = 0; $i < $query->getQueryCount(); $i++) {
//					$this->item["tag_list"][$key][] = '<a href="?tags='.$query->getQueryResult($i, "name").'">'.$query->getQueryResult($i, "name").'</a>';
					$this->item["tag_list"][$key][] = $query->getQueryResult($i, "name");
				}
				$this->item["tag_list"][$key] = array_list($this->item["tag_list"][$key]);
			}
			else {
				$this->item["tag_list"][$key] = "";
			}

		}
	}

	/**
	*
	* @return String name#db_id_name->id separated string (name can contain : which is why -> is used as separator)
	*/
	function getAutoCompleteTags(){

		$query = new Query();
		$tag_string = '';

		if($query->sql("SELECT id, name FROM ".UT_TAG)) {
			for($i = 0; $i < $query->getQueryCount(); $i++) {
				$tag_string .= ($tag_string ? "#" : "") . $query->getQueryResult($i, "name") . "->" . $query->getQueryResult($i, "id");
			}
		}
		return $tag_string;
	}

	/**
	* Add a new tag to item
	* If tag exist, it is added to tags, else added as b-tag
	*
	* @param int $item_id Item id
	* @param string $tag Tag name
	*/
	function addTag($item_id, $tag) {
		if($tag) {
			$query = new Query();
			// if tag exists save its id
			if($query->sql("SELECT id FROM ".UT_TAG." WHERE name = '$tag'")) {
				$vars = "DEFAULT";
				$vars .= ",'$item_id'";
				$vars .= ",'".$query->getQueryResult(0, "id")."'";

				$query->sql("INSERT INTO ".UT_ITE_TAG." VALUES($vars)", true);
			}
			// create new b-tag
			else {
				$vars = "DEFAULT";
				$vars .= ",'$tag'";
				$vars .= ", CURRENT_TIMESTAMP";
				$vars .= ",'$item_id'";
				$vars .= ",'".Session::getLogin()->getUserId()."'";
				$query->sql("INSERT INTO ".UT_TAG_BLI." VALUES($vars)");
			}
			messageHandler()->addStatusMessage("Tag added");
		}
	}

	function deleteATag($tag_id, $item_id) {
		$query = new Query();
		$query->sql("DELETE FROM ".UT_ITE_TAG." WHERE tag_id = $tag_id AND item_id = $item_id");
		messageHandler()->addStatusMessage("Tag deleted");
	}
	function deleteBTag($tag_id, $item_id) {
		$query = new Query();
		$query->sql("DELETE FROM ".UT_TAG_BLI." WHERE id = $tag_id AND item_id = $item_id");
		messageHandler()->addStatusMessage("Tag deleted");
	}
	function deleteCTag($tag_id, $item_id) {
		$query = new Query();
		$query->sql("DELETE FROM ".UT_TAG_CLI." WHERE id = $tag_id AND item_id = $item_id");
		messageHandler()->addStatusMessage("Tag deleted");
	}


	

	/**
	* Search
	* Sets search values in session
	*/
	function search() {
		Session::setSearch("itemtype_id", getVar("itemtype_id"));
		Session::setSearch("tags", getVar("tags"));
		Session::setSearch("sindex", getVar("sindex"));
	}

	/**
	* Reset Search 
	* Resets search values in session
	*/
	function searchReset() {
		Session::resetSearch("itemtype_id");
		Session::resetSearch("tags");
		Session::resetSearch("sindex");
	}

	/**
	* Search form
	*
	* @return string HTML view
	*/
	function searchOptions() {
		global $HTML;

		$_ = '';
		$_ .= $HTML->head($this->translate("Search items"));
		$_ .= $HTML->select($this->translate("Select itemtype"), "itemtype_id", $this->itemtypes, stringOr(Session::getSearch("itemtype_id")), array("", "-"), "Util.Ajax.submitContainer('container:item_search');");
		$_ .= $HTML->select($this->translate("Select tag"), "tags", Page::getItems(UT_TAG), stringOr(Session::getSearch("tags")), array("", "-"), "Util.Ajax.submitContainer('container:item_search');");
//		$_ .= $HTML->select($this->translate("Select page"), "sindex", Page::getItems(UT_TAG), stringOr(Session::getSearch("sindex")), array("", "-"), "Util.Ajax.submitContainer('container:item_search');");

		return $_;
	}

	/**
	* Get all items by iterating based on relations
	*
	* @param Integer $relation Idetifier for iteration
	* @return array|false Item array or false on error
	*/
	function addNesting($items, $relation) {
		if($relation == 0) {
			return "";
		}
		foreach($items["id"] as $key => $value) {
			if($value == $relation) {
				return $this->addNesting($items, $items["relation"][$key]) . "/" . $items["name"][$key];
			}
		}
		return "";
	}
	
	
	/*
function getDavs() {
//		$items = $this->navigationClass->getItems();
	$items = Page::getItems(UT_NAV);

	$query = new Query();
	foreach($this->item["id"] as $key => $value) {
		if($query->sql("SELECT nav.id, nav.name, nav.relation FROM ".UT_NAV." AS item_tags, ".UT_TAG." AS tag WHERE tag.id = item_tags.tag_id AND item_tags.item_id = '".$value."'")) {
			for($i = 0; $i < $query->getQueryCount(); $i++) {
				$this->item["tags_id"][$key][] = $query->getQueryResult($i, "id");
				$this->item["tags"][$key][] = $this->addNesting($items, $query->getQueryResult($i, "relation")) ."/". $query->getQueryResult($i, "name");
			}
		}
		else {
			$this->item["tags"][$key] = false;
		}
	}
}
*/
		/**
		* List dav options
		* 
		* @return string HTML-view
		*/
/*
		function listDavs() {
			global $HTML;
			global $page;
			global $id;
			$this->getDavs();

	//		print_r($this->navigationClass->getItems());

			$_ = '';
			$_ .= '<div class="init:form form:action:'.$page->url.'" id="container:tags">';
			$_ .= $HTML->inputHidden("id", $id);
			$_ .= $HTML->inputHidden("item_id", $id);

			$_ .= $HTML->head($this->translate("Navigation"), "2");

			if(Session::getLogin()->validatePage("tags_add")) {
//				$items = $this->navigationClass->getItems();
				$items = Page::getItems(UT_NAV);
				if(!$items) {
					$_ .= $HTML->p($this->translate("You haven't created navigation folders yet. Add your first by clicking here!"), "hint status:link:/sites/navigation.php");
				}

				$_ .= $HTML->inputHidden("page_status", "tags_add");
				$_ .= $HTML->select($this->translate("Add to folder"), "tag", $items, false, array("", "-"));
				$_ .= $HTML->smartButton($this->translate("Add"), false, "tags_add", "fright");
				$_ .= $HTML->separator();
			}

			// davs
			$table = $HTML->table();
			$table->setHeader(0, $this->varnames["tags"], "");
			$column = false;
			$ids = false;
			$status = false;

			foreach($this->item["tags"] as $item_key => $value) {
				if($value) {
					foreach($value as $tag_key => $tag) {
						$column[] = $tag;
						$ids[] = $this->item["tags_id"][$item_key][$tag_key];
						$status[] = "tags_a_delete";
					}
				}
			}
			if(!$column) {
				$column[] = $this->translate("No folders");
				$table->setColumnValues($column);
			}
			else {
				$table->setRowId($ids);
				$table->setColumnValues($column);
				if(Session::getLogin()->validatePage("tags_a_delete")) {
					$table->setRowStatus($status);
					$table->setRowClass("delete");
				}
			}
			$_ .= $table->build();

			$_ .= '</div>';
			return $_;
		}
*/
	/**
	* Add a new tag to item
	* If tag exist, it is added to tags, else added as b-tag
	*
	* @param int $item_id Item id
	* @param string $tag Tag name
*/
/*
	function addDav($item_id, $dav_id) {
		if($dav_id) {
			$query = new Query();

			$vars = "DEFAULT";
			$vars .= ",'$item_id'";
			$vars .= ",'$dav_id'";

			$query->sql("INSERT INTO ".UT_ITE_DAV." VALUES($vars)", true);
			messageHandler()->addStatusMessage($this->translate("Folder added"));
		}
	}

	function deleteDav($dav_id) {
		$query = new Query();
		$query->sql("DELETE FROM ".UT_ITE_DAV." WHERE dav_id = $dav_id");
		messageHandler()->addStatusMessage($this->translate("Folder deleted"));
	}
*/



}

?>
