<?php
/**
* @package framework.devices
*/

include_once("device.class.php");
include_once("class/system/validator.class.php");


class DeviceCleanup extends Device {
	
	public $varnames;
	public $vars;
	public $validator;

	/**
	* Init, set varnames, validation rules
	* @return void
	*/
	function __construct() {
		// initiate helpers before calling View construct
		$this->addTranslation(__FILE__);

		parent::__construct();
	}

	/**
	* Get all devices
	*
	* @return array|false Item array or false on error
	*/
	function getCleanupItems($brand_id=false, $contenttype_type_id=false, $useragent_pattern=false, $limit=false) {

		$items = array();

		$query = new Query();

		$SELECT = array();
		$FROM = array();
		$WHERE = array();
		$GROUP_BY = "";
		$ORDER = array();


		$SELECT[] = "devices.id as id";
		$SELECT[] = "devices.model as model";
		$SELECT[] = "brands.name as brand";

		$FROM[] = UT_DEV." as devices";
		$FROM[] = UT_BAS_BRA." as brands";

		$GROUP_BY = "devices.id";
//		$WHERE[] = "devices.id = ".$id;
		$WHERE[] = "devices.brand_id = brands.id";
		if($brand_id) {
			$WHERE[] = "brands.id = $brand_id";
		}

		if($contenttype_type_id) {
			$WHERE[] = "devices.id NOT IN (SELECT device_id FROM ".UT_DEV_CON." as device_contenttypes, ".UT_BAS_CON." as contenttypes WHERE device_contenttypes.contenttype_id = contenttypes.id AND contenttypes.name LIKE '$contenttype_type_id/%')";
		}

		if($useragent_pattern) {
			$FROM[] = UT_DEV_USE." as useragents";
			$WHERE[] = "devices.id = useragents.device_id";
			$WHERE[] = "useragents.useragent LIKE '%$useragent_pattern%'";
		}

		if(!$limit) {
			$ORDER[] = "brand";
			$ORDER[] = "model";
		}

		// if limit, order by time
		if($limit) {
			$limit = " LIMIT $limit";
			$ORDER[] = "id DESC";
		}

//		$query->sql("SELECT ".UT_DEV.".id, ".UT_DEV.".model, ".UT_BAS_BRA.".name FROM ".UT_DEV.", ".UT_BAS_BRA." WHERE ".UT_DEV.".id = ".$id." AND ".UT_DEV.".brand_id = ".UT_BAS_BRA.".id ORDER BY name, model");
//		print $query->makeQuery($SELECT, $FROM, $WHERE, $GROUP_BY, $ORDER);
		$query->sql($query->makeQuery($SELECT, $FROM, $WHERE, $GROUP_BY, $ORDER) . $limit);


//		$query->sql("SELECT * FROM ".UT_DEV." WHERE parent >= '0' ORDER BY phbrand_id, model");

		for($i = 0; $i < $query->getQueryCount(); $i++) {
			$items["id"][] = $query->getQueryResult($i, "id");

			$items["values"][] =  $query->getQueryResult($i, "brand"). ", ".$query->getQueryResult($i, "model"); 

			$items["brand"][] = $query->getQueryResult($i, "brand");
			$items["model"][] = $query->getQueryResult($i, "model");

			$items["display"][] = $this->getDeviceProporty("display", $query->getQueryResult($i, "id"));
			$items["browser"][] = $this->getDeviceProporty("browser", $query->getQueryResult($i, "id"));
			$items["segment"][] = $this->getDeviceSegment($query->getQueryResult($i, "id"));

		}

		if(!count($items)) {
			return false;
		}
		else {
			return $items;
		}
	}

	/**
	* Get search items
	*
	* @uses Item::getItems()
	*/
	function getSearchItems() {
		$brand_id = Session::getSearch("brand_id");
		$contenttype_type_id = Session::getSearch("contenttype_type_id");
		$useragent_pattern = Session::getSearch("pattern");

		if($brand_id || $contenttype_type_id || $useragent_pattern) {
			return $this->getCleanupItems($brand_id, $contenttype_type_id, $useragent_pattern);
		}
		else {
			return $this->getItems(false, false, false, 20);
		}
	}


	/**
	* Search
	* Sets search values in session
	*/
	function search() {
		Session::setSearch("brand_id", getVar("brand_id"));
		Session::setSearch("contenttype_type_id", getVar("contenttype_type_id"));
		Session::setSearch("pattern", getVar("pattern"));
	}
	
	/**
	* Reset Search 
	* Resets search values in session
	*/
	function searchReset() {
		Session::resetSearch("brand_id");
		Session::resetSearch("contenttype_type_id");
		Session::resetSearch("pattern");
	}

	/**
	* Search form
	*
	* @return string HTML view
	*/
	function searchOptions() {
		global $HTML;

		$contenttypes = Generic::getItems(UT_BAS_CON, false, "name");
//		print_r($contenttypes["values"]);

		$contenttypes_type = array();
		$contenttypes_type["id"] = array();
		$contenttypes_type["values"] = array();

		foreach($contenttypes["values"] as $index => $value) {
			list($type) = explode("/", $value);

			if(!array_search($type, $contenttypes_type["values"])) {

				$contenttypes_type["values"][] = $type;
				$contenttypes_type["id"][] = $type;

			}
		}

//		print_r($contenttypes_type);

		$_ = '';
		$_ .= $HTML->head($this->translate("Search devices"));
		$_ .= $HTML->select($this->translate("Select brand"), "brand_id", Generic::getItems(UT_BAS_BRA, false, "name"), stringOr(Session::getSearch("brand_id")), array("", "-"), "Util.Ajax.submitContainer('container:item_search');");
		$_ .= $HTML->select($this->translate("Select missing contenttype"), "contenttype_type_id", $contenttypes_type, stringOr(Session::getSearch("contenttype_type_id")), array("", "-"), "Util.Ajax.submitContainer('container:item_search');");
		$_ .= $HTML->input($this->varnames["pattern"], "pattern", stringOr(Session::getSearch("pattern")));

		return $_;
	}



	function manualTask() {
		$query = new Query();

		print "do some manual task";

		print "<div>";

		// testing unique id
		// $query->sql("SELECT * FROM ".UT_DEV_USE);
		// for($i = 0; $i < $query->getQueryCount(); $i++) {
		// 
		// 	$id = $query->getQueryResult($i, "id");
		// 	$useragent = $query->getQueryResult($i, "useragent");
		// 
		// 	if(preg_match("/Android/", $useragent) && !preg_match("/crios|ipod|ipad|symbian|blackberry|fban|firefox/i", $useragent)) {
		// 
		// 		// HTCs
		// 		if(preg_match("/HTC/", $useragent)) {
		// 
		// 			if(preg_match("/Android 2.[23]{1}[^$]+HTC[\s_]{1}Desire[\s_]?S[^$]+AppleWebKit\/533/", $useragent)) {
		// 
		// 				print $useragent."<br>";
		// 			}
		// 
		// 
		// 		}
		// 
		// 	}
		// 
		// 
		// }
		print "</div>";



// 		// move wrong useragents from chrome 11 - correcting a serious mess
// 		if($query->sql("SELECT device_id FROM ".UT_DEV_USE." WHERE useragent = 'Chrome 11.0, Desktop'")) {
// 			$device_id = $query->getQueryResult(0, "device_id");
// 
// 			print "dev_id:" . $device_id."<br>";
// 
// 			if($query->sql("SELECT * FROM ".UT_DEV_USE." WHERE device_id = $device_id ORDER BY id desc")) {
// 
// //				for($i = 0; $i < 10; $i++) {
// 				for($i = 0; $i < $query->getQueryCount(); $i++) {
// 					$id = $query->getQueryResult($i, "id");
// 					$useragent = $query->getQueryResult($i, "useragent");
// 
// 					$this->deleteUseragent($id);
// 
// 					print $useragent."<br>";
// 
// 					// cannot be identified because match occurs because of mis-index
// 					// $identification = $this->identifyDevice($useragent, false);
// 					// print_r($identification);
// 					// if($identification && $identification["method"] == "unique") {
// 					// 	print "real:" . $useragent . "\n";
// 					// }
// 
// //					print $useragent."<br>";
// 				}
// 			}
// 		}
	}
}

?>