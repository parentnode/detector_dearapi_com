<?php
/**
* @package framework
*/
include_once("class/items/type.log.core.class.php");

/**
* www typeLog
*
*/
class TypeLog extends TypeLogCore  {

	/**
	* Default settings
	*/
	function __construct() {
		parent::__construct();
		
		$this->addTranslation(__FILE__);
	}

}

?>