<?php

// This class identifies a device based on its useragent
	
	

class Identify {

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {

		// itemtype database
		$this->db = SITE_DB.".item_device";
		$this->db_useragents = SITE_DB.".device_useragents";
		$this->db_unidentified = SITE_DB.".unidentified_useragents";

		$this->trimming_patterns = [
			"[ ]+\[FB[^\]]+\]$",
			"[ ]+\(iPhone[^\)]+scale[^\)]+gamut[^\)]+\)$",
			"[ ]+[a-zA-Z]{2}[-_][a-zA-Z]{2}( ;|;)"
		];


		//		Mozilla/5.0 (iPhone; CPU iPhone OS 10_2_1 like Mac OS X) AppleWebKit/602.4.6 (KHTML, like Gecko) Mobile/14D27 [FBAN/FBIOS;FBAV/83.0.0.38.70;FBBV/51754296;FBDV/iPhone8,4;FBMD/iPhone;FBSN/iOS;FBSV/10.2.1;FBSS/2;FBCR/TELIA;FBID/phone;FBLC/da_DK;FBOP/5;FBRV/52433023]

		//		Mozilla/5.0 (Linux; Android 6.0.1; SM-G920F Build/MMB29K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/56.0.2924.87 Mobile Safari/537.36 [FB_IAB/MESSENGER;FBAV/109.0.0.23.70;]

		//		Mozilla/5.0 (iPhone; CPU iPhone OS 10_2_1 like Mac OS X) AppleWebKit/602.4.6 (KHTML, like Gecko) Mobile/14D27 Instagram 10.12.0 (iPhone7,2; iOS 10_2_1; en_GB; en-GB; scale=2.00; gamut=normal; 750x1334)


	}

	// added optional logging - now function can be used for manual identification work
	function identifyDevice($useragent, $log=true, $mail=true, $details=true) {

//		print "IDENTIFYING:" . $useragent . "\n";


		// no useragent - don't try to identify, just return basic
		if(!$useragent) {
			return array("segment" => "basic");
//			return "basic";
		}



		// Experiment with trimming UA before doing analysis
		// The goal is to remove non-identifying fragments to make regex process faster
		foreach($this->trimming_patterns as $pattern) {
			$useragent = preg_replace("/".$pattern."/", "", $useragent);
		}



		// Include static detection script for initial test
		$detection_script = PUBLIC_FILE_PATH."/detection_script.php";
		if(file_exists($detection_script)) {
			include($detection_script);
		}
//		print $device_name;




		// did static test return match
		if(isset($device_name) && isset($device_segment)) {

			// if log is true, use fastest method to return segment
			if($log) {

				// add to general id log
				$this->logString("UA MARKER", $useragent, $device_segment, "marker");

				// return segment
				return array("segment" => $device_segment);
			}

			// if details are required
			if($details) {

				// get additional information
				$query = new Query();
				if($query->sql("SELECT item_id FROM ".$this->db." WHERE name = '$device_name'")) {
					$device_id = $query->result(0, "item_id");

					// get complete device
					$IC = new Items();
					$device = $IC->getItem(array("id" => $device_id, "extend" => array("tags" => true)));
					$device["method"] = "marker";
					return $device;
				}
			}
		}


		$IC = new Items();
		$query = new Query();
		$DC = $IC->typeObject("device");


		// continue with old match patterns




//		$this->perf->mark("identify", true);

		// perfect match
		if($query->sql("SELECT item_id FROM ".$this->db_useragents." WHERE useragent = '$useragent'")) {

//			print "perfect match\n";

//			$this->perf->mark("identified");

			$device_id = $query->result(0, "item_id");

//			print "IDENTIFIED AS:" . $device_id . "\n";
	

//			print_r($device);

			// if log is true, use fastest method to return segment
			if($log) {
				$segment = $DC->segment($device_id);

				// add to general id log
				$this->logString("IDENTIFIED", $useragent, $segment, "identification");

				return array("segment" => $segment);
//				return $segment;
			}

			$device = $IC->getItem(array("id" => $device_id, "extend" => array("tags" => true)));
			$device["method"] = "match";
			return $device;
		}

		// unidentified
		else {

//			print "checking unique\n";

//			$this->perf->mark("guessing");


			// register device for manual indexing
			// if($log) {
			// 	$this->saveForIdentification($useragent);
			// }

//			$this->perf->mark("guessing - logged");

//			print "start guessing\n";


			// Start extensive identification attempt



			// Mozilla/5.0 (Linux; U; Android 2.3.3; de-de; HTC Desire Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1

			// shorten all version numbers to one digit
			// replace known system variables: Linux
			//
			// Mozilla/5%(%;%; 2%;%Desire%Build/FRF91)%AppleWebKit/533%Version/4%Mobile%Safari/533%


			// start looking, replacing language indicator and operating system version
			// " [a-zA-Z]{2}[-_][a-zA-Z]{2}[;\)]" - " da-da;", " da-da)", " da_da)"
			// " [a-z]{2}[;\)]" - " da)", " da)"

			// " NT 6\.[0-9]" - " NT 6.1" - NOT 5.x (windows xp)

 			// " OS X 10[\._][0-9][\._]?[0-9]*" - " OS X 10.6", " OS X 10_6", " OS X 10.6.12", " OS X 10_6_12"

			// " OS [0-9]_[0-9][_]?[0-9]*" - " OS 4_0", " OS 4_0_5"

			// " Android [0-9].[0-9][.]?[0-9]?" - " Android 1.2", " Android 1.2.3"


			$partial_useragent = preg_replace('/ [a-zA-Z]{2}[-_][a-zA-Z]{2}[;\)]| [a-zA-Z]{2}[;\)]| NT 6\.[0-9]| OS X 10[\._][0-9][\._]?[0-9]*| OS [0-9]_[0-9][_]?[0-9]*| Android [0-9].[0-9][.]?[0-9]?/', "%", $useragent);
			$sql = "SELECT item_id FROM ".$this->db_useragents." WHERE useragent LIKE '$partial_useragent%'";
			if($query->sql($sql)) {
				$device_id = $query->result(0, "item_id");

//				print "found match: $device_id \n";

				if($log) {
					$segment = $DC->segment($device_id);

					// add to general id log
					$this->logString("UA GUESSED (known parts)", $useragent, $segment, "guesses");

					// save for email notification
					$this->notificationString("GUESS (known parts)", $useragent, $segment, "guesses", $partial_useragent);

					return array("segment" => $segment);
//					return $segment;
				}

//				$this->perf->mark("guessed - (known parts)");

				if($details) {
					$device = $IC->getItem(array("id" => $device_id, "extend" => array("tags" => true)));
				}
				else {
					$device = array();
					$device["id"] = $device_id;
					$device["useragent"] = $useragent;
				}
				$device["method"] = "guess 1";
				$device["guess"] = $partial_useragent;
				return $device;
			}


//			print "second method\n";


			// remove fragments by spaces from the end
			$partial_useragent = $useragent;
			while($partial_useragent = implode(explode(" ", $partial_useragent, -1), " ")) {
				$sql = "SELECT item_id FROM ".$this->db_useragents." WHERE useragent LIKE '%$partial_useragent%'";
				if($query->sql($sql)) {
					$device_id = $query->result(0, "item_id");

//					print "found match: $device_id \n";

					if($log) {
						$segment = $DC->segment($device_id);

						// add to general id log
						$this->logString("UA GUESSED (space)", $useragent, $segment, "guesses");

						// save for email notification
						$this->notificationString("GUESS (space)", $useragent, $segment, "guesses", $partial_useragent);

						return array("segment" => $segment);
//						return $segment;
					}

//					$this->perf->mark("guessed - (space)");

					if($details) {
						$device = $IC->getItem(array("id" => $device_id, "extend" => array("tags" => true)));
					}
					else {
						$device = array();
						$device["id"] = $device_id;
						$device["useragent"] = $useragent;
					}
					$device["method"] = "guess (space)";
					$device["guess"] = $partial_useragent;
					return $device;
				}
				if(strpos($partial_useragent, " ") === false) {break;}
			}


//			print "third method\n";


			// remove agent parts from behind
			$partial_useragent = $useragent;
			while($partial_useragent = implode(explode("/", $partial_useragent, -1), "/")) {
				$sql = "SELECT item_id FROM ".$this->db_useragents." WHERE useragent LIKE '%$partial_useragent%'";
				if($query->sql($sql)) {
					$device_id = $query->result(0, "item_id");

//					print "found match: $device_id \n";

					if($log) {
						$segment = $DC->segment($device_id);

						// add to general id log
						$this->logString("UA GUESSED (/)", $useragent, $segment, "guesses");

						// save for email notification
						$this->notificationString("GUESS (/)", $useragent, $segment, "guesses", $partial_useragent);

						return array("segment" => $segment);
						//return $segment;
					}
//					$this->perf->mark("guessed - (/)");

					if($details) {
						$device = $IC->getItem(array("id" => $device_id, "extend" => array("tags" => true)));
					}
					else {
						$device = array();
						$device["id"] = $device_id;
						$device["useragent"] = $useragent;
					}
					$device["method"] = "guess (/)";
					$device["guess"] = $partial_useragent;
					return $device;
				}
			}


//			print "forth method\n";


			// do a complete wildcard search as last option

			$partial_useragent = preg_replace('/ [a-zA-Z]{2}[-_][a-zA-Z]{2}[;\)]| [a-zA-Z]{2}[;\)]| NT 6\.[0-9]| OS X 10[\._][0-9][\._]?[0-9]*| OS [0-9]_[0-9][_]?[0-9]*| Android [0-9].[0-9][.]?[0-9]?/', "%", $useragent);
			$sql = "SELECT item_id FROM ".$this->db_useragents." WHERE useragent LIKE '%$partial_useragent%'";
			if($query->sql($sql)) {
				$device_id = $query->result(0, "item_id");

//				print "found match: $device_id \n";

				if($log) {
					$segment = $DC->segment($device_id);

					// add to general id log
					$this->logString("UA GUESSED (pure wildcard)", $useragent, $segment, "guesses");

					// save for email notification
					$this->notificationString("GUESS (pure wildcard)", $useragent, $segment, "guesses", $partial_useragent);

					return array("segment" => $segment);
//					return $segment;
				}

//				$this->perf->mark("guessed - (known parts)");

				if($details) {
					$device = $IC->getItem(array("id" => $device_id, "extend" => array("tags" => true)));
				}
				else {
					$device = array();
					$device["id"] = $device_id;
					$device["useragent"] = $useragent;
				}

				$device["method"] = "guess wildcard";
				$device["guess"] = $partial_useragent;
				return $device;
			}

		}






//		print "UNIDENTIFIED";

		// FATAL ERROR
		// ID not possible - NOTIFY imediately
		if($log) {

			$this->logString("UA UNIDENTIFIED", $useragent, "unknown", "unidentified");

			// Send notification imidiately
			$string = "REFFERER: " . stringOr(getVar("site"), "?") . stringOr(getVar("file"), "?");
			$string .= ";\n USERAGENT: ".$useragent;
			$string .= ";\n HOST: " . $_SERVER["HTTP_HOST"];

			// add as much info as possible
			$string .= ";\n\n ".'$_SERVER: ' . print_r($_SERVER, true);

			// send mail
			if($mail) {
				global $page;
				$page->mail(array("subject" => "UNABLE TO IDENTIFY: $useragent", "message" => $string));
			}
		}

		if($details) {
			return array("segment" => "desktop", "name" => "UNIDENTIFIED", "method" => "UNIDENTIFIED");
		}

		return array("segment" => "desktop");

//		return false;
	}



	// log useragent for manual indexing
	// only logs unidentified useragents
	function saveForIdentification($useragent, $segment = "") {
		$query = new Query();

		// only save if this exact ua hasn't already been identified
		$sql = "SELECT id FROM ".$this->db_useragents." WHERE useragent = '$useragent'";
		if(!$query->sql($sql)) {

			$comment = stringOr(getVar("site"), SITE_UID).stringOr(getVar("file"), "?")."\n";
			$headers = apache_request_headers();
			foreach($headers as $key => $value) {
				$comment .= "$key: $value\n";
			}
			$query->sql("INSERT INTO ".$this->db_unidentified." VALUES(DEFAULT, '$useragent', '$comment', '$segment', DEFAULT)");
		}
	}


	/**
	* Save log string for notification
	*/
	function notificationString($status, $useragent, $segment, $collection, $match = "") {
		global $page;

		$string = $status;
		$string = "; REFFERER: " . stringOr(getVar("site"), "?") . stringOr(getVar("file"), "?");
		$string .= "; USERAGENT: ".$useragent;
		$string .= "; SEGMENT: " . $segment;
		if($match) {
			$string .= "; MATCH: " . $match;
		}


		$page->collectNotification($string, $collection);
	}

	/**
	* Save string for log entry
	*/
	function logString($status, $useragent, $segment, $collection) {
		global $page;
		
		$this->saveForIdentification($useragent, $segment);


		$string = "$status: " . $segment . "; UA: ".$useragent;
		$page->addLog($string, $collection);
	}

}

?>
