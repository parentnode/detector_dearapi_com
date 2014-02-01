<?php
/**
* @package hvidevarehuset.items
* This file contains presentation maintenance functionality
*/

class TypeDevice extends Model {

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {

		// itemtype database
		$this->db = SITE_DB.".item_device";
		$this->db_useragents = SITE_DB.".device_useragents";
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

		// Description
		$this->addToModel("useragent", array(
			"type" => "text",
			"label" => "Useragent",
			"hint_message" => "Device useragent. Only add actual useragents."
		));

		// Tags
		$this->addToModel("tags", array(
			"type" => "tags",
			"label" => "Tag",
			"hint_message" => "Start typing to filter options.",
			"error_message" => "Must be correct Tag format. A correct tag has this format: context:value."
		));

		parent::__construct();
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

			// get slides
			if($query->sql("SELECT * FROM ".$this->db_useragents." WHERE item_id = $item_id")) {

				$useragents = $query->results();
				foreach($useragents as $i => $useragent) {
					$item["useragents"][$i]["id"] = $useragent["id"];
					$item["useragents"][$i]["useragent"] = $useragent["useragent"];
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
		$IC = new Item();

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
		$IC = new Item();
		$query = new Query();


		if(count($action) == 2) {

			$device = $IC->getCompleteItem($action[1]);

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
						$IC->addTag($new_id, $tag["id"]);
					}
				}

				message()->addMessage("Device cloned");

				// get and return new device (id will be used to redirect to new device page)
				$item = $IC->getCompleteItem($new_id);
				return $item;
			}
		}

		message()->addMessage("Device could not be cloned", array("type" => "error"));
		return false;
	}


	// add useragent - 3 parameters exactly
	// /device/#item_id#/addUseragent
	function addUseragent($action) {

		if(count($action) == 3) {

			$useragent = getPost("useragent");
			$query = new Query();

			if($query->sql("INSERT INTO ".$this->db_useragents." VALUES(DEFAULT, ".$action[1].", '".$useragent."')")) {

				// update modified time of device
				$query->sql("UPDATE ".UT_ITEMS." SET modified_at=CURRENT_TIMESTAMP WHERE id = ".$action[1]);

				message()->addMessage("Useragent added");
				return true;
			}
		}

		message()->addMessage("Useragent could not be added", array("type" => "error"));
		return false;
	}


	// delete device useragent - 4 parameters exactly
	// /device/#item_id#/deleteUseragent/#useragent_id#
	function deleteUseragent($action) {

		if(count($action) == 4) {

			$query = new Query();
			$sql = "SELECT useragent FROM ".$this->db_useragents." WHERE id = '".$action[3]."'";
			$query->sql($sql);

			$ua = $query->result(0, "useragent");

			$sql = "DELETE FROM ".$this->db_useragents." WHERE item_id = ".$action[1]." AND id = '".$action[3]."'";
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

			$item["id"] = $query->result($i, "items.id");
			$item["itemtype"] = "device";
			$item["sindex"] = $query->result($i, "items.sindex");
			$item["status"] = $query->result($i, "items.status");
			$item["published_at"] = $query->result($i, "items.published_at");

			$items[] = $item;
		}

		return $items;
	}


	// TODO: make it possible to delete unidentified useragent
	function deleteUnidentified($action) {
		
		// check parameter count
		if(count($action) == 2) {
			$query = new Query();

			$sql = "SELECT useragent FROM ".$this->db_unidentified." WHERE id = ".$action[1];
//			print $sql."\n";
			$query->sql($sql);

			$ua = $query->result(0, "useragent");
//			print $ua."\n";


			$sql = "DELETE FROM ".$this->db_unidentified." WHERE useragent = '$ua'";
//				print $sql."\n";
			if($query->sql($sql)) {

				message()->addMessage("Useragent ".$action[1].", deleted");
				return true;
			}
		}

		message()->addMessage("Useragent ".$action[1].", could not be deleted", array("type" => "error"));
		return false;
	}


	// add unidentified useragent to device
	// /admin/device/addUnidentifiedToDevice/#device_id#/#unidentified_ua_id#
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

					// // get name of device, if useragent has already been identif
					// if($results[0]["identified_as"]) {
					// 	$sql = "SELECT name FROM ".$this->db." WHERE item_id = '".$results[0]["identified_as"]."'";
					// 	$query->sql();
					// 	$results[0]["identified_as_device"] = $query->result(0, "name");
					// }
					// // new identification attempt
					// else {
					// 	$results[0]["identified_as_device"] = "unknown";
					// }

//					print_r($query->results());
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

		$WHERE[] = "items.id NOT IN(SELECT item_id FROM devices_dearapi_com.tags as tags, devices_dearapi_com.taggings as taggings WHERE tags.id = taggings.tag_id AND tags.context = 'type' AND tags.value = 'unique' GROUP BY item_id)";

		$GROUP_BY = "items.id";

		$ORDER[] = "uas DESC";

		$LIMIT = 50;

		$items = array();


//		print $query->compileQuery($SELECT, $FROM, array("LEFTJOIN" => $LEFTJOIN, "WHERE" => $WHERE, "HAVING" => $HAVING, "GROUP_BY" => $GROUP_BY, "ORDER" => $ORDER, "LIMIT" => $LIMIT));
//		return array();
		$query->sql($query->compileQuery($SELECT, $FROM, array("LEFTJOIN" => $LEFTJOIN, "WHERE" => $WHERE, "HAVING" => $HAVING, "GROUP_BY" => $GROUP_BY, "ORDER" => $ORDER, "LIMIT" => $LIMIT)));
		for($i = 0; $i < $query->count(); $i++){

			$item = array();

			$item["id"] = $query->result($i, "items.id");
			$item["itemtype"] = "device";
			$item["sindex"] = $query->result($i, "items.sindex");
			$item["status"] = $query->result($i, "items.status");
			$item["published_at"] = $query->result($i, "items.published_at");
			$item["uas"] = $query->result($i, "uas");

			$items[] = $item;
		}

		return $items;
	}

}

?>