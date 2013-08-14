<?php
/**
* This file contains the site backbone, the Page Class.
*/
header("Content-type: text/html; charset=UTF-8");

/**
* Include base functions and classes
*/

include_once("include/functions.inc.php");

include_once("class/system/template.class.php");
include_once("class/system/generic.class.php");


include_once("class/system/filesystem.class.php");

include_once("class/system/login.class.php");

include_once("class/system/message.class.php");
include_once("class/system/session.class.php");

include_once("class/system/performance.class.php");

/**
* Get id var
*/
$id = getVar("id");

/**
* Site backbone, the Page class
*/
class PageCore extends Template {
	
	public $url;
	public $title;
	public $description;
	public $classname;
	
	private $status;
	private $objects = array();
	
	/**
	* Get required page information
	*/
	function __construct() {
		parent::__construct();

		$this->setStatus(stringOr(getVar("page_status"), getVar("ps")));
		$this->codeErrorTxt = "";

		// set language
		if($this->getStatus("language")) {
			Session::setLanguageISO(getVar("language"));
			// language translations
		}

		$this->addTranslation(__FILE__);
		$this->addTranslation($_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF']);
	}

	/**
	* Add object to page class namespace
	*
	* @param object $object Instance of class
	* @param string $name Name of instance
	*/
	function addObject($file) {
		include_once("class/".$file);
		if(!array_key_exists($file, $this->objects)) {
			preg_match("/\b([a-zA-Z0-9_.]+).class\b/i", $file, $matches);
			$object = ucfirst(preg_replace("/_([a-z])/e", "strtoupper('$1')", $matches[1]));
			$object = ucfirst(preg_replace("/\.([a-z])/e", "strtoupper('$1')", $object));
			$this->objects[$object] = new $object();

			return $object;
		}
	}

	/**
	* Get object form page Objects
	*
	* @param string $name
	* @return object Object named $name
	*/
	function getObject($name) {
		if(array_key_exists($name, $this->objects)) {
			return $this->objects[$name];
		}
		return false;
	}

	/**
	* Get page status
	*
	* @param String $action action parameter to check for in status (status can be combined page,list)
	* @return bool Page status
	*/
	function getStatus($action = false) {
		if($action) {
			if(preg_match("/\b".$action."\b/i", $this->status)) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return $this->status;
		}
	}

	/**
	* Set page status
	*
	* @param string|bool $status Page status
	*/
	function setStatus($status){
		if(!Session::getLogin()->validatepage($status)) {
			if(!Session::getLogin()->getUserId()) {
				$this->throwOff($_SERVER["REQUEST_URI"]);
			}
			else {
				$this->throwOff();
			}
		}
		else {
			$this->status = $status;
		}
	}

	/**
	* Simple logoff
	* Logoff user and redirect to login page
	*/
	function logOff() {
		$this->addLog("Logoff ". UT_USE);
		//$this->user_id = "";
		Session::resetLogin();
		header("Location: /index.php");
		exit();
	}

	/**
	* Throw off if user is caught on page without permission
	*
	* @param String $url Optional url to forward to after login
	*/
	function throwOff($url=false) {
		$this->addLog("Login - insufficient privileges:".$this->url." ". UT_USE);
		//$this->user_id = "";
		Session::resetLogin();
		if($url) {
			Session::setLoginForward($url);
		}
		print "<script>location.href='?page_status=logoff'</script>";
//		header("Location: /index.php");
		exit();
	}

	/**
	* Throw off if user is caught on page without permission
	*/
	/*
	function throwOffScript() {
		$this->addLog("Login - insufficient privileges:".$this->url."+".$this->getStatus()." ". UT_USE);
		print "<script>location.href='?page_status=logoff'</script>";
		Session::resetLogin();
		if($url) {
			Session::setLoginForward($url);
		}
		exit();
	}
	*/

	/**
	* Hide codeErrorOutput()
	*
	* @param string $message Notification
	*/
	function codeError($HTML = true) {
		if($HTML !== true) {
			$this->codeErrorTxt .= $HTML . "<br />";
		}
		else if($HTML === false && $this->codeErrorTxt) {
			return $this->codeErrorTxt;
		}
		else if($HTML === true && $this->codeErrorTxt) {
			return '<div class="codeError"><div>'.$this->codeErrorTxt.'</div></div>';
		}
		else {
			return '';
		}
	}


	/**
	* Notify admin of a problem
	*
	* @param string $message Notification
	*/
	function notifyAdmin($message) {
		$message = $message."\n\nfile:".$this->url;
		mail("martin@think.dk", "SERVER NOTICE", $message);
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

		$log = date("Y-m-d H:i:s", $timestamp). " $user_id $user_ip $message";

		// year-month as folder
		// day as file
		$log_position = BACKUP_FILE_PATH."log/".date("Y/m", $timestamp);
		$log_cursor = BACKUP_FILE_PATH."log/".date("Y/m/Y-m-d", $timestamp);
		FileSystem::mkdirr($log_position);

		$fp = fopen($log_cursor, "a+");
		fwrite($fp, $log."\n");
		fclose($fp);

	}


	/**
	* Get all items for optional db standard result set
	*
	* @todo Result could be cached in session to minimize load on server
	* @param string $db Database to query.
	* @param string $which Optional limitation of returned result. ("id" or "values")
	* @return array|false Item array or false on error
	*/
	function getItems($db, $which=false, $optional=false) {
		Page::codeError("uncached request");
		Page::codeError("use generic directly?");
		return Generic::getItems($db, $which, false, $optional);
	}

}

?>