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
		$this->db = SITE_DB.".devices";
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
			"hint_message" => "Start typing to get suggestions. A correct tag has this format: context:value.",
			"error_message" => "Must be correct Tag format."
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



	// CMS SECTION

	// update item type - based on posted values
	function update($item_id) {

		$query = new Query();
		$IC = new Item();

		$query->checkDbExistance($this->db);
		$query->checkDbExistance($this->db_useragents);

		$uploads = $IC->upload($item_id, array("proportion" => 1/1, "filegroup" => "image", "auto_add_variant" => true));
		if($uploads) {
			foreach($uploads as $upload) {
				$query->sql("INSERT INTO ".$this->db_images." VALUES(DEFAULT, $item_id, '".$upload["name"]."', '".$upload["format"]."', '".$upload["variant"]."', 0)");
			}
		}


		$entities = $this->data_entities;
		$names = array();
		$values = array();

		foreach($entities as $name => $entity) {
			if($entity["value"] != false && $name != "published_at" && $name != "status" && $name != "tags" && $name != "prices") {
				$names[] = $name;
				$values[] = $name."='".$entity["value"]."'";
			}
		}

		if($this->validateList($names, $item_id)) {
			if($values) {
				$sql = "UPDATE ".$this->db." SET ".implode(",", $values)." WHERE item_id = ".$item_id;
//					print $sql;
			}

			if(!$values || $query->sql($sql)) {
				return true;
			}
		}

		return false;
	}


	// custom loopback function

	// delete product image - 4 parameters exactly
	// /product/#item_id#/deleteImage/#image_id#
	function deleteImage($action) {

		if(count($action) == 4) {

			$query = new Query();

//			print "DELETE FROM ".$this->db_images." WHERE item_id = ".$action[0]." AND variant = '".$action[2]."'";

			if($query->sql("DELETE FROM ".$this->db_images." WHERE item_id = ".$action[1]." AND variant = '".$action[3]."'")) {
				FileSystem::removeDirRecursively(PUBLIC_FILE_PATH."/".$action[1]."/".$action[3]);
				FileSystem::removeDirRecursively(PRIVATE_FILE_PATH."/".$action[1]."/".$action[3]);

				message()->addMessage("Image deleted");
				return true;
			}
		}

		message()->addMessage("Image could not be deleted", array("type" => "error"));
		return false;
	}

}

?>