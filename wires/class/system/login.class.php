<?php

/**
* This file contains login functionality
*/
class Login extends Translation {

//	private $user_id;
//	private $user_level_id;
//	private $user_name;

	public $user_id;
	public $user_level_id;
	public $user_name;

	/**
	* Get user IP
	*
	* @return String User IP
	*/
	function getUserIp() {
		return getenv("HTTP_X_FORWARDED_FOR") ? getenv("HTTP_X_FORWARDED_FOR") : getenv("REMOTE_ADDR");
	}

	/**
	* Get user id
	*
	* @return Integer User id for current user
	*/
	function getUserId() {
		return $this->user_id ? $this->user_id : false;
	}

	/**
	* Get Access level for user
	*
	* @return Integer Access level id for current user
	*/
	function getUserLevel() {
		if(!$this->user_level_id) {
			$this->sql("SELECT access_level_id FROM ".UT_USE." WHERE id = ".$this->user_id);
			$this->user_level_id = $this->getQueryResult(0, "access_level_id");
 		}
		return $this->user_level_id;
	}

	/**
	* Get Username for user
	*
	* @return String Username id for current user
	*/
	function getUserName() {
		return $this->nickname;
		/*
		if(!$this->user_name) {
			$this->sql("SELECT nickname FROM ".UT_USE." WHERE id = ".$this->user_id);
			$this->user_name = $this->getQueryResult(0, "nickname");
 		}
		return $this->user_name;
		*/
	}

	/**
	* Attempt to do login
	* On success, forward to relevant page
	* On failure, add log, login page will handle the rest
	*
	* @param String $username Username
	* @param String $password Password
	*/
	function doLogin($username, $password, $location=false) {
//	function doLogin($user_id, $nickname, $location=false) {

		if($this->sql("SELECT id, nickname FROM ".UT_USE." WHERE status > 0 AND (email = '$username' OR mobile = '$username') AND password = '".sha1($password)."'")) {
	
			if($this->getQueryCount() == 1) {
				$this->user_id = $this->getQueryResult(0, "id");
				$this->nickname = $this->getQueryResult(0, "nickname");
				Page::addLog("Login success, U:$username ". UT_USE);

				$this->translater = new Translation(LOCAL_PATH."/templates/menu.summary.php");
				//$this->compileMenu();

				$forward = Session::getLoginForward();
				$location = $forward ? $forward : ($location ? $location : "/index.php");
				header("Location: $location");
				exit();
			}
		}
		else {
			Page::addLog("Login failure, U:$username ". UT_USE);
		}

		// if($this->sql("SELECT id, access_level_id FROM ".UT_USE." WHERE user_id = '$user_id'")) {
		// 	$this->user_id = $this->getQueryResult(0, "id");
		// 	$this->nickname = $nickname;
		// 	$this->access_level_id = $this->getQueryResult(0, "access_level_id");
		// 
		// 	Page::addLog("Login success, U:".$this->user_id);
		// 
		// 	$this->translater = new Translation(LOCAL_PATH."/templates/menu.summary.php");
		// 	//$this->compileMenu();
		// 
		// 	$forward = Session::getLoginForward();
		// 	$location = $forward ? $forward : ($location ? $location : "/front/index.php");
		// 	header("Location: $location");
		// 	exit();
		// }
		// else {
		// 	Page::addLog("Login failure, U:$user_id ". UT_USE);
		// }
	}

	/**
	* Compile menu, based on menu/user access
	* Used when reloading menu after change of language/country
	* Stored in session
	*/
	function compileMenu() {
		global $page;
		Session::setMenu($page->getNavigationItems(0));
	}

	/**
	* Validate navigation item
	*
	* @param $point file point to validate
	* @param $action Optional action to validate
	*/
	function validatePoint($point, $action=false, $access_item, $access_default=false) {

//		print "action:" . $action.":".$point.":".$access_default.":";
//		print_r($access_item);
//		exit;

//		return true;

//		print "action:" . $action."<br>";

		// safety check after cleanup
		if(is_array($action)) {
			print "ERROR POINT:" . $point;
			print_r($action);
		}
		if(!$action && $access_default) {
//			print $point . "fisk";
			return $this->validatePoint($point, $access_default, $access_item);
		}
		// multiple actions (iterate)
		else if(strpos($action, ",") !== false) {
			$actions = explode(",", $action);

			foreach($actions as $action) {
				if(!$this->validatePoint($point, $action, $access_item)) {
					return false;
				}
			}

			// print "valid-c<br>";
			// exit;

			return true;
		}

		// no access restrictions
		if($access_item == false || $action == "page" || $action == "logoff") {

			// print "valid-a<br>";
			// exit;

			return true;
		}
		else if(isset($access_item[$action]) && $access_item[$action] === false) {

			// print "valid-b<br>";
			// exit;
			
			return true;
		}

		// find parent access item
		else if(isset($access_item[$action]) && $access_item[$action] !== true) {

			return $this->validatePoint($point, $access_item[$action], $access_item);
		}

		$point_id = $this->getPointId($point);
		$level_id = $this->getUserLevel();

		// print "SELECT id FROM ".UT_ACC_LEV_POI." WHERE level_id = $level_id AND point_id = $point_id".($action ? " AND action = '$action'" : "");
		// exit;

		if($this->sql("SELECT id FROM ".UT_ACC_LEV_POI." WHERE level_id = $level_id AND point_id = $point_id".($action ? " AND action = '$action'" : ""))) {
//			print "valid-s<br>";
			return true;
		}
		else {
//			print "invalid-s<br>";
			return false;
		}
		
	}

	/**
	* Validate navigation item
	*
	* @param $point file point to validate
	* @param $action Optional action to validate
	* @return boolean
	*/
	function validateNavigation($point, $action=false) {

		$read_access = true;
		if(is_file($point) && file_exists($point)) {
			include($point);
			return $this->validatePoint($point, $action, $access_item, isset($access_default) ? $access_default : false);
		}
	}

	/**
	* Validate access to page (using current page)
	*
	* @param $action Optional action to validate
	* @return boolean
	*/
	function validatePage($action=false) {

		global $access_item;
		global $access_default;
		$point = $_SERVER["SCRIPT_FILENAME"];
		return $this->validatePoint($point, $action, $access_item, isset($access_default) ? $access_default : false);
	}

	/**
	* Get id for point
	*
	* @param String $point Point to find id for
	*/
	function getPointId($point) {
		if($this->sql("SELECT id FROM ".UT_ACC_POI." WHERE file = '$point'")) {
			return $this->getQueryResult(0, "id");
		}
		else {
			return 0;
		}
	}

}

?>