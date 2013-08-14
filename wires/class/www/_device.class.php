<?php
/**
* This file contains site menu maintenance functionality
*/
include_once("class/devices/device.core.class.php");

/**
* Navigation, extends NavigationCore
*
*/
class Device extends DeviceCore {

	// used as menu structure container
//	public $menu_layout;
//	public $item_indent;

//	public $varnames;
//	public $vars;
//	private $validator;

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {
		// initiate helpers
		$this->addTranslation(__FILE__);

		parent::__construct();
	}



}

?>