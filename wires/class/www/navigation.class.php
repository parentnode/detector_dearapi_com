<?php
/**
* This file contains site menu maintenance functionality
*/
include_once("class/sites/navigation.core.class.php");

/**
* Navigation, extends NavigationCore
*
*/
class Navigation extends NavigationCore {

	// used as menu structure container
//	public $menu_layout;
//	public $item_indent;

//	public $varnames;
//	public $vars;
//	private $validator;

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {
		// initiate helpers
		$this->addTranslation(__FILE__);
		$this->addTranslation(LOCAL_PATH."/templates/www/navigation.summary.php");

		$this->selected = false;
		parent::__construct();
	}

	/**
	* Get menu items by iterating based on relations
	*
	* @param Integer $relation Idetifier for iteration (default to 0 = baselevel)
	* @return array Item array
	*/
	function getItems($relation = 0) {
		global $page;
		$query = new Query();
		// not id = 1 (frontpage item)
		$query->sql("SELECT * FROM ".$this->db." WHERE relation = $relation AND enabled = 1 AND id != 1 ORDER BY sequence ASC");

		$items = array();
		for($i = 0; $i < $query->getQueryCount(); $i++) {
			$id = $query->getQueryResult($i,"id");
			$name = $query->getQueryResult($i, "name");
			$url = $query->getQueryResult($i, "url");
			$tags = $query->getQueryResult($i, "tags");
			$hidden = $query->getQueryResult($i, "hidden");
			$classname = $query->getQueryResult($i, "classname");
			$sindex = $query->getQueryResult($i, "sindex");

			if(!$this->selected) {
				$page->trail[] = array("id" => $id, "name" => $name, "sindex" => $sindex);
				$items["trail"][] = true;
				$items["selected"][] = false;
			}
			else {
				$items["trail"][] = false;
				$items["selected"][] = false;
			}

			// separator
			if($name == "----") {
				$items["id"][] = $id;
				$items["name"][] = $name;
				$items["url"][] = false;
				$items["classname"][] = false;
				$items["hidden"][] = false;
				$items["sindex"][] = false;
				$items["children"][] = false;
			}
			else {
				$items["id"][] = $id;
				$items["name"][] = $this->translate($name);
				$items["tags"][] = $tags ? $tags : false;
				$items["url"][] = $url ? $url : "/nav/".$sindex;
				$items["classname"][] = $classname;
				$items["hidden"][] = $hidden;
				$items["sindex"][] = $sindex;
				$items["children"][] = $this->getItems($id);
			}

			if($sindex == strtolower(Session::getValue("nav_sindex"))) {
				$page->trail[] = array("id" => $id, "name" => $name, "sindex" => $sindex);
				$items["selected"][count($items["trail"])-1] = true;
				$this->selected = true;
			}

			if(!$this->selected) {
				array_pop($page->trail);
				$items["trail"][count($items["trail"])-1] = false;
//				$items["selected"][count($items["trail"])-1] = false;
			}

		}
		return count($items) ? $items : false;
	}

}


?>