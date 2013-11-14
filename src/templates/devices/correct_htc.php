<?php
	$IC = new Item();
	$device = $IC->typeObject("device");


	$query = new Query();

	$query->sql("SELECT * FROM ".$device->db_useragents." WHERE device_id = 6510");
	$useragents = $query->results();
	foreach($useragents as $useragent) {
		print $useragent["useragent"]."<br>";

		if(!preg_match("/Desire S|Desire_S|DesireS/", $useragent["useragent"]) && $useragent["useragent"] && $useragent["useragent"] != '"') {
			print "INSERT INTO ".$device->db_unidentified." VALUES(DEFAULT, ".$useragent["useragent"].", '', '".SITE_UID."', CURRENT_TIMESTAMP)<br>";
			$query->sql("INSERT INTO ".$device->db_unidentified." VALUES(DEFAULT, ".$useragent["useragent"].", '', ".SITE_UID.", CURRENT_TIMESTAMP)");
			print "DELETE FROM ".$device->db_useragents." WHERE id = ".$useragent["id"]."<br>";
			$query->sql("DELETE FROM ".$device->db_useragents." WHERE id = ".$useragent["id"]);

		}
		

//		print_r($useragent);
		// $query->sql("INSERT INTO ".$device->db_unidentified." WHERE device_id = 6510");
		// $query->sql("DELETE FROM ".$device->db_useragents." WHERE device_id = 6510");
		
	}

//	print_r($devices);
?>
