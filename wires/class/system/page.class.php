<?php
/**
* This file contains the site backbone, the Page Class.
*/

/**
* Include base configuration
*/
include_once($_SERVER["LOCAL_PATH"]."/config/config.php");
include_once("class/system/page.core.class.php");

/**
* Site backbone, the Page class
*/
class Page extends PageCore {
	
	public $trail = array();
	
	/**
	* Get required page information
	*/
	function __construct() {
		parent::__construct();
		$this->url = str_replace(ADMIN_PATH, "", $_SERVER['PHP_SELF']);

		// login in progress
		if($this->getStatus() == "login") {

			global $username;
			global $password;

			Session::setLogin(new Login());
			Session::getLogin()->doLogin($username, $password, defined('ADMIN_FRONT') ? ADMIN_FRONT :"/front/index.php");

// //			http_post_fields('http://api.users.local/login', array('username' => $username, 'password' => $password));
// //			print_r(http_parse_message($response));
// 
// 
// 			$request = new HttpRequest('http://wiresusers.dearapi.com/login', HttpRequest::METH_POST);
// //			$request = new HttpRequest('http://users.wires.dk/login', HttpRequest::METH_POST);
// 			$request->addPostFields(array('username' => $username, 'password' => $password));
// 
// 			try {
// 
// //				print "#.#".$request->send()->getBody()."#:#";
// 				$response = new DOMDocument('1.0', 'UTF-8');
// //				print $request->send()->getBody();
// 				$response->loadXML($request->send()->getBody());
// 
// 				if($response->schemaValidate(FRAMEWORK_PATH."/library/translations/login.xsd")) {
// 
// 					// $user_id = $response->getElementById("user_id");
// 					// $nickname = $response->getElementById("nickname");
// 
// 					// getElementById doesn't work (works on my 5.3 on mac)
// 					// use this workaround instead
// 					$xpath = new DOMXPath($response);
// 					$user_id = $xpath->query("//*[@id='user_id']")->item(0);
// 					$nickname = $xpath->query("//*[@id='nickname']")->item(0);
// 
// 					if($user_id && $nickname) {
// 
// 						// print $user_id->nodeValue;
// 
// 						Session::setLogin(new Login());
// 						Session::getLogin()->doLogin($user_id->nodeValue, $nickname->nodeValue, defined('ADMIN_FRONT') ? ADMIN_FRONT :"/front/index.php");
// 					}
// 				}
// 			}
// 			catch (HttpException $e) {
// 
// //				return false;
// 			}
		}
		else if($this->getStatus() == "logoff") {

			$this->logOff();
		}
		// no login, send to login page
		else if(!Session::getLogin()) {
			Session::setLoginForward($this->url);
			header("Location: /index.php");
			exit();
		}

		$this->addTranslation(__FILE__);
		$this->addTranslation(LOCAL_PATH."/library/admin.navigation.summary.php");
	}




	/**
	* Get page title
	* this is sat via page->header
	*
	* @return String page title
	*/
	function getTitle() {
		if($this->title) {
			return $this->title;
		}
		else if(Session::getValue("nav_sindex")) {
			$query = new Query();
			if($query->sql("SELECT name FROM ".UT_NAV." WHERE sindex = '".Session::getValue("sindex")."'")) {
				return $query->getQueryResult(0, "name");
			}
		}
		return SITE_NAME;
	}

	/**
	* Get body class
	* this is sat via page->header
	*
	* @return String body class
	*/
	function getClass() {
		return $this->classname ? $this->classname : "theme"."1";//rand(1, 6);
	}

	/**
	* Add page header
	*
	* @return String HTML header
	*/
	function header($title = "", $classname = "") {
		$this->title = $title;
		$this->classname = $classname;

		$this->identifyPage();
		$this->getTemplate("shell.header.php", false, 0, 0, 0, true);
	}

	/**
	* Add page footer
	*
	* @return String HTML footer
	*/
	function footer() {
		$this->getTemplate("shell.footer.php", false, 0, 0, 0, true);
	}


	/**
	* Get menu items by iterating based on relations
	*
	* @param Integer $relation Idetifier for iteration
	* @return array Item array
	*/
	function getNavigationItems($relation) {
		global $page;
		$actual_content = false;

		$query = new Query();
		$query->sql("SELECT * FROM ".UT_MEN." WHERE relation = $relation ORDER BY sequence ASC");

		$items = array();
		for($i = 0; $i < $query->getQueryCount(); $i++) {
			$id = $query->getQueryResult($i,"id");
			$url = $query->getQueryResult($i,"url");
			$name = $this->translate($query->getQueryResult($i, "name"));

			$children = $this->getNavigationItems($id);
			// see if url contains page_status action
			$url_parts = (strpos($url, "?page_status=") ? explode('?page_status=', $url) : array($url, false));

			if((!$url && $children) || Session::getLogin()->validateNavigation($url_parts[0], $url_parts[1]) || $name == "----") {
				$item = null;
				$item->url = false;
				if($name != "----"){
					$actual_content = true;
					$item->url = preg_replace("/\A\/admin/", "", removeKnownPaths($url, true));
				}
				$item->name = $name;

				// comwell fuck! Windows
//				if(substr(FRAMEWORK_PATH, 0, 2) == "D:") {
//					$item->url = str_replace(str_replace("D:", "", FRAMEWORK_PATH."/admin"), "", $url);
//					$item->url = str_replace(str_replace("D:", "", GLOBAL_PATH."/admin"), "", $item->url);
//				}
//				else {
/*
					$item->url = str_replace(FRAMEWORK_PATH.'/admin', "", $url);
					$item->url = str_replace(GLOBAL_PATH.'/admin', "", $item->url);
					$item->url = str_replace(REGIONAL_PATH.'/admin', "", $item->url);
*/
//				}

				$item->id = $id;
				$item->children = $children;
				$items[] = $item;
			}
		}
		return $actual_content ? $items : false;
	}

	/**
	* Identify the current page
	*
	* @return bool
	*/
	function identifyPage() {
//		$req_uri = $_SERVER['REQUEST_URI'];
//		print "init";
		$query = new Query();
		if($query->sql("SELECT id, relation, name, url FROM " . UT_MEN . " WHERE url LIKE '%".$this->url."%'")) {
			$item->id = $query->getQueryResult(0, "id");
			$item->name = $this->translate($query->getQueryResult(0, "name"));

			$item->url = str_replace(FRAMEWORK_PATH."/admin", "", $this->url);
			$item->url = str_replace(GLOBAL_PATH."/admin", "", $item->url);
			$item->url = str_replace(REGIONAL_PATH."/admin", "", $item->url);

//			$item->url = ereg_replace(FRAMEWORK_PATH."/admin|".GLOBAL_PATH."/admin|".REGIONAL_PATH."/admin", "", $query->getQueryResult(0, "url"));
			array_unshift($this->trail, $item);
			$relation = $query->getQueryResult(0, "relation");
			if($relation) {
				$this->pageTrail($relation);
			}
			return true;
		}
		return false;
	}

	function pageTrail($id) {
		$query = new Query();
		if($query->sql("SELECT id, relation, name, url FROM " . UT_MEN . " WHERE id = '".$id."'")) {
			$item->name = $this->translate($query->getQueryResult(0, "name"));
			$item->url = str_replace(FRAMEWORK_PATH."/admin", "", $query->getQueryResult(0, "url"));
			$item->url = str_replace(GLOBAL_PATH."/admin", "", $item->url);
			$item->url = str_replace(REGIONAL_PATH."/admin", "", $item->url);

			$item->id = $query->getQueryResult(0, "id");
			array_unshift($this->trail, $item);
			$relation = $query->getQueryResult(0, "relation");
			if($relation) {
				$this->pageTrail($relation);
			}
			return true;
		}
		return false;
		
	}

}

$page = new Page();

?>