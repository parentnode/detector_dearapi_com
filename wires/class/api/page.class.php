<?php
/**
* This file contains the API backbone, the Page Class.
*/

/**
* Include base configuration
*/
include_once($_SERVER["LOCAL_PATH"]."/config/config.php");

include_once("include/functions.inc.php");

include_once("class/system/translation.class.php");

include_once("class/system/filesystem.class.php");
include_once("class/system/login.class.php");
include_once("class/system/session.class.php");

include_once("class/system/performance.class.php");

/**
* API backbone, the Page class
*/
class Page extends Translation {

	/**
	* Get required page information
	*/
	function __construct() {
		parent::__construct();

//		print $_SERVER['PHP_SELF'];
//		$url = 
		$this->url = substr($_SERVER['PHP_SELF'], 0, (preg_match("/.php/i", $_SERVER['PHP_SELF']) ? strpos($_SERVER['PHP_SELF'], ".php") : count($_SERVER['PHP_SELF'])));

//		$this->addLog($this->url);
	}


	/**
	* Add log entry.
	* Adds user id and user IP along with message and optional values.
	*
	* @param string $message Log message.
	* @return bool Success
	*/
	function addLog($message) {

		$timestamp = time();

		if(Session::getLogin()) {
			$user_id = Session::getLogin()->getUserId();
			$user_ip = Session::getLogin()->getUserIp();
		}
		else {
			$user_id = "N/A";
			$user_ip = "N/A";
		}

		$log = date("Y-m-d H:i:s", $timestamp). " $user_id $user_ip ".session_id()." $message";

		// year-month as folder
		// day as file
		$log_position = BACKUP_FILE_PATH."log_api/".date("Y/m", $timestamp);
		$log_cursor = BACKUP_FILE_PATH."log_api/".date("Y/m/Y-m-d", $timestamp);
		FileSystem::mkdirr($log_position);

		$fp = fopen($log_cursor, "a+");
		fwrite($fp, $log."\n");
		fclose($fp);

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

$page = new Page();

?>