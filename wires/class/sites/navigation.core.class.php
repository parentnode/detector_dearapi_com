<?php
/**
* This file contains site menu maintenance functionality
*/
include_once("class/system/generic.class.php");
include_once("class/system/filesystem.class.php");

/**
* DavNavigation, extends DavNavigation views
*
*/
class NavigationCore extends Translation {

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {
		// initiate helpers before calling View construct
		$this->addTranslation(__FILE__);
		$this->db = UT_NAV;
		$this->dav_folder = LOCAL_PATH."/library/dav";
	}

	/**
	* Get selected item
	* Makes query result available
	*
	* @param int $id Item id
	* @return bool
	* @uses Generic::getItem()
	*/
	function getItem($id) {
		return Generic::getItem($id, $this->db);
	}

	/**
	* Get selected item name
	*
	* @param int $id Item id
	* @return string|false Item name or false on error
	* @uses Generic::getItemName()
	*/
	function getItemName($id) {
		return Generic::getItemName($id, $this->db);
	}

	/**
	* Get all items by iterating based on relations
	*
	* @param Integer $relation Idetifier for iteration
	* @return array|false Item array or false on error
	*/
	function getItems($relation = 0, $items = false, $indent = 0) {
//		print "gi<br>";
		if(!$items) {
			$items = array();
		}
		$query = new Query();

		if(!$this->sql("SELECT id FROM ".$this->db." WHERE id = 1")) {
			$this->sql("INSERT INTO ".$this->db." VALUES(1, '/ (frontpage)', 'frontpage', '', 0, 0, 1, 0, 'frontpage', '')");
			$this->sql("INSERT INTO ".UT_TAG." VALUES(1, 'frontpage')");
		}

		$query->sql("SELECT * FROM ".$this->db." WHERE relation = $relation ORDER BY sequence ASC");

		// preliminary sort of menu items
		for($i = 0; $i < $query->getQueryCount(); $i++) {
			$items["id"][] = $query->getQueryResult($i,"id");
			$items["name"][] = $query->getQueryResult($i,"name");
			$items["tags"][] = $query->getQueryResult($i,"tags");
			$items["url"][] = $query->getQueryResult($i,"url");
			$items["values"][] = $this->addNesting($items, $query->getQueryResult($i,"relation"))."/".$query->getQueryResult($i,"name");
			$items["relation"][] = $query->getQueryResult($i,"relation");
			$items["sequence"][] = $query->getQueryResult($i,"sequence");
			$items["hidden"][] = $query->getQueryResult($i,"hidden");
			$items["enabled"][] = $query->getQueryResult($i,"enabled");
			$items["classname"][] = $query->getQueryResult($i,"classname");
			$items["sindex"][] = $query->getQueryResult($i,"sindex");
			$items["indent"][] = $indent;
			$items = NavigationCore::getItems($query->getQueryResult($i,"id"), $items, $indent+1);
		}
		if(isset($items["id"])) {
			return $items;
		}
		return false;
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


	function updateDav() {
//		print "udav";
		FileSystem::rmdirr($this->dav_folder);
		FileSystem::mkdirr($this->dav_folder);

		$items = NavigationCore::getItems();
		if($items) {
			foreach($items["name"] as $key => $value) {
				// Not frontpage item or pages without tags
				if($items["id"][$key] != "1" && $items["tags"][$key]) {
					FileSystem::mkdirr($this->dav_folder.$this->addNesting($items, $items["relation"][$key])."/".$value);
				}
			}
		}
	}

	function checkDav() {
		global $page;

//		print "udav";
		//print "fe:" . file_exists("/tmp/DAVLock") . "::";
		//print_r(file("/tmp/DAVLock"));

		if(file_exists($this->dav_folder)) {
			$davs = NavigationCore::getItems();

			$files = FileSystem::folderIterator($this->dav_folder, "", array(), array(".jpg", ".txt"));
			foreach($files as $filepath) {
				$folder = dirname(str_replace($this->dav_folder, "", $filepath));
				$file = basename($filepath);
				
//				print "F:" . $folder . ":F:";

				// root folder = frontpage
				if($folder == "/") {
					$nav_id = 1;
				}
				else {
//					print "id folder";
//					print_r($davs);
					$nav_id = $this->idFolder($folder, $davs);
					// get folder id
//					$nav_id = array_search($folder, $davs["values"]) !== false ? $davs["id"][array_search($folder, $davs["values"])] : false;

					// new folder
//					if(!$nav_id) {

//						$query = new Query();
//						$query->sql("INSERT INTO ".$this->db." VALUES(DEFAULT, '$folder', 0, 0)", true);
//						$nav_id = $query->getLastInsertId();

//					}
				}

				if(substr($file, -4) == ".jpg") {
//					print $page->getObject("Item");


					$item_id = $page->getObject("Item")->save("photo", 1, 1, $filepath);

					// TODO
					// add navigation tags to element

//					$page->getObject("Item")->addDav($item_id, $nav_id);
//					$item_id = ItemCore::save("photo", 1, $filepath);
//					print "photo:" . $item_id;
//					$this->save
//					return $item_id;
				}
//				print "DAV full file::".$filepath."<br />";
//				print "DAV file::".$file.substr($file, -4)."<br />";
//				print "DAV Folder::".$folder."=".$nav_id."<br />";
			}
		}

		$this->updateDAV();
	}
	function idFolder($folder, $davs) {

//		print "<br>fpr:".$folder;
//		print_r($davs);
		// get folder id

		$nav_id = array_search($folder, $davs["values"]) !== false ? $davs["id"][array_search($folder, $davs["values"])] : false;
//		print "<br>nid:".$nav_id;

		$new_folder = dirname($folder);
//		print "<br>fpo:".$folder;


		// new folder
		if(!$nav_id) {
//			print $folder;
			
			if($new_folder == "/") {
				$nav_id = 0;
			}
			else {
				$nav_id = $this->idFolder($new_folder, $davs);
				
			} 

//print "<br>create new-navid:" . $nav_id . ":". basename($folder). ":";
			$query = new Query();
			$query->sql("INSERT INTO ".$this->db." VALUES(DEFAULT, '".basename($folder)."', $nav_id, 0)");
			return $query->getLastInsertId();
				
				
//			}
//			else {

				
//			}
			

		}
		else {
			return $nav_id;
		}

		
	}
	
	/**
	* Navigation service function handling tasks associated with navigation update
	*/
	function updateRelatedSystems() {
		$this->makeNavigationForTranslation();
		$this->makeSitemap();
		$this->checkDav();
	}

	/**
	* Creates sitemap XML based on menu structure
	*/
	function makeSitemap() {
		$filename = LOCAL_PATH."/www/sitemap.xml";
		$file = fopen($filename, "w+");
		fwrite($file, '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset
		  xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
		  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		  xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
		                      http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'."\n");

		$query = new Query();
		$query->sql("SELECT name, tags, url, sindex FROM " . $this->db . " WHERE enabled = 1");
		for($i = 0; $i < $query->getQueryCount(); $i++) {
			$name = $query->getQueryResult($i,"name");
			$sindex = $query->getQueryResult($i,"sindex");
//			$tags = $query->getQueryResult($i,"tags");
			$url = $query->getQueryResult($i,"url");
//			$sindex = $query->getQueryResult($i,"sindex");
			if($sindex === "frontpage") {
				fwrite($file, '<url><loc>http://'.SITE_URL.'</loc><lastmod>'.date("Y-m-d\TH:i:s\Z", time()).'</lastmod><priority>1.0000</priority><changefreq>daily</changefreq></url>'."\n");
			}
			else if($url && substr($url, 0, 4) != "http") {
				fwrite($file, '<url><loc>http://'.SITE_URL.$url.'</loc><lastmod>'.date("Y-m-d\TH:i:s\Z", time()).'</lastmod><priority>1.0000</priority><changefreq>weekly</changefreq></url>'."\n");
			}
			else if($sindex) {
				fwrite($file, '<url><loc>http://'.SITE_URL.'/nav/'.$sindex.'</loc><lastmod>'.date("Y-m-d\TH:i:s\Z", time()).'</lastmod><priority>1.0000</priority><changefreq>daily</changefreq></url>'."\n");
			}
		}
		fwrite($file, '</urlset>');
		fclose($file);
	}
	
	/**
	* Update navigation items order
	* Update the selected order of the items matching a given navigation item
	*
	* @param integer $id Navigation item id
	*/
	function updateNavigationItems($id) {
		$query = new Query();
		$query->sql("SELECT sindex FROM ".UT_NAV." WHERE id = $id");
		$sindex = $query->getQueryResult(0, "sindex");

		$items = getVar("items");
		$query = new Query();
		$query->sql("DELETE FROM ".UT_NAV_ITE." WHERE sindex = '$sindex'");
		
		foreach($items as $sequence => $item_id) {
			$query->sql("INSERT INTO ".UT_NAV_ITE." VALUES(DEFAULT, $sequence, 0, '$sindex', $item_id)");
		}
	}

	/**
	* Create translation file for navigation items
	* @todo Perhaps this is not needed if translations are to include DB content directly?
	*/
	function makeNavigationForTranslation() {
		$filename = LOCAL_PATH."/library/www.navigation.summary.php";
		$file = fopen($filename, "w+");
		fwrite($file, '<?xml version="1.0" encoding="UTF-8"?>'."\n");

		$query = new Query();
		$query->sql("SELECT name FROM " . $this->db);
		for($i = 0; $i < $query->getQueryCount(); $i++) {
			$name = $query->getQueryResult($i,"name");
			if($name != "----") {
				fwrite($file, '<element><?= $this->translate("'.$name.'")?></element>'."\n");
			}
		}
		fclose($file);
	}

	/**
	* Check if item has children
	*
	* @param Integer $id Item id
	* @return bool
	*/
	function checkUsage($id) {
		if($this->sql("SELECT id FROM ".$this->db." WHERE relation = $id")) {
			return true;
		}
		else {
			return false;
		}
	}

}

?>