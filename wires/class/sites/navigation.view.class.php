<?php
/**
* This file contains menu views functionality
* Extended by the menu class
*/

include_once("navigation.core.class.php");

class NavigationView extends NavigationCore {

	/**
	* Get translation for file
	*/
	function __construct() {
		$this->addTranslation(__FILE__);
		parent::__construct();
	}


	function pageList($which=false) {
		$exclude_dirs = array("css", "js", "img", "error", "temp", "config", "include", "class", "library", "templates", "reset", "cron", "docs", "wurfl", "admin");
		$extensions = array(".php", ".html");

		$local_path = FileSystem::folderIterator(LOCAL_PATH, "www/", $exclude_dirs, $extensions);
//		$global_path = FileSystem::folderIterator(GLOBAL_PATH, "www/", $exclude_dirs, $extensions);
//		$framework_path = FileSystem::folderIterator(FRAMEWORK_PATH, "www/", $exclude_dirs, $extensions);

//		$files = array_merge_recursive($local_path, $global_path, $framework_path);
		$files = $local_path;

		foreach($files as $value) {
//			$items["file"][] = $value;
			$items["file"][] = str_replace(LOCAL_PATH . "/www", "", $value);
			$items["values"][] = str_replace(LOCAL_PATH . "/www", "", $value);
		}

		if(!count($items)) {
			return false;
		}
		else if($which) {
			return $items[$which];
		}
		else {
			return $items;
		}
	}

}

?>