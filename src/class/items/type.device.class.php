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
		$this->db_unidentified = SITE_DB.".devices_unidentified";

		// Name
		$this->addToModel("published_at", array(
			"type" => "datetime",
			"label" => "Publish date (yyyy-mm-dd hh:mm:ss)",
			"hint_message" => "Date to publish product on site. Until this date product will remain hidden on site. Leave empty for instnt publication", 
			"error_message" => "Date must be of format (yyyy-mm-dd hh:mm:ss)"
		));

		// Name
		$this->addToModel("name", array(
			"type" => "string",
			"label" => "Name",
			"required" => true,
			"unique" => $this->db,
			"hint_message" => "Name of the product - Format: Miele - AX11.", 
			"error_message" => "Name must to be unique."
		));

		// Description
		$this->addToModel("description", array(
			"type" => "text",
			"label" => "Description",
			"hint_message" => "Write a meaningful description of the product. Remember product descriptions are very important for Google - Make sure to use varied language and include all relevant keywords in your description."
		));

		// Files
		$this->addToModel("files", array(
			"type" => "files",
			"label" => "Drag images here to add",
			"allowed_formats" => "png,jpg",
			"allowed_proportions" => "1/1",
			"hint_message" => "Add product images here. Use png or jpg in 1/1 proportion.",
			"error_message" => "Image does not fit requirements."
		));

		// Tags
		$this->addToModel("tags", array(
			"type" => "tags",
			"label" => "Tag",
			"hint_message" => "Start typing to get suggestions. A correct tag has this format: context:value.",
			"error_message" => "Must be correct Tag format."
		));

		// Prices
		// hadcoded for DKK, 25%
		$this->addToModel("prices", array(
			"type" => "prices",
			"label" => "Price in DKK, excl. VAT",
			"currency" => "DKK",
			"vatrate" => 1,
			"hint_message" => "Price excl. VAT. If you do not state a price, product appear without a buy button.",
			"error_message" => "Must be a number."
		));


		parent::__construct();
	}



	/**
	* Get item
	*/
	function get($item_id) {
		$query = new Query();
		$query_images = new Query();

		if($query->sql("SELECT * FROM ".$this->db." WHERE item_id = $item_id")) {
			$item = $query->result(0);
			unset($item["id"]);

			$item["images"] = false;

			// get slides
			if($query_images->sql("SELECT * FROM ".$this->db_images." WHERE item_id = $item_id ORDER BY position DESC, id DESC")) {

				$images = $query_images->results();
				foreach($images as $i => $image) {
					$item["images"][$i]["id"] = $image["id"];
					$item["images"][$i]["variant"] = $image["variant"];
					$item["images"][$i]["format"] = $image["format"];
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
		$query->checkDbExistance($this->db_images);

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