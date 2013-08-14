<?php
/**
* @package framework.devices
*/

include_once("device.view.class.php");
include_once("class/system/validator.class.php");

include_once("class/system/performance.class.php");


class Device extends DeviceView {
	
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
		$this->validator = new Validator($this);



		$this->perf = new Performance();



		parent::__construct();

		$this->varnames["model"] = "Model";
		$this->validator->rule("model", "txt", "Please enter a modelname");

		$this->varnames["brand_id"] = "Brand";
		$this->validator->rule("brand_id", "num", "please select a phone brand");

		$this->varnames["useragent"] = "Useragent";

		$this->varnames["contenttype"] = "Contenttype";


		$this->varnames["pattern"] = "Match pattern";

		
		$this->vars = getVars($this->varnames);
		
	}


	
	/**
	* Save new item, based on submitted values
	*
	* @return bool
	* @uses Message
	*/
	function saveItem() {
		if($this->validator->validateList("brand", "model")) {
			$vars = "DEFAULT";
			$vars .= ",'".$this->vars['model']."'";
			$vars .= ",".$this->vars['brand_id'];
			
			if(!$this->sql("SELECT * FROM ".$this->db." WHERE brand_id = '".$this->vars['brand_id']."' AND model = '".$this->vars['model']."'")) {
//				print "INSERT INTO ".$this->db." VALUES($vars)";
				if($this->sql("INSERT INTO ".$this->db." VALUES($vars)")) {
					$device_id = $this->getLastInsertId();
					if($this->vars['useragent']) {
						if($this->addUseragent($device_id, $this->vars['useragent'])) {

							$query = new Query();
							$query->sql("DELETE FROM ".UT_DEV_UNI." WHERE useragent = '".$this->vars['useragent']."'");

						}
					}

					messageHandler()->addStatusMessage("Item saved");
					return $device_id;
				}
				else {
					messageHandler()->addErrorMessage($this->dbError());
					return false;
				}
			}
			else {
				messageHandler()->addErrorMessage("This device allready exsist");
			}	
		}
		else {
			messageHandler()->addErrorMessage("Please complete missing information");
			return false;
		}
	}


	/**
	* Clone item, based on submitted values
	*
	* @return bool
	* @uses Message
	*/
	function cloneDevice($id, $useragent) {

		if($this->getItem($id)) {
			$model = $this->getQueryResult(0, "model");
			$brand_id = $this->getQueryResult(0, "brand_id");
			$contenttypes = $this->getContenttypes($id);

			$this->vars['model'] = $model." (clone:".time().")";
			$this->vars['brand_id'] = $brand_id;
			$this->vars['useragent'] = $useragent;

			$device_id = $this->saveItem();
			if($device_id) {
				$this->saveDeviceContenttypes($device_id, array_flip($contenttypes["id"]));
				return $device_id;
			}

			return false;
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
		$query = new Query();

		if($this->validator->validateList("brand", "model")) {

			$vars = "model = '".$this->vars['model']."'";
			$vars .= ",brand_id = '".$this->vars['brand_id']."'";
			//$vars .= ",memory = '".$this->vars['memory']."'";
			//$vars .= ",size_id = '".$this->vars['display']."'";
			//$vars .= ",wappush = '".$this->vars['wappush']."'";
			//$vars .= ",parent = '".$this->vars['type']."'";

//			print "UPDATE ".$this->db." SET $vars WHERE id = $id";

			if($query->sql("UPDATE ".$this->db." SET $vars WHERE id = $id")) {
				messageHandler()->addStatusMessage("Item Updated");
				Page::addLog("Device updated ($id)");

				$this->saveDeviceContenttypes($id, $this->vars['contenttype']);

				if($this->vars['useragent']) {
					$this->addUseragent($id, $this->vars['useragent']);
				}
//				$this->deleteItemInfo($id);
//				$this->saveItemInfo($id);
				return true;
			}
			else {
				messageHandler()->addStatusMessage($query->dbError());
				return false;
			}
		}
		else {
			messageHandler()->addErrorMessage("Please complete missing information");
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
//		$this->deleteItemInfo($id);
		if($this->sql("DELETE FROM ".$this->db." WHERE id = $id")) {
			messageHandler()->addStatusMessage("Item deleted");
			return true;
		}
		else {
			messageHandler()->addErrorMessage($this->dbError());
			return false;
		}
	}


	/**
	* Save contenttypes associatet with this device 
	*
	* @param int $device_id Device id
	* @param array $contenttypes array of contenttype ids
	* @return bool
	*/
	function saveDeviceContenttypes($device_id, $contenttypes) {

		$query = new Query();
		$query->sql("DELETE FROM ".UT_DEV_CON." WHERE device_id = $device_id");

		foreach($contenttypes as $contenttype_id => $value ) {
			$vars = "DEFAULT";
			$vars .= ",'".$device_id."'";
			$vars .= ",'".$contenttype_id."'";
			
			$query->sql("INSERT INTO ".UT_DEV_CON." VALUES($vars)");
		}
		return true;
	}

	function getUseragents($id) {
		$items = array();
		$query = new Query();
		$query->sql("SELECT id, useragent FROM ".UT_DEV_USE." WHERE device_id = ".$id);

		for($i = 0; $i < $query->getQueryCount(); $i++) {
			$items["id"][] = $query->getQueryResult($i, "id");
			$items["values"][] =  $query->getQueryResult($i, "useragent"); 
		}

		if(!count($items)) {
			return false;
		}
		else {
			return $items;
		}
	}


	function addUseragent($device_id, $useragent) {
//		print $device_id . ":" . $useragent;

		// quote issue ... might not be all resolved
		$useragent = str_replace('&quot;','"', $useragent);

		$query = new Query();
		if(!$query->sql("SELECT id FROM ".UT_DEV_USE." WHERE useragent = '$useragent'")) {

			$query->sql("INSERT INTO ".UT_DEV_USE." VALUES(DEFAULT, '$useragent', $device_id)");
			Page::addLog("Useragent added to ($device_id): $useragent");
			messageHandler()->addStatusMessage($this->translate("Useragent added"));
			return true;
		}

		messageHandler()->addErrorMessage($this->translate("Useragent already exists"));
		return false;
	}


	function deleteUseragent($useragent_id) {
		$query = new Query();
		
		if($query->sql("SELECT useragent, device_id FROM ".UT_DEV_USE." WHERE id = $useragent_id")) {
			$useragent = stripForDB($query->getQueryResult(0, "useragent"));

//			$useragent = $query->getQueryResult(0, "useragent");

			$device_id = $query->getQueryResult(0, "device_id");
			$query->sql("INSERT INTO ".UT_DEV_UNI." VALUES(DEFAULT, '$useragent', 'Orphaned', '".SITE_UID."', DEFAULT)");
			$query->sql("DELETE FROM ".UT_DEV_USE." WHERE id = $useragent_id");
			Page::addLog("Useragent deleted from ($device_id): $useragent");
			messageHandler()->addStatusMessage($this->translate("Useragent deleted"));
			return true;
		}

		messageHandler()->addErrorMessage($this->dbError());
		return false;

	}

	function deleteUnidentifiedUseragent($useragent_id) {
		$query = new Query();

		if($query->sql("SELECT useragent FROM ".UT_DEV_UNI." WHERE id = $useragent_id")) {
			$useragent = stripForDB($query->getQueryResult(0, "useragent"));
//			$useragent = $query->getQueryResult(0, "useragent");

//			print "DELETE FROM ".UT_DEV_UNI." WHERE useragent = '$useragent'<br>";
//			print "DELETE FROM ".UT_DEV_UNI." WHERE useragent = '".stripForDB($useragent)."'<br>";

			$query->sql("DELETE FROM ".UT_DEV_UNI." WHERE useragent = '$useragent'");
			Page::addLog("Unidentified useragent deleted: $useragent");
			
			messageHandler()->addStatusMessage($this->translate("Useragent deleted"));
			return true;
		}

		messageHandler()->addErrorMessage($this->dbError());
		return false;

	}

	/**
	* Get all contenttypes for this itemtype
	*
	* @param int $item_id mimetype id
	* @return array|false Item array or false on error
	*/
	function getContenttypes($device_id) {

		$query = new Query();
		$query->sql("SELECT 
				".UT_DEV_CON.".contenttype_id, 
				".UT_BAS_CON.".name, 
				".UT_BAS_CON.".contenttype
			 FROM 
				".UT_DEV_CON.", ".UT_BAS_CON." 
			WHERE
				".UT_DEV_CON.".contenttype_id = ".UT_BAS_CON.".id AND
				".UT_DEV_CON.".device_id = '".$device_id."'
			ORDER BY 
				".UT_BAS_CON.".name, 
				".UT_BAS_CON.".contenttype"
		);
 
		for($i = 0; $query->getQueryResult($i, "contenttype_id"); $i++) { 
			$contenttypes["id"][$i] = $query->getQueryResult($i, "contenttype_id");
			$contenttypes["values"][$i] = $query->getQueryResult($i, "name");
			$contenttypes["contenttype"][$i] = $query->getQueryResult($i, "contenttype");
		}

		if(isset($contenttypes)) {
			return $contenttypes;
		}
		else {
			return false;
		}
	}




	/**
	* Get all undentified devices
	*
	*/
	function getUnidentifiedDevices() {

//		$this->perf->mark("u-device query u-devices");

		$query = new Query();
//		$query->sql("SELECT * FROM ".UT_DEV_UNI." GROUP BY useragent ORDER BY timestamp DESC LIMIT 0,25");
		// make sure latest useragent entry is used to order list
		$query->sql("SELECT *, COUNT(*) AS visits, MAX(timestamp) as lastentry FROM ".UT_DEV_UNI." GROUP BY useragent ORDER BY lastentry DESC");

		$items = array();

//		$this->perf->mark("u-device loop results");

		for($i = 0; $i < $query->getQueryCount(); $i++) {

//			$this->perf->mark("u-device loop result: $i");

			$items["id"][] = $query->getQueryResult($i, "id");
			$items["useragent"][] = $query->getQueryResult($i, "useragent");
			$items["timestamp"][] = $query->getQueryResult($i, "lastentry");

			$items["visits"][] = $query->getQueryResult($i, "visits");
		}
		
		if(count($items)) {
			return $items;
		}
		return false;
	}

	/**
	* Get all undentified devices based on pattern
	*
	*/
	function getUnidentifiedDevicesByPattern($pattern) {
		$query = new Query();
		$query->sql("SELECT id, useragent FROM ".UT_DEV_UNI." WHERE useragent LIKE '%$pattern%' GROUP BY useragent");

		$items = array();

		for($i = 0; $i < $query->getQueryCount(); $i++) {
			$items["id"][] = $query->getQueryResult($i, "id");
			$items["useragent"][] = $query->getQueryResult($i, "useragent");
		}

		if(count($items)) {
			return $items;
		}
		return false;
	}

	function getUnidentifiedDevicesByUnique() {

//		$this->perf->mark("u-device xref", true);

		$items = false;
		$query = new Query();

		$query_ua_exists = new Query();
		$query->sql("SELECT * FROM ".UT_DEV_UNI." GROUP BY useragent ORDER BY timestamp DESC");

		for($i = 0; $i < $query->getQueryCount(); $i++) {

			$useragent = $query->getQueryResult($i, "useragent");

			// check for unique matches (that could have been added after the visit)
			$identification = $this->identifyDevice($useragent, false);

//			$this->perf->mark("xref guessed result: $i");

			if($identification && $identification["method"] && $identification["method"] == "unique_id") {

				$items["id"][] = $query->getQueryResult($i, "id");
				$items["useragent"][] = $query->getQueryResult($i, "useragent");

			}
		}

		return $items;
	}


	function getUnidentifiedDevicesByUniqueTest() {

//		$this->perf->mark("u-device xref", true);

		$items = false;
		$query = new Query();

		$query_ua_exists = new Query();
		$query->sql("SELECT * FROM ".UT_DEV_UNI." GROUP BY useragent ORDER BY timestamp DESC");

		for($i = 0; $i < $query->getQueryCount(); $i++) {

			$useragent = $query->getQueryResult($i, "useragent");

			// check for unique matches (that could have been added after the visit)
			$identification = $this->identifyDevice($useragent, false);

//			$this->perf->mark("xref guessed result: $i");

			if($identification && $identification["method"] && $identification["method"] == "unique_id_test") {

				$items["id"][] = $query->getQueryResult($i, "id");
				$items["useragent"][] = $query->getQueryResult($i, "useragent");

			}
		}

		return $items;
	}

	/**
	* cross-reference all unidentified useragents with known devices to automatically eliminate useragents which have been identified since first visit
	*
	*/
	function xRefUnidentifiedDevices() {

//		$this->perf->mark("u-device xref", true);

		$items = false;
		$query = new Query();

		$query_ua_exists = new Query();
		$query->sql("SELECT * FROM ".UT_DEV_UNI." GROUP BY useragent ORDER BY timestamp DESC");

//		$this->perf->mark("xref: SELECT * FROM ".UT_DEV_UNI." GROUP BY useragent ORDER BY timestamp DESC");


		for($i = 0; $i < $query->getQueryCount(); $i++) {

			$useragent = $query->getQueryResult($i, "useragent");

//			$this->perf->mark("xref result: $i : $useragent");

			// check for unique matches (that could have been added after the visit)
			$matches = $this->guessDevice(str_replace('&quot;','"', $useragent));


//			$this->perf->mark("xref guessed result: $i");

			if($matches["identified"]) {

//				$query_ua_exists->sql("DELETE FROM ".UT_DEV_UNI." WHERE useragent = '".str_replace('&quot;','"', $useragent)."'");

				Page::addLog("UUA DELETED: (".array_pop($matches["match"]).") ".str_replace('&quot;','"', $useragent));

				$items = true;
				messageHandler()->addErrorMessage("UA deleted: $useragent");
			}
		}

		if(!$items) {
			messageHandler()->addStatusMessage("No known UA found");
		}
		return true;
	}

	/**
	* Add group of useragents to device
	* @param $unidentified_device_id Array of useragents to be added
	* @param $device_id ID of device to add useragents to
	*/
	function addGroupToDevice($unidentified_device_id, $device_id) {
		$query = new Query();

		foreach($unidentified_device_id as $unidentified_useragent_id => $value) {

			// get useragent
			if($query->sql("SELECT useragent FROM ".UT_DEV_UNI." WHERE id = $unidentified_useragent_id")) {
//				$useragent = $query->getQueryResult(0, "useragent");
				$useragent = stripForDB($query->getQueryResult(0, "useragent"));

				// add useragent
				$this->addUseragent($device_id, $useragent);

				// delete unidentified useragent
				$query->sql("DELETE FROM ".UT_DEV_UNI." WHERE useragent = '$useragent'");
			}

		}
	}

	/**
	* Get undentified device
	*
	*/
	function getUnidentifiedDevice($id) {
		$query = new Query();

		if($query->sql("SELECT useragent FROM ".UT_DEV_UNI." WHERE id = $id")) {

			$items = array();
			$useragent = stripForDB($query->getQueryResult(0, "useragent"));

			// print "#" . $useragent . "#<br>";
			// print "#" . stripForDB($useragent) . "#<br>";
			// print "#" . "SELECT * FROM ".UT_DEV_UNI." WHERE useragent = '$useragent' ORDER BY timestamp DESC" . "#<br>";

			$query->sql("SELECT * FROM ".UT_DEV_UNI." WHERE useragent = '$useragent' ORDER BY timestamp DESC");

//			$query->sql("SELECT * FROM ".UT_DEV_UNI." WHERE useragent = '".str_replace('&quot;','"', $useragent)."' ORDER BY timestamp DESC");
			for($i = 0; $i < $query->getQueryCount(); $i++) {
				$items["id"][] = $query->getQueryResult($i, "id");
				$items["useragent"][] = $query->getQueryResult($i, "useragent");
				$items["header"][] = $query->getQueryResult($i, "header");
				$items["site_id"][] = $query->getQueryResult($i, "site_id");
				$items["timestamp"][] = $query->getQueryResult($i, "timestamp");
			}

			if(count($items)) {
				return $items;
			}
			
		}
		return false;
	}

	/**
	* Get selected item
	* Makes query result available
	*
	* @param int $id Item id
	* @return bool
	*/
	function getItem($id) {
		return $this->sql("SELECT ".UT_DEV.".model, ".UT_BAS_BRA.".name as brand, ".UT_BAS_BRA.".id as brand_id FROM ".UT_DEV.", ".UT_BAS_BRA." WHERE ".UT_DEV.".id = ".$id." AND ".UT_DEV.".brand_id = ".UT_BAS_BRA.".id");
	}

	/**
	* Get search items
	*
	* @uses Item::getItems()
	*/
	function getSearchItems() {
		$brand_id = Session::getSearch("brand_id");
		$contenttype_id = Session::getSearch("contenttype_id");
		$useragent_pattern = Session::getSearch("pattern");

		if($brand_id || $contenttype_id || $useragent_pattern) {
			return $this->getItems($brand_id, $contenttype_id, $useragent_pattern);
		}
		else {
			return $this->getItems(false, false, false, 20);
		}
	}


	function addToDevice($unidentified_device_id, $device_id) {
		$query = new Query();

		if($query->sql("SELECT useragent FROM ".UT_DEV_UNI." WHERE id = $unidentified_device_id")) {
//			$useragent = $query->getQueryResult(0, "useragent");
			$useragent = stripForDB($query->getQueryResult(0, "useragent"));
			$this->addUseragent($device_id, $useragent);

			$query->sql("DELETE FROM ".UT_DEV_UNI." WHERE useragent = '$useragent'");
		}
	}

	/**
	* Guessing an unidentified device
	* @return Array with any possible matches
	*/
	function guessDevice($useragent) {
		$query = new Query();
		$matches = array();
		$matches["identified"] = false;

		$matches["match_by"][] = "Current best match";
		$matches["match"][] = "";

		$device_id = $this->identifyDevice($useragent, false);

		$identification = $this->identifyDevice($useragent, false);
		if($identification && isset($identification["device_id"])) {

			$matches["match_by"][] = $identification["method"] . ($identification["method"] == "guess" ? " (".$identification["guess"].")" : "");
			$matches["match"][] = $identification["device_id"];

//			$device = $deviceClass->getDeviceBase($identification["device_id"]);

		}
		// if($device_id) {
		// 	$matches["match_by"][] = $useragent;
		// 	$matches["match"][] = $device_id;
		// }



		// split by whitespace from end
		$matches["match_by"][] = "remove by whitespace from end";
		$matches["match"][] = "";

		$partial_useragent = $useragent;
		while($partial_useragent = implode(explode(" ", $partial_useragent, -1), " ")) {
//			Page::addLog("Attempting: '%$partial_useragent%'");

			if($query->sql("SELECT device_id FROM ".UT_DEV_USE." WHERE useragent LIKE '%$partial_useragent%'")) {
				$device_id = $query->getQueryResult(0, "device_id");
//				Page::addLog("Partial match found: ($device_id) ".$this->getItemName($device_id).", '%$partial_useragent%'");
//				if(array_search($device_id, $matches) === false) {
					$matches["match_by"][] = "'%$partial_useragent%'";
					$matches["match"][] = $device_id;
					break;
//				}
			}
			if(strpos($partial_useragent, " ") === false) {break;}
		}


		// split by dots from end
		$matches["match_by"][] = "remove by dot from end";
		$matches["match"][] = "";

		$partial_useragent = $useragent;
		while($partial_useragent = implode(explode(".", $partial_useragent, -1), ".")) {
//			Page::addLog("Attempting: '%$partial_useragent%'");

			if($query->sql("SELECT device_id FROM ".UT_DEV_USE." WHERE useragent LIKE '%$partial_useragent%'")) {
				$device_id = $query->getQueryResult(0, "device_id");
//				Page::addLog("Partial match found: ($device_id) ".$this->getItemName($device_id).", '%$partial_useragent%'");
//				if(array_search($device_id, $matches) === false) {
					$matches["match_by"][] = "'%$partial_useragent%'";
					$matches["match"][] = $device_id;
					break;
//				}
			}
			if(strpos($partial_useragent, ".") === false) {break;}
		}


		// split by slash from end
		$matches["match_by"][] = "remove by slash from end";
		$matches["match"][] = "";

		$partial_useragent = $useragent;
		while($partial_useragent = implode(explode("/", $partial_useragent, -1), "/")) {
//			Page::addLog("Attempting: '%$partial_useragent%'");

			if($query->sql("SELECT device_id FROM ".UT_DEV_USE." WHERE useragent LIKE '%$partial_useragent%'")) {
				$device_id = $query->getQueryResult(0, "device_id");
//				Page::addLog("Partial match found: ($device_id) ".$this->getItemName($device_id).", '%$partial_useragent%'");
//				if(array_search($device_id, $matches) === false) {
					$matches["match_by"][] = "'%$partial_useragent%'";
					$matches["match"][] = $device_id;
					break;
//				}
			}
			if(strpos($partial_useragent, "/") === false) {break;}
		}


		// split by slash from beginging
		$matches["match_by"][] = "remove by slash from begining";
		$matches["match"][] = "";

		$partial_useragent = $useragent;
		while($partial_useragent = substr($partial_useragent, strpos($partial_useragent, "/")+1)) {
//			Page::addLog("Attempting: '%$partial_useragent%'");

			if($query->sql("SELECT device_id FROM ".UT_DEV_USE." WHERE useragent LIKE '%$partial_useragent%'")) {
				$device_id = $query->getQueryResult(0, "device_id");
//				Page::addLog("Partial match found: ($device_id) ".$this->getItemName($device_id).", '%$partial_useragent%'");
//				if(array_search($device_id, $matches) === false) {
					$matches["match_by"][] = "'%$partial_useragent%'";
					$matches["match"][] = $device_id;
					break;
//				}
			}
			// avoid endles loop
			if(strpos($partial_useragent, "/") === false) {break;}
		}


		/*
		// interchanging end/beginning ... one from end, one from beginning, one from end etc
		$matches["match_by"][] = "remove by / interchanging end/beginning";
		$matches["match"][] = "";

		$partial_useragent = $useragent;
		while($partial_useragent = implode(explode("/", $partial_useragent, -1), "/")) {
//			Page::addLog("Attempting: '%$partial_useragent%'");

			// end
			print "SELECT device_id FROM ".UT_DEV_USE." WHERE useragent LIKE '%$partial_useragent%'<br>";
			if($query->sql("SELECT device_id FROM ".UT_DEV_USE." WHERE useragent LIKE '%$partial_useragent%'")) {
				$device_id = $query->getQueryResult(0, "device_id");
//				Page::addLog("Partial match found: ($device_id) ".$this->getItemName($device_id).", '%$partial_useragent%'");
//				if(array_search($device_id, $matches) === false) {
					$matches["match_by"][] = "'%$partial_useragent%'";
					$matches["match"][] = $device_id;
//					break;
//				}
			}
			// avoid endles loop
			if(strpos($partial_useragent, "/") === false) {break;}

			// beginning
			$partial_useragent = substr($partial_useragent, strpos($partial_useragent, "/")+1);
			print "SELECT device_id FROM ".UT_DEV_USE." WHERE useragent LIKE '%$partial_useragent%'<br>";
			if($query->sql("SELECT device_id FROM ".UT_DEV_USE." WHERE useragent LIKE '%$partial_useragent%'")) {
				$device_id = $query->getQueryResult(0, "device_id");
//				Page::addLog("Partial match found: ($device_id) ".$this->getItemName($device_id).", '%$partial_useragent%'");
//				if(array_search($device_id, $matches) === false) {
					$matches["match_by"][] = "'%$partial_useragent%'";
					$matches["match"][] = $device_id;
					break;
//				}
			}
			// avoid endles loop
			if(strpos($partial_useragent, "/") === false) {break;}
		}
		*/



		return $matches;
	}

}

?>