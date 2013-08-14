<?php
/**
* @package framework.log
*/

/**
*/
class LogCore extends Translation {
	
	/**
	* Init, set varnames, validation rules
	* @return void
	*/
	function __construct() {
		// initiate helpers before calling View construct
		$this->addTranslation(__FILE__);

	}


	/**
	* Get all devices
	*
	* @param string $which Optional limitation of returned result. ("id", "values", "brand" or "model")
	* @return array|false Item array or false on error
	*/
	function getItems($brand_id = false, $contenttype_id=false, $limit = false) {

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
		$FROM[] = UT_BRA." as brands";

//		$WHERE[] = "devices.id = ".$id;
		$WHERE[] = "devices.brand_id = brands.id";
		if($brand_id) {
			$WHERE[] = "brands.id = $brand_id";
		}

		if($contenttype_id) {
			$FROM[] = UT_DEV_CON." as contenttypes";
			$WHERE[] = "devices.id = contenttypes.device_id";
			$WHERE[] = "contenttypes.contenttype_id = $contenttype_id";
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

//		$query->sql("SELECT ".UT_DEV.".id, ".UT_DEV.".model, ".UT_BRA.".name FROM ".UT_DEV.", ".UT_BRA." WHERE ".UT_DEV.".id = ".$id." AND ".UT_DEV.".brand_id = ".UT_BRA.".id ORDER BY name, model");
//		print $query->makeQuery($SELECT, $FROM, $WHERE, $GROUP_BY, $ORDER);
		$query->sql($query->makeQuery($SELECT, $FROM, $WHERE, $GROUP_BY, $ORDER) . $limit);
		

//		$query->sql("SELECT * FROM ".UT_DEV." WHERE parent >= '0' ORDER BY phbrand_id, model");
		
		for($i = 0; $i < $query->getQueryCount(); $i++) {
			$items["id"][] = $query->getQueryResult($i, "id");

			$items["values"][] =  $query->getQueryResult($i, "brand"). ", ".$query->getQueryResult($i, "model"); 

			$items["brand"][] = $query->getQueryResult($i, "brand");
			$items["model"][] = $query->getQueryResult($i, "model");
		}

		if(!count($items)) {
			return false;
		}
		else {
			return $items;
		}
	}



	
}

?>