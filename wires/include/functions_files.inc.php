<?php
/**
* This file contains generel file functions available throughout the site
*
* @package Functions
*/

/**
* Check if file is excluded
*
* @param string $file File/Folder to check
* @param array $exclude Array of items to exclude
* @return bool
*/
function validFile($file, $exclude) {
	if($file != "." && $file != ".." && substr($file, 0, 1) != "." && array_search($file, $exclude) === false) {
		return true;
	}
	else {
		return false;
	}
}

?>