<?php
/**
* @package framework.devices
*/
/**
*
*/

include_once("class/system/performance.class.php");


class DeviceCore extends Translation {
	
	/**
	* Init, set varnames, validation rules
	* @return void
	*/
	function __construct() {
		// initiate helpers before calling View construct
		$this->addTranslation(__FILE__);
		$this->db = UT_DEV;

		$this->perf = new Performance();

	}

	/**
	* Get selected item name
	*
	* @param int $id Item id
	* @return string|false Item name or false on error
	*/
	function getItemName($id) {
		$query = new Query();
		$query->sql("SELECT ".UT_DEV.".model, ".UT_BAS_BRA.".name as brand FROM ".UT_DEV.", ".UT_BAS_BRA." WHERE ".UT_DEV.".id = ".$id." AND ".UT_DEV.".brand_id = ".UT_BAS_BRA.".id");
		return $query->getQueryResult(0,"model").", ".$query->getQueryResult(0,"brand");
	}

	function getModels($brand_id) {
		$models = $this->getItems($brand_id);
		$values["id"] = $models["id"];
		$values["values"] = $models["model"];
		return $values;
	}

	/**
	* Get all devices
	*
	* @return array|false Item array or false on error
	*/
	function getItems($brand_id=false, $contenttype_id=false, $useragent_pattern=false, $limit=false) {

		$items = array();

		$query = new Query();

		$SELECT = array();
		$FROM = array();
		$WHERE = array();
		$GROUP_BY = "";
		$ORDER = array();


		$SELECT[] = "devices.id as id";
		$SELECT[] = "devices.model as model";
		$SELECT[] = "brands.name as brand";
		$SELECT[] = "brands.id as brand_id";

		$FROM[] = UT_DEV." as devices";
		$FROM[] = UT_BAS_BRA." as brands";

		$GROUP_BY = "devices.id";
//		$WHERE[] = "devices.id = ".$id;
		$WHERE[] = "devices.brand_id = brands.id";
		if($brand_id) {
			$WHERE[] = "brands.id = $brand_id";
		}

		if($contenttype_id) {
			$FROM[] = UT_DEV_CON." as contenttypes";
			$WHERE[] = "devices.id = contenttypes.device_id";
			$WHERE[] = "contenttypes.contenttype_id = $contenttype_id";
		}

		if($useragent_pattern) {
			$FROM[] = UT_DEV_USE." as useragents";
			$WHERE[] = "devices.id = useragents.device_id";
			$WHERE[] = "(useragents.useragent LIKE '%$useragent_pattern%' OR devices.model LIKE '%$useragent_pattern%')";
		}
		
		if(!$limit) {
			$ORDER[] = "brand";
			$ORDER[] = "model";
		}

		// if limit, order by time
		if($limit) {
			$limit = " LIMIT $limit";
			$ORDER[] = "id DESC";
		}

//		$query->sql("SELECT ".UT_DEV.".id, ".UT_DEV.".model, ".UT_BAS_BRA.".name FROM ".UT_DEV.", ".UT_BAS_BRA." WHERE ".UT_DEV.".id = ".$id." AND ".UT_DEV.".brand_id = ".UT_BAS_BRA.".id ORDER BY name, model");
//		print $query->makeQuery($SELECT, $FROM, $WHERE, $GROUP_BY, $ORDER);
		$query->sql($query->makeQuery($SELECT, $FROM, $WHERE, $GROUP_BY, $ORDER) . $limit);
		

//		$query->sql("SELECT * FROM ".UT_DEV." WHERE parent >= '0' ORDER BY phbrand_id, model");
		
		for($i = 0; $i < $query->getQueryCount(); $i++) {
			$items["id"][] = $query->getQueryResult($i, "id");

			$items["values"][] =  $query->getQueryResult($i, "brand"). ", ".$query->getQueryResult($i, "model"); 

			$items["brand"][] = $query->getQueryResult($i, "brand");
			$items["brand_id"][] = $query->getQueryResult($i, "brand_id");
			$items["model"][] = $query->getQueryResult($i, "model");

			$items["display"][] = $this->getDeviceProporty("display", $query->getQueryResult($i, "id"));
			$items["browser"][] = $this->getDeviceProporty("browser", $query->getQueryResult($i, "id"));
			$items["segment"][] = $this->getDeviceSegment($query->getQueryResult($i, "id"));

		}

		if(!count($items)) {
			return false;
		}
		else {
			return $items;
		}
	}

	// added optional logging - now function can be used for manual identification work
	function identifyDevice($useragent, $log=true) {
		$query = new Query();

//		$this->perf->mark("identify", true);

		// perfect match
		if($query->sql("SELECT device_id FROM ".UT_DEV_USE." WHERE useragent = '$useragent'")) {

//			$this->perf->mark("identified");

			$device_id = $query->getQueryResult(0, "device_id");
			if($log) {
				Page::addLog("UA IDENTIFIED" . $this->logString("IDENTIFIED", $useragent, $device_id));
			}
			return array("method" => "match", "device_id" => $device_id);
		}
		// unidentified
		else {

//			$this->perf->mark("guessing");

			// Unique identifiers

			// IE specific (limit scope for extensive search)
			if(preg_match("/MSIE/", $useragent)) {

				// Desktop
				if(!preg_match("/phone|opera|chromeframe|iemobile/i", $useragent)) {

					// IE 10
					if(preg_match("/MSIE 10.0; Windows NT [5-6]{1}.[0-2]{1}/", $useragent)) {
						return $this->IDUnique($useragent, "MSIE 10.0, Desktop", $log);
					}

					// IE 9
					if(preg_match("/MSIE 9.0; Windows NT [5-6]{1}.[0-2]{1}/", $useragent)) {
						return $this->IDUnique($useragent, "MSIE 9.0, Desktop", $log);
					}

					// IE 8
					if(preg_match("/MSIE 8.0; Windows NT [5-6]{1}.[0-2]{1}/", $useragent)) {
						return $this->IDUnique($useragent, "MSIE 8.0, Desktop", $log);
					}

					// IE 7
					if(preg_match("/MSIE 7.0; Windows NT [5-6]{1}.[0-2]{1}/", $useragent)) {
						return $this->IDUnique($useragent, "MSIE 7.0, Desktop", $log);
					}

					// IE 6
					if(preg_match("/MSIE 6.0; Windows NT [5-6]{1}.[0-2]{1}/", $useragent)) {
						return $this->IDUnique($useragent, "MSIE 6.0, Desktop", $log);
					}

				}
				// chromeframes
				else if(preg_match("/chromeframe/i", $useragent)) {

					// chromeFrame 27 - IN TEST
					if(preg_match("/chromeframe\/27.0/", $useragent)) {
						return $this->IDUniqueTest($useragent, "ChromeFrame 27.0, Desktop", $log);
					}

					// chromeFrame 26 - IN TEST
					if(preg_match("/chromeframe\/26.0/", $useragent)) {
						return $this->IDUniqueTest($useragent, "ChromeFrame 26.0, Desktop", $log);
					}

					// chromeFrame 25
					if(preg_match("/chromeframe\/25.0/", $useragent)) {
						return $this->IDUnique($useragent, "ChromeFrame 25.0, Desktop", $log);
					}

					// chromeFrame 24
					if(preg_match("/chromeframe\/24.0/", $useragent)) {
						return $this->IDUnique($useragent, "ChromeFrame 24.0, Desktop", $log);
					}

					// chromeFrame 23
					if(preg_match("/chromeframe\/23.0/", $useragent)) {
						return $this->IDUnique($useragent, "ChromeFrame 23.0, Desktop", $log);
					}

					// chromeFrame 22
					if(preg_match("/chromeframe\/22.0/", $useragent)) {
						return $this->IDUnique($useragent, "ChromeFrame 22.0, Desktop", $log);
					}

					// chromeFrame 21
					if(preg_match("/chromeframe\/21.0/", $useragent)) {
						return $this->IDUnique($useragent, "ChromeFrame 21.0, Desktop", $log);
					}

					// chromeFrame 20
					if(preg_match("/chromeframe\/20.0/", $useragent)) {
						return $this->IDUnique($useragent, "ChromeFrame 20.0, Desktop", $log);
					}

					// chromeFrame 19
					if(preg_match("/chromeframe\/19.0/", $useragent)) {
						return $this->IDUnique($useragent, "ChromeFrame 19.0, Desktop", $log);
					}

					// chromeFrame 18
					if(preg_match("/chromeframe\/18.0/", $useragent)) {
						return $this->IDUnique($useragent, "ChromeFrame 18.0, Desktop", $log);
					}

					// chromeFrame 17
					if(preg_match("/chromeframe\/17.0/", $useragent)) {
						return $this->IDUnique($useragent, "ChromeFrame 17.0, Desktop", $log);
					}

					// chromeFrame 16 - IN TEST
					if(preg_match("/chromeframe\/16.0/", $useragent)) {
						return $this->IDUniqueTest($useragent, "ChromeFrame 16.0, Desktop", $log);
					}

					// chromeFrame 15 - IN TEST
					if(preg_match("/chromeframe\/15.0/", $useragent)) {
						return $this->IDUniqueTest($useragent, "ChromeFrame 15.0, Desktop", $log);
					}

					// chromeFrame 14 - IN TEST
					if(preg_match("/chromeframe\/14.0/", $useragent)) {
						return $this->IDUniqueTest($useragent, "ChromeFrame 14.0, Desktop", $log);
					}

					// chromeFrame 13 - IN TEST
					if(preg_match("/chromeframe\/13.0/", $useragent)) {
						return $this->IDUniqueTest($useragent, "ChromeFrame 13.0, Desktop", $log);
					}

					// chromeFrame 12 - IN TEST
					if(preg_match("/chromeframe\/12.0/", $useragent)) {
						return $this->IDUniqueTest($useragent, "ChromeFrame 12.0, Desktop", $log);
					}

				}

			}

			// Firefox specific (limit scope for extensive search)
			if(preg_match("/Firefox/", $useragent) && !preg_match("/phone|mobile|fennec|tablet|maemo|kylo/i", $useragent)) {

				// Firefox 22 - IN TEST
				if(preg_match("/rv:22.0[^$]+Gecko[^$]+Firefox\/22.0/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Firefox 22.0, Desktop", $log);
				}

				// Firefox 21 - IN TEST
				if(preg_match("/rv:21.0[^$]+Gecko[^$]+Firefox\/21.0/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Firefox 21.0, Desktop", $log);
				}

				// Firefox 20 - IN TEST
				if(preg_match("/rv:20.0[^$]+Gecko[^$]+Firefox\/20.0/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Firefox 20.0, Desktop", $log);
				}

				// Firefox 19 - IN TEST
				if(preg_match("/rv:19.0[^$]+Gecko[^$]+Firefox\/19.0/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Firefox 19.0, Desktop", $log);
				}

				// Firefox 18
				if(preg_match("/rv:18.0[^$]+Gecko[^$]+Firefox\/18.0/", $useragent)) {
					return $this->IDUnique($useragent, "Firefox 18.0, Desktop", $log);
				}

				// Firefox 17
				if(preg_match("/rv:17.0[^$]+Gecko[^$]+Firefox\/17.0/", $useragent)) {
					return $this->IDUnique($useragent, "Firefox 17.0, Desktop", $log);
				}

				// Firefox 16
				if(preg_match("/rv:16.0[^$]+Gecko[^$]+Firefox\/16.0/", $useragent)) {
					return $this->IDUnique($useragent, "Firefox 16.0, Desktop", $log);
				}

				// Firefox 15
				if(preg_match("/rv:15.0[^$]+Gecko[^$]+Firefox\/15.0/", $useragent)) {
					return $this->IDUnique($useragent, "Firefox 15.0, Desktop", $log);
				}

				// Firefox 14
				if(preg_match("/rv:14.0[^$]+Gecko[^$]+Firefox\/14.0/", $useragent)) {
					return $this->IDUnique($useragent, "Firefox 14.0, Desktop", $log);
				}

				// Firefox 13
				if(preg_match("/rv:13.0[^$]+Gecko[^$]+Firefox\/13.0/", $useragent)) {
					return $this->IDUnique($useragent, "Firefox 13.0, Desktop", $log);
				}

				// Firefox 12
				if(preg_match("/rv:12.0[^$]+Gecko[^$]+Firefox\/12.0/", $useragent)) {
					return $this->IDUnique($useragent, "Firefox 12.0, Desktop", $log);
				}

				// Firefox 11 - IN TEST
				if(preg_match("/rv:11.0[^$]+Gecko[^$]+Firefox\/11.0/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Firefox 11.0, Desktop", $log);
				}

				// Firefox 10
				if(preg_match("/rv:10.0[^$]+Gecko[^$]+Firefox\/10.0/", $useragent)) {
					return $this->IDUnique($useragent, "Firefox 10.0, Desktop", $log);
				}

				// Firefox 9
				if(preg_match("/rv:9.0[^$]+Gecko[^$]+Firefox\/9.0/", $useragent)) {
					return $this->IDUnique($useragent, "Firefox 9.0, Desktop", $log);
				}

				// Firefox 8
				if(preg_match("/rv:8.0[^$]+Gecko[^$]+Firefox\/8.0/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Firefox 8.0, Desktop", $log);
				}

				// Firefox 7
				if(preg_match("/rv:7.0[^$]+Gecko[^$]+Firefox\/7.0/", $useragent)) {
					return $this->IDUnique($useragent, "Firefox 7.0, Desktop", $log);
				}

				// Firefox 6
				if(preg_match("/rv:6.0[^$]+Gecko[^$]+Firefox\/6.0/", $useragent)) {
					return $this->IDUnique($useragent, "Firefox 6.0, Desktop", $log);
				}

				// Firefox 5
				if(preg_match("/rv:5.0[^$]+Gecko[^$]+Firefox\/5.0/", $useragent)) {
					return $this->IDUnique($useragent, "Firefox 5.0, Desktop", $log);
				}

				// Firefox 4
				if(preg_match("/rv:2.0[^$]+Gecko[^$]+Firefox\/4.0/", $useragent)) {
					return $this->IDUnique($useragent, "Firefox 4.0, Desktop", $log);
				}

				// Firefox 3.5 + 3.6
				if(preg_match("/rv:1.9.[1-2]{1}[^$]+Gecko[^$]+Firefox\/3.[5-6]{1}/", $useragent)) {
					return $this->IDUnique($useragent, "Firefox 3.5, Desktop", $log);
				}

				// Firefox 3.0
				if(preg_match("/rv:1.9.0[^$]+Gecko[^$]+Firefox\/3.0/", $useragent)) {
					return $this->IDUnique($useragent, "Firefox 3.0, Desktop", $log);
				}

			}

			// Chrome specific (limit scope for extensive search)
			if(preg_match("/Chrome/", $useragent) && !preg_match("/phone|mobile|chromeframe|android/i", $useragent)) {

				// Chrome 29 - IN TEST
				if(preg_match("/AppleWebKit\/537[^$]+Gecko[^$]+Chrome\/29.0[^$]+Safari\/537/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Chrome 29.0, Desktop", $log);
				}

				// Chrome 28 - IN TEST
				if(preg_match("/AppleWebKit\/537[^$]+Gecko[^$]+Chrome\/28.0[^$]+Safari\/537/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Chrome 28.0, Desktop", $log);
				}

				// Chrome 27 - IN TEST
				if(preg_match("/AppleWebKit\/537[^$]+Gecko[^$]+Chrome\/27.0[^$]+Safari\/537/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Chrome 27.0, Desktop", $log);
				}

				// Chrome 26
				if(preg_match("/AppleWebKit\/537[^$]+Gecko[^$]+Chrome\/26.0[^$]+Safari\/537/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 26.0, Desktop", $log);
				}

				// Chrome 25
				if(preg_match("/AppleWebKit\/537[^$]+Gecko[^$]+Chrome\/25.0[^$]+Safari\/537/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 25.0, Desktop", $log);
				}

				// Chrome 24
				if(preg_match("/AppleWebKit\/537[^$]+Gecko[^$]+Chrome\/24.0[^$]+Safari\/537/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 24.0, Desktop", $log);
				}

				// Chrome 23
				if(preg_match("/AppleWebKit\/537[^$]+Gecko[^$]+Chrome\/23.0[^$]+Safari\/537/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 23.0, Desktop", $log);
				}

				// Chrome 22
				if(preg_match("/AppleWebKit\/537[^$]+Gecko[^$]+Chrome\/22.0[^$]+Safari\/537/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 22.0, Desktop", $log);
				}

				// Chrome 21
				if(preg_match("/AppleWebKit\/537[^$]+Gecko[^$]+Chrome\/21.0[^$]+Safari\/537/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 21.0, Desktop", $log);
				}

				// Chrome 20
				if(preg_match("/AppleWebKit\/536[^$]+Gecko[^$]+Chrome\/20.0[^$]+Safari\/536/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 20.0, Desktop", $log);
				}

				// Chrome 19
				if(preg_match("/AppleWebKit\/536[^$]+Gecko[^$]+Chrome\/19.0[^$]+Safari\/536/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 19.0, Desktop", $log);
				}

				// Chrome 18
				if(preg_match("/AppleWebKit\/535[^$]+Gecko[^$]+Chrome\/18.0[^$]+Safari\/535/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 18.0, Desktop", $log);
				}

				// Chrome 17
				if(preg_match("/AppleWebKit\/535[^$]+Gecko[^$]+Chrome\/17.0[^$]+Safari\/535/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 17.0, Desktop", $log);
				}

				// Chrome 16
				if(preg_match("/AppleWebKit\/535[^$]+Gecko[^$]+Chrome\/16.0[^$]+Safari\/535/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 16.0, Desktop", $log);
				}

				// Chrome 15.0
				if(preg_match("/AppleWebKit\/535[^$]+Gecko[^$]+Chrome\/15.0[^$]+Safari\/535/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 15.0, Desktop", $log);
				}

				// Chrome 14.0
				if(preg_match("/AppleWebKit\/535[^$]+Gecko[^$]+Chrome\/14.0[^$]+Safari\/535/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 14.0, Desktop", $log);
				}

				// Chrome 13.0
				if(preg_match("/AppleWebKit\/535[^$]+Gecko[^$]+Chrome\/13.0[^$]+Safari\/535/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 13.0, Desktop", $log);
				}

				// Chrome 12.0
				if(preg_match("/AppleWebKit\/534[^$]+Gecko[^$]+Chrome\/12.0[^$]+Safari\/534/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 12.0, Desktop", $log);
				}

				// Chrome 11.0
				if(preg_match("/AppleWebKit\/534[^$]+Gecko[^$]+Chrome\/11.0[^$]+Safari\/534/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 11.0, Desktop", $log);
				}

				// Chrome 10.0
				if(preg_match("/AppleWebKit\/534[^$]+Gecko[^$]+Chrome\/10.0[^$]+Safari\/534/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 10.0, Desktop", $log);
				}

				// Chrome 9.0
				if(preg_match("/AppleWebKit\/534[^$]+Gecko[^$]+Chrome\/9.0[^$]+Safari\/534/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 9.0, Desktop", $log);
				}

				// Chrome 8.0
				if(preg_match("/AppleWebKit\/534[^$]+Gecko[^$]+Chrome\/8.0[^$]+Safari\/534/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 8.0, Desktop", $log);
				}

				// Chrome 7.0
				if(preg_match("/AppleWebKit\/534[^$]+Gecko[^$]+Chrome\/7.0[^$]+Safari\/534/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 7.0, Desktop", $log);
				}

				// Chrome 6.0
				if(preg_match("/AppleWebKit\/534[^$]+Gecko[^$]+Chrome\/6.0[^$]+Safari\/534/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 8.0, Desktop", $log);
				}

				// Chrome 5.0
				if(preg_match("/AppleWebKit\/533[^$]+Gecko[^$]+Chrome\/5.0[^$]+Safari\/533/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 5.0, Desktop", $log);
				}

			}


			// iPhone chrome specific (limit scope for extensive search)
			if(preg_match("/CriOS/", $useragent) && !preg_match("/chromeframe|android/i", $useragent)) {

				// Chrome for iPad 19 - IN TEST
				if(preg_match("/iPad[^$]+AppleWebKit\/534[^$]+Gecko[^$]+CriOS\/19.0/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Chrome 19.0, iPad", $log);
				}
				// Chrome for iPad 21
				if(preg_match("/iPad[^$]+AppleWebKit\/534[^$]+Gecko[^$]+CriOS\/21.0/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 21.0, iPad", $log);
				}
				// Chrome for iPad 23
				if(preg_match("/iPad[^$]+AppleWebKit\/53[456]{1}[^$]+Gecko[^$]+CriOS\/23.0/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 23.0, iPad", $log);
				}
				// Chrome for iPad 25 - IN TEST
				if(preg_match("/iPad[^$]+AppleWebKit\/53[456]{1}[^$]+Gecko[^$]+CriOS\/25.0/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Chrome 25.0, iPad", $log);
				}

				// Chrome for iPod 19 - IN TEST
				if(preg_match("/iPod[^$]+AppleWebKit\/534[^$]+Gecko[^$]+CriOS\/19.0/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Chrome 19.0, iPod", $log);
				}
				// Chrome for iPod 21 - IN TEST
				if(preg_match("/iPod[^$]+AppleWebKit\/534[^$]+Gecko[^$]+CriOS\/21.0/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Chrome 21.0, iPod", $log);
				}
				// Chrome for iPod 23 - IN TEST
				if(preg_match("/iPod[^$]+AppleWebKit\/53[456]{1}[^$]+Gecko[^$]+CriOS\/23.0/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Chrome 23.0, iPod", $log);
				}
				// Chrome for iPod 25 - IN TEST
				if(preg_match("/iPod[^$]+AppleWebKit\/53[456]{1}[^$]+Gecko[^$]+CriOS\/25.0/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Chrome 25.0, iPod", $log);
				}

				// Chrome for iPhone 19 - IN TEST
				if(preg_match("/iPhone[^$]+AppleWebKit\/534[^$]+Gecko[^$]+CriOS\/19.0/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Chrome 19.0, iPhone", $log);
				}
				// Chrome for iPhone 21
				if(preg_match("/iPhone[^$]+AppleWebKit\/534[^$]+Gecko[^$]+CriOS\/21.0/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 21.0, iPhone", $log);
				}
				// Chrome for iPhone 23
				if(preg_match("/iPhone[^$]+AppleWebKit\/53[456]{1}[^$]+Gecko[^$]+CriOS\/23.0/", $useragent)) {
					return $this->IDUnique($useragent, "Chrome 23.0, iPhone", $log);
				}
				// Chrome for iPhone 25 - IN TEST
				if(preg_match("/iPhone[^$]+AppleWebKit\/53[456]{1}[^$]+Gecko[^$]+CriOS\/25.0/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Chrome 25.0, iPhone", $log);
				}

			}


			// Desktop Safari specific (limit scope for extensive search)
			if(preg_match("/AppleWebKit\/53[123456]{1}/", $useragent) && !preg_match("/htc|mobile|iphone|ipod|ipad|android|symbian|blackberry/i", $useragent)) {

				// Safari 6.0 - IN TEST
				if(preg_match("/AppleWebKit\/53[6]{1}[^$]+Gecko[^$]+Version\/6.[01]{1}[^$]+Safari\/53[6]{1}/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Safari 6.0, Desktop", $log);
				}

				// Safari 5.0
				if(preg_match("/AppleWebKit\/53[345]{1}[^$]+Gecko[^$]+Version\/5.[01]{1}[^$]+Safari\/53[345]{1}/", $useragent)) {
					return $this->IDUnique($useragent, "Safari 5.0, Desktop", $log);
				}

				// Safari 4.1
				if(preg_match("/AppleWebKit\/533[^$]+Gecko[^$]+Version\/4.1[^$]+Safari\/533/", $useragent)) {
					return $this->IDUnique($useragent, "Safari 4.1, Desktop", $log);
				}

				// Safari 4.0
				if(preg_match("/AppleWebKit\/53[12]{1}[^$]+Gecko[^$]+Version\/4.0[^$]+Safari\/53[12]{1}/", $useragent)) {
					return $this->IDUnique($useragent, "Safari 4.0, Desktop", $log);
				}

			}

			// iPad Mobile Safari specific (limit scope for extensive search)
			if(preg_match("/iPad[^$]+CPU OS[^$]+AppleWebKit/", $useragent) && !preg_match("/crios|htc|ipod|android|symbian|blackberry|fban|opera|firefox/i", $useragent)) {

				// iPad Mobile Safari 6.0 - IN TEST
				if(preg_match("/AppleWebKit\/536[^$]+Gecko[^$]+Mobile\/10/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Mobile Safari 6, iPad", $log);
				}

				// iPad Mobile Safari 5.0
				if(preg_match("/AppleWebKit\/53[34]{1}[^$]+Gecko[^$]+Mobile\/[89]{1}/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Mobile Safari 5, iPad", $log);
				}

				// iPad Mobile Safari 4.0
				if(preg_match("/AppleWebKit\/53[123]{1}[^$]+Gecko[^$]+Mobile\/[78]{1}/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Mobile Safari 4, iPad", $log);
				}

			}

			// iPhone Mobile Safari specific (limit scope for extensive search)
			if(preg_match("/iPhone[^$]+iPhone OS[^$]+AppleWebKit/", $useragent) && !preg_match("/crios|htc|ipod|ipad|android|symbian|blackberry|fban|opera|firefox/i", $useragent)) {

				// iPhone Mobile Safari 6.0 - IN TEST
				if(preg_match("/AppleWebKit\/536[^$]+Gecko[^$]+Mobile\/10/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Mobile Safari 6, iPhone", $log);
				}

				// iPhone Mobile Safari 5.0
				if(preg_match("/AppleWebKit\/53[34]{1}[^$]+Gecko[^$]+Mobile\/[89]{1}/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Mobile Safari 5, iPhone", $log);
				}

				// iPhone Mobile Safari 4.0
				if(preg_match("/AppleWebKit\/53[123]{1}[^$]+Gecko[^$]+Mobile\/[78]{1}/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Mobile Safari 4, iPhone", $log);
				}

			}

			// iPod Mobile Safari specific (limit scope for extensive search)
			if(preg_match("/iPod[^$]+iPhone OS[^$]+AppleWebKit/", $useragent) && !preg_match("/crios|htc|ipad|android|symbian|blackberry|fban|opera|firefox/i", $useragent)) {

				// iPod Mobile Safari 6.0 - IN TEST
				if(preg_match("/AppleWebKit\/536[^$]+Gecko[^$]+Mobile\/10/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Mobile Safari 6, iPod", $log);
				}

				// iPod Mobile Safari 5.0
				if(preg_match("/AppleWebKit\/53[34]{1}[^$]+Gecko[^$]+Mobile\/[89]{1}/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Mobile Safari 5, iPod", $log);
				}

				// iPod Mobile Safari 4.0
				if(preg_match("/AppleWebKit\/53[123]{1}[^$]+Gecko[^$]+Mobile\/[78]{1}/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Mobile Safari 4, iPod", $log);
				}

			}

			// Android specific (limit scope for extensive search)
			if(preg_match("/Android/", $useragent) && !preg_match("/crios|ipod|ipad|symbian|blackberry|fban|firefox/i", $useragent)) {

				// HTCs
				if(preg_match("/HTC/", $useragent)) {

					// // Desire HD, 2.2+
					// // Android 2.2 or 2.3 and HTC DesireHD or HTC_DesireHD and webkit 533
					// if(preg_match("/Android 2.[23]{1}[^$]+HTC[\s_]{1}Desire[\s_]?HD[^$]+AppleWebKit\/533/", $useragent)) {
					// 	return $this->IDUniqueTest($useragent, "HTC Desire HD, Android 2.2+", $log);
					// }
					// 
					// // Desire HD, 4.0+
					// // Android 4.0 or 4.1 and HTC DesireHD or HTC_DesireHD and webkit 534
					// if(preg_match("/Android 4.[01]{1}[^$]+HTC[\s_]{1}Desire[\s_]?HD[^$]+AppleWebKit\/53[45]{1}/", $useragent)) {
					// 	return $this->IDUniqueTest($useragent, "HTC Desire HD, Android 4.0+", $log);
					// }
					// 
					// // Desire S, 2.2+
					// // Android 2.2 or 2.3 and HTC DesireS or HTC_DesireS and webkit 533
					// if(preg_match("/Android 2.[23]{1}[^$]+HTC[\s_]{1}Desire[\s_]?S[^$]+AppleWebKit\/533/", $useragent)) {
					// 	return $this->IDUniqueTest($useragent, "HTC Desire S, Android 2.2+", $log);
					// }
					// 
					// // Desire S, 4.0+
					// // Android 4.0 or 4.1 and HTC DesireS or HTC_DesireS or HTC Desire S and webkit 534
					// if(preg_match("/Android 4.[01]{1}[^$]+HTC[\s_]{1}Desire[\s_]?S[^$]+AppleWebKit\/53[45]{1}/", $useragent)) {
					// 	return $this->IDUniqueTest($useragent, "HTC Desire S, Android 4.0+", $log);
					// }
					// 
					// // Desire C, 4.0+
					// // Android 4.0 or 4.1 and HTC DesireC or HTC_DesireC or HTC Desire S and webkit 534
					// if(preg_match("/Android 4.[01]{1}[^$]+HTC[\s_]{1}Desire[\s_]?C[^$]+AppleWebKit\/53[45]{1}/", $useragent)) {
					// 	return $this->IDUniqueTest($useragent, "HTC Desire C, Android 4.0+", $log);
					// }
					// 
					// // One X, 4.0+
					// if(preg_match("/Android 4.[01]{1}[^$]+HTC[\s_]{1}One[\s_]?X[^$]+AppleWebKit\/53[45]{1}/", $useragent)) {
					// 	return $this->IDUniqueTest($useragent, "HTC One X, Android 4.0+", $log);
					// }
					// 
					// // One V, 4.0+
					// if(preg_match("/Android 4.[01]{1}[^$]+HTC[\s_]{1}One[\s_]?V[^$]+AppleWebKit\/53[45]{1}/", $useragent)) {
					// 	return $this->IDUniqueTest($useragent, "HTC One V, Android 4.0+", $log);
					// }
					// 
					// // One S, 4.0+
					// if(preg_match("/Android 4.[01]{1}[^$]+HTC[\s_]{1}One[\s_]?S[^$]+AppleWebKit\/53[45]{1}/", $useragent)) {
					// 	return $this->IDUniqueTest($useragent, "HTC One S, Android 4.0+", $log);
					// }




				}

			}

			// Netfront
			// Teleca/Obigo
			
			
			// KNOWN MOBILE BROWSER FRAGMENTS
			// SEMC - SonyEricsson Mobile Corp
			// UP.Browser - Openwave
			// BlackBerry####/1-6 - BlackBerry non webkit browser
			// NetFront/3.[0-5] - Versioned NetFront browsers
			if(preg_match("/SEMC|UP.Browser|BlackBerry|NetFront/", $useragent) && !preg_match("/webkit|android/", $useragent)) {

				// SEMC Browser - IN TEST
				if(preg_match("/SEMC-Browser\/[0-9]{1}/", $useragent)) {
					return $this->IDUniqueTest($useragent, "SEMC, Generic", $log);
				}
				// Openwave Browser - IN TEST
				if(preg_match("/UP.Browser\/[0-9]{1}/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Openwave, Generic", $log);
				}
				// BlackBerry Browser - IN TEST
				if(preg_match("/BlackBerry[0-9]+\/[1-6]{1}/", $useragent)) {
					return $this->IDUniqueTest($useragent, "BlackBerry, Generic", $log);
				}


				// NetFront 3.x Browser - IN TEST
				if(preg_match("/NetFront\/(3.[0-5]{1})/", $useragent, $match)) {

					return $this->IDUniqueTest($useragent, "NetFront ".$match[1].", Generic", $log);

				}

			}


			// bot, spider and crawler specific (limit scope for extensive search)
			if(preg_match("/bot|spider|crawler/i", $useragent)) {

				// bots - IN TEST
				if(preg_match("/AcoonBot|MJ12bot|Daumoa|linkdex|UnwindFetchor|Nutch/", $useragent)) {
					return $this->IDUniqueTest($useragent, "Bot, Generic", $log);
				}

			}



			

			// Discontinued brands - So old that basic is the only choice

			// \bERICY - Ericsson
			// \bEricsson - Ericsson
			if(preg_match("/\bERICY|\bEricsson/", $useragent)) {
				return $this->IDUniqueTest($useragent, "Ericcson, Generic", $log);
			}


//			$this->perf->mark("guessing - no unique");

			// Still unidentified
			// register device for manual indexing
			if($log) {
				$this->logForIdentification($useragent);
			}

//			$this->perf->mark("guessing - logged");


			// Mozilla/5.0 (Linux; U; Android 2.3.3; de-de; HTC Desire Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1
			// shorten all version numbers to one digit
			// replace known system variables: Linux
			//
			// Mozilla/5%(%;%; 2%;%Desire%Build/FRF91)%AppleWebKit/533%Version/4%Mobile%Safari/533%



			// continue here
			// disable xref


			// Start extensive id attempt


			// start looking, replacing language indicator and operating system version
			// " [a-zA-Z]{2}[-_][a-zA-Z]{2}[;\)]" - " da-da;", " da-da)", " da_da)"
			// " [a-z]{2}[;\)]" - " da)", " da)"

			// " NT 6\.[0-9]" - " NT 6.1" - NOT 5.x (windows xp)

 			// " OS X 10[\._][0-9][\._]?[0-9]*" - " OS X 10.6", " OS X 10_6", " OS X 10.6.12", " OS X 10_6_12"

			// " OS [0-9]_[0-9][_]?[0-9]*" - " OS 4_0", " OS 4_0_5"

			// " Android [0-9].[0-9][.]?[0-9]?" - " Android 1.2", " Android 1.2.3"


			$partial_useragent = preg_replace('/ [a-zA-Z]{2}[-_][a-zA-Z]{2}[;\)]| [a-zA-Z]{2}[;\)]| NT 6\.[0-9]| OS X 10[\._][0-9][\._]?[0-9]*| OS [0-9]_[0-9][_]?[0-9]*| Android [0-9].[0-9][.]?[0-9]?/', "%", $useragent);
//			print "SELECT device_id FROM ".UT_DEV_USE." WHERE useragent LIKE '$partial_useragent%'<br>";
			if($query->sql("SELECT device_id FROM ".UT_DEV_USE." WHERE useragent LIKE '$partial_useragent%'")) {
				$device_id = $query->getQueryResult(0, "device_id");
				if($log) {
					include_once("include/notifier.php");
					collectNotification("guesses", "GUESS (known parts)" . $this->notificationString($useragent, $device_id));

					Page::addLog("UA GUESSED (known parts)" . $this->logString("IDENTIFIED", $useragent, $device_id) . "; PARTIAL:" . $partial_useragent);
				}
//				$this->perf->mark("guessed - (known parts)");

//				return $device_id;
				return array("method" => "guess", "guess" => $partial_useragent, "device_id" => $device_id);
			}


			// remove spaces from the end
			$partial_useragent = $useragent;
			while($partial_useragent = implode(explode(" ", $partial_useragent, -1), " ")) {
				if($query->sql("SELECT device_id FROM ".UT_DEV_USE." WHERE useragent LIKE '%$partial_useragent%'")) {
					$device_id = $query->getQueryResult(0, "device_id");
					if($log) {
						include_once("include/notifier.php");
						collectNotification("guesses", "GUESS (space)" . $this->notificationString($useragent, $device_id));

						Page::addLog("UA GUESSED (space)" . $this->logString("IDENTIFIED", $useragent, $device_id) . "; PARTIAL:" . $partial_useragent);
					}
//					$this->perf->mark("guessed - (space)");

//					return $device_id;
					return array("method" => "guess", "guess" => $partial_useragent, "device_id" => $device_id);
				}
				if(strpos($partial_useragent, " ") === false) {break;}
			}


			// remove agent parts from behind
			$partial_useragent = $useragent;
			while($partial_useragent = implode(explode("/", $partial_useragent, -1), "/")) {
				if($query->sql("SELECT device_id FROM ".UT_DEV_USE." WHERE useragent LIKE '%$partial_useragent%'")) {
					$device_id = $query->getQueryResult(0, "device_id");
					if($log) {
						
						include_once("include/notifier.php");
						collectNotification("guesses", "GUESS (/)" . $this->notificationString($useragent, $device_id));

						Page::addLog("UA GUESSED (/)" . $this->logString("IDENTIFIED", $useragent, $device_id) . "; PARTIAL:" . $partial_useragent);
					}
//					$this->perf->mark("guessed - (/)");

//					return $device_id;
					return array("method" => "guess", "guess" => $partial_useragent, "device_id" => $device_id);
				}
			}
		}

		// ID not possible - NOTIFY imediately
		if($log) {

			Page::addLog("UA UNIDENTIFIED; UA: " . $useragent);

			include_once("include/notifier.php");

			$string = "REFFERER: " . stringOr(getVar("site"), "?") . stringOr(getVar("file"), "?");
			$string .= ";\n USERAGENT: ".$useragent;
			$string .= ";\n HOST: " . $_SERVER["HTTP_HOST"];

			// add as much info as possible
			$string .= ";\n\n ".'$_SERVER: ' . print_r($_SERVER, true);


			notifier("UNABLE TO IDENTIFY: $useragent", $string);
		}

		return false;
	}


	function logForIdentification($useragent) {
		$query = new Query();

		$header = "";
		$headers = apache_request_headers();
		foreach($headers as $key => $value) {
			$header .= "$key: $value\n";
		}

		$query->sql("INSERT INTO ".UT_DEV_UNI." VALUES(DEFAULT, '$useragent', '$header', '".stringOr(getVar("site"), SITE_UID).stringOr(getVar("file"), "?")."', DEFAULT)");
	}

	/**
	* Unique id - still in test mode - gathering details for verification
	*
	*/
	function IDUnique($useragent, $ua_id, $log) {
		$query = new Query();

		if($query->sql("SELECT device_id FROM ".UT_DEV_USE." WHERE useragent = '$ua_id'")) {
			$device_id = $query->getQueryResult(0, "device_id");
			if($log) {
				Page::addLog("UA UNIQUE" . $this->logString("IDENTIFIED", $useragent, $device_id));
			}
//			return $device_id;
			return array("method" => "unique_id", "device_id" => $device_id);
		}

		// missing ID - notify imediately
		include_once("include/notifier.php");
		notifier("MISSING UNIQUE ID: $ua_id", $_SERVER["HTTP_HOST"]);

		// no match
		return false;
	}

	/**
	* Unique id - still in test mode - gathering details for verification
	*
	* Check UA for regexp match - if matches look up ua_id in devices
	*/
	function IDUniqueTest($useragent, $ua_id, $log) {
		$query = new Query();

		if($query->sql("SELECT device_id FROM ".UT_DEV_USE." WHERE useragent = '$ua_id'")) {
			$device_id = $query->getQueryResult(0, "device_id");
			if($log) {
				include_once("include/notifier.php");
				collectNotification("unique-test", "UNIQUE-TEST" . $this->notificationString($useragent, $device_id));

				Page::addLog("UA UNIQUE" . $this->logString("IDENTIFIED", $useragent, $device_id));

				// still collection useragents
				$this->logForIdentification($useragent);
			}
//			return $device_id;
			return array("method" => "unique_id_test", "device_id" => $device_id);
		}

		// missing ID - notify imediately
		include_once("include/notifier.php");
		notifier("MISSING UNIQUE ID: $ua_id", $_SERVER["HTTP_HOST"]);

		// no match
		return false;
	}

	/**
	* Build notification string
	*/
	function notificationString($useragent, $device_id) {

		$string = "; REFFERER: " . stringOr(getVar("site"), "?") . stringOr(getVar("file"), "?");
		$string .= "; USERAGENT: ".$useragent;
		$string .= "; IDENTIFIED: " . $this->getItemName($device_id);
		return $string;

	}

	/**
	* Build string for log entry
	*/
	function logString($status, $useragent, $device_id) {

		$string = "; UA: ".$useragent;
		$string .= "; $status: " . $this->getItemName($device_id) . " ($device_id)";
		return $string;

	}


	function getDeviceBase($device_id) {
		$query = new Query();

		if($device_id && $query->sql("SELECT ".UT_BAS_BRA.".name as brand, ".UT_DEV.".model as model FROM ".UT_BAS_BRA.", ".UT_DEV." WHERE ".UT_DEV.".id = $device_id AND ".UT_DEV.".brand_id = ".UT_BAS_BRA.".id")) {
			$device["id"] = $device_id;
			$device["model"] = $query->getQueryResult(0, "model");
			$device["brand"] = $query->getQueryResult(0, "brand");

			$display = $this->getDeviceProporty("display", $device_id);
			list($width, $height) = $display ? explode("x", $display) : array(0, 0);
			$device["display_width"] = $width;
			$device["display_height"] = $height;

			$device["segment"] = $this->getDeviceSegment($device_id);

			return $device;
		}
		return false;
	}

	function getDeviceProporty($proporty, $device_id = false) {
		$query = new Query();

		// if no device id is passed, we use browser useragent
		if(!$device_id) {
			$device_id = Session::getDevice("id");
		}

		if($device_id) {
			if($query->sql("SELECT ".UT_BAS_CON.".name AS name FROM ".UT_BAS_CON.", ".UT_DEV_CON." WHERE ".UT_BAS_CON.".id  = ".UT_DEV_CON.".contenttype_id AND ".UT_DEV_CON.".device_id = $device_id AND ".UT_BAS_CON.".name LIKE '$proporty/%'")) {
				$contenttype = $query->getQueryResult(0, "name");
				return str_replace("$proporty/", "", $contenttype);
			}
		}

		return false;
	}

	function getDeviceSegment($device_id = false) {
		$query = new Query();

		// if no device id is passed, we use browser useragent
		if(!$device_id) {
			$device_id = Session::getDevice("id");
		}

		// first se if device already has segment defined
		$segment = $this->getDeviceProporty("segment", $device_id);

		if($segment) {
			return $segment;
		}


		$browser = $this->getDeviceProporty("browser", $device_id);

		// find segment based on parent browser
		if($browser) {

			$SELECT = array();
			$FROM = array();
			$WHERE = array();
			$GROUP_BY = array();
			$ORDER = array();

			$SELECT[] = "bc.name";

			$FROM[] = UT_BAS_CON." AS bc";
			$FROM[] = UT_DEV_CON." AS dc";

			$WHERE[] = "bc.name LIKE 'segment/%'";

			$WHERE[] = "bc.id  = dc.contenttype_id";

			$WHERE[] = "dc.device_id IN (SELECT dc.device_id FROM ".UT_DEV_CON." AS dc, ".UT_BAS_CON." AS bc WHERE dc.contenttype_id = bc.id AND bc.name = 'parent')";
			$WHERE[] = "dc.device_id IN (SELECT dc.device_id FROM ".UT_DEV_CON." AS dc, ".UT_BAS_CON." AS bc WHERE dc.contenttype_id = bc.id AND bc.name = 'browser/$browser')";

//			print $query->makeQuery($SELECT, $FROM, $WHERE, $GROUP_BY, $ORDER);

			// find segment for device with same display and browser
			if($query->sql($query->makeQuery($SELECT, $FROM, $WHERE, $GROUP_BY, $ORDER))) {
				$contenttype = $query->getQueryResult(0, "name");
				return str_replace("segment/", "", $contenttype);
			}

		}

		// get current device display
		$display = $this->getDeviceProporty("display", $device_id);

		// find segment based on browser and display size match
		if($browser && $display) {

//			$query->sql("SELECT id FROM ".UT_BAS_CON." WHERE name = 'browser/$browser'");
//			$ct_browser_id = $query->getQueryResult(0, "id");

//			$query->sql("SELECT id FROM ".UT_BAS_CON." WHERE name = 'display/$display'");
//			$ct_display_id = $query->getQueryResult(0, "id");


			$SELECT = array();
			$FROM = array();
			$WHERE = array();
			$GROUP_BY = array();
			$ORDER = array();

			$SELECT[] = "bc.name";

			$FROM[] = UT_BAS_CON." AS bc";
			$FROM[] = UT_DEV_CON." AS dc";

			$WHERE[] = "bc.name LIKE 'segment/%'";

			$WHERE[] = "bc.id  = dc.contenttype_id";
			
			$WHERE[] = "dc.device_id IN (SELECT dc.device_id FROM ".UT_DEV_CON." AS dc, ".UT_BAS_CON." AS bc WHERE dc.contenttype_id = bc.id AND bc.name = 'browser/$browser')";
			$WHERE[] = "dc.device_id IN (SELECT dc.device_id FROM ".UT_DEV_CON." AS dc, ".UT_BAS_CON." AS bc WHERE dc.contenttype_id = bc.id AND bc.name = 'display/$display')";
//			$WHERE[] = "dc.device_id IN (SELECT device_id FROM ".UT_DEV_CON." AS dc WHERE dc.contenttype_id = $ct_browser_id)";
//			$WHERE[] = "dc.device_id IN (SELECT device_id FROM ".UT_DEV_CON." AS dc WHERE dc.contenttype_id = $ct_display_id)";

			// find segment for device with same display and browser
			if($query->sql($query->makeQuery($SELECT, $FROM, $WHERE, $GROUP_BY, $ORDER))) {
				$contenttype = $query->getQueryResult(0, "name");
				return str_replace("segment/", "", $contenttype);
			}
		}

		
		// attempting with matching browser
		if($browser) {

			$SELECT = array();
			$FROM = array();
			$WHERE = array();
			$GROUP_BY = array();
			$ORDER = array();

			$SELECT[] = "bc.name";

			$FROM[] = UT_BAS_CON." AS bc";
			$FROM[] = UT_DEV_CON." AS dc";

			$WHERE[] = "bc.name LIKE 'segment/%'";

			$WHERE[] = "bc.id  = dc.contenttype_id";

//			$WHERE[] = "dc.device_id IN (SELECT device_id FROM ".UT_DEV_CON." AS dc WHERE dc.contenttype_id = $ct_browser_id)";
			$WHERE[] = "dc.device_id IN (SELECT dc.device_id FROM ".UT_DEV_CON." AS dc, ".UT_BAS_CON." AS bc WHERE dc.contenttype_id = bc.id AND bc.name = 'browser/$browser')";

			// find segment for device with same display and browser
			if($query->sql($query->makeQuery($SELECT, $FROM, $WHERE, $GROUP_BY, $ORDER))) {
				$contenttype = $query->getQueryResult(0, "name");
				return str_replace("segment/", "", $contenttype);
			}
		}

		// not able to find any segment
		return "basic";
	}


	/**
	* Checking usage of selected item
	*
	* Checking if item id is in use in database tables:
	*
	* @param int $id Item id
	* @return bool
	*/
	function checkUsage($id) {
		
		$return = false;
		return $return;
	}




	
}

?>