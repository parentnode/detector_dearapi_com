<?php
/**
* This file contains the site backbone, the Page Class.
*/

/**
* Include base configuration
*/
include_once("class/system/page.core.class.php");
include_once("class/www/navigation.class.php");
/**
* Site backbone, the Page class
*/
class PageWCore extends PageCore {

	/**
	* Get required page information
	*/
	function __construct() {
		parent::__construct();

		$this->url = str_replace(WWW_PATH, "", $_SERVER['PHP_SELF']);

		// login in progress
		if($this->getStatus("login")) {
			global $username;
			global $password;

			Session::setLogin(new Login());
			Session::getLogin()->doLogin($username, $password, "index.php");
		}
		else if($this->getStatus("logoff")) {

			$this->logOff();
		}
		else if($this->getStatus("segment")) {
			Session::setDevice("segment", getVar("segment"));
			header("Location: ".$this->url);
		}

		$this->addTranslation($_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF']);
		$this->addTranslation(LOCAL_PATH."/library/www.navigation.summary.php");

		// because I want to gather information about all device-useragents, also on sites not having segmentation implemented in the templates
		Session::getDevice();
	}

	/**
	* Load external template
	*
	* @param string $template Path to template
	* @param string $template_object Class object to use in template
	* @param string $response_column Column type classname
	* @param string $container_id Id of wrapping container
	* @param string $target_id If template needs to link to other target
	* @param string $silent Get template without getting message (default loud)
	*/
	//function getTemplate($template, $object=false, $response_column=false, $container_id=false){
	function getTemplate($template, $object=false, $response_column=false, $container_id=false, $target_id=false, $silent=false) {
		global $HTML;
		global $id;
		// when including template, page->status should be resat
		//$this->setStatus(false);

//		print "c". $container_id;
		$this->template_object = $object;
		$this->response_column = $response_column ? $response_column : $this->response_column;
		$this->container_id = $container_id;
		$this->target_id = $target_id;

		print ((!$silent && $container_id) ? messageHandler()->getMessages("js") : '');

		if(file_exists(LOCAL_PATH."/templates/www/".$template)) {
			$file = LOCAL_PATH."/templates/www/".$template;
		}
		else if(defined("REGIONAL_PATH") && file_exists(REGIONAL_PATH."/templates/www/".$template)) {
			$file = REGIONAL_PATH."/templates/www/".$template;
		}
		else if(defined("GLOBAL_PATH") && file_exists(GLOBAL_PATH."/templates/www/".$template)) {
			$file = GLOBAL_PATH."/templates/www/".$template;
		}
		else if(file_exists(FRAMEWORK_PATH."/templates/www/".$template)) {
			$file = FRAMEWORK_PATH."/templates/www/".$template;
		}
		else {
			$file = FRAMEWORK_PATH."/templates/defaults/".$template;
		}

		$this->addTranslation($file);
		include($file);
	}


	/**
	* Access device API and get info about current useragent
	*
	* @return Array Array containing device info, or fallback 
	*/
	// returns currently used browser info to be stored in session
	function getDevice() {

//		$perf = new Performance();
//		$perf->mark("device id, ext", true);
///		$xml = file_get_contents("http://api.metomo.com/device/xml?ua=".urlencode($_SERVER["HTTP_USER_AGENT"]));
//		$xml = file_get_contents("http://w.mtm.api/device/xml?ua=".urlencode($_SERVER["HTTP_USER_AGENT"]));
//		$perf->mark("xml");
//		$sxml = simplexml_load_string($xml);
//		$perf->mark("sxml");
//		$array = (array) $sxml;
//		$perf->mark("array");

//		print_r($array);
//		print "http://api.metomo.com/device/xml?ua=".$_SERVER["HTTP_USER_AGENT"];
//		return (array) simplexml_load_string(file_get_contents("http://w.mtm.api/device/xml?ua=".urlencode($_SERVER["HTTP_USER_AGENT"])));

//		try {
//			$device_id = file_get_contents("http://devices.dearapi.com/xml?ua=".urlencode($_SERVER["HTTP_USER_AGENT"]));

			$device_id = file_get_contents("http://devices.dearapi.com/xml?ua=".urlencode($_SERVER["HTTP_USER_AGENT"])."&site=".urlencode($_SERVER["HTTP_HOST"]));
//			$device_id = file_get_contents("http://devices.local/xml?ua=".urlencode($_SERVER["HTTP_USER_AGENT"])."&site=".urlencode($_SERVER["HTTP_HOST"]));
//			$device_id = file_get_contents("http://devices.wires.dk/xml?ua=".urlencode($_SERVER["HTTP_USER_AGENT"]));
			$device = simplexml_load_string($device_id);
			if($device) {
				return (array) $device;
			}
//		}
//		catch (getaddrinfo $e) {
//			print "e:" .  $e;
			// TODO exception?
//		}

		// offline default value
		return array("segment" => "seg_desktop");

//		$deviceClass = new Device();
//		$device_id = $deviceClass->identifyDevice($_SERVER["HTTP_USER_AGENT"]);
//		return $deviceClass->getDeviceBase($device_id);
	}


	/**
	* Expect REST params
	* Parses url and redirects to "/" if parameters or specified index does not exist
	*
	* returns array if no index is specified
	* returns string if index is specified
	* returns false if parameters (or specified index) does not exist
	* 
	* @param int $index Optional parameter index to return
	* @return boolean|array|string
	*/
	function expectRESTParams($index=false) {
		//
		$params = $this->getRestParams($index);
		if($params) {
			return $params;
		}
		else {
			header("Location: /");
		}
	}

	/**
	* Parse REST parameters from url
	* returns array if no index is specified
	* returns string if index is specified
	* returns false if parameters (or specified index) does not exist
	* 
	* @param int $index Optional parameter index to return
	* @return boolean|array|string
	*/
	function getRESTParams($index=false) {
		// no path
		if(!isset($_SERVER["PATH_INFO"]) || $_SERVER["PATH_INFO"] == "/") {
			return false;
		}
		else {
			// get params
			$params = explode("/", substr($_SERVER["PATH_INFO"], 1));
			if($index !== false && isset($params[$index])) {
				return $params[$index];
			}
			else {
				return $params;
			}
		}
		return false;
	}

}

?>