<div class="scene">
<?php

// GRANT ALL PRIVILEGES ON devices.* TO 'devices'@'localhost' IDENTIFIED BY 'd1vIc35s' WITH GRANT OPTION;



// move files from itemtype folders to id folders - DONE
// move devices, useragents and tags
function fase_1() {

	// Do some restructuring in library
	$query = new Query();
	print "START - Moving all devices<br>";

	$IC = new Item();

	// existing devices
	$sql = "SELECT devices.devices.id as id, devices.devices.model as model, devices.basics_brands.name as brand FROM devices.devices, devices.basics_brands WHERE devices.devices.brand_id = devices.basics_brands.id";
//	print $sql."<br>";
	$query->sql($sql);

	$devices = $query->results();
	foreach($devices as $i => $device) {

		// limited test loop
//		if($i < 2000) {

			$sql = "SELECT * FROM devices.basics_contenttypes as tags, devices.device_contenttypes as taggings WHERE taggings.device_id = ".$device["id"]." AND tags.id = taggings.contenttype_id";
			$query->sql($sql);
			$tags = $query->results();

			$sql = "SELECT * FROM devices.device_useragents as ua WHERE ua.device_id = ".$device["id"];
			$query->sql($sql);
			$uas = $query->results();

			$comment = "";
			// ONLY INCLUDE DEVICES WITH TAGS AND USERAGENTS
			if($tags && $uas) {

				print "###".$device["model"]."<br>";

				if(preg_match("/\(([^\)]+)\)/", $device["model"], $matches)) {
					if(isset($matches[1])) {
						print "ADDITIONAL AKAS:" . $matches[1] . "<br>";
						$comment = $matches[1];

						$device["model"] = preg_replace("/[ ]?\(".preg_replace("/\+/", "\+", preg_replace("/\//", "\/", addslashes($matches[1])))."\)/", "", $device["model"]);
						print "NEW MODEL:" .$device["model"]."<br>";
					}
					else {
						print "WHATTTT?<br>";
						print_r($matches);
					}
				}


				// ADD DEVICE TO NEW ITEMS DB - DEFAULT RELEASE DATE - 1970
				$sql = "INSERT INTO devices_dearapi_com.items VALUES(DEFAULT, '', 1, 'device', DEFAULT, DEFAULT, CURRENT_TIMESTAMP, '1970-01-01 00:00:00')";
				$query->sql($sql);
				$device_id = $query->lastInsertId();

				// ADD DEVICE TO NEW ITEM_DEVICE DB
				$sql = "INSERT INTO devices_dearapi_com.item_device VALUES(DEFAULT, $device_id, '".$device["model"]."', '$comment')";
				$query->sql($sql);

				// ADD BRAND TAG
				$IC->addTag($device_id, "brand:".$device["brand"]);

	//			print "brand:".$device["brand"]."<br>";
				foreach($tags as $tag) {
					if(preg_match("/\//", $tag["name"])) {
//						print $tag["name"]."<br>";

// TODO: IMPORTANT: browser version is lost when splitting on /
						$contenttype = explode("/", $tag["name"]);
						$context = array_shift($contenttype);
						$value = implode(" ", $contenttype);
						print $context.":".$value."<br>";

//						list($context, $value) = explode("/", $tag["name"]);
			
						if($context == "released") {
							$released = preg_replace("/_/", "/", $value);
							if(preg_match("/january/", $released)) {
								$released = preg_replace("/january/", "01", $released);
							}
							if(preg_match("/february/", $released)) {
								$released = preg_replace("/february/", "02", $released);
							}
							if(preg_match("/march/", $released)) {
								$released = preg_replace("/march/", "03", $released);
							}
							if(preg_match("/april/", $released)) {
								$released = preg_replace("/april/", "04", $released);
							}
							if(preg_match("/may/", $released)) {
								$released = preg_replace("/may/", "05", $released);
							}
							if(preg_match("/june/", $released)) {
								$released = preg_replace("/june/", "06", $released);
							}
							if(preg_match("/july/", $released)) {
								$released = preg_replace("/july/", "07", $released);
							}
							if(preg_match("/august/", $released)) {
								$released = preg_replace("/august/", "08", $released);
							}
							if(preg_match("/september/", $released)) {
								$released = preg_replace("/september/", "09", $released);
							}
							if(preg_match("/october/", $released)) {
								$released = preg_replace("/october/", "10", $released);
							}
							if(preg_match("/november/", $released)) {
								$released = preg_replace("/november/", "11", $released);
							}
							if(preg_match("/december/", $released)) {
								$released = preg_replace("/december/", "12", $released);
							}

							// UPDATE RELEASE DATA IN ITEMS
							list($year, $month) = explode("/", $released);

							$sql = "UPDATE devices_dearapi_com.items SET published_at = '$year-$month-01 00:00:00' WHERE id = $device_id";
							$query->sql($sql);

						}
						else {
							$IC->addTag($device_id, $context.":".$value);
						}
					}
					else if($tag["name"] == "parent") {
						$IC->addTag($device_id, "type:parent");

//						print "INVALID TAG:" . $tag["name"] . "<br>";
					}

					// $context = 
					// $value =
	//				print $context.":".$value."<br>";
				}

				foreach($uas as $ua) {

					// ADD USERAGENT
					$sql = "INSERT INTO devices_dearapi_com.device_useragents VALUES(DEFAULT, $device_id, '".$ua["useragent"]."')";
//					print $sql."<br>";
					$query->sql($sql);

				}


	//			print_r($tag);
			}
			else {

				// CROSS CHECK NO TAGS DEVICES BEFORE CONTINUING WITH RELEAVANT DEVICES

				// print "###".$device["model"]."<br>";
				// print "brand:".$device["brand"]."<br>";
				// print "NO TAGS<br>";
				// 
				// $sql = "SELECT * FROM devices.device_useragents as ua WHERE ua.device_id = ".$device["id"];
				// $query->sql($sql);
				// $uas = $query->results();
				// 
				// print_r($uas);
			}

//		}
	}
//	print_r($devices);


	print "DONE - Moving all devices<br>";

}

// copy unidentified useragents
function fase_2() {


	$query = new Query();
	print "START - Moving all unidentified useragents<br>";

	// existing devices
	$sql = "SELECT * FROM devices.devices_unidentified";
//	print $sql."<br>";
	$query->sql($sql);
	$count = $query->count();
	print $count;
	$start = 0;

	while($start < $count) {
		print $count . ", " . $start . "<br>";

		$sql = "SELECT * FROM devices.devices_unidentified LIMIT ".$start.", 100";
	//	print $sql."<br>";
		$start = $start+100;
	
	//	print $sql."<br>";
		$query->sql($sql);
	
		$uas = $query->results();
		foreach($uas as $i => $ua) {
	
	
			// INSERT UA, PUT SITE_ID IN COMMENT
			// ADD USERAGENT
			$sql = "INSERT INTO devices_dearapi_com.unidentified_useragents VALUES(DEFAULT, '".$ua["useragent"]."', '".$ua["site_id"]."', '', '".$ua["timestamp"]."')";
//					print $sql."<br>";
			$query->sql($sql);
	
	
		}
		
		
	}

}


// execute
fase_1();
//fase_2();

?>
	

</div>