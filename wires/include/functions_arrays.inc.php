<?php
/**
* This file contains generel array functions available throughout the site
*
* @package Functions
*/

/**
* List content of array
*
* @param Array $array Array to list
* @return String Array as list
*/
function array_list($array) {
	$list = "";
	if(is_array($array)) {
		foreach($array as $value) {
			$list .= $list ? ", ".$value : $value;
		}
	}
	return $list;
}

/**
* Remove an index i from array
*
* @param Array $array Array to remove index from
* @param Integer $i Index to remove from array
* @return Array Array without the removed index
*/
function removeArrayIndex($array, $i) {
	$temp_array = null;
	for($a = 0, $b = 0; $a < count($array); $a++) {
		if($a != $i) {
			$temp_array[$b++] = $array[$a];
		}
	}
	return $temp_array;
}

/**
* Removing entries with no value
*
* @param Array $array Array to clean
* @return cleaned array
*/
function cleanArray($array) {
	$temp_array = null;
	if(is_array($array)) {
		foreach($array as $key => $value) {
			if(is_string($value) && $value != "") {
				$temp_array[$key] = $value;
			}
		}
	}
	else {
		$temp_array = $array;
	}
	return $temp_array;
}

?>