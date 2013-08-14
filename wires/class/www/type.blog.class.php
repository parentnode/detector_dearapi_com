<?php
/**
* @package framework
*/
include_once("class/items/type.blog.core.class.php");

/**
* www typeBlog
*
*/
class TypeBlog extends TypeBlogCore  {
	
	/**
	* Default settings
	*/
	function __construct() {
		parent::__construct();
		
		$this->addTranslation(__FILE__);
	}

}

?>