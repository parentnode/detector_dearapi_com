<?php
/**
* @package framework
*/
include_once("class/system/image_tools.class.php");
include_once("class/items/type.photo.core.class.php");

/**
* www typePhoto
*
*/
class TypePhoto extends TypePhotoCore {

	/**
	* Default settings
	*/
	function __construct() {
		parent::__construct();

		$this->addTranslation(__FILE__);
	}

}

?>