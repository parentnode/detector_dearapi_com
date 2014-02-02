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

	}


	// added optional logging - now function can be used for manual identification work
	function identifyDevice($useragent, $log=true) {

//		print "IDENTIFYING:" . $useragent . "\n";


		// no useragent - don't try to identify, just return basic
		if(!$useragent) {
			return array("segment" => "basic");
//			return "basic";
		}

		$IC = new Item();
		$query = new Query();
		$DC = $IC->typeObject("device");

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

			$device = $IC->getCompleteItem($device_id);
			$device["method"] = "match";
			return $device;
		}
		// unidentified
		else {

//			print "checking unique\n";

//			$this->perf->mark("guessing");

			// Unique identifiers

			// IE + IEchromeFrame specific (limit scope for extensive search)
			if(preg_match("/^Mozilla\/[4-5]{1}.0[^$]+MSIE/", $useragent)) {

				// Desktop
				if(!preg_match("/phone|opera|chromeframe|mobile|touch|AppleWebKit|Gecko/i", $useragent)) {

					// DESKTOP IE 6-8
					if(preg_match("/MSIE ([678]{1}).0;[^$]+Windows NT [5-6]{1}.[0-2]{1}/", $useragent, $matches)) {
						return $this->uniqueId($useragent, "MSIE ".$matches[1].", Desktop", "desktop_light", $log);
					}
					if(preg_match("/MSIE (9|10).0;[^$]+Windows NT [5-6]{1}.[0-2]{1}/", $useragent, $matches)) {
						return $this->uniqueIdTest($useragent, "MSIE ".$matches[1].", Desktop", "desktop_ie", $log, "unique-test-ie9-10");
					}

				}
				// chromeframes
				else if(preg_match("/chromeframe/i", $useragent)) {

					// chromeFrame 27 - IN TEST
					if(preg_match("/chromeframe\/([\d]+).0/", $useragent, $matches)) {
						return $this->uniqueIdTest($useragent, "ChromeFrame ".$matches[1].", Desktop", "desktop", $log, "unique-test-chromeframe");
					}

				}
			}


			// TODO: find unique pattern for IE 11 - testing this
			// Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; EIE10;DADKMSN; rv:11.0) like Gecko 
			// IE 11
			if(preg_match("/^Mozilla\/5.0[^$]+Trident\/7.0[^$]+rv:11.0\) like Gecko/", $useragent)) {
				return $this->uniqueIdTest($useragent, "MSIE 11, Desktop", "desktop_ie", $log, "unique-test-ie11");
			}



			// Firefox specific (limit scope for extensive search)
			if(preg_match("/^Mozilla\/5.0[^$]+Firefox/", $useragent) && !preg_match("/phone|mobile|fennec|tablet|maemo|kylo|trident|touch/i", $useragent)) {

				// Firefox - IN TEST
				if(preg_match("/rv:([5-9]{1}|[0-9]{2}).0[^$]+Gecko[^$]+Firefox\/([5-9]{1}|[0-9]{2}).0/", $useragent, $matches)) {
					if($matches[1] == $matches[1]) {
						return $this->uniqueIdTest($useragent, "Firefox ".$matches[1].", Desktop", "desktop", $log, "unique-test-firefox5up");
					}
				}

				// Firefox 4
				if(preg_match("/rv:2.0[^$]+Gecko[^$]+Firefox\/4.0/", $useragent)) {
					return $this->uniqueIdTest($useragent, "Firefox 4, Desktop", "desktop", $log, "unique-test-firefox4");
				}

				// Firefox 3.5 + 3.6
				if(preg_match("/rv:1.9.[0-2]{1}[^$]+Gecko[^$]+Firefox\/(3.[056]{1})/", $useragent, $matches)) {
					return $this->uniqueIdTest($useragent, "Firefox ".$matches[1].", Desktop", "desktop_light", $log, "unique-test-firefox36down");
				}

			}

			// Chrome specific (limit scope for extensive search)
			if(preg_match("/^Mozilla\/5.0[^$]+Chrome/", $useragent) && !preg_match("/phone|mobile|chromeframe|android/i", $useragent)) {

				// Chrome for iPad >= version 19
				if(preg_match("/AppleWebKit\/53[3-7]{1}[^$]+Gecko[^$]+Chrome\/([0-9]{1,2}).[^$]+Safari\/53[3-7]{1}/", $useragent, $matches)) {
					if($matches[1] >= 5) {
						return $this->uniqueIdTest($useragent, "Chrome ".$matches[1].", Desktop", "desktop", $log, "unique-test-chrome");
					}
				}
			}

			// Facebook iOS specific (limit scope for extensive search)
			if(preg_match("/^Mozilla\/5.0[^$]+FBAN\/FBIOS/", $useragent) && !preg_match("/android/i", $useragent)) {

				// Facebook iPad app
				if(preg_match("/iPad[^$]+AppleWebKit\/53[4-7]{1}[^$]+Gecko[^$]+FBSV\/([0-9]{1})./", $useragent, $matches)) {
					return $this->uniqueIdTest($useragent, "Facebook iPad, Safari ".$matches[1], "tablet", $log, "unique-test-facebook");
				}

				// Facebook iPod app
				if(preg_match("/iPod[^$]+AppleWebKit\/53[4-7]{1}[^$]+Gecko[^$]+FBSV\/([0-9]{1})./", $useragent, $matches)) {
					return $this->uniqueIdTest($useragent, "Facebook iPod, Safari ".$matches[1], "mobile_touch", $log, "unique-test-facebook");
				}

				// Facebook iPhone app
				if(preg_match("/iPhone[^$]+AppleWebKit\/53[4-7]{1}[^$]+Gecko[^$]+FBSV\/([0-9]{1})./", $useragent, $matches)) {
					return $this->uniqueIdTest($useragent, "Facebook iPhone, Safari ".$matches[1], "mobile_touch", $log, "unique-test-facebook");
				}

			}

			// iOS chrome specific (limit scope for extensive search)
			if(preg_match("/^Mozilla\/5.0[^$]+CriOS/", $useragent) && !preg_match("/chromeframe|android/i", $useragent)) {

				// iPhone last, because "iPhone" might occur in iPod and iPad useragents
				// Chrome for iPad >= version 19
				if(preg_match("/iPad[^$]+AppleWebKit\/53[4-7]{1}[^$]+Gecko[^$]+CriOS\/([0-9]{2}).0/", $useragent, $matches)) {
					if($matches[1] >= 19) {
						return $this->uniqueIdTest($useragent, "Chrome ".$matches[1].", iPad", "tablet", $log, "unique-test-crios");
					}
				}

				// Chrome for iPod >= version 19
				if(preg_match("/iPod[^$]+AppleWebKit\/53[4-7]{1}[^$]+Gecko[^$]+CriOS\/([0-9]{2}).0/", $useragent, $matches)) {
					if($matches[1] >= 19) {
						return $this->uniqueIdTest($useragent, "Chrome ".$matches[1].", iPod", "mobile_touch", $log, "unique-test-crios");
					}
				}

				// Chrome for iPhone >= version 19
				if(preg_match("/iPhone[^$]+AppleWebKit\/53[4-7]{1}[^$]+Gecko[^$]+CriOS\/([0-9]{2}).0/", $useragent, $matches)) {
					if($matches[1] >= 19) {
						return $this->uniqueIdTest($useragent, "Chrome ".$matches[1].", iPhone", "mobile_touch", $log, "unique-test-crios");
					}
				}

			}


			// Desktop Safari specific (limit scope for extensive search)
			if(preg_match("/^Mozilla\/5.0[^$]+AppleWebKit\/[0-9]{3}/", $useragent) && !preg_match("/htc|mobile|iphone|ipod|ipad|android|symbian|blackberry|trident/i", $useragent)) {

				// Safari 5-7
				if(preg_match("/AppleWebKit\/53[3-7]{1}[^$]+Gecko[^$]+Version\/([5-7]{1})[^$]+Safari\/53[3-7]{1}/", $useragent, $matches)) {
					return $this->uniqueIdTest($useragent, "Safari ".$matches[1].", Desktop", "desktop", $log, "unique-test-safari5up");
				}

				// Separate 4 from 4.1
				// Safari 4.1
				if(preg_match("/AppleWebKit\/533[^$]+Gecko[^$]+Version\/4.1[^$]+Safari\/533/", $useragent, $matches)) {
					return $this->uniqueIdTest($useragent, "Safari 4.1, Desktop", "desktop_light", $log, "unique-test-safari41");
				}
				// Safari 3-4
				if(preg_match("/AppleWebKit\/[4-5]{1}[0-9]{2}[^$]+Gecko[^$]+Version\/([3-4]{1})[^$]+Safari\/[4-5]{1}[0-9]{2}/", $useragent, $matches)) {
					return $this->uniqueIdTest($useragent, "Safari ".$matches[1].", Desktop", "desktop_light", $log, "unique-test-safari4down");
				}

			}

			// iPad Mobile Safari specific (limit scope for extensive search)
			if(preg_match("/^Mozilla\/5.0[^$]+iPad[^$]+CPU OS[^$]+AppleWebKit/", $useragent) && !preg_match("/crios|htc|ipod|android|symbian|blackberry|fban|opera|firefox/i", $useragent)) {

				// more often then not, version section of useragent is missing
				// iPad Mobile Safari 5-7 (BUILD, WebKit and Version aligned in 95% of cases)
				if(preg_match("/AppleWebKit\/53([5-7]{1})[^$]+Gecko[^$]+Mobile\/(9|10|11)/", $useragent, $matches)) {
//					print $matches[1] . ", " . ($matches[2]-4)."\n";
					if($matches[1] == ($matches[2]-4)) {
						return $this->uniqueIdTest($useragent, "Mobile Safari ".$matches[1].", iPad", "tablet", $log, "unique-test-ipad");
					}
				}

				// iPad Mobile Safari 4-5 (BUILD, WebKit and Version rarely aligned)
				if(preg_match("/AppleWebKit\/53([1-4]{1})[^$]+Gecko[^$]+Mobile\/([789]{1})/", $useragent, $matches)) {
					if($matches[1] >= 3 && $matches[2] >= 8) {
						return $this->uniqueIdTest($useragent, "Mobile Safari 5, iPad", "tablet", $log, "unique-test-ipad");
					}
					else {
						return $this->uniqueIdTest($useragent, "Mobile Safari 4, iPad", "tablet", $log, "unique-test-ipad");
					}
				}
			}

			// iPod Mobile Safari specific (limit scope for extensive search)
			if(preg_match("/^Mozilla\/5.0[^$]+iPod[^$]+iPhone OS[^$]+AppleWebKit/", $useragent) && !preg_match("/crios|htc|ipad|android|symbian|blackberry|fban|opera|firefox/i", $useragent)) {

				// more often then not, version section of useragent is missing
				// iPod Mobile Safari 5-7 (BUILD, WebKit and Version aligned in 95% of cases)
				if(preg_match("/AppleWebKit\/53([5-7]{1})[^$]+Gecko[^$]+Mobile\/(9|10|11)/", $useragent, $matches)) {
					if($matches[1] == ($matches[2]-4)) {
						return $this->uniqueIdTest($useragent, "Mobile Safari ".$matches[1].", iPod", "mobile_touch", $log, "unique-test-ipod");
					}
				}

				// iPod Mobile Safari 4-5 (BUILD, WebKit and Version rarely aligned)
				if(preg_match("/AppleWebKit\/53([1-4]{1})[^$]+Gecko[^$]+Mobile\/([789]{1})/", $useragent, $matches)) {
					if($matches[1] >= 3 && $matches[2] >= 8) {
						return $this->uniqueIdTest($useragent, "Mobile Safari 5, iPod", "mobile_touch", $log, "unique-test-ipod");
					}
					else {
						return $this->uniqueIdTest($useragent, "Mobile Safari 4, iPod", "mobile", $log, "unique-test-ipod");
					}
				}
			}

			// iPhone Mobile Safari specific (limit scope for extensive search)
			if(preg_match("/^Mozilla\/5.0[^$]+iPhone[^$]+iPhone OS[^$]+AppleWebKit/", $useragent) && !preg_match("/crios|htc|ipod|ipad|android|symbian|blackberry|fban|opera|firefox/i", $useragent)) {

				// more often then not, version section of useragent is missing
				// iPhone Mobile Safari 5-7 (BUILD, WebKit and Version aligned in 95% of cases)
				if(preg_match("/AppleWebKit\/53([5-7]{1})[^$]+Gecko[^$]+Mobile\/(9|10|11)/", $useragent, $matches)) {
					if($matches[1] == ($matches[2]-4)) {
						return $this->uniqueIdTest($useragent, "Mobile Safari ".$matches[1].", iPhone", "mobile_touch", $log, "unique-test-iphone");
					}
				}

				// iPhone Mobile Safari 4-5 (BUILD, WebKit and Version rarely aligned)
				if(preg_match("/AppleWebKit\/53([1-4]{1})[^$]+Gecko[^$]+Mobile\/([789]{1})/", $useragent, $matches)) {
					if($matches[1] >= 3 && $matches[2] >= 8) {
						return $this->uniqueIdTest($useragent, "Mobile Safari 5, iPhone", "mobile_touch", $log, "unique-test-iphone");
					}
					else {
						return $this->uniqueIdTest($useragent, "Mobile Safari 4, iPhone", "mobile", $log, "unique-test-iphone");
					}
				}
			}


			// Android specific scope
			if(preg_match("/^Mozilla\/5.0[^$]+Linux[^$]+Android/", $useragent) && !preg_match("/crios|ipod|ipad|symbian|blackberry|fban|firefox/i", $useragent)) {

				// Android 4+ specific (limit scope for extensive search)
				if(preg_match("/Android 4[^$]+/", $useragent)) {

					// SAMSUNG
					// Samsung Galaxy S II, Android 4.0+
					if(preg_match("/GT-I9100[^$]+AppleWebKit\/53[4-7]{1}/", $useragent)) {
						return $this->uniqueIdTest($useragent, "Samsung Galaxy S II, Android 4.0+", "mobile_touch", $log, "unique-test-samsung");
					}

					// Samsung Galaxy S III, Android 4.0+
					if(preg_match("/(GT-I9300|GT-I9305)[^$]+AppleWebKit\/53[4-7]{1}/", $useragent)) {
						return $this->uniqueIdTest($useragent, "Samsung Galaxy S III, Android 4.0+", "mobile_touch", $log, "unique-test-samsung");
					}

					// Samsung Nexus 4, Android 4.0+
					if(preg_match("/Nexus 4[^$]+AppleWebKit\/53[4-7]{1}/", $useragent)) {
						return $this->uniqueIdTest($useragent, "Nexus 4, Android 4.0+", "mobile_touch", $log, "unique-test-samsung");
					}

					// Galaxy Note 10.1", Android 4.0+
					if(preg_match("/(GT-N8000|GT-N8005|GT-N8010|GT-N8013|GT-N8020)[^$]+AppleWebKit\/53[4-7]{1}/", $useragent)) {
						return $this->uniqueIdTest($useragent, "Galaxy Note 10.1, Android 4.0+", "tablet", $log, "unique-test-samsung");
					}


					// HTC
					// TODO: Find pattern of HTC one's to still be able to separate them


				}

				// Android 2.2+ specific (limit scope for extensive search)
				if(preg_match("/Android 2[^$]+/", $useragent)) {

					// SANSUNG
					// Samsung Galaxy S II, Android 2.2+
					if(preg_match("/GT-I9100[^$]+AppleWebKit\/533/", $useragent)) {
						return $this->uniqueIdTest($useragent, "Samsung Galaxy S II, Android 2.2+", "mobile_touch", $log, "unique-test-samsung");
					}


					// HTC
					// Desire S, Android 2.2+
					if(preg_match("/HTC[\s_]{1}Desire[\s_]?S[^$]+AppleWebKit\/533/", $useragent)) {
						return $this->uniqueIdTest($useragent, "Desire S, Android 2.2+", "mobile_touch", $log, "unique-test-htc");
					}

					// Desire HD, Android 2.2+
					if(preg_match("/HTC[\s_]{1}Desire[\s_]?HD[^$]+AppleWebKit\/533/", $useragent)) {
						return $this->uniqueIdTest($useragent, "Desire HD, Android 2.2+", "mobile_touch", $log, "unique-test-htc");
					}



					// // Desire HD, 4.0+
					// // Android 4.0 or 4.1 and HTC DesireHD or HTC_DesireHD and webkit 534
					// if(preg_match("/Android 4.[01]{1}[^$]+HTC[\s_]{1}Desire[\s_]?HD[^$]+AppleWebKit\/53[45]{1}/", $useragent)) {
					// 	return $this->IDUniqueTest($useragent, "HTC Desire HD, Android 4.0+", $log);
					// }

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
					return $this->uniqueIdTest($useragent, "SEMC, Generic", "mobile_light", $log, "unique-test-prehistoric");
				}
				// Openwave Browser - IN TEST
				if(preg_match("/UP.Browser\/[0-9]{1}/", $useragent)) {
					return $this->uniqueIdTest($useragent, "Openwave, Generic", "mobile_light", $log, "unique-test-prehistoric");
				}
				// BlackBerry Browser - IN TEST
				if(preg_match("/BlackBerry[0-9]+\/[1-6]{1}/", $useragent)) {
					return $this->uniqueIdTest($useragent, "BlackBerry, Generic", "mobile_light", $log, "unique-test-prehistoric");
				}


				// NetFront 3.x Browser - IN TEST
				if(preg_match("/NetFront\/(3.[0-5]{1})/", $useragent, $matches)) {
					if($matches >= 3.4) {
						return $this->uniqueIdTest($useragent, "NetFront ".$matches[1].", Generic", "mobile", $log, "unique-test-prehistoric");
					}
					else {
						return $this->uniqueIdTest($useragent, "NetFront ".$matches[1].", Generic", "mobile_light", $log, "unique-test-prehistoric");
					}
				}
			}


			// bot, spider, fetcher and crawler specific
			// OS or rendering engine info generally means it wants a full load
			if(preg_match("/bot|spider|crawler|Nutch|fetcher|feed/i", $useragent) && !preg_match("/AppleWebKit|Gecko|MSIE|Trident|Windows|Mac OS X|Linux/i", $useragent)) {

				// bots - IN TEST
//				if(preg_match("/AcoonBot|MJ12bot|Daumoa|linkdex|UnwindFetchor|Nutch|Feedfetcher-Google/", $useragent)) {
					return $this->uniqueIdTest($useragent, "Bot, Generic", "basic", $log, "unique-test-bot");
//				}

			}


			// Discontinued brands - So old that basic is the only choice

			// \bERICY - Ericsson
			// \bEricsson - Ericsson
			if(preg_match("/\bERICY|\bEricsson/", $useragent) && !preg_match("/android|iphone/", $useragent)) {
				return $this->uniqueIdTest($useragent, "Ericcson, Generic", "basic", $log, "unique-test-prehistoric");
			}


//			$this->perf->mark("guessing - no unique");

			// Still unidentified
			// register device for manual indexing
			if($log) {
				$this->saveForIdentification($useragent);
			}

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



			// additional fallback methods
			// TODO: perform various methods and see if two return same result



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

				$device = $IC->getCompleteItem($device_id);
				$device["method"] = "guess 1";
				$device["guess"] = $partial_useragent;
				return $device;
			}


//			print "second method\n";


			// remove spaces from the end
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

					$device = $IC->getCompleteItem($device_id);
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

					$device = $IC->getCompleteItem($device_id);
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

				$device = $IC->getCompleteItem($device_id);
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
			global $page;
			$page->mail(array("subject" => "UNABLE TO IDENTIFY: $useragent", "message" => $string));
		}

		return false;
	}


	// identified by Unique ID
	function uniqueId($useragent, $device, $segment, $log) {
		global $page;

//		print "UNIQUE:" . $ua_id . ", " . $log;

		// if log is true, use fastest method to return segment
		if($log) {
			// add to general id log
			$this->logString("UA UNIQUE", $useragent, $segment, "unique");

			// return segment
			return array("segment" => $segment);
		}

		// else manual indexing, return additional information
		$query = new Query();
		if($query->sql("SELECT item_id FROM ".$this->db_useragents." WHERE useragent = '$device'")) {
			$device_id = $query->result(0, "item_id");

			// get complete device
			$IC = new Item();
			$device = $IC->getCompleteItem($device_id);
			$device["method"] = "unique_id";
			return $device;
		}

		// FATAL ERROR
		// missing ID - notify imediately
		global $page;
		$page->mail(array("subject" => "MISSING UNIQUE ID: $device", "message" => $device.", ".$useragent));

		// no match - return false to continue identification
		return array("segment" => $segment, "name" => $device, "id" => "unknown", "method" => "unique_id - missing id");
	}

	// Unique ID - still in test phase
	function uniqueIdTest($useragent, $device, $segment, $log, $collection = "unique-test") {

//		print "UNIQUE TEST:" . $useragent . ", " . $device . ", " . $log;

		// if log is true, use fastest method to return segment
		if($log) {
			// add to general id log
			$this->logString("UA UNIQUE TEST", $useragent, $segment, "uniquetest");

			// save useragent for manuel indexing
			$this->saveForIdentification($useragent);

			// save for email notification
			$this->notificationString("UNIQUE-TEST", $useragent, $segment, $collection);

			// return segment
			return array("segment" => $segment);
		}

		// else manual indexing, return additional information
		$query = new Query();
		$sql = "SELECT item_id FROM ".$this->db_useragents." WHERE useragent = '$device'";
		if($query->sql($sql)) {
			$device_id = $query->result(0, "item_id");

			// get complete device
			$IC = new Item();
			$device = $IC->getCompleteItem($device_id);
			$device["method"] = "unique_id";
			return $device;
		}

		// FATAL ERROR
		// missing ID - notify imediately
		global $page;
		$page->mail(array("subject" => "MISSING UNIQUE ID: $device", "message" => $device.", ".$useragent));

		// no match - return false to continue identification
		return array("segment" => $segment, "name" => $device, "id" => "unknown", "method" => "unique_id - missing id");
	}


	// log useragent for manual indexing
	function saveForIdentification($useragent, $device_id = "") {
		$query = new Query();

		$comment = stringOr(getVar("site"), SITE_UID).stringOr(getVar("file"), "?")."\n";
		$headers = apache_request_headers();
		foreach($headers as $key => $value) {
			$comment .= "$key: $value\n";
		}

		// TODO: update insert
		$query->sql("INSERT INTO ".$this->db_unidentified." VALUES(DEFAULT, '$useragent', '$comment', '$device_id', DEFAULT)");
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

		$string = "$status: " . $segment . "; UA: ".$useragent;
		$page->addLog($string, $collection);
	}

}

?>
