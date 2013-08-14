<?php
/**
* @package framework
*/
/**
*
*/

include_once("class/www/item.wcore.class.php");
include_once("class/system/validator.class.php");


/**
* This file contains site item maintenance functionality
* Item
* @package local
*/
class Item extends ItemWCore {

	function __construct() {
		$this->addTranslation(__FILE__);
		$this->validator = new Validator($this);
		parent::__construct();

		$this->varnames["name"] = $this->translate("Your name");
		$this->validator->rule("name", "txt");

		$this->varnames["email"] = $this->translate("Your email");
		$this->validator->rule("email", "txt");

		$this->varnames["comment"] = $this->translate("Your comment");
		$this->validator->rule("comment", "txt");

		$this->vars = getVars($this->varnames);
	}

	/**
	* Get the type specific parts of the item
	*/
	function getTypeItem() {
		foreach($this->item["id"] as $key => $id) {
			$this->getTypeObject($this->item["itemtype"][$key])->getTypeItem($id);
		}
	}

	/**
	* Get the type specific name of the item
	* returns the name in case of just one index (last index)
	*/
	function getTypeName() {
		foreach($this->item["id"] as $key => $id) {
			$name = $this->getTypeObject($this->item["itemtype"][$key])->getTypeName($id);
		}
		return $name;
	}


	/**
	* Get tags
	* Goes through $this->item and adds tags to any indexes found
	* Descriptions indexes,
	* - ["tags_id"][$index][]
	* - ["tags"][$index][]
	* - ["tags_c_id"][$index][]
	* - ["tags_c"][$index][]
	*
	* If no a-tags, ["tags"][$index] = false
	* If no c-tags, ["tags_c"][$index] = false
	*/
	function getTags() {
		$query = new Query();

		if($this->item()) {
			foreach($this->item["id"] as $key => $value) {
				// A-tags
				if($query->sql("SELECT tags.id, tags.name FROM ".UT_ITE_TAG." AS item_tags, ".UT_TAG." AS tags WHERE tags.id = item_tags.tag_id AND item_tags.item_id = '".$value."'")) {
					for($i = 0; $i < $query->getQueryCount(); $i++) {
						$this->item["tags_id"][$key][] = $query->getQueryResult($i, "id");
						$this->item["tags"][$key][] = $query->getQueryResult($i, "name");
					}
				}
				else {
					$this->item["tags"][$key] = false;
				}

				// C-tags
				if($query->sql("SELECT id, name, user_id FROM ".UT_TAG_CLI." WHERE item_id = '".$value."'")) {
					for($i = 0; $i < $query->getQueryCount(); $i++) {
						$this->item["tags_c_id"][$key][] = $query->getQueryResult($i, "id");
						$this->item["tags_c"][$key][] = $query->getQueryResult($i, "name");
						$this->item["tags_c_user_id"][$key][] = $query->getQueryResult($i, "user_id");
					}
				}
				else {
					$this->item["tags_c"][$key] = false;
				}
			}
		}
	}

	/**
	* Get tags
	* Goes through $this->item and collects found tags
	* Descriptions indexes,
	* - ["id"][$index]
	* - ["values"][$index]
	*
	*/
	function getRelatedTags() {
		$this->getTags();
		$related_tags = array("id" => array(), "values" => array());
		if($this->item()) {
			foreach($this->item["id"] as $key => $id) {
				if($this->item["tags"][$key]) {
					foreach($this->item["tags_id"][$key] as $index => $tag_id) {
						if(array_search($tag_id, $related_tags["id"]) === false) {
							$related_tags["id"][] = $tag_id;
							$related_tags["values"][] = $this->item["tags"][$key][$index];
						}
					}
					
				}
			}
		}
		return count($related_tags["id"]) ? $related_tags : false;
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
			// A-tags
			if($query->sql("SELECT tags.id, tags.name FROM ".UT_ITE_TAG." AS item_tags, ".UT_TAG." AS tags WHERE tags.id = item_tags.tag_id AND item_tags.item_id = '".$value."'")) {
				for($i = 0; $i < $query->getQueryCount(); $i++) {
					$this->item["tag_list"][$key][] = '<a rel="tag" href="/list?tags='.$query->getQueryResult($i, "name").'">'.$query->getQueryResult($i, "name").'</a>';
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
		$items = Page::getItems(UT_TAG);
		$tag_string = '';
		foreach($items["id"] as $key => $tag_id) {
			$tag_string .= ($tag_string ? "#" : "") . $items["values"][$key] . "->" . $tag_id;
		}
		return $tag_string;
	}

	/**
	* Add a new tag to item
	* If tag exist, it is added to tags, else added as c-tag
	*
	* @param int $item_id Item id
	* @param string $tag Tag name
	*/
	function addTag($item_id, $tag) {
		if($tag) {
			$query = new Query();

			$vars = "DEFAULT";
			$vars .= ",'$tag'";
			$vars .= ", CURRENT_TIMESTAMP";
			$vars .= ",'$item_id'";
			$vars .= ",'".Session::getLogin()->getUserId()."'";
			$vars .= ",'".$this->site_id."'";

			$query->sql("INSERT INTO ".UT_TAG_CLI." VALUES($vars)");
			messageHandler()->addStatusMessage("Tag added");
		}
	}

	function deleteCTag($tag_id) {
		$query = new Query();
		$query->sql("DELETE FROM ".UT_TAG_CLI." WHERE id = $tag_id");
		messageHandler()->addStatusMessage("Tag deleted");
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
		$_ .= $HTML->inputHidden("id", $id);
		$_ .= $HTML->inputHidden("item_id", $id);

		$_ .= $HTML->head($this->translate("Tags"), "2");
		if(Session::getLogin()->validatePage("tags_add")) {
			$_ .= $HTML->inputHidden("page_status", "tags_add");
			$_ .= $HTML->input($this->translate("Add tag"), "tag", false, "init:autocomplete autocomplete:tags_get:".$page->url, false, 'autocomplete="off"');
			$_ .= $HTML->smartButton($this->translate("Add"), false, "tags_add", "fright");
			$_ .= $HTML->separator();
		}

		// A tags
		$column = false;
		$ids = false;
		$status = false;

		foreach($this->item["tags"] as $item_key => $value) {
			if($value) {
				foreach($value as $tag_key => $tag) {
					$column[] = $tag;
					$ids[] = $this->item["tags_id"][$item_key][$tag_key];
				}
			}
		}
		if($column) {
			$table = $HTML->table();
			$table->setHeader(0, $this->translate("Tags"), "");

			$table->setRowId($ids);
			$table->setColumnValues($column);

			$_ .= $table->build();
		}

		// C tags
		$column = false;
		$ids = false;
		$status = false;

		foreach($this->item["tags_c"] as $item_key => $value) {
			if($value) {
				foreach($value as $tag_key => $tag) {
					$column[] = $tag;
					$ids[] = $this->item["tags_c_id"][$item_key][$tag_key]; 
					if($this->item["user_id"] == $this->item["tags_c_user_id"][$item_key][$tag_key]) {
						$status[] = "tags_c_delete";
					}
					else {
						$status[] = "";
					}
				}
			}
		}
		if($column) {
			$table = $HTML->table();
			$table->setHeader(0, $this->translate("User-submitted tags"), "");

			$table->setRowStatus($status);
			$table->setRowId($ids);
			$table->setColumnValues($column);

			$_ .= $table->build();
		}

		$_ .= '</div>';
		return $_;
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
	*/
	function getDescription() {
		$query = new Query();
		foreach($this->item["id"] as $key => $value) {
			if($query->sql("SELECT title, description_short, description_long, language_id FROM ".UT_ITE_DES." WHERE item_id = $value")) {
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
	* Add a new comment to item
	*
	*/
	function addComment() {

		$this->vars["name"] = $_POST["name"];
		$this->vars["email"] = $_POST["email"];
		$this->vars["title"] = $_POST["title"];
		$this->vars["comment"] = $_POST["comment"];

		$this->vars["item_id"] = $_POST["item_id"];
		$this->vars["language_id"] = $_POST["language_id"];

		if($this->validator->validateList()) {
			$query = new Query();

			/*
			$vars = "DEFAULT";
			$vars .= ",'$tag'";
			$vars .= ", CURRENT_TIMESTAMP";
			$vars .= ",'$item_id'";
			$vars .= ",'".Session::getLogin()->getUserId()."'";
			$vars .= ",'".$this->site_id."'";

			if($query->sql("INSERT INTO ".UT_ITE_COM." VALUES($vars)")) {
				messageHandler()->addStatusMessage("Tag added");
			}
			else {
				messageHandler()->addErrorMessage($this->dbError());
				return false;
			}
			*/
			print "ok";
		}
		else {
			messageHandler()->addErrorMessage($this->translate("Please complete missing information"));
			return false;
		}
	}

	function getAvgRate($item_id, $site_id=false) {return 0;}
	function getMaxRate($item_id, $site_id=false) {return 0;}
	function getMinRate($item_id, $site_id=false) {return 0;}
	function getReviews($item_id) {return array();}



}