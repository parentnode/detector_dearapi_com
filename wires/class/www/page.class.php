<?php
/**
* This file contains the site backbone, the Page Class.
*/

/**
* Include base configuration
*/
include_once($_SERVER["LOCAL_PATH"]."/config/config.php");
include_once("class/www/page.wcore.class.php");

/**
* Site backbone, the Page class
*/
class Page extends PageWCore {

	/**
	* Get required page information
	*/
	function __construct() {
		parent::__construct();
	}

	/**
	* Get page title
	*
	* The page title is complex
	* You can set the title manually via Page::header in your controller
	* If you don't, I will look for, prioritized:
	* - An Item title
	* - Tags - and create a list of them
	* - A Navigation item title
	* - The fallback SITE_NAME
	*
	* @return String page title
	*/
	function getTitle() {
		
		// if title already set
		if($this->title) {
			return $this->title;
		}
		// look for title of item
		else if(Session::getValue("item_sindex")) {
			// if the item_sindex is set, we assume there must be an item class available.
			$item = $this->getObject("Item");
			// get search item (auto usage of session)
			$item->getSearchItem();
			// get name
			$item->getTypeName();
			$this->title = $item->item["name"][0];
		}
		// if tags, list them as title
		else if(Session::getValue("tags")) {
			// to do
			$this->title = "Tags";
		}
		// look for title of navigation item (nav_index will often exist as navigation help and therefor it is last option)
		else if(Session::getValue("nav_sindex")) {
			$query = new Query();
			if($query->sql("SELECT name FROM ".UT_NAV." WHERE sindex = '".Session::getValue("nav_sindex")."' AND enabled = 1")) {
				$this->title = $query->getQueryResult(0, "name");
			}
		}
		// last resort - use constant
		else {
			$this->title = SITE_NAME;
		}

		return $this->title;
	}
	/**
	* Get page description
	* Mostly for page header
	*
	* The page description is complex
	* I will look for, prioritized:
	* - An Item description
	* - Tags - TODO
	* - A Navigation item description - TODO
	* - The fallback $this->title
	*
	* @uses Page::title
	* @uses Page::getObject()
	* @return String page description
	*/
	function getDescription() {
		// if description already set
		if($this->description) {
			return $this->description;
		}
		// look for title of item
		else if(Session::getValue("item_sindex")) {
			// if the item_sindex is set, we assume there must be an item class available.
			$item = $this->getObject("Item");
			// get search item (auto usage of session)
			$item->getSearchItem();
			// get name
			$item->getDescription();
			if($item->item["description_language_id"][0]) {
				$this->description = $item->item["description_short"][0][0];
			}
			else {
				$this->description = $this->getTitle();
			}
		}
		// if tags, to do
		else if(Session::getValue("tags")) {
			// to do
			$this->description = $this->getTitle();
		}
		// look for description of navigation item (still to be implemented)
		else if(Session::getValue("nav_sindex")) {
			// todo
			$this->description = $this->getTitle();
		}
		// last resort - use constant
		else {
			$this->description = $this->getTitle();
		}

		return $this->description;
	}

	/**
	* Get body class
	* this can be sat via page->header
	* 
	* @return String body class
	*/
	function getClass() {
		// if classname already set
		if($this->classname) {
			return $this->classname;
		}
		else if(Session::getValue("nav_sindex")) {
			$query = new Query();
			if($query->sql("SELECT classname FROM ".UT_NAV." WHERE sindex = '".Session::getValue("nav_sindex")."' AND enabled = 1")) {
				$this->classname = $query->getQueryResult(0, "classname");
			}
		}
		return $this->classname;
	}

	/**
	* Add Page class name
	* Made to uncomplicate complicated things
	*/
	function addClass($classname) {
		if($classname) {
			if($this->classname) {
				$this->classname .= " ".$classname;
			}
			else {
				$this->classname = $classname;
			}
		}
	}
	/**
	* Add page header
	*
	* @return String HTML header
	*/
	function header($title="", $classname="") {
		$this->title = $title;
		$this->addClass($classname);
		$this->getTemplate("shell.header.php", false, 0, 0, 0, true);
	}

	/**
	* Add page footer
	*
	* @return String HTML footer
	*/
	function footer() {
		$this->getTemplate("shell.footer.php", false, 0, 0, 0, true);
	}


	/**
	* Get navigation items from navigation class
	*/
	function getNavigationItems() {
		$this->navClass = new Navigation();
		return $this->navClass->getItems();
	}

}

$page = new Page();

?>