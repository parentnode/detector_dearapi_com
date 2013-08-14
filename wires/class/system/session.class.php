<?php
/**
* Start session
*/

session_start();

// for safety - known bugs in old devices, should be tested
session_regenerate_id();

/**
* This class contains session value exchange functionality
*/
class Session {

	/**
	* Get Device
	* optional specific value
	* Get device from api if it does not exist already
	*
	* @uses Page::getDevice()
	* @param string $index (optional)
	* @return string|boolean Value or false if none
	*/
	function getDevice($index = false) {
//		Page::addLog("Get device: $index");
		if(!isset($_SESSION["SESSION_VALUES"]["DEVICE"])) {
			$_SESSION["SESSION_VALUES"]["DEVICE"] = Page::getDevice();
		}
		if(!$index) {
			return $_SESSION["SESSION_VALUES"]["DEVICE"];
		}
		else if($index && isset($_SESSION["SESSION_VALUES"]["DEVICE"][$index])) {
			return $_SESSION["SESSION_VALUES"]["DEVICE"][$index];
		}
		else {
			return false;
		}
	}
	/**
	* set device proporty (mostly for testing)
	*/
	function setDevice($index, $value) {
		$_SESSION["SESSION_VALUES"]["DEVICE"][$index] = $value;
	}


	/**
	* Get Menu
	*
	* @return Navigation structure (got it or get it)
	* @uses Page::getNavigationItems()
	*/
	function getNavigation() {
//		if(empty($_SESSION["SESSION_VALUES"]["menu_structure"])) {
			Page::codeError("uncached menu request");
			Session::setNavigation(Page::getNavigationItems(0));
//		}
		return $_SESSION["SESSION_VALUES"]["menu_structure"];
	}
	/**
	* Set Navigation
	*
	* @return 
	*/
	function setNavigation($menu_structure) {
		$_SESSION["SESSION_VALUES"]["menu_structure"] = $menu_structure;
	}

	/**
	* Get language ISO code
	*
	* @return String langugae ISO code
	*/
	function getLanguageISO() {
		// TODO: check for cookie
		if(!isset($_SESSION["SESSION_VALUES"]["LANGUAGE"]["ISO"])) {
			$_SESSION["SESSION_VALUES"]["LANGUAGE"]["ISO"] = DEFAULT_LANGUAGE_ISO;
		}
		return $_SESSION["SESSION_VALUES"]["LANGUAGE"]["ISO"];
	}
	/**
	* Set language ISO code
	*
	* @parem String $language Language ISO code
	*/
	function setLanguageISO($language_iso) {
		$_SESSION["SESSION_VALUES"]["LANGUAGE"]["ISO"] = $language_iso;
	}

	/**
	* Get language ID
	*
	* @return String langugae ID
	*/
	/*
	function getLanguageId() {
		Page::codeError("DEPRECATED language_id");
		
		return Session::getLanguageISO();
	}
	*/
	/**
	* Set language ID
	*
	* @parem String $language Language ID
	*/
	/*
	function setLanguageId($language_id) {
		Page::codeError("DEPRECATED language_id");
		setLanguageISO($language_id);
	}
*/
	/**
	* Get country ISO code
	*
	* @return String country ISO code
	*/
	function getCountryISO() {
		// TODO: check for cookie
		if(!isset($_SESSION["SESSION_VALUES"]["COUNTRY"]["ISO"])) {
			$_SESSION["SESSION_VALUES"]["COUNTRY"]["ISO"] = DEFAULT_COUNTRY_ISO;
		}
		return $_SESSION["SESSION_VALUES"]["COUNTRY"]["ISO"];
	}
	/**
	* Set country ISO code
	*
	* @parem String $country Country ISO code
	*/
	function setCountryISO($country_iso) {
		$_SESSION["SESSION_VALUES"]["COUNTRY"]["ISO"] = $country_iso;
	}

	/**
	* Get country ID
	*
	* @return String country ID
	*/
	/*
	function getCountryId() {
		Page::codeError("DEPRECATED country_id");
		return Session::getCountryISO();
	}
	*/
	/**
	* Set country ID
	*
	* @parem String $country Country ID
	*/
	/*
	function setCountryId($country_id) {
		Page::codeError("DEPRECATED country_id");
		setCountryISO($country_id);
	}
	*/

	/**
	* Get Side ID
	*
	* @return String Site ID
	*/
	function getSiteId() {
		// TODO check for cookie
		if(empty($_SESSION["SESSION_VALUES"]["SITE"]["ID"])) {
			Session::setSiteId(SITE_UID);
		}
		return $_SESSION["SESSION_VALUES"]["SITE"]["ID"];
	}
	/**
	* Set Site ID
	*
	* @parem String $site_id Site ID
	*/
	function setSiteId($site_id) {
		$_SESSION["SESSION_VALUES"]["SITE"]["ID"] = $site_id;
	}

	/**
	* Get login
	*
	* @return Class instance of login class
	*/
	function getLogin() {
		if(empty($_SESSION["loginClass"])) {
			$_SESSION["loginClass"] = new Login();
//			return false;
		}
		return $_SESSION["loginClass"];
	}
	/**
	* Set login
	*
	* @param Class $login instance of login class
	*/
	function setLogin($loginClass) {
		$_SESSION["loginClass"] = $loginClass;
	}

	/**
	* Reset login
	*/
	function resetLogin() {
		Session::resetValue();
	}
	
	/**
	* Get login forward
	*
	* @return String Page-url to forward to after login
	*/
	function getLoginForward() {
		if(empty($_SESSION["loginForward"])) {
			return false;
		}
		$forward_url = $_SESSION["loginForward"];
		unset($_SESSION["loginForward"]);
		return $forward_url;
	}
	/**
	* Set login forwarding url ... 
	*
	* @param Class $login instance of login class
	*/
	function setLoginForward($forward_url) {
		$_SESSION["loginForward"] = $forward_url;
	}

	/**
	* Set file key
	*
	* @param String $key Random key
	* @param String $path Path associated with key
	*/
	/*
	function setContentKey($key, $path) {
		$_SESSION["SESSION_VALUES"]["CONTENT"][$key] = $path;
	}
	*/

	/**
	* Get file,
	* NOTICE: Get only works one time!
	* NOT WORKING CURRENTY ... file grappers (image/audio) are out of scope
	*
	* @param String $key Key to file
	* @return String Path to file
	*/
	/*
	function getContent($key) {
		$value = $_SESSION["SESSION_VALUES"]["CONTENT"][$key];
		return $value;
	}
	*/


	/**
	* Get Search
	*
	* @param String $index Optional search index (if omitted whole search index will be returned)
	* @return Array|String 
	*/
	function getSearch($index=false) {
		global $page;
		if(!empty($_SESSION["SESSION_VALUES"]["SEARCH"][$page->url])) {
			if($index) {
				return isset($_SESSION["SESSION_VALUES"]["SEARCH"][$page->url][$index]) ? $_SESSION["SESSION_VALUES"]["SEARCH"][$page->url][$index] : false;
			}
			return $_SESSION["SESSION_VALUES"]["SEARCH"][$page->url];
		}
		return false;
	}

	/**
	* Set Search
	*
	* @param String $index Search index
	* @param String $value Search value
	*/
	function setSearch($index, $value) {
		global $page;
		$_SESSION["SESSION_VALUES"]["SEARCH"][$page->url][$index] = $value;
	}

	/**
	* Reset Search
	*
	* @param String $index Optional search index (if omitted whole search index will be resat)
	*/
	function resetSearch($index=false) {
		global $page;
		if($index) {
			unset($_SESSION["SESSION_VALUES"]["SEARCH"][$page->url][$index]);
		}
		else {
			unset($_SESSION["SESSION_VALUES"]["SEARCH"][$page->url]);
		}
	}

	/**
	* Set value
	*
	* @param String $key Key
	* @param String $value Value to save
	*/
	function setValue($key, $value) {
		$_SESSION["SESSION_VALUES"]["VALUES"][$key] = $value;
	}

	/**
	* Get value
	*
	* @param String $key Key
	* @return String value
	*/
	function getValue($key) {
		if(!isset($_SESSION["SESSION_VALUES"]["VALUES"]) || !isset($_SESSION["SESSION_VALUES"]["VALUES"][$key])) {
			return false;
		}
		return $_SESSION["SESSION_VALUES"]["VALUES"][$key];
	}

	/**
	* Reset value and all sub values
	*/
	function resetValue($value = false) {
		if($value) {
			unset($_SESSION["SESSION_VALUES"]["VALUES"][$value]);
		}
		else {
			session_unset();
		}
	}

	/**
	* Reset value and all sub values
	*/
	function resetValues() {
		unset($_SESSION["SESSION_VALUES"]["VALUES"]);
	}

}

?>