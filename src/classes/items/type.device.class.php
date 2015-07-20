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


			// add comments to new device
			$dest_device = $this->get($device_id_destination);
			$updated_comment = $dest_device["description"]."\n\n".$device["name"]."\n".$device["description"];
			$sql = "UPDATE ".$this->db." SET description = '$updated_comment' WHERE item_id = ".$device_id_destination;
			$query->sql($sql);


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

	// add marker - 2 parameters exactly
	// /janitor/device/addMarker/#item_id#
	function addMarker($action) {

		$marker = getPost("marker");

		if(count($action) == 2 && $marker) {
			$item_id = $action[1];

			$query = new Query();
			$query->checkDbExistance($this->db_markers);

			if($query->sql("INSERT INTO ".$this->db_markers." VALUES(DEFAULT, ".$item_id.", '".$marker."')")) {

				$marker_id = $query->lastInsertId();

				// update modified time of device
				$query->sql("UPDATE ".UT_ITEMS." SET modified_at=CURRENT_TIMESTAMP WHERE id = ".$item_id);

				message()->addMessage("Marker added");
				return array("marker" => prepareForHTML($marker), "id" => $marker_id);
			}
		}

		message()->addMessage("Marker could not be added", array("type" => "error"));
		return false;
	}

	// update marker - 3 parameters exactly
	// /janitor/device/updateMarker/#item_id#/#marker_id#
	function updateMarker($action) {

		$marker = getPost("marker");

		if(count($action) == 3 && $marker) {
			$item_id = $action[1];
			$marker_id = $action[2];

			$query = new Query();

			if($query->sql("UPDATE ".$this->db_markers." SET marker = '$marker' WHERE item_id = $item_id AND id = $marker_id")) {

				// update modified time of device
				$query->sql("UPDATE ".UT_ITEMS." SET modified_at=CURRENT_TIMESTAMP WHERE id = ".$item_id);

				message()->addMessage("Marker updated");
				return true;
			}
		}

		message()->addMessage("Marker could not be updated", array("type" => "error"));
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

	// add exception - 2 parameters exactly
	// /janitor/device/addException/#item_id#
	function addException($action) {

		$exception = getPost("exception");

		if(count($action) == 2 && $exception) {
			$item_id = $action[1];

			$query = new Query();
			$query->checkDbExistance($this->db_exceptions);

			if($query->sql("INSERT INTO ".$this->db_exceptions." VALUES(DEFAULT, ".$item_id.", '".$exception."')")) {

				$exception_id = $query->lastInsertId();

				// update modified time of device
				$query->sql("UPDATE ".UT_ITEMS." SET modified_at=CURRENT_TIMESTAMP WHERE id = ".$item_id);

				message()->addMessage("Exception added");
				return array("exception" => prepareForHTML($exception), "id" => $exception_id);
			}
		}

		message()->addMessage("Exception could not be added", array("type" => "error"));
		return false;
	}

	// update exception - 3 parameters exactly
	// /janitor/device/updateException/#item_id#/#exception_id#
	function updateException($action) {

		$exception = getPost("exception");

		if(count($action) == 3 && $exception) {
			$item_id = $action[1];
			$exception_id = $action[2];

			$query = new Query();

			if($query->sql("UPDATE ".$this->db_exceptions." SET exception = '$exception' WHERE item_id = $item_id AND id = $exception_id")) {

				// update modified time of device
				$query->sql("UPDATE ".UT_ITEMS." SET modified_at=CURRENT_TIMESTAMP WHERE id = ".$item_id);

				message()->addMessage("Exception updated");
				return true;
			}
		}

		message()->addMessage("Exception could not be updated", array("type" => "error"));
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

			// get global grouping
			$device_segment = $this->segment($device_id);
			$global_group_device = $this->getGlobalPatterns($device_segment);


			// compile regular expression for specific device
			$regex_pos = $this->createRegex($device, "markers", "marker");
			$regex_neg = $this->createRegex($device, "exceptions", "exception");
			// $regex_pos = "";
			// $regex_neg = "";
			//
			// if(isset($device["markers"]) && $device["markers"]) {
			// 	$markers = array();
			//
			// 	foreach($device["markers"] as $marker) {
			// 		array_push($markers, $marker["marker"]);
			// 	}
			//
			// 	$regex_pos = implode($markers, "|");
			// }
			//
			// if(isset($device["exceptions"]) && $device["exceptions"]) {
			// 	$exceptions = array();
			//
			// 	foreach($device["exceptions"] as $exception) {
			// 		array_push($exceptions, $exception["exception"]);
			// 	}
			//
			// 	$regex_neg = implode($exceptions, "|");
			// }


			// compile regular expression for global grouping
			// $group_regex_pos = "";
			// $group_regex_neg = "";
			$group_regex_pos = $this->createRegex($global_group_device, "markers", "marker");
			$group_regex_neg = $this->createRegex($global_group_device, "exceptions", "exception");

			// if($global_group_device["markers"]) {
			// 	$markers = array();
			//
			// 	foreach($global_group_device["markers"] as $marker) {
			// 		array_push($markers, $marker["marker"]);
			// 	}
			//
			// 	$group_regex_pos = implode($markers, "|");
			// }
			//
			// if($global_group_device["exceptions"]) {
			// 	$exceptions = array();
			//
			// 	foreach($global_group_device["exceptions"] as $exception) {
			// 		array_push($exceptions, $exception["exception"]);
			// 	}
			//
			// 	$group_regex_neg = implode($exceptions, "|");
			// }


			// print "preg_match(/".$regex_pos."/i<br>\n";
			// print "preg_match(/".$regex_neg."/i<br>\n";

			// print "regex_pos:/".preg_replace("/([\/\.])/", "\$1"$regex_pos)."/i<br>\n";
			// print "regex_neg:/".addcslashes($regex_neg)."/i<br>\n";
			//
			// print "regex_pos:/".escape($regex_pos)."/i<br>\n";
			// print "regex_neg:/".escape($regex_neg)."/i<br>\n";



			$not_matched_useragents = array();

			if($device["useragents"]) {
				// first run test on current device to identify holes in identification
				foreach($device["useragents"] as $useragent) {

					// each UA should match both global and specific marker/exception test
					if((
						!(
							(!$group_regex_pos || preg_match("/(".$group_regex_pos.")/i", $useragent["useragent"])) && 
							(!$group_regex_neg || !preg_match("/(".$group_regex_neg.")/i", $useragent["useragent"]))
						)
						||
						!(
							(!$regex_pos || preg_match("/(".$regex_pos.")/i", $useragent["useragent"])) && 
							(!$regex_neg || !preg_match("/(".$regex_neg.")/i", $useragent["useragent"]))
						)
					)) {
						array_push($not_matched_useragents, array("id" => $useragent["id"], "useragent" => $useragent["useragent"]));
					}
				}
			}


			// get all useragents
			$query = new Query();

			$sql = "SELECT * FROM ".$this->db_useragents;
			$query->sql($sql);
			$all_useragents = $query->results();
			$bad_matched_useragents = array();

			foreach($all_useragents as $useragent) {

				// don't check useragent for this device
				// check group pattern
				if($useragent["item_id"] != $device_id && (
					(!$group_regex_pos || preg_match("/(".$group_regex_pos.")/i", $useragent["useragent"])) && 
					(!$group_regex_neg || !preg_match("/(".$group_regex_neg.")/i", $useragent["useragent"]))
				)) {

					// check device pattern
					if(
						(!$regex_pos || preg_match("/(".$regex_pos.")/i", $useragent["useragent"])) && 
						(!$regex_neg || !preg_match("/(".$regex_neg.")/i", $useragent["useragent"]))
					) {

						if(!isset($bad_matched_useragents[$useragent["item_id"]])) {
							$bad_matched_useragents[$useragent["item_id"]] = array();

							$query->sql("SELECT * FROM ".$this->db." WHERE item_id = ".$useragent["item_id"]);
							$name = $query->result(0, "name");
							$matched_device_id = $query->result(0, "item_id");

							$bad_matched_useragents[$useragent["item_id"]]["id"] = $matched_device_id;
							$bad_matched_useragents[$useragent["item_id"]]["name"] = $name;
							$bad_matched_useragents[$useragent["item_id"]]["useragents"] = array();

							// is matched device already marked
							if($query->sql("SELECT * FROM ".$this->db_markers." WHERE item_id = ".$useragent["item_id"])) {
								$bad_matched_useragents[$useragent["item_id"]]["marked"] = true;
							}
							else {
								$bad_matched_useragents[$useragent["item_id"]]["marked"] = false;
							}

						}
						array_push($bad_matched_useragents[$useragent["item_id"]]["useragents"], $useragent["useragent"]);
					}
				}
			}

			$result = array($not_matched_useragents, $bad_matched_useragents);

//				print_r($result);


			return $result;

		}

		message()->addMessage("Device could not be tested", array("type" => "error"));
		return false;

	}



	// get global patterns for groupings (using nested regex' to improve performance)
	// will also be called when testing markers on device
	// - then the device segment needs to be converted to valid global pattern
	function getGlobalPatterns($segment) {


		$IC = new Items();

		if(preg_match("/desktop_ie/", $segment)) {
			$global_segment = "desktop_ie";
		}
		else if(preg_match("/desktop_light/", $segment)) {
			$global_segment = "desktop_light";
		}
		else if(preg_match("/desktop/", $segment)) {
			$global_segment = "desktop";
		}
		else if(preg_match("/tablet/", $segment)) {
			$global_segment = "tablet";
		}
		else if(preg_match("/smartphone/", $segment)) {
			$global_segment = "smartphone";
		}
		else if(preg_match("/mobile/", $segment)) {
			$global_segment = "mobile";
		}
		// use segment as is
		else {
			$global_segment = $segment;
		}


		$global_device = $IC->getItems(array("tags" => "segment:".$global_segment.";type:global", "limit" => 1));
		if($global_device) {
			return $this->get($global_device[0]["id"]);
		}


		// no global device was found
		return false;

	}


	// Find all devices with markers to create list for testing unidentified devices
	function getDevicesWithPatterns($segment) {

		$query = new Query();
		$IC = new Items();


		$sql = "SELECT item_id, name FROM ".$this->db." as devices WHERE ";
		$sql .= "(";
			$sql .= "devices.item_id IN (SELECT item_id FROM ".$this->db_markers.") OR ";
			$sql .= "devices.item_id IN (SELECT item_id FROM ".$this->db_exceptions.")";
		$sql .= ")";
		$sql .= " AND devices.item_id NOT IN (SELECT taggings.item_id FROM ".UT_TAG." as tags, ".UT_TAGGINGS." as taggings WHERE tags.id = taggings.tag_id AND tags.context = 'type' AND tags.value = 'global')";
//		$sql .= " AND devices.item_id NOT IN (SELECT taggings.item_id FROM ".UT_TAG." as tags, ".UT_TAGGINGS." as taggings WHERE tags.id = taggings.tag_id AND tags.context = 'type' AND tags.value = 'fallback')";

//		print $sql;
		$query->sql($sql);


		$devices_with_patterns = $query->results();
		$segment_patterns = array();

		if($devices_with_patterns) {

			foreach($devices_with_patterns as $device_with_pattern) {
				
				$device = $IC->getItem(array("id" => $device_with_pattern["item_id"], "extend" => true));

				// forget unnecessary data
				unset($device["useragents"]);
				unset($device["tags"]);

				$device["segment"] = $segment;

//				print_r($device);

				$segment_patterns[] = $device;

			}
			
		}
		// loop through devices and get markers/exceptions

//		print_r($segment_patterns);
		return $segment_patterns;

	}


	// Find all devices with patterns for segment
	function getSegmentPatterns($segment) {

		$query = new Query();
		$IC = new Items();


		$sql = "SELECT item_id, name FROM ".$this->db." as devices WHERE ";
		$sql .= "(";
			$sql .= "devices.item_id IN (SELECT item_id FROM ".$this->db_markers.") OR ";
			$sql .= "devices.item_id IN (SELECT item_id FROM ".$this->db_exceptions.")";
		$sql .= ")";
		$sql .= " AND devices.item_id IN (SELECT taggings.item_id FROM ".UT_TAG." as tags, ".UT_TAGGINGS." as taggings WHERE tags.id = taggings.tag_id AND tags.context = 'segment' AND tags.value = '".$segment."')";
		$sql .= " AND devices.item_id NOT IN (SELECT taggings.item_id FROM ".UT_TAG." as tags, ".UT_TAGGINGS." as taggings WHERE tags.id = taggings.tag_id AND tags.context = 'type' AND tags.value = 'global')";
		$sql .= " AND devices.item_id NOT IN (SELECT taggings.item_id FROM ".UT_TAG." as tags, ".UT_TAGGINGS." as taggings WHERE tags.id = taggings.tag_id AND tags.context = 'type' AND tags.value = 'fallback')";

//		print $sql;
		$query->sql($sql);



		$devices_with_patterns = $query->results();
		$segment_patterns = array();

		if($devices_with_patterns) {

			foreach($devices_with_patterns as $device_with_pattern) {

				$device = $IC->getItem(array("id" => $device_with_pattern["item_id"], "extend" => true));

				// forget unnecessary data
				unset($device["useragents"]);
				unset($device["tags"]);

				$device["segment"] = $segment;

//				print_r($device);

				$segment_patterns[] = $device;

			}
			
		}
		// loop through devices and get markers/exceptions

//		print_r($segment_patterns);
		return $segment_patterns;

	}


	// get fallback pattern
	function getFallbackPattern($segment) {

		$IC = new Items();

		// $markers = false;
		// $exceptions = false;

		$fallback_device = $IC->getItems(array("tags" => "segment:".$segment.";type:fallback", "limit" => 1));
		if($fallback_device) {
			$device = $IC->getItem(array("id" => $fallback_device[0]["id"], "extend" => true));

			unset($device["useragents"]);
			unset($device["tags"]);

			if($segment == "desktop_ie_light") {
				$device["segment"] = "desktop_light";
			}
			else {
				$device["segment"] = $segment;
			}

			// if($device["markers"]) {
			// 	$markers = $device["markers"];
			// }
			// if($device["exceptions"]) {
			// 	$exceptions = $device["exceptions"];
			// }

			return array($device);


//			return $this->get($fallback_device[0]["id"]);
		}

		return array();
		// return false;
		// return array("markers" => $markers, "exceptions" => $exceptions);
	}


	// filter out empty segment patterns (to make compile function cleaner)
	function cleanSegmentPatterns($segment_patterns) {
		foreach($segment_patterns as $i => $segment_pattern) {
			if(!count($segment_pattern)) {
				unset($segment_patterns[$i]);
			}
		}
		return $segment_patterns;
	}

	function createRegex($array, $i, $j) {

		// prepare pattern if it exist
		if(isset($array[$i]) && $array[$i]) {

			$matches = array();

			foreach($array[$i] as $marker) {
				array_push($matches, $marker[$j]);
			}

			return implode($matches, "|");
		}

		return false;
	}

	// compile detection data
	//
	// Gets global devices in specific order for grouping regex and minimizing identification time
	// For each global device, it gets the related segments in specific order
	// For each releated segment, it gets all matching devices with markers
	// Returns detection data in array structure for generation of individual detection scripts
	function compileDetectionData() {

		$groups = array();


		// global group priority


		// desktop_ie
		$group_patterns = $this->getGlobalPatterns("desktop_ie");

			// individual segment priority
			$segment_patterns = array();

			// desktop_ie9
			$segment_patterns[] = $this->getSegmentPatterns("desktop_ie9");

			// desktop_ie10
			$segment_patterns[] = $this->getSegmentPatterns("desktop_ie10");

			// desktop_ie11
			$segment_patterns[] = $this->getSegmentPatterns("desktop_ie11");

		// add combined group info
		$groups[] = array("group_patterns" => $group_patterns, "segment_patterns" => $this->cleanSegmentPatterns($segment_patterns));



		// desktop
		$group_patterns = $this->getGlobalPatterns("desktop");

			// individual segment priority
			$segment_patterns = array();

			// desktop_edge
			$segment_patterns[] = $this->getSegmentPatterns("desktop_edge");

			// desktop
			$segment_patterns[] = $this->getSegmentPatterns("desktop");

		// add combined group info
		$groups[] = array("group_patterns" => $group_patterns, "segment_patterns" => $this->cleanSegmentPatterns($segment_patterns));



		// TV
		$group_patterns = $this->getGlobalPatterns("tv");

			// individual segment priority
			$segment_patterns = array();

			// tv
			$segment_patterns[] = $this->getSegmentPatterns("tv");


		// add combined group info
		$groups[] = array("group_patterns" => $group_patterns, "segment_patterns" => $this->cleanSegmentPatterns($segment_patterns));



		// desktop_light
		$group_patterns = $this->getGlobalPatterns("desktop_light");

			// individual segment priority
			$segment_patterns = array();

			// desktop_light
			$segment_patterns[] = $this->getSegmentPatterns("desktop_light");

		// add combined group info
		$groups[] = array("group_patterns" => $group_patterns, "segment_patterns" => $this->cleanSegmentPatterns($segment_patterns));



		// tablet
		$group_patterns = $this->getGlobalPatterns("tablet");

			// individual segment priority
			$segment_patterns = array();

			// tablet
			$segment_patterns[] = $this->getSegmentPatterns("tablet");

			// tablet_light
			$segment_patterns[] = $this->getSegmentPatterns("tablet_light");

		// add combined group info
		$groups[] = array("group_patterns" => $group_patterns, "segment_patterns" => $this->cleanSegmentPatterns($segment_patterns));



		// smartphone
		$group_patterns = $this->getGlobalPatterns("smartphone");

			// individual segment priority
			$segment_patterns = array();

			// smartphone
			$segment_patterns[] = $this->getSegmentPatterns("smartphone");

		// add combined group info
		$groups[] = array("group_patterns" => $group_patterns, "segment_patterns" => $this->cleanSegmentPatterns($segment_patterns));



		// mobile
		$group_patterns = $this->getGlobalPatterns("mobile");

			// individual segment priority
			$segment_patterns = array();

			// mobile
			$segment_patterns[] = $this->getSegmentPatterns("mobile");

			// mobile_light
			$segment_patterns[] = $this->getSegmentPatterns("mobile_light");

		// add combined group info
		$groups[] = array("group_patterns" => $group_patterns, "segment_patterns" => $this->cleanSegmentPatterns($segment_patterns));



		// seo
		$group_patterns = $this->getGlobalPatterns("seo");

			// individual segment priority
			$segment_patterns = array();

			// seo
			$segment_patterns[] = $this->getSegmentPatterns("seo");

		// add combined group info
		$groups[] = array("group_patterns" => $group_patterns, "segment_patterns" => $this->cleanSegmentPatterns($segment_patterns));



		// fallback patterns
		$fallback_patterns = array();

		$fallback_patterns[] = $this->getFallbackPattern("desktop_ie9");
		$fallback_patterns[] = $this->getFallbackPattern("desktop_ie10");
		$fallback_patterns[] = $this->getFallbackPattern("desktop_ie11");
		$fallback_patterns[] = $this->getFallbackPattern("desktop_edge");
		$fallback_patterns[] = $this->getFallbackPattern("desktop");
		$fallback_patterns[] = $this->getFallbackPattern("tv");
		$fallback_patterns[] = $this->getFallbackPattern("desktop_light");
		$fallback_patterns[] = $this->getFallbackPattern("desktop_ie_light");
		$fallback_patterns[] = $this->getFallbackPattern("tablet");
		$fallback_patterns[] = $this->getFallbackPattern("tablet_light");
		$fallback_patterns[] = $this->getFallbackPattern("smartphone");
		$fallback_patterns[] = $this->getFallbackPattern("mobile");
		$fallback_patterns[] = $this->getFallbackPattern("mobile_light");
		$fallback_patterns[] = $this->getFallbackPattern("seo");

		$groups[] = array("group_patterns" => array(), "segment_patterns" => $this->cleanSegmentPatterns($fallback_patterns));

//		print_r($groups);

		return $groups;
	}



	// create detections core
	// used for API detection
	function createDetectionCore() {

		$patterns = $this->compileDetectionData();

//		print_r($patterns);

		$_ = "";

		$group_add_else = false;

		$_ .= '$ua = $useragent ? $useragent : stringOr(getVar("ua"), $_SERVER["HTTP_USER_AGENT"]);'."\n\n";

		$_ .= 'function detectionCore($ua) {'."\n";
		// loop through all grouping patterns

		foreach($patterns as $pattern) {


			$_ .= "// START GROUP\n";


			// get group and segment patterns
			$group_patterns = $pattern["group_patterns"];
			$segment_patterns = $pattern["segment_patterns"];


//			print_r($pattern);

			// create pattern-statement for group
			$group_regex_pos = $this->createRegex($group_patterns, "markers", "marker");
			$group_regex_neg = $this->createRegex($group_patterns, "exceptions", "exception");
//			$group_regex_neg = "";

			// // prepare group pattern markers if they exist
			// if(isset($group_patterns["markers"]) && $group_patterns["markers"]) {
			//
			// 	$markers = array();
			//
			// 	foreach($group_patterns["markers"] as $marker) {
			// 		array_push($markers, $marker["marker"]);
			// 	}
			//
			// 	$group_regex_pos = implode($markers, "|");
			// }

			// prepare group pattern exceptions if they exist
			// if(isset($group_patterns["exceptions"]) && $group_patterns["exceptions"]) {
			// 	$exceptions = array();
			//
			// 	foreach($group_patterns["exceptions"] as $exception) {
			// 		array_push($exceptions, $exception["exception"]);
			// 	}
			//
			// 	$group_regex_neg = implode($exceptions, "|");
			// }


			// if first level grouping is started, add else to statement
			// if($group_regex_pos || $group_regex_neg || $segment_patterns) {
			// 	$_ .= $group_add_else ? "else " : "";
			// }

			// indent segments or keep them on global level
			if($group_regex_pos || $group_regex_neg) {
				$group_indent = "	";
			}
			else {
				$group_indent = "";
			}


			// create regex for patterns for group
			if($group_regex_neg && $group_regex_pos) {
				$_ .= 'if(!preg_match("/('.$group_regex_neg.')/i", $ua) && preg_match("/('.$group_regex_pos.')/i", $ua)) {'."\n";
			}
			else if($group_regex_pos) {
				$_ .= 'if(preg_match("/('.$group_regex_pos.')/i", $ua)) {'."\n";
			}
			else if($group_regex_neg) {
				$_ .= 'if(!preg_match("/('.$group_regex_neg.')/i", $ua)) {'."\n";
			}



			// create segment patterns
			$segment_add_else = false;

			// loop through segments
			if($segment_patterns) {
				foreach($segment_patterns as $segment_pattern) {

					foreach($segment_pattern as $device_pattern) {


						$_ .= $group_indent."// START DEVICE\n";


						// create pattern-statement for device
						$device_regex_pos = $this->createRegex($device_pattern, "markers", "marker");
						$device_regex_neg = $this->createRegex($device_pattern, "exceptions", "exception");
						// $device_regex_pos = "";
						// $device_regex_neg = "";
						//
						// // prepare device pattern markers if they exist
						// if(isset($device_pattern["markers"]) && $device_pattern["markers"]) {
						//
						// 	$markers = array();
						//
						// 	foreach($device_pattern["markers"] as $marker) {
						// 		array_push($markers, $marker["marker"]);
						// 	}
						//
						// 	$device_regex_pos = implode($markers, "|");
						// }
						//
						// // prepare device pattern exceptions if they exist
						// if(isset($device_pattern["exceptions"]) && $device_pattern["exceptions"]) {
						// 	$exceptions = array();
						//
						// 	foreach($device_pattern["exceptions"] as $exception) {
						// 		array_push($exceptions, $exception["exception"]);
						// 	}
						//
						// 	$device_regex_neg = implode($exceptions, "|");
						// }


						// if second level statement is started, add else to statement
						$_ .= $segment_add_else ? $group_indent."else " : "";


						// create regex for patterns for device
						if($device_regex_neg && $device_regex_pos) {
							$_ .= $group_indent.'if(!preg_match("/('.$device_regex_neg.')/i", $ua) && preg_match("/('.$device_regex_pos.')/i", $ua)) {'."\n";
						}
						else if($device_regex_pos) {
							$_ .= $group_indent.'if(preg_match("/('.$device_regex_pos.')/i", $ua)) {'."\n";
						}
						else if($device_regex_neg) {
							$_ .= $group_indent.'if(!preg_match("/('.$device_regex_neg.')/i", $ua)) {'."\n";
						}


						$_ .= $group_indent.'	return "'.$device_pattern["segment"].';'.$device_pattern["name"].'";'."\n";
//						$_ .= $group_indent.'	$device_name = "'.$device_pattern["name"].'";'."\n";

						$_ .= $group_indent.'}'."\n";


						// add else statement on next loop
						$segment_add_else = true;

						// segment could be global
						// else statement needs to be switched on even if no global marker is available
						$group_add_else = true;

					}

				}
			}



			// only end group pattern if it exists
			if($group_regex_pos || $group_regex_neg) {
				$_ .= '}'."\n";

				// grouping statements started
				$group_add_else = true;
				$group_indent = "	";
			}
			else {
				$group_indent = "";
			}




		}

		$_ .= '}'."\n";
		$_ .= '$identified = detectionCore($ua);'."\n";
		$_ .= 'if($identified) {'."\n";
			$_ .= 'list($device_segment, $device_name) = explode(";", $identified);'."\n";
		$_ .= '}'."\n";

		return $_;

	}

	function createPHPDetection() {
		

	}


	function createJavaScriptDetection() {
		

	}


	// write detection script to library/public
	function writeDetectionCore($action) {

		$_ = '<?php // timestamp: '.date("Ymd hi")  ."\n";
		$_ .= $this->createDetectionCore();
		$_ .= '?>'."\n";

		if(file_put_contents(PUBLIC_FILE_PATH."/detection_script.php", $_)) {

			message()->addMessage("Script created");
			return true;
		}

		message()->addMessage("Script creation failed", array("type" => "error"));
		return false;
	}



	// TODO: update test to new standard


	// test device markers on unidentified useragents
	// testMarkersOnUnidentified/#device_id#
	function testMarkersOnUnidentified($device_id) {
	
		$device = $this->get($device_id);

		// compile regular expression for device
		$regex_pos = $this->createRegex($device, "markers", "marker");
		$regex_neg = $this->createRegex($device, "exceptions", "exception");
		// $regex_pos = "";
		// $regex_neg = "";
		//
		// if(isset($device["markers"]) && $device["markers"]) {
		//
		// 	$markers = array();
		//
		// 	foreach($device["markers"] as $marker) {
		// 		array_push($markers, $marker["marker"]);
		// 	}
		//
		// 	$regex_pos = implode($markers, "|");
		//
		// }
		//
		// if(isset($device["exceptions"]) && $device["exceptions"]) {
		// 	$exceptions = array();
		//
		// 	foreach($device["exceptions"] as $exception) {
		// 		array_push($exceptions, $exception["exception"]);
		// 	}
		//
		// 	$regex_neg = implode($exceptions, "|");
		// }

		// get global grouping
		$device_segment = $this->segment($device_id);
		$global_group_device = $this->getGlobalPatterns($device_segment);


		// compile regular expression for global grouping
		$group_regex_pos = $this->createRegex($global_group_device, "markers", "marker");
		$group_regex_neg = $this->createRegex($global_group_device, "exceptions", "exception");

		// if(isset($global_group_device["markers"]) && $global_group_device["markers"]) {
		// 	$markers = array();
		//
		// 	foreach($global_group_device["markers"] as $marker) {
		// 		array_push($markers, $marker["marker"]);
		// 	}
		//
		// 	$group_regex_pos = implode($markers, "|");
		// }

		// if(isset($global_group_device["exceptions"]) && $global_group_device["exceptions"]) {
		// 	$exceptions = array();
		//
		// 	foreach($global_group_device["exceptions"] as $exception) {
		// 		array_push($exceptions, $exception["exception"]);
		// 	}
		//
		// 	$group_regex_neg = implode($exceptions, "|");
		// }

		if($regex_pos || $regex_neg || $group_regex_pos || $group_regex_neg) {
			// print "regex_pos: preg_match(/".$regex_pos."/i<br>\n";
			// print "regex_neg: preg_match(/".$regex_neg."/i<br>\n";
			//
			// print "group_regex_pos: preg_match(/".$group_regex_pos."/i<br>\n";
			// print "group_regex_neg: preg_match(/".$group_regex_neg."/i<br>\n";

			// get all useragents
			$query = new Query();

			$sql = "SELECT id, useragent FROM ".$this->db_unidentified." GROUP BY useragent ORDER BY useragent";
//			$sql = "SELECT id, useragent FROM ".$this->db_unidentified." WHERE useragent like '%Chrome%' GROUP BY useragent";
			$query->sql($sql);
			$all_useragents = $query->results();
			$matched_useragents = array();



//			print "count:" .count($all_useragents)."<br>\n";
			foreach($all_useragents as $useragent) {

				// check group pattern
				if(
					(!$group_regex_pos || preg_match("/(".$group_regex_pos.")/i", $useragent["useragent"])) && 
					(!$group_regex_neg || !preg_match("/(".$group_regex_neg.")/i", $useragent["useragent"]))
				) {
//					print "passed global test";
						// check device pattern
					if(
						(!$regex_pos || preg_match("/(".$regex_pos.")/i", $useragent["useragent"])) && 
						(!$regex_neg || !preg_match("/(".$regex_neg.")/i", $useragent["useragent"]))
					) {
//						print "passed local test";

						$matched_useragents[] = array("id" => $useragent["id"], "useragent" => $useragent["useragent"]);

					}
				}	
			}


			return $matched_useragents;

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
				$sql = "DELETE FROM ".$this->db_unidentified." WHERE useragent = '".addSlashes($ua)."'";
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

			$ua = prepareForDB($query->result(0, "useragent"));
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
//			$sql = "SELECT * FROM ".$this->db_unidentified." GROUP BY useragent";
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




	// ADVANCED MAINTENANCE TOOLS


	// All devices without useragents
	function listEmptyDevices($_options = false) {

		$query = new Query();
		$sql = "SELECT * FROM ".$this->db." as devices WHERE item_id NOT IN (SELECT item_id FROM ".$this->db_useragents.")";
		$query->sql($sql);
		return $query->results();
	}

	// Somehow some useragents are left without device
	// could be the result an old error - should not be possible anymore
	function listLostUseragents($_options = false) {

		$query = new Query();
		$sql = "SELECT * FROM ".$this->db_useragents." as devices WHERE item_id NOT IN (SELECT item_id FROM ".$this->db.")";
		$query->sql($sql);
		return $query->results();

	}

	// Move lost useragents to unidentified for re-indexing
	function deleteLostUseragents($action) {

		// check parameter count
		if(count($action) == 1) {

			$query = new Query();
			// $sql = "SELECT * FROM ".$this->db_useragents." as devices WHERE item_id NOT IN (SELECT item_id FROM ".$this->db.")";
			// $query->sql($sql);
			$uas = $this->listLostUseragents(); //$query->results();

			foreach($uas as $ua) {
				$sql = "DELETE FROM ".$this->db_useragents." WHERE id = '".$ua["id"]."'";
				if($query->sql($sql)) {

					$sql = "INSERT INTO ".$this->db_unidentified." VALUES(DEFAULT, '".$ua["useragent"]."', 'orphanaged', '', DEFAULT)";
					$query->sql($sql);
				}
			}
		}

	}


	// Somehow some items are left without device
	// could be the result an old error - should not be possible anymore
	function listLostDevices($_options = false) {

		$query = new Query();
		$sql = "SELECT * FROM ".UT_ITEMS." as items WHERE id NOT IN (SELECT item_id FROM ".$this->db.")";
		$query->sql($sql);
		return $query->results();

	}

	// Delete lost device items
	function deleteLostDevices($action) {

		// check parameter count
		if(count($action) == 1) {

			$query = new Query();
			// $sql = "SELECT * FROM ".$this->db_useragents." as devices WHERE item_id NOT IN (SELECT item_id FROM ".$this->db.")";
			// $query->sql($sql);
			$items = $this->listLostDevices(); //$query->results();

			foreach($items as $item) {
				$sql = "DELETE FROM ".UT_ITEMS." WHERE id = '".$item["id"]."'";
				$query->sql($sql);

			}
		}

	}


	// Somehow some don't have any tags - that makes it really easy for them to hide
	function listDevicesWithoutTags($_options = false) {

		$query = new Query();
		$sql = "SELECT * FROM ".UT_ITEMS." as items WHERE id NOT IN (SELECT item_id FROM ".UT_TAGGINGS.")";
		$query->sql($sql);
		return $query->results();

	}

	// Somehow some don't have any Brand-tag - that makes it really easy for them to hide
	function listDevicesWithoutBrand($_options = false) {

		$query = new Query();
		$sql = "SELECT * FROM ".UT_ITEMS." as items WHERE id NOT IN (SELECT item_id FROM ".UT_TAG." as tags, ".UT_TAGGINGS." as taggings WHERE tags.context = 'brand' AND tags.id = taggings.tag_id)";
		$query->sql($sql);
		return $query->results();

	}




	// OLDER MAINTENANCE FUNCTIONS


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