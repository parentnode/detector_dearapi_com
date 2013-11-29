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



	/**
	* Get item
	*/
	function get($item_id) {
		$query = new Query();

		if($query->sql("SELECT * FROM ".$this->db." WHERE item_id = $item_id")) {
			$item = $query->result(0);
			unset($item["id"]);

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

	// custom loopback function


	// add useragent - 3 parameters exactly
	// /device/#item_id#/addUseragent
	function addUseragent($action) {

		if(count($action) == 3) {

			$useragent = getPost("useragent");
			$query = new Query();

			if($query->sql("INSERT INTO ".$this->db_useragents." VALUES(DEFAULT, ".$action[1].", '".$useragent."')")) {

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

			if($query->sql("DELETE FROM ".$this->db_useragents." WHERE item_id = ".$action[1]." AND id = '".$action[3]."'")) {

				message()->addMessage("Useragent deleted");
				return true;
			}
		}

		message()->addMessage("Useragent could not be deleted", array("type" => "error"));
		return false;
	}


	function unidentifiedUseragents() {

		$query = new Query();

		$sql = "SELECT id, useragent FROM ".$this->db_unidentified." GROUP BY useragent";
		print $sql."<br>";
		if($query->sql($sql)) {
			return $query->results();
		}
	}

}

?>