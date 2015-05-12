<?php
/**
* @package hvidevarehuset.items
* This file contains presentation maintenance functionality
*/

class TypeDevice extends Itemtype {

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {

		parent::__construct(get_class());


		// itemtype database
		$this->db = SITE_DB.".item_device";
		$this->db_useragents = SITE_DB.".device_useragents";
		$this->db_markers = SITE_DB.".device_markers";
		$this->db_exceptions = SITE_DB.".device_exceptions";


		$this->db_unidentified = SITE_DB.".unidentified_useragents";

		// Name
		$this->addToModel("published_at", array(
			"type" => "datetime",
			"label" => "Released (yyyy-mm)",
			"pattern" => "^[\d]{4}-[\d]{2}[0-9\-\/ \:]*$",
			"hint_message" => "Date device was first release to the market", 
			"error_message" => "Date must be of format (yyyy-mm)"
		));

		// Name
		$this->addToModel("name", array(
			"type" => "string",
			"label" => "Name",
			"required" => true,
			"unique" => $this->db,
			"hint_message" => "Name of the device", 
			"error_message" => "Name must to be unique."
		));

		// Description
		$this->addToModel("description", array(
			"type" => "text",
			"label" => "Description/AKA",
			"hint_message" => "Devices may have many names, especially when released under Network operator subbrands like O2, Orange, Vodafone or T-Mobile. Add these names here. You can also add any interesting details about the device."
		));

		// Useragent
		$this->addToModel("useragent", array(
			"type" => "text",
			"label" => "Useragent",
			"hint_message" => "Device useragent. Only add actual useragents."
		));

		// Markers (unique markers for device)
		$this->addToModel("marker", array(
			"type" => "string",
			"label" => "Marker",
			"hint_message" => "Device marker. Always test your markers."
		));

		// Exceptions (unique exections for device)
		$this->addToModel("exception", array(
			"type" => "string",
			"label" => "Exception",
			"hint_message" => "Device exception. Always test your exceptions."
		));

		// // Tags
		// $this->addToModel("tags", array(
		// 	"type" => "tags",
		// 	"label" => "Tag",
		// 	"hint_message" => "Start typing to filter options.",
		// 	"error_message" => "Must be correct Tag format. A correct tag has this format: context:value."
		// ));

	}



	// TODO: make get always return segment as well
	// remove segment logic from identification, and just get device (which will then always have segment)


	/**
	* Get item
	*/
	function get($item_id) {
		$query = new Query();

		if($query->sql("SELECT * FROM ".$this->db." WHERE item_id = $item_id")) {
			$item = $query->result(0);
			unset($item["id"]);


			// get segment for device
			if(!isset($item["segment"])) {
				$item["segment"] = $this->segment($item_id);
			}


			// useragents
			$item["useragents"] = false;
			// get useragents
			if($query->sql("SELECT * FROM ".$this->db_useragents." WHERE item_id = $item_id")) {

				$useragents = $query->results();
				foreach($useragents as $i => $useragent) {
					$item["useragents"][$i]["id"] = $useragent["id"];
					$item["useragents"][$i]["useragent"] = $useragent["useragent"];
				}
			}


			// markers
			$item["markers"] = false;
			// get markers
			if($query->sql("SELECT * FROM ".$this->db_markers." WHERE item_id = $item_id")) {

				$markers = $query->results();
				foreach($markers as $i => $marker) {
					$item["markers"][$i]["id"] = $marker["id"];
					$item["markers"][$i]["marker"] = $marker["marker"];
				}
			}


			// exceptions
			$item["exceptions"] = false;
			// get exceptions
			if($query->sql("SELECT * FROM ".$this->db_exceptions." WHERE item_id = $item_id")) {

				$exceptions = $query->results();
				foreach($exceptions as $i => $exception) {
					$item["exceptions"][$i]["id"] = $exception["id"];
					$item["exceptions"][$i]["exception"] = $exception["exception"];
				}
			}


			return $item;
		}
		else {
			return false;
		}
	}


	// get segment for device id - or find segment for similar device
	function segment($device_id, $tags = false) {
		$query = new Query();
		$IC = new Items();

		// tags not passed as parameter, get them
		if(!$tags) {
			$tags = $IC->getTags(array("item_id" => $device_id));
		}

		// look for segment in device tags
		if($tags) {
			foreach($tags as $tag) {
				if($tag["context"] == "segment") {
					return $tag["value"];
				}
			}


			// look for inherited segment tag based on devices with similar tags

			// priority of contexts - defines order of tags, while searching for tags-based matches
			// prioritize type:parent devices (parent browser devices)
			// for each attempt, one element will be removed from the end of tags array
			$context_priority = array("browser", "type", "display", "pointing", "system", "brand");
			$tag_string_array = array();


			// add seach tags in order of priority
			while($context_priority) {

				// add existing tags
				foreach($tags as $index => $tag) {

					// the right tag
					if($tag["context"] == $context_priority[0]) {
						// add tag to stack
						array_push($tag_string_array, $tag["context"].":".$tag["value"]);
						// remove tag
						array_splice($tags, $index, 0);

						// add type tag right after browser
						if($tag["context"] == "browser") {
							array_push($tag_string_array, "type:parent");
						}
					}
				}
				// next priority
				array_shift($context_priority);
			}
			// merge remaining tags with prioritized
			array_merge($tag_string_array, $tags);


			// look for match, while removing one tag at the time, hopefully finding match
			while(count($tag_string_array) > 0) {
				// get matching devices
				$devices = $IC->getItems(array("tags" => implode(";", $tag_string_array)));
	//			print_r($devices);
				if($devices) {

					$tags = $IC->getTags(array("item_id" => $devices[0]["id"], "context" => "segment"));
					if($tags) {
	//					print "segment:" . $tags[0]["value"];
						return $tags[0]["value"];
					}

				}
				array_pop($tag_string_array);
			}

//			print_r($tag_string_array);
		}

		// not able to find any segment for this device
		return false;
	}

	// custom loopback function


	// clone device, including tags
	function cloneDevice($action) {

		$IC = new Items();
		$this->getPostedEntities();

		$query = new Query();
		

		if(count($action) == 2) {

			$device = $IC->getItem(array("id" => $action[1], "extend" => array("tags" => true)));

			// create new device
			$sql = "INSERT INTO ".UT_ITEMS." VALUES(DEFAULT, DEFAULT, 1, 'device', DEFAULT, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '".$device["published_at"]."')";
//			print $sql;
			$query->sql($sql);
			$new_id = $query->lastInsertId();

			// insert device
			$sql = "INSERT INTO ".$this->db." VALUES(DEFAULT, $new_id, '".$device["name"]." (cloned)', '".$device["description"]."')";
//			print $sql;
			if($query->sql($sql)) {

				// add tags
				if($device["tags"]) {
					foreach($device["tags"] as $tag) {

						unset($_POST);
						$_POST["tags"] = $tag["id"];
						$this->addTag(array("addTags", $new_id));

					}
				}

				message()->addMessage("Device cloned");

				// get and return new device (id will be used to redirect to new device page)
				$item = $IC->getItem(array("id" => $new_id, "extend" => array("tags" => true)));
				return $item;
			}
		}

		message()->addMessage("Device could not be cloned", array("type" => "error"));
		return false;
	}


	// merge device, copy all user-agents to new device and delete original
	// mergeDevice/#device_id_source#/#device_id_destination#
	function mergeDevice($action) {

		$IC = new Items();
		$this->getPostedEntities();

		$query = new Query();


		if(count($action) == 3) {

			$device_id_source = $action[1];
			$device_id_destination = $action[2];

			// get source device details
			$device = $this->get($device_id_source);

			// switch useragent to new device
			if($device["useragents"]) {
				// copy useragents
				foreach($device["useragents"] as $useragent) {

					$sql = "UPDATE ".$this->db_useragents." SET item_id = $device_id_destination WHERE id = ".$useragent["id"];
					$query->sql($sql);

				}
			}

			// switch markers to new device
			if($device["markers"]) {
				// copy useragents
				foreach($device["markers"] as $marker) {

					$sql = "UPDATE ".$this->db_markers." SET item_id = $device_id_destination WHERE id = ".$marker["id"];
					$query->sql($sql);

				}
			}

			// switch exceptions to new device
			if($device["exceptions"]) {
				// copy useragents
				foreach($device["exceptions"] as $exception) {

					$sql = "UPDATE ".$this->db_exceptions." SET item_id = $device_id_destination WHERE id = ".$exception["id"];
					$query->sql($sql);

				}
			}


			// delete source device
			$this->delete(array("delete", $device_id_source));


			message()->addMessage("Devices merged");
			return $device_id_destination;

		}

		message()->addMessage("Device could not be merged", array("type" => "error"));
		return false;
	}



	// MARKERS

	// add marker - 3 parameters exactly
	// /janitor/device/addMarker/#item_id#
	function addMarker($action) {

		$marker = getPost("marker");

		if(count($action) == 2 && $marker) {
			$item_id = $action[1];

			$query = new Query();
			$query->checkDbExistance($this->db_markers);

			if($query->sql("INSERT INTO ".$this->db_markers." VALUES(DEFAULT, ".$item_id.", '".$marker."')")) {

				// update modified time of device
				$query->sql("UPDATE ".UT_ITEMS." SET modified_at=CURRENT_TIMESTAMP WHERE id = ".$item_id);

				message()->addMessage("Marker added");
				return true;
			}
		}

		message()->addMessage("Marker could not be added", array("type" => "error"));
		return false;
	}

	// delete device marker - 3 parameters exactly
	// /janitor/device/deleteMarker/#item_id#/#marker_id#
	function deleteMarker($action) {

		if(count($action) == 3) {

			$item_id = $action[1];
			$marker_id = $action[2];

			$query = new Query();

			$sql = "DELETE FROM ".$this->db_markers." WHERE item_id = ".$item_id." AND id = '".$marker_id."'";
			if($query->sql($sql)) {

				// update modified time of device
				$query->sql("UPDATE ".UT_ITEMS." SET modified_at=CURRENT_TIMESTAMP WHERE id = ".$item_id);

				message()->addMessage("Marker deleted");
				return true;
			}
		}

		message()->addMessage("Marker could not be deleted", array("type" => "error"));
		return false;
	}



	// EXCEPTIONS

	// add exception - 3 parameters exactly
	// /janitor/device/addException/#item_id#
	function addException($action) {

		$exception = getPost("exception");

		if(count($action) == 2 && $exception) {
			$item_id = $action[1];

			$query = new Query();
			$query->checkDbExistance($this->db_exceptions);

			if($query->sql("INSERT INTO ".$this->db_exceptions." VALUES(DEFAULT, ".$item_id.", '".$exception."')")) {

				// update modified time of device
				$query->sql("UPDATE ".UT_ITEMS." SET modified_at=CURRENT_TIMESTAMP WHERE id = ".$item_id);

				message()->addMessage("Exception added");
				return true;
			}
		}

		message()->addMessage("Exception could not be added", array("type" => "error"));
		return false;
	}

	// delete device exception - 3 parameters exactly
	// /janitor/device/deleteException/#item_id#/#exception_id#
	function deleteException($action) {

		if(count($action) == 3) {

			$item_id = $action[1];
			$exception_id = $action[2];

			$query = new Query();

			$sql = "DELETE FROM ".$this->db_exceptions." WHERE item_id = ".$item_id." AND id = '".$exception_id."'";
			if($query->sql($sql)) {

				// update modified time of device
				$query->sql("UPDATE ".UT_ITEMS." SET modified_at=CURRENT_TIMESTAMP WHERE id = ".$item_id);

				message()->addMessage("Exception deleted");
				return true;
			}
		}

		message()->addMessage("Exception could not be deleted", array("type" => "error"));
		return false;
	}



	// USERAGENTS

	// add useragent - 3 parameters exactly
	// /janitor/device/addUseragent/#item_id#
	function addUseragent($action) {

		if(count($action) == 2) {
			$item_id = $action[1];

			$useragent = getPost("useragent");
			$query = new Query();

			if($query->sql("INSERT INTO ".$this->db_useragents." VALUES(DEFAULT, ".$item_id.", '".$useragent."')")) {

				// update modified time of device
				$query->sql("UPDATE ".UT_ITEMS." SET modified_at=CURRENT_TIMESTAMP WHERE id = ".$item_id);

				message()->addMessage("Useragent added");
				return true;
			}
		}

		message()->addMessage("Useragent could not be added", array("type" => "error"));
		return false;
	}

	// delete device useragent - 3 parameters exactly
	// /janitor/device/deleteUseragent/#item_id#/#useragent_id#
	function deleteUseragent($action) {

		if(count($action) == 3) {

			$query = new Query();
			$sql = "SELECT useragent FROM ".$this->db_useragents." WHERE id = '".$action[2]."'";
			$query->sql($sql);

			$ua = $query->result(0, "useragent");

			$sql = "DELETE FROM ".$this->db_useragents." WHERE item_id = ".$action[1]." AND id = '".$action[2]."'";
			if($query->sql($sql)) {

				$sql = "INSERT INTO ".$this->db_unidentified." VALUES(DEFAULT, '$ua', 'orphanaged', '', DEFAULT)";
				$query->sql($sql);

				// update modified time of device
				$query->sql("UPDATE ".UT_ITEMS." SET modified_at=CURRENT_TIMESTAMP WHERE id = ".$action[1]);

				message()->addMessage("Useragent deleted");
				return true;
			}
		}

		message()->addMessage("Useragent could not be deleted", array("type" => "error"));
		return false;
	}



	// TEST MARKERS FOR DEVICE

	// test device markers
	// testMarkers/#device_id#
	function testMarkers($action) {
	
		if(count($action) == 2) {

			$device_id = $action[1];
			$device = $this->get($device_id);

			// compile regular expression

			if($device["markers"]) {

				$markers = array();
				$reg_exp_pos = "";
				$reg_exp_neg = "";

				foreach($device["markers"] as $marker) {
					array_push($markers, $marker["marker"]);
				}

				$reg_exp_pos = implode($markers, "|");


				if($device["exceptions"]) {
					$exceptions = array();

					foreach($device["exceptions"] as $exception) {
						array_push($exceptions, $exception["exception"]);
					}

					$reg_exp_neg = implode($exceptions, "|");
				}

				$not_matched_useragents = array();

				// first run test on current device to identify holes in identification
				foreach($device["useragents"] as $useragent) {
					if(!(preg_match("/".$reg_exp_pos."/i", $useragent["useragent"]) && (!$reg_exp_neg || !preg_match("/".$reg_exp_neg."/i", $useragent["useragent"])))) {
						array_push($not_matched_useragents, $useragent["useragent"]);
					}
				}

				// get all useragents
				$query = new Query();

				$sql = "SELECT * FROM ".$this->db_useragents;
				$query->sql($sql);
				$all_useragents = $query->results();
				$bad_matched_useragents = array();

				foreach($all_useragents as $useragent) {
					if($useragent["item_id"] != $device_id && (preg_match("/".$reg_exp_pos."/i", $useragent["useragent"]) && (!$reg_exp_neg || !preg_match("/".$reg_exp_neg."/i", $useragent["useragent"])))) {

						if(!isset($bad_matched_useragents[$useragent["item_id"]])) {
							$bad_matched_useragents[$useragent["item_id"]] = array();

							$query->sql("SELECT * FROM ".$this->db." WHERE item_id = ".$useragent["item_id"]);
							$name = $query->result(0, "name");

							$bad_matched_useragents[$useragent["item_id"]]["name"] = $name;
							$bad_matched_useragents[$useragent["item_id"]]["useragents"] = array();
						}
						array_push($bad_matched_useragents[$useragent["item_id"]]["useragents"], $useragent["useragent"]);

					}
				}

				$result = array($not_matched_useragents, $bad_matched_useragents);

//				print_r($result);


				return $result;
			}


		
		}

		message()->addMessage("Device could not be tested", array("type" => "error"));
		return false;

	}


	// full LIKE search on name, description and useragent
	function searchDevices($_options = false) {

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "search_string"   : $search_string   = $_value; break;
					case "tags"            : $tags            = $_value; break;
				}

			}
		}

		$query = new Query();

		$SELECT = array();
		$FROM = array();
		$LEFTJOIN = array();
		$WHERE = array();
		$GROUP_BY = "";
		$HAVING = "";
		$ORDER = array();


		$SELECT[] = "items.id";
		$SELECT[] = "items.sindex";
		$SELECT[] = "items.status";
		$SELECT[] = "items.published_at";

	 	$FROM[] = UT_ITEMS." as items";


		$WHERE[] = "items.itemtype = 'device'";


		if(isset($tags) && is_string($tags)) {

			$LEFTJOIN[] = UT_TAGGINGS." as taggings ON taggings.item_id = items.id";
			$LEFTJOIN[] = UT_TAG." as tags ON tags.id = taggings.tag_id";

			$tag_array = explode(";", $tags);
			$tag_sql = "";

			foreach($tag_array as $tag) {
//				$exclude = false;
				// tag id
				if($tag) {

					// firgure the nature of tha tag, positive or negative
					$exclude = false;

					// negative tag, exclude
					if(substr($tag, 0, 1) == "!") {
						$tag = substr($tag, 1);
						$exclude = true;
					}

					// if tag has both context and value
					if(strpos($tag, ":")) {
						list($context, $value) = explode(":", $tag);
					}
					// only context present, value false
					else {
						$context = $tag;
						$value = false;
					}

					if($context || $value) {
//						AND tags.context = 'brand' AND tags.value = 'Mozilla'

						// Negative !tag
						// TODO: not yet sure how to make negative query in best possible way
						if($exclude) {
							// ($context ? " AND tags.context = '$context'" : "") . ($value ? " AND tags.value = '$value'" : "")
							// $WHERE[] = "tags.context = 'brand' AND tags.value = 'Mozilla'";
							// $WHERE[] = "items.id NOT IN (SELECT item_id FROM ".UT_TAGGINGS." as item_tags, ".UT_TAG." as tags WHERE item_tags.tag_id = tags.id" . ($context ? " AND tags.context = '$context'" : "") . ($value ? " AND tags.value = '$value'" : "") . ")";
						}
						// positive tag
						else {
							if($context && $value) {
								$tag_sql .= ($tag_sql ? " OR " : "") .  "tags.context = '$context' AND tags.value = '$value'";
								

//								$WHERE[] = "tags.context = '$context'";
							}
							else if($context) {
								$tag_sql .= ($tag_sql ? " OR " : "") .  "tags.context = '$context'";
							}
							// if($value) {
							// 	$WHERE[] = "tags.value = '$value'";
							// }
						}
					}
				}
			}
			$WHERE[] = "(".$tag_sql.")";
			$HAVING = "count(*) = ".count($tag_array);
		}
	 


		// TODO: TEST IMPLEMENTING THIS IN GLOBAL getItems for extended search
		if(isset($search_string) && $search_string) {

			$LEFTJOIN[] = $this->db." as device ON device.item_id = items.id";
			$LEFTJOIN[] = $this->db_useragents." as ua ON ua.item_id = items.id";

			$WHERE[] = "(device.name LIKE '%$search_string%' OR device.description LIKE '%$search_string%' OR ua.useragent LIKE '%$search_string%')";
		}


		$GROUP_BY = "items.id";

		$ORDER[] = "items.published_at DESC";



		$items = array();


//		print $query->compileQuery($SELECT, $FROM, array("LEFTJOIN" => $LEFTJOIN, "WHERE" => $WHERE, "HAVING" => $HAVING, "GROUP_BY" => $GROUP_BY, "ORDER" => $ORDER));
//		return array();
		$query->sql($query->compileQuery($SELECT, $FROM, array("LEFTJOIN" => $LEFTJOIN, "WHERE" => $WHERE, "HAVING" => $HAVING, "GROUP_BY" => $GROUP_BY, "ORDER" => $ORDER)));
		for($i = 0; $i < $query->count(); $i++){

			$item = array();

			$item["id"] = $query->result($i, "id");
			$item["itemtype"] = "device";
			$item["sindex"] = $query->result($i, "sindex");
			$item["status"] = $query->result($i, "status");
			$item["published_at"] = $query->result($i, "published_at");

			$items[] = $item;
		}

		return $items;
	}


	// delete unidentified useragent
	// /janitor/device/deleteUnidentified/#ua_id#
	function deleteUnidentified($action) {
		
		// check parameter count
		if(count($action) == 2) {
			$query = new Query();

			$sql = "SELECT useragent FROM ".$this->db_unidentified." WHERE id = ".$action[1];
			if($query->sql($sql)) {

				$ua = $query->result(0, "useragent");
				$sql = "DELETE FROM ".$this->db_unidentified." WHERE useragent = '$ua'";
//				print $sql."\n";
				if($query->sql($sql)) {

					message()->addMessage("Useragent ".$action[1].", deleted");
					return true;

				}
			}
		}

		message()->addMessage("Useragent ".$action[1].", could not be deleted", array("type" => "error"));
		return false;
	}


	// add unidentified useragent to device
	// /janitor/device/addUnidentifiedToDevice/#device_id#/#unidentified_ua_id#
	function addUnidentifiedToDevice($action) {

		// check parameter count
		if(count($action) == 3) {
			$query = new Query();

			$sql = "SELECT useragent FROM ".$this->db_unidentified." WHERE id = ".$action[2];
//			print $sql."\n";
			$query->sql($sql);

			$ua = $query->result(0, "useragent");
//			print $ua."\n";


			$sql = "INSERT INTO ".$this->db_useragents." VALUES(DEFAULT, ".$action[1].", '$ua')";
//			print $sql."\n";
			if($query->sql($sql)) {

				$sql = "DELETE FROM ".$this->db_unidentified." WHERE useragent = '$ua'";
//				print $sql."\n";
				$query->sql($sql);

				// update modified time of device
				$query->sql("UPDATE ".UT_ITEMS." SET modified_at=CURRENT_TIMESTAMP WHERE id = ".$action[1]);

				message()->addMessage("Useragent ".$action[2].", added to ".$action[1]);
				return true;
			}
		}

		message()->addMessage("Useragent ".$action[2].", could not be added to ".$action[1], array("type" => "error"));
		return false;
	}


	// get unidentified useragents, all or based on pattern
	function unidentifiedUseragents($pattern = false) {

		$query = new Query();

		if($pattern) {
			$sql = "SELECT *, MAX(identified_at) as lastentry FROM ".$this->db_unidentified." WHERE useragent LIKE '%$pattern%' GROUP BY useragent ORDER BY lastentry DESC";
		}
		else {
			$sql = "SELECT *, MAX(identified_at) as lastentry FROM ".$this->db_unidentified." GROUP BY useragent ORDER BY lastentry DESC";
		}

//		print $sql."<br>";
		if($query->sql($sql)) {
			return $query->results();
		}
	}


	// get unidentified useragent details
	function unidentifiedUseragentDetails($action) {

		if(count($action) == 2) {

			$query = new Query();
			$sql = "SELECT useragent FROM ".$this->db_unidentified." WHERE id = ".$action[1];
			if($query->sql($sql)) {

//				$useragent = stripForDB($query->getQueryResult(0, "useragent"));
				$useragent = prepareForDB($query->result(0, "useragent"));
				$sql = "SELECT * FROM ".$this->db_unidentified." WHERE useragent = '$useragent' ORDER BY identified_at DESC";
//				print $sql."<br>";
				if($query->sql($sql)) {
					$results = $query->results();

					return $results;
				}
			}
		}

		message()->addMessage("Useragent could not be found", array("type" => "error"));
		return false;
	}


	// attempt to identify unidentified useragent
	function identifyUnidentifiedId($action) {

		if(count($action) == 2) {

			$Identify = new Identify();
			$query = new Query();
			if($query->sql("SELECT useragent FROM ".$this->db_unidentified." WHERE id = ".$action[1])) {
				$ua = $query->result(0, "useragent");

				// identify device and return result
				$device = $Identify->identifyDevice($ua, false);
//				print_r($device);

				return $device;
			}
		}
		return false;
	}


	// full run through of all unidentified useragents to check if they match current unique IDs
	function searchForUniqueMatches($_options = false) {

		$query = new Query();
		$Identify = new Identify();

		$devices = array();
		$uas = $this->unidentifiedUseragents();
//		print "count:" . count($uas)."<br>";
//		foreach($uas as $i => $ua) {
		for($i = 0; $i < 500 && $i < count($uas); $i++) {
			$ua = $uas[$i];
			$device = $Identify->identifyDevice($ua["useragent"], false, false, false);
//			print_r($device);
			if($device && ($device["method"] == "unique_id - missing id" || $device["method"] == "unique_id" || $device["method"] == "match")) {
				$device["useragent"] = $ua["useragent"];
				$device["id"] = $ua["id"];
				$devices[] = $device;
			}
			unset($uas[$i]);
		}

		unset($uas);

		return $devices;
	}


	// full LIKE search on name, description and useragent
	function searchForUniquePotential($_options = false) {

		$query = new Query();

		$SELECT = array();
		$FROM = array();
		$LEFTJOIN = array();
		$WHERE = array();
		$GROUP_BY = "";
		$HAVING = "";
		$ORDER = array();


		$SELECT[] = "items.id";
		$SELECT[] = "items.sindex";
		$SELECT[] = "items.status";
		$SELECT[] = "items.published_at";

		$SELECT[] = "count(ua.id) AS uas";

	 	$FROM[] = UT_ITEMS." as items";
	 	$FROM[] = $this->db_useragents." as ua";


		$WHERE[] = "items.itemtype = 'device'";
		$WHERE[] = "ua.item_id = items.id";

		$WHERE[] = "items.id NOT IN(SELECT item_id FROM ".UT_TAGGINGS." as taggings WHERE taggings.tag_id = 600 GROUP BY item_id)";

		$GROUP_BY = "items.id";

		$ORDER[] = "uas DESC";

		$LIMIT = 50;

		$items = array();


//		print $query->compileQuery($SELECT, $FROM, array("LEFTJOIN" => $LEFTJOIN, "WHERE" => $WHERE, "HAVING" => $HAVING, "GROUP_BY" => $GROUP_BY, "ORDER" => $ORDER, "LIMIT" => $LIMIT));
//		return array();
		$query->sql($query->compileQuery($SELECT, $FROM, array("LEFTJOIN" => $LEFTJOIN, "WHERE" => $WHERE, "HAVING" => $HAVING, "GROUP_BY" => $GROUP_BY, "ORDER" => $ORDER, "LIMIT" => $LIMIT)));
		for($i = 0; $i < $query->count(); $i++){

			$item = array();

			$item["id"] = $query->result($i, "id");
			$item["itemtype"] = "device";
			$item["sindex"] = $query->result($i, "sindex");
			$item["status"] = $query->result($i, "status");
			$item["published_at"] = $query->result($i, "published_at");
			$item["uas"] = $query->result($i, "uas");

			$items[] = $item;
		}

		return $items;
	}

}

?>