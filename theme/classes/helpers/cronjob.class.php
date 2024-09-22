<?php
	


class CronjobHelper {


	public $auto_order;


	/**
	*
	*/
	function __construct() {


		/**
		* The order of which to index the segments
		*
		* This list also exists in the PHP cronjob helper – updates must be made both here and there
		*/
		$this->auto_order = [
			"bot-crawler-spider-fetcher-indexer",
			"internet-explorer-9",
			"internet-explorer-10",
			"internet-explorer-11",
			"microsoft-edge-desktop",
			"firefox-desktop",
			"chrome-desktop",
			"safari-desktop",

			"internet-explorer-desktop-light",
			"microsoft-edge-desktop-light",
			"firefox-desktop-light",
			"chrome-desktop-light",
			"safari-desktop-light",
			"opera-desktop-light",
			"generic-desktop-light",

			"generic-tv",

			"safari-tablet",
			"firefox-tablet",
			"android-tablet",

			"safari-tablet-light",
			"firefox-tablet-light",
			"android-tablet-light",
			"opera-tablet-light",
			"generic-tablet-light",

			"firefox-smartphone",
			"microsoft-edge-smartphone",
			"safari-smartphone",
			"generic-smartphone",

			"safari-mobile",
			"internet-explorer-mobile",
			"firefox-mobile",
			"opera-mobile",
			"generic-mobile",

			"lynx",
			"validator",

			"android-smartphone",
			"android-mobile",

			"internet-explorer-mobile-light",
			"opera-mobile-light",
			"generic-mobile-light",

			"fallback-desktop",
			"fallback-desktop-ie10",
			"fallback-desktop-ie9",
			"fallback-desktop-light",
			"fallback-tablet-light",
			"fallback-smartphone",
			"fallback-mobile",
			"fallback-mobile-light",
			"fallback-seo",
		];

	}


	/**
	* Run auto indexing for devices
	*
	* Multi-step process
	* For performance reasons this method will only index one segment per day	
	*
	* Process consists of following steps:
	* – getting progres tracker value from library/private/auto-index-step
	* – getting matching devices based on segment markers (method??)
	* – Identifying each UA in result set ()
	* – If identification is confirmed, then index UA 
	*/
	function autoIndexDevice() {


		$IC = new Items();
		$model = $IC->typeObject("device");

		include_once("classes/helpers/identify.class.php");
		$ID = new Identify();

		$step = intval(@file_get_contents(PRIVATE_FILE_PATH."/auto-index-step") ?: 0);
		print "autoIndexDevice started – current step: " . $step."<br><br>\n\n";

		$devices = $model->getDevicesWithPatterns();
		// debug([$devices]);

		$device_id = false;
		$confirmed = 0;
		$ignored = 0;


		// Never go boyong android smartphone (for now)
		// This requires cross-referencing and typically manuel indexing of the remaining UAs
		if($this->auto_order[$step] === "android-smartphone") {

			print "Reached Android smartphone indexing – resetting progress";

			@file_put_contents(PRIVATE_FILE_PATH."/auto-index-step", "0");

			$result = mailer()->send([
				"subject" => SITE_URL." auto index reached Android smartphone – manual help is needed", 
				"message" => "It is time to do a little manuel indexing – or to build the next step of auto indexing – there never is a better time than the present",
				"template" => "system"
			]);

			return;
		}


		// Find next item in list
		foreach($devices as $device) {
			if($device["sindex"] === $this->auto_order[$step]) {
				print "Starting auto index process for \"".$device["name"]."\"<br><br>\n\n";
				$device_id = $device["id"];
				break;
			}
		}

		if($device_id) {

			$uas = $model->testMarkersOnUnidentified($device_id);
			if($uas) {

				print count($uas)." matching useragents<br><br>\n\n";

				foreach($uas as $ua) {

					print "testing id: ".$ua["id"].", ua: ".$ua["useragent"]."<br>\n";

					$ided_device = $model->identifyUnidentifiedId(["identifyUnidentifiedId", $ua["id"]]);
					// debug([$device["name"], $ided_device["name"]]);

					if($ided_device && $ided_device["name"] !== "UNIDENTIFIED" && isset($ided_device["id"]) && $ided_device["id"] === $device_id) {

						print "UA identification confirmed: id: ".$ided_device["id"]." (".$ided_device["name"].")<br><br>\n\n";
						$model->addUnidentifiedToDevice(["addUnidentifiedToDevice", $device_id, $ua["id"]]);

						message()->resetMessages();

						$confirmed++;
					}
					else {

						// This requires manual indexing
						print "UA could not be confirmed, ignoring, id:".$ua["id"]." (".$ided_device["name"].")<br><br>\n\n";

						$ignored++;

					}

				}


				print "$confirmed confirmed useragents<br>\n";
				print "$ignored ignored useragents<br>\n";
			}
			else {

				print "No matching useragents<br>\n";

			}



		}
		// No valid starting point
		else {

			// Send notification to admin

			// Send mail to admin
			mailer()->send([
				"subject" => SITE_URL." auto index lost track of progress", 
				"message" => "Script does not know where to start – current step is ".$step,
				"template" => "system"
			]);

		}


		// Increment progress step and write back to file
		@file_put_contents(PRIVATE_FILE_PATH."/auto-index-step", ($step+1));

	}

}
