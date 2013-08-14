<?php
/**
* This file contains user-interface and -basic functionality
*/
//include_once("user.view.class.php");
include_once("accesslevel.class.php");

include_once("class/system/generic.class.php");
include_once("class/system/validator.class.php");

/**
* User, extends user views
*/
class User extends Translation {

	public $varnames;
	public $vars;
	private $validator;

	/**
	* init, set varnames, validation rules
	*/
	function __construct() {
		// initiate helpers before calling View construct
		$this->addTranslation(__FILE__);
		$this->validator = new Validator($this);
		parent::__construct();
		$this->db = UT_USE;

		$this->accesslevelClass = new Accesslevel();
//		$this->countryClass = new Country();
//		$this->languageClass = new Language();

/*
		$this->varnames["email"] = $this->translate("Email");
		$this->validator->rule("email", "email", $this->translate("Invalid email"));
		$this->validator->rule("email", "unik", $this->translate("Email is used"), $this->db);
		// perhaps add alternate values to rule instead of optional model. Optional fails by not being able to control/encompass other entries respecfully
		// perhaps other funktion optionalRule(); ?
		//$this->validator->rule("email", "optional", "mobile");

		$this->varnames["mobile"] = $this->translate("Phone");
		$this->validator->rule("mobile", "num");
		//$this->validator->rule("mobile", "optional", "email");

		$this->varnames["password"] = $this->translate("Password (between 6 and 16 characters)");
		$this->validator->rule("password", "txt", false, 6, 16);

		$this->varnames["password2"] = $this->translate("Repeat password");
		$this->validator->rule("password2", "pwr", $this->translate("Passwords does not match"), "password");
*/
//		$this->varnames["nickname"] = $this->translate("Nickname");
//		$this->validator->rule("nickname", "unik", $this->translate("Nickname is taken"), $this->db);

/*		$this->varnames["firstname"] = $this->translate("Firstname");
		$this->validator->rule("firstname", "txt");

		$this->varnames["lastname"] = $this->translate("Lastname");
		$this->validator->rule("lastname", "txt");

		$this->varnames["image"] = $this->translate("Image");

		// 1 = enabled, 0 = disabled
		$this->varnames["status"] = $this->translate("Status");
		$this->validator->rule("status", "num");
*/

		$this->varnames["access_level_id"] = $this->translate("Access level");
		$this->validator->rule("access_level_id", "num");

/*
		$this->varnames["country_id"] = $this->translate("Country");
		$this->validator->rule("country_id", "txt", 2, 2);

		$this->varnames["language_id"] = $this->translate("Language");
		$this->validator->rule("language_id", "txt", 2, 2);

		$this->varnames["site_id"] = $this->translate("Site");
*/
		$this->vars = getVars($this->varnames);
	}

	/**
	* Get selected item
	* Makes query result available
	*
	* @param int $id Item id
	* @return bool
	* @uses Generic::getItem()
	*/
	function getItem($id) {
		return Generic::getItem($id, $this->db);
	}

	/**
	* Get selected item name
	*
	* @param int $id Item id
	* @return string|false Item name or false on error
	*/
	/*
	function getItemName($id) {
		if($this->sql("SELECT nickname FROM ".$this->db." WHERE id = $id")) {
			return $this->getQueryResult(0, "nickname");
		}
		else {
			messageHandler()->addErrorMessage($this->translate("The requested info does not exist!"));
			return false;
		}
	}
	*/

	/**
	* Get selected item email
	*
	* @param int $id Item id
	* @return string|false Item email or false on error
	*/
	/*
	function getEmail($id) {
		if($this->sql("SELECT email FROM ".$this->db." WHERE id = $id")) {
			return $this->getQueryResult(0, "email");
		}
		else {
			messageHandler()->addErrorMessage($this->translate("The requested info does not exist!"));
			return false;
		}
	}
	*/

	/**
	* Get selected item Access Level ID
	*
	* @param int $id Item id
	* @return Integer|false Item access_level_id or false on error
	*/
	function getAccessLevelId($id) {
		if($this->sql("SELECT access_level_id FROM ".$this->db." WHERE id = $id")) {
			return $this->getQueryResult(0, "access_level_id");
		}
		else {
			messageHandler()->addErrorMessage($this->translate("The requested info does not exist!"));
			return false;
		}
	}

	/**
	* Get all items
	*
	* @param string $which Optional limitation of returned result. ("id" or "values")
	* @return array|false Item array or false on error
	*/
	function getItems($which=false) {
		$items = array();
		$query = new Query();
		$query->sql("SELECT id, user_id, access_level_id FROM $this->db");
		
		for($i = 0; $i < $query->getQueryCount(); $i++) {
			$items["id"][$i] = $query->getQueryResult($i, "id");
			$items["user_id"][$i] = $query->getQueryResult($i, "user_id");
//			$items["nickname"][$i] = $query->getQueryResult($i, "nickname");
			$items["access_level_id"][$i] = $query->getQueryResult($i, "access_level_id");
			$items["access_level"][$i] = $this->accesslevelClass->getItemName($query->getQueryResult($i, "access_level_id"));
		}

		if(!count($items)) {
			return false;
		}
		else if($which) {
			return $items[$which];
		}
		else {
			return $items;
		}
	}

	/**
	* User status options
	* Static values
	*
	* @return Array Array of status options
	*/
	/*
	function getStatusOptions() {
		global $page;
		$options["id"][] = 1;
		$options["values"][] = $this->translate("enabled");
		$options["id"][] = 0;
		$options["values"][] = $this->translate("disabled");
		return $options;
	}
	*/

	/**
	* Get status value
	*
	* @param Integer $id Index-id of value
	* @return String Value of Status index
	*/
	/*
	function getStatusValue($id) {
		$status = $this->getStatusOptions();
		$index = array_search($id, $status["id"]);
		if($index !== false){
			return $status["values"][$index];
		}
		else {
			return "N/A";
		}
	}
	*/

	/**
	* Encrypt password 
	*
	* @param String $string Password to encrypt
	* @return String Encrypted password
	*/
	/*
	function encryptPassword($string) {
		return sha1($string);
	}
	*/

	/**
	* Save new item, based on submitted values
	*
	* @return bool
	* @uses Message
	*/
	/*
	function saveItem() {
		if($this->validator->validateAll("nickname", "image")) {
			$vars = "DEFAULT";
			$vars .= ",'".$this->vars['email']."'";
			$vars .= ",'".$this->vars['mobile']."'";
			$vars .= ",'".$this->encryptPassword($this->vars['password'])."'";
			$vars .= ",'".$this->vars['firstname']." ".$this->vars['lastname']."'"; // nickname
			$vars .= ",'".$this->vars['firstname']."'";
			$vars .= ",'".$this->vars['lastname']."'";
			$vars .= ",".$this->vars['status'];
			$vars .= ",CURRENT_TIMESTAMP";

			$vars .= ",".$this->vars['access_level_id'];
			$vars .= ",'".$this->vars['country_id']."'";
			$vars .= ",'".$this->vars['language_id']."'";
			$vars .= ",'".SITE_UID."'";

			if($this->sql("INSERT INTO ".$this->db." VALUES($vars)")) {
				messageHandler()->addStatusMessage($this->translate("User saved"));
				return $this->getLastInsertId();
			}
			else {
				messageHandler()->addErrorMessage($this->dbError());
				return false;
			}
		}
		else {
			messageHandler()->addErrorMessage($this->translate("Please complete missing information"));
			return false;
		}
	}
	*/

	/**
	* Update edited item
	*
	* @param int $id Item id
	* @return bool
	* @uses Message
	*/
	/*
	function updateItem($id) {
		if($this->validator->validateAll("password", "password2")) {
			$vars = "email='".$this->vars['email']."'";
			$vars .= ",mobile='".$this->vars['mobile']."'";
			$vars .= ",nickname='".$this->vars['nickname']."'";

			$vars .= ",firstname='".$this->vars['firstname']."'";
			$vars .= ",lastname='".$this->vars['lastname']."'";

			$vars .= ",country_id='".$this->vars['country_id']."'";
			$vars .= ",language_id='".$this->vars['language_id']."'";

			if(Session::getLogin()->getUserLevel() == 1) {
				$vars .= ",status=".$this->vars['status'];
				$vars .= ",access_level_id=".$this->vars['access_level_id'];
			}

			if($this->sql("UPDATE ".$this->db." SET $vars WHERE id = $id")) {
				messageHandler()->addStatusMessage($this->translate("User updated"));
				return true;
			}
			else {
				messageHandler()->addErrorMessage($this->dbError());
				return false;
			}
		}
		else {
			messageHandler()->addErrorMessage($this->translate("Please complete missing information"));
			return false;
		}
	}
	*/

	/**
	* Update edited item
	*
	* @param int $id Item id
	* @return bool
	* @uses Message
	*/
	/*
	function updateProfile($id) {
		if($this->validator->validateAll("password", "password2", "status", "access_level_id")) {
			$vars = "email='".$this->vars['email']."'";
			$vars .= ",mobile='".$this->vars['mobile']."'";
			$vars .= ",nickname='".$this->vars['nickname']."'";

			$vars .= ",firstname='".$this->vars['firstname']."'";
			$vars .= ",lastname='".$this->vars['lastname']."'";

			$vars .= ",country_id='".$this->vars['country_id']."'";
			$vars .= ",language_id='".$this->vars['language_id']."'";

			if($this->sql("UPDATE ".$this->db." SET $vars WHERE id = $id")) {
				messageHandler()->addStatusMessage($this->translate("User updated"));
				return true;
			}
			else {
				messageHandler()->addErrorMessage($this->dbError());
				return false;
			}
		}
		else {
			messageHandler()->addErrorMessage($this->translate("Please complete missing information"));
			return false;
		}
	}
	*/

	/**
	* Delete selected item
	*
	* @param int $id Item id
	* @return bool
	* @uses Message
	*/
	/*
	function deleteItem($id) {
		if(!$this->checkUsage($id) && $id != Session::getLogin()->getUserId()) {
			if($this->sql("DELETE FROM ".$this->db." WHERE id = $id")) {
				messageHandler()->addStatusMessage($this->translate("Item deleted"));
				return true;
			}
			else {
				messageHandler()->addErrorMessage($this->dbError());
				return false;
			}
		}
		else {
			messageHandler()->addErrorMessage($this->translate("User is currently being used"));
		}
	}
	^*/

	/**
	* Update password
	*
	* @param Integer $id Item id
	* @return bool
	* @uses Message
	*/
	/*
	function updatePassword($id) {
		if($this->validator->validateList("password", "password2")) {
			$vars = "password='".$this->encryptPassword($this->vars['password'])."'";
			if($this->sql("UPDATE ".$this->db." SET $vars WHERE id = $id")) {
				messageHandler()->addStatusMessage($this->translate("Password updated"));
				return true;
			}
			else {
				messageHandler()->addErrorMessage($this->dbError());
				return false;
			}
		}
		else {
			messageHandler()->addErrorMessage($this->translate("Please complete missing information"));
			return false;
		}
	}
	*/

	/**
	* Check if the given item has been used for action
	* (if item appear in the action log)
	*
	* @param Integer $id id
	* @return bool True if user has been used for actions, false if not
	*/
	function checkUsage($id) {
		return false;

		if($this->sql("SELECT id FROM ".UT_ITE." WHERE user_id = $id") || $this->sql("SELECT id FROM ".UT_TAG_BLI." WHERE user_id = $id") || $this->sql("SELECT id FROM ".UT_TAG_CLI." WHERE user_id = $id") || $id == Session::getLogin()->getUserId()) {
			return true;
		}
		else {
			return false;
		}
	}

}
?>