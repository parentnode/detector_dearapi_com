<?php
/**
* @package framework.log
*/

include_once("class/log/log.core.class.php");


/**
*
*/
class Log extends LogCore {
	

	/**
	* @return void
	*/
	function __construct() {
		// initiate helpers before calling View construct
		$this->addTranslation(__FILE__);
		parent::__construct();

	}



	/**
	* Get search items
	*
	*/
	function getSearchItems() {
	}


	/**
	* Search
	* Sets search values in session
	*/
	function search() {
		Session::setSearch("brand_id", getVar("brand_id"));
		Session::setSearch("contenttype_id", getVar("contenttype_id"));
	}
	
	/**
	* Reset Search 
	* Resets search values in session
	*/
	function searchReset() {
		Session::resetSearch("brand_id");
		Session::resetSearch("contenttype_id");
	}

	/**
	* Search form
	*
	* @return string HTML view
	*/
	function searchOptions() {
		global $HTML;

		$_ = '';
		$_ .= $HTML->head($this->translate("Search log files"));


		return $_;
	}

}

?>