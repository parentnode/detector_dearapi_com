<?php
/**
* @package framework
*/
include_once("class/items/type.html.core.class.php");

/**
* www typeHtml
*
*/
class TypeHtml extends TypeHtmlCore  {

	/**
	* Default settings
	*/
	function __construct() {
		parent::__construct();
		
		$this->addTranslation(__FILE__);
	}

}

?>