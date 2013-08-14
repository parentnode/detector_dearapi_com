<?php
$access_item = false;if(isset($read_access) && $read_access){return;}


// create a brand
function createBrand($name){
	$query = new Query();
	if($query->sql("SELECT id FROM ".UT_BAS_BRA." WHERE name = '$name'")) {
		$brand_id = $query->getQueryResult(0, "id");
	}
	else {
		$query->sql("INSERT INTO ".UT_BAS_BRA." VALUES('','".$name."', '')");
		$brand_id = $query->getLastInsertId();
	}
	return $brand_id;
}

function createDevice($model, $brand_id) {
	$query = new Query();

	if($query->sql("SELECT id FROM ".UT_DEV." WHERE model = '".addslashes($model)."' AND brand_id = $brand_id")) {
		$device_id = $query->getQueryResult(0, "id");
	}
	else {
		$query->sql("INSERT INTO ".UT_DEV." VALUES('','".addslashes($model)."', $brand_id)");
		$device_id = $query->getLastInsertId();
	}
	return $device_id;
}


function addUseragent($device_id, $useragent) {
	$query = new Query();
	if(!$query->sql("SELECT id FROM ".UT_DEV_USE." WHERE useragent = '$useragent'")) {
		$query->sql("INSERT INTO ".UT_DEV_USE." VALUES('','".$useragent."', $device_id)");
	}
}

function addContenttype($device_id, $name) {
	if($name) {
		$query = new Query();
		if($query->sql("SELECT id FROM ".UT_BAS_CON." WHERE name = '$name'")) {
			$contenttype_id = $query->getQueryResult(0, "id");
		}
		else {
			$query->sql("INSERT INTO ".UT_BAS_CON." VALUES('','".$name."', '')");
			$contenttype_id = $query->getLastInsertId();
		}

		if(!$query->sql("SELECT id FROM ".UT_DEV_CON." WHERE device_id = $device_id AND contenttype_id = $contenttype_id")) {
			$query->sql("INSERT INTO ".UT_DEV_CON." VALUES('', $device_id, $contenttype_id)");
		}
	}
}

?>