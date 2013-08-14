<?php
/**
* This file contains site menu maintenance functionality
*/
include_once("navigation.view.class.php");
include_once("class/system/validator.class.php");

/**
* DavNavigation, extends DavNavigation views
*
*/
class Navigation extends NavigationView {

	// used as menu structure container
	public $menu_layout;
	public $item_indent;

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

		$this->varnames["name"] = $this->translate("Name");
		$this->validator->rule("name", "txt");

		$this->varnames["type"] = $this->translate("Navigation type");
//		$this->validator->rule("type", "txt");

		// tag based
		$this->varnames["tags"] = $this->translate("Tag");
		$this->varnames["hidden"] = $this->translate("Hidden (only show page trail)");

		// URL
		$this->varnames["url"] = $this->translate("Url");
		$this->varnames["page_list"] = $this->translate("Local pages");


		$this->varnames["relation"] = "";
		$this->varnames["classname"] = "Classname";
		$this->varnames["sindex"] = "SEO index";
		$this->validator->rule("sindex", "unik", false, $this->db);

//		$this->varnames["conditions"] = $this->translate("Req/Excl/Incl");

		$this->vars = getVars($this->varnames);

	}


	/**
	* Save new item, based on submitted values
	*
	* @return bool
	* @uses Message
	*/
	function saveItem() {
		if($this->validator->validateAll()) {
			$vars = "DEFAULT";
			$vars .= ",'".$this->vars['name']."'";

			// url navigation
			if($this->vars['type'] == "url") {
				if(!$this->vars['url']) {
					messageHandler()->addErrorMessage($this->translate("Type the URL"));
					return false;
				}

				$vars .= ",NULL"; // no tags
				$vars .= ",'".$this->vars['url']."'";
			}

			// tag navigation
			else if($this->vars['type'] == "tags") {

				print_r($this->vars['tags']);

				if(is_array($this->vars['tags']) && $this->vars['tags'][0]) {
					$vars .= ",'";
					for($i = 0; $i < count($this->vars['tags']); $i++) {
						if($this->vars['tags'][$i]) {
							$vars .= ($i ? "," : "") . $this->vars['tags'][$i]."";
						}
					}
					$vars .= "'";
					$vars .= ",NULL"; // no url
				}
				// tag-based but no tags - create default page tag 
				else {
					$query = new Query();
					// look for existing tag id
					if($query->sql("SELECT id FROM ".UT_TAG." WHERE tag = 'page:".$this->vars['name']."'")) {
						$tag_id = $query->getQueryResult(0, "id");

					}
					// create new one
					else {

						$query->sql("INSERT INTO ".UT_TAG." VALUES(DEFAULT, 'page:".$this->vars['name']."')");
						$tag_id = $query->getLastInsertId();
					}

					$vars .= ",'".$tag_id."'"; // no tags
					$vars .= ",NULL"; // no url

//					messageHandler()->addErrorMessage("Tag based without tags?");
//					return false;
				}
			}
			// folder navigation
			else {
				$vars .= ",NULL"; // no tags
				$vars .= ",NULL"; // no url
			}

			$vars .= ",'0'"; // relation
			$vars .= ",'0'"; // sequence
			$vars .= ",'1'"; // enabled by default
			$vars .= ",'".$this->vars['hidden']."'";
			$vars .= ",'".$this->vars['classname']."'";
			$vars .= ",'".$this->vars['sindex']."'";
			print "INSERT INTO ".$this->db." VALUES($vars)";

			if($this->sql("INSERT INTO ".$this->db." VALUES($vars)")) {

				$id = $this->getLastInsertId();
				messageHandler()->addStatusMessage($this->translate("Item saved"));

				$this->updateRelatedSystems();

//				print "##".$id ."##";

				return $id;
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

	// get tag id's from url
	function getTags($taglist) {

//		print $taglist;
//		list($taglist, $tags) = $tags = explode("=", $url);
		$tag = explode(",", $taglist);

		$query = new Query();

		foreach($tag as $tag_id) {
			$query->sql("SELECT * FROM ".UT_TAG." WHERE id = $tag_id");
			$items["id"][] = $query->getQueryResult(0, "id");
			$items["values"][] = $query->getQueryResult(0, "name");
		}
		if(!isset($items)) {
			return false;
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
		if($this->validator->validateAll()) {
			$vars = "name='".$this->vars['name']."'";

			// url navigation
			if($this->vars['type'] == "url") {
				if(!$this->vars['url']) {
					messageHandler()->addErrorMessage($this->translate("Type the URL"));
					return false;
				}
				$vars .= ",tags=NULL";
				$vars .= ",url='".$this->vars['url']."'";
			}
			// tag navigation
			else if($this->vars['type'] == "tags") {

				if(is_array($this->vars['tags']) && $this->vars['tags'][0]) {
					$vars .= ",tags='";
					for($i = 0; $i < count($this->vars['tags']); $i++) {
						if($this->vars['tags'][$i]) {
							$vars .= ($i ? "," : "") . $this->vars['tags'][$i]."";
						}
					}
					$vars .= "'";
					$vars .= ",url=NULL";
				}
				else {
					messageHandler()->addErrorMessage("Tag based without tags?");
					return false;
				}
			}
			// folder navigation
			else {
				$vars .= ",tags=NULL"; // no tags
				$vars .= ",url=NULL"; // no url
			}

			$vars .= ",hidden='".$this->vars['hidden']."'";
			$vars .= ",classname='".$this->vars['classname']."'";
			$vars .= ",sindex='".$this->vars['sindex']."'";

			if($this->sql("UPDATE ".$this->db." SET $vars WHERE id = $id")) {

				messageHandler()->addStatusMessage($this->translate("Item updated"));
				$this->updateRelatedSystems();

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

	/**
	* Delete selected item
	*
	* @param int $id Item id
	* @return bool
	* @uses Message
	*/
	function deleteItem($id) {
		$query = new Query();
		if(!$this->checkUsage($id)) {
			if($query->sql("DELETE FROM ".$this->db." WHERE id = $id")) {

				messageHandler()->addStatusMessage($this->translate("Item deleted"));
				$this->updateRelatedSystems();

				return true;
			}
			else {
				messageHandler()->addErrorMessage($this->dbError());
				return false;
			}
		}
	}

	/**
	* Toggle enable/disable
	*/
	function enableDisable($nav_id) {
		$query = new Query();
		$query->sql("SELECT enabled FROM " . $this->db . " WHERE id = '$nav_id'");
		$status = $query->getQueryResult(0, "enabled");
		$query->sql("UPDATE " . $this->db . " SET enabled = '" . ($status ? 0 : 1) . "' WHERE id = '$nav_id'");
		messageHandler()->addStatusMessage($status ? $this->translate("Disabled") : $this->translate("Enabled"));
		$this->updateRelatedSystems();
	}

	/**
	* Update structure
	*
	* @param int $id Item id
	* @return bool
	* @uses Message
	*/
	function updateStructure($id) {
		$query = new Query();

		$sequence = array();
		$updates = 0;

		for($i = 0; $i < count($id); $i++) {
			$sequence[$this->vars['relation'][$i]] = isset($sequence[$this->vars['relation'][$i]]) ? $sequence[$this->vars['relation'][$i]]+1 : 0;
			if($query->sql("UPDATE ".$this->db." SET relation = ".$this->vars['relation'][$i].", sequence = ".$sequence[$this->vars['relation'][$i]]." WHERE id = ".$id[$i])){
				$updates++;
			}
		}

		if($updates == count($id)) {
			$query->sql("SELECT site_id FROM ".$this->db." WHERE id = ".$id[0]);
			messageHandler()->addStatusMessage($this->translate("Structure updated"));

			$this->updateRelatedSystems();
			return true;
		}
		else {
			messageHandler()->addErrorMessage($this->dbError());
			return false;
		}
	}





}

?>