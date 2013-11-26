<div class="scene">
<?php

function deleteColumn($column, $table, $db) {
	$query = new Query();

//	print "SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = '$column' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'<br>";
	if($query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = '$column' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {
		print "ALTER TABLE `$db`.`$table` DROP `$column`<br>";
	 	if($query->sql("ALTER TABLE `$db`.`$table` DROP `$column`")) {
			print "DROP $column<br>";
		}
	}
	
}



// testing ffmpeg
//exec("ffmpeg -i ".LOCAL_PATH."/www/mp3 -acodec libmp3lame -ab 128k ".PRIVATE_FILE_PATH."/test.mp3 2>&1", $output);
//print_r($output);


// move files from itemtype folders to id folders - DONE
function fase_1() {
	// Do some restructuring in library
	$query = new Query();
	
	// move all files into item_id based folders in private
	
	// loop through files
	// lookup item_id based on type_id
	// move folder
	print "START - Moving files all files<br>";
	
	$files = FileSystem::files(LOCAL_PATH."/library", array("deny_folders" => "private,public"));
	$IC = new Item();

	foreach($files as $file) {
//		print $file."<br>";
		
		preg_match("/\/([a-z\-_]+)\/([0-9]+)/", str_replace(LOCAL_PATH."/library", "", $file), $matches);
//		print_r($matches);

		if(count($matches)) {

			print $file;
			$itemtype = $matches[1];
			$type_id = $matches[2];

			$i_ = array();

			if($itemtype == "frontpages") {
				$itemtype_db = "frontpage_images";
			}
			else {
				$itemtype_db = $itemtype;
			}

			if($query->sql("SELECT item_id FROM ".DB_LOC.".item_".$itemtype_db." WHERE id = $type_id")) {

				$item_id = $query->result(0, "item_id");
				if(isset($i_[$item_id])) {
	//				print "double index???<br>";
				}
				else {
					$new_file = preg_replace("/".$itemtype."\/".$type_id."/", $item_id, $file);
					print "From File:" . $file . "<br>";
					print "To File:" . $new_file . "<br>";

					FileSystem::makeDirRecursively(dirname($new_file));

					copy($file, $new_file);
					unlink($file);

					print "good<br>";
				}


				$i_[$item_id] = true;
	//			print "item_id:" . $item_id . "<br>";

			}
			else {
				print "DELETING:" . $file . "<br>";
				unlink($file);
//				break;
				//print "SELECT item_id FROM ".DB_LOC.".item_".$itemtype." WHERE id = $type_id<br>";
			}
//			break;
	//		print "type_id:" . $type_id."<br>";
			
		}
	}

	print "DONE - Moving files into item ids<br>";

}

// FILES

// news - files - done
function fase_2() {

	$query = new Query();
//	$query_update = new Query();
	$IC = new Item();

	$db = "supersonic_v1";
	$table = "item_news";
	$db_table = "`$db`.`$table`";


	FileSystem::makeDirRecursively(PRIVATE_FILE_PATH);


	print "START - cleaning up NEWS images<br>";
//	print "SELECT id FROM ".UT_ITE." WHERE itemtype = 'news'<br>";
	if($query->sql("SELECT id FROM ".UT_ITE." WHERE itemtype = 'news'")) {
		$results = $query->results();
		foreach($results as $result) {
			$item = array();
			$item["id"] = $result["id"];

//			print "SELECT * FROM $db_table WHERE item_id = " . $item["id"] . "<br>";
			if($query->sql("SELECT * FROM $db_table WHERE item_id = " . $item["id"])) {
				$item["image"] = $query->result(0, "image");
//				print LOCAL_PATH."/library/".$item["id"]."/".$item["image"]."<br>";
				if(file_exists(LOCAL_PATH."/library/".$item["id"]."/".$item["image"])) {
					print "copy: " . LOCAL_PATH."/library/".$item["id"]."/".$item["image"]." -> ".PRIVATE_FILE_PATH."/".$item["id"]."/jpg<br>";
					FileSystem::makeDirRecursively(PRIVATE_FILE_PATH."/".$item["id"]);
					copy(LOCAL_PATH."/library/".$item["id"]."/".$item["image"], PRIVATE_FILE_PATH."/".$item["id"]."/jpg");
					print "delete: " . LOCAL_PATH."/library/".$item["id"]."/".$item["image"]."<br>";
					unlink(LOCAL_PATH."/library/".$item["id"]."/".$item["image"]);
				}
			}
			else {
				$query_delete = new Query();
				if(!$query_delete->sql("DELETE FROM ".UT_ITE." WHERE id = ".$result["id"])) {
					print "problem deleting: ".$result["id"] . "<br>";
				}
			}
		}
	}
	print "END - cleaning up NEWS images<br>";
}

// people - files - done
function fase_3() {

	$query = new Query();
	$IC = new Item();

	$db = "supersonic_v1";
	$table = "item_people";
	$db_table = "`$db`.`$table`";

	print "START - cleaning up PEOPLE images<br>";
	if($query->sql("SELECT id FROM ".UT_ITE." WHERE itemtype = 'people'")) {
		$results = $query->results();
		foreach($results as $result) {
			$item = array();
			$item["id"] = $result["id"];

//			print "SELECT * FROM $db_table WHERE item_id = " . $item["id"] . "<br>";
			if($query->sql("SELECT * FROM $db_table WHERE item_id = " . $item["id"])) {
				$item["image"] = $query->result(0, "image");

				if(file_exists(LOCAL_PATH."/library/".$item["id"]."/".$item["image"])) {
					print "copy: " . LOCAL_PATH."/library/".$item["id"]."/".$item["image"]." -> ".PRIVATE_FILE_PATH."/".$item["id"]."/jpg<br>";
					FileSystem::makeDirRecursively(PRIVATE_FILE_PATH."/".$item["id"]);
					copy(LOCAL_PATH."/library/".$item["id"]."/".$item["image"], PRIVATE_FILE_PATH."/".$item["id"]."/jpg");
					print "delete: " . LOCAL_PATH."/library/".$item["id"]."/".$item["image"]."<br>";
					unlink(LOCAL_PATH."/library/".$item["id"]."/".$item["image"]);
				}
			}
			else {
				$query_delete = new Query();
				if(!$query_delete->sql("DELETE FROM ".UT_ITE." WHERE id = ".$result["id"])) {
					print "problem deleting: ".$result["id"] . "<br>";
				}
			}
		}
	}
	print "END - cleaning up PEOPLE images<br>";

}

// about - files (keep file names? or just file-extension?)
function fase_4() {

	$query = new Query();
	$IC = new Item();

	$db = "supersonic_v1";
	$table = "item_about";
	$db_table = "`$db`.`$table`";

	print "START - cleaning up ABOUT images<br>";
	if($query->sql("SELECT id FROM ".UT_ITE." WHERE itemtype = 'about'")) {
		$results = $query->results();
		foreach($results as $result) {
			$item = array();
			$item["id"] = $result["id"];

//			print "SELECT * FROM $db_table WHERE item_id = " . $item["id"] . "<br>";
			if($query->sql("SELECT * FROM $db_table WHERE item_id = " . $item["id"])) {
				$item["file"] = $query->result(0, "file");


			// $item = $IC->TypeObject("about")->get($result["id"]);
			// if($item) {
			// 	$item = array_merge($result, $item);
				if(file_exists(LOCAL_PATH."/library/".$item["id"]."/".$item["file"])) {
					print "copy: " . LOCAL_PATH."/library/".$item["id"]."/".$item["file"]." -> ".PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"]."<br>";
					FileSystem::makeDirRecursively(PRIVATE_FILE_PATH."/".$item["id"]);
					copy(LOCAL_PATH."/library/".$item["id"]."/".$item["file"], PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"]);
					print "delete: " . LOCAL_PATH."/library/".$item["id"]."/".$item["file"]."<br>";
					unlink(LOCAL_PATH."/library/".$item["id"]."/".$item["file"]);
				}
			}
			else {
				$query_delete = new Query();
				if(!$query_delete->sql("DELETE FROM ".UT_ITE." WHERE id = ".$result["id"])) {
					print "problem deleting: ".$result["id"] . "<br>";
				}
			}
		}
	}
	print "END - cleaning up ABOUT images<br>";

}

// clips - files - skipping vfx and prepost - DONE
function fase_5() {

	$query = new Query();
	$IC = new Item();

	$db = "supersonic_v1";
	$table = "item_clips";
	$db_table = "`$db`.`$table`";

	// clips
	print "START - cleaning up CLIPS images<br>";
	if($query->sql("SELECT id FROM ".UT_ITE." WHERE itemtype = 'clip' AND status = 1")) {
		$results = $query->results();
		foreach($results as $result) {
			$item = array();
			$item["id"] = $result["id"];

//			print "SELECT * FROM $db_table WHERE item_id = " . $item["id"] . "<br>";
			if($query->sql("SELECT * FROM $db_table WHERE item_id = " . $item["id"])) {
				$item["clip"] = $query->result(0, "clip");
				$item["thumbnail"] = $query->result(0, "thumbnail");
				$item["screendump"] = $query->result(0, "screendump");


			// $item = $IC->TypeObject("clip")->get($result["id"]);
			// 
			// if($item) {
			// 	$item = array_merge($result, $item);
//				print_r($item);
				if($item["thumbnail"] && file_exists(LOCAL_PATH."/library/".$item["id"]."/thumbnail/".$item["thumbnail"])) {
					print "copy: " . LOCAL_PATH."/library/".$item["id"]."/thumbnail/".$item["thumbnail"]." -> ".PRIVATE_FILE_PATH."/".$item["id"]."/thumbnail/jpg<br>";
					FileSystem::makeDirRecursively(PRIVATE_FILE_PATH."/".$item["id"]."/thumbnail");
					copy(LOCAL_PATH."/library/".$item["id"]."/thumbnail/".$item["thumbnail"], PRIVATE_FILE_PATH."/".$item["id"]."/thumbnail/jpg");
					print "delete: " . LOCAL_PATH."/library/".$item["id"]."/thumbnail/".$item["thumbnail"]."<br>";
					unlink(LOCAL_PATH."/library/".$item["id"]."/thumbnail/".$item["thumbnail"]);
				}

				if($item["screendump"] && file_exists(LOCAL_PATH."/library/".$item["id"]."/screendump/".$item["screendump"])) {
					print "copy: " . LOCAL_PATH."/library/".$item["id"]."/screendump/".$item["screendump"]." -> ".PRIVATE_FILE_PATH."/".$item["id"]."/screendump/jpg<br>";
					FileSystem::makeDirRecursively(PRIVATE_FILE_PATH."/".$item["id"]."/screendump");
					copy(LOCAL_PATH."/library/".$item["id"]."/screendump/".$item["screendump"], PRIVATE_FILE_PATH."/".$item["id"]."/screendump/jpg");
					print "delete: " . LOCAL_PATH."/library/".$item["id"]."/screendump/".$item["screendump"]."<br>";
					unlink(LOCAL_PATH."/library/".$item["id"]."/screendump/".$item["screendump"]);
				}

				if($item["clip"] && file_exists(LOCAL_PATH."/library/".$item["id"]."/clip/".$item["clip"])) {
					print "copy: " . LOCAL_PATH."/library/".$item["id"]."/clip/".$item["clip"]." -> ".PRIVATE_FILE_PATH."/".$item["id"]."/clip/mov<br>";
					FileSystem::makeDirRecursively(PRIVATE_FILE_PATH."/".$item["id"]."/clip");
					copy(LOCAL_PATH."/library/".$item["id"]."/clip/".$item["clip"], PRIVATE_FILE_PATH."/".$item["id"]."/clip/mov");
					print "delete: " . LOCAL_PATH."/library/".$item["id"]."/clip/".$item["clip"]."<br>";
					unlink(LOCAL_PATH."/library/".$item["id"]."/clip/".$item["clip"]);
				}

				// if($item["vfxshowcase"] && file_exists(LOCAL_PATH."/library/".$item["id"]."/vfxshowcase/".$item["vfxshowcase"])) {
				// 	print "ignore vfxshowcase<br>";
				// }
				// if($item["prepost"] && file_exists(LOCAL_PATH."/library/".$item["id"]."/prepost/".$item["prepost"])) {
				// 	print "ignore prepost<br>";
				// }

			}
			else {
				$query_delete = new Query();
				if(!$query_delete->sql("DELETE FROM ".UT_ITE." WHERE id = ".$result["id"])) {
					print "problem deleting: ".$result["id"] . "<br>";
				}
			}

		}
	}
	print "END - cleaning up CLIPS images<br>";
}

// frontpage - images - DONE
function fase_6() {

	$query = new Query();
	$IC = new Item();

	$db = "supersonic_v1";
	$table = "item_frontpage_images";
	$db_table = "`$db`.`$table`";

	print "START - cleaning up frontpage images<br>";

	if($query->sql("SELECT id FROM ".UT_ITE." WHERE itemtype = 'frontpage_image' and status = 1")) {
		$results = $query->results();
		foreach($results as $result) {
			$item = array();
			$item["id"] = $result["id"];

//			print "SELECT * FROM $db_table WHERE item_id = " . $item["id"] . "<br>";
			if($query->sql("SELECT * FROM $db_table WHERE item_id = " . $item["id"])) {
				$item["file"] = $query->result(0, "file");

			// $item = $IC->TypeObject("frontpage_image")->get($result["id"]);
			// if($item) {
			// 	$item = array_merge($result, $item);
				if(file_exists(LOCAL_PATH."/library/".$item["id"]."/".$item["file"])) {
					print "copy: " . LOCAL_PATH."/library/".$item["id"]."/".$item["file"]." -> ".PRIVATE_FILE_PATH."/".$item["id"]."/jpg<br>";
					FileSystem::makeDirRecursively(PRIVATE_FILE_PATH."/".$item["id"]);
					copy(LOCAL_PATH."/library/".$item["id"]."/".$item["file"], PRIVATE_FILE_PATH."/".$item["id"]."/jpg");
					print "delete: " . LOCAL_PATH."/library/".$item["id"]."/".$item["file"]."<br>";
					unlink(LOCAL_PATH."/library/".$item["id"]."/".$item["file"]);
				}
			}
			else {
				$query_delete = new Query();
				if(!$query_delete->sql("DELETE FROM ".UT_ITE." WHERE id = ".$result["id"])) {
					print "problem deleting: ".$result["id"] . "<br>";
				}
			}
		}
	}
	print "END - cleaning up ABOUT images<br>";

}

// radio - files - DONE
function fase_7() {

	$query = new Query();
	$IC = new Item();

	$db = "supersonic_v1";
	$table = "item_radio";
	$db_table = "`$db`.`$table`";

	print "START - cleaning up RADIO files<br>";

	if($query->sql("SELECT id FROM ".UT_ITE." WHERE itemtype = 'radio' and status = 1")) {
		$results = $query->results();
		foreach($results as $result) {
			$item = array();
			$item["id"] = $result["id"];

//			print "SELECT * FROM $db_table WHERE item_id = " . $item["id"] . "<br>";
			if($query->sql("SELECT * FROM $db_table WHERE item_id = " . $item["id"])) {
				$item["file"] = $query->result(0, "file");

			// $item = $IC->TypeObject("radio")->get($result["id"]);
			// if($item) {
			// 	$item = array_merge($result, $item);
				if(file_exists(LOCAL_PATH."/library/".$item["id"]."/".$item["file"])) {
					print "copy: " . LOCAL_PATH."/library/".$item["id"]."/".$item["file"]." -> ".PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"]."<br>";
					FileSystem::makeDirRecursively(PRIVATE_FILE_PATH."/".$item["id"]);
					copy(LOCAL_PATH."/library/".$item["id"]."/".$item["file"], PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"]);
					print "delete: " . LOCAL_PATH."/library/".$item["id"]."/".$item["file"]."<br>";
					unlink(LOCAL_PATH."/library/".$item["id"]."/".$item["file"]);
				}

				// conversion or renaming
				if(file_exists(PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"])) {
					print "file has not been converted/renamed yet!<br>";
					// is file mp3?
					// rename to just mp3
					if(preg_match("/.mp3$/", $item["file"])) {
						print $item["id"] . " file is mp3<br>";

						print "copy: " . PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"]." -> ".PRIVATE_FILE_PATH."/".$item["id"]."/mp3<br>";
						copy(PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"], PRIVATE_FILE_PATH."/".$item["id"]."/mp3");
						print "delete: " . PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"]."<br>";
						unlink(PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"]);

					}
					// if filename is mov
					// convert to mp3
					else {
						print $item["id"] . " file is mov<br>";

						print "Convert from .mov to .mp3<br>";

						print "ffmpeg -i ".PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"]." -acodec libmp3lame -ab 128k ".PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"].".mp3<br>";
						system("ffmpeg -i ".PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"]." -acodec libmp3lame -ab 128k ".PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"].".mp3");
//						print "#" . exec("ffmpeg -i ".PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"]." 2>&1", $output);

						print "copy: " . PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"].".mp3 -> ".PRIVATE_FILE_PATH."/".$item["id"]."/mp3<br>";
						copy(PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"].".mp3", PRIVATE_FILE_PATH."/".$item["id"]."/mp3");

						print "delete: " . PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"].".mp3<br>";
						print "delete: " . PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"]."<br>";
						unlink(PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"].".mp3");
						unlink(PRIVATE_FILE_PATH."/".$item["id"]."/".$item["file"]);
					}
				}
			}
			else {
				$query_delete = new Query();
				if(!$query_delete->sql("DELETE FROM ".UT_ITE." WHERE id = ".$result["id"])) {
					print "problem deleting: ".$result["id"] . "<br>";
				}
			}


		}
	}

	print "END - cleaning up RADIO files<br>";

}


// STATUS on remaining files - DONE
function fase_8() {

	print "START - MIDLIFE files check<br>";

	$query = new Query();

	$files = FileSystem::files(LOCAL_PATH."/library", array("deny_folders" => "private,public"));

	foreach($files as $file) {
//		print $file."<br>";

		preg_match("/\/([0-9]+)/", str_replace(LOCAL_PATH."/library", "", $file), $matches);
//		print_r($matches);

		if(count($matches)) {

//			print $file;

			if($query->sql("SELECT id, itemtype, status FROM ".UT_ITE." WHERE id = ".$matches[1])) {

				$id = $query->result(0, "id");
				$itemtype = $query->result(0, "itemtype");
				$status = $query->result(0, "status");

				if($status != 0) {

					if($itemtype == "clip" && !preg_match("/prepost|vfxshowcase/", $file)) {
						print "HANDLE THIS: " . $id . ", " . $itemtype . ", " . $status . ", " . $file . "<br>";
						unlink($file);
					}
					else if($itemtype == "frontpage_image" && !preg_match("/thumbnail/", $file)) {
						print "HANDLE THIS: " . $id . ", " . $itemtype . ", " . $status . ", " . $file . "<br>";
						unlink($file);
					}
					else if($itemtype != "clip" && $itemtype != "frontpage_image") {
						print "HANDLE THIS: " . $id . ", " . $itemtype . ", " . $status . ", " . $file . "<br>";
						unlink($file);
					}
					else {
						print "UNKNOWN PROBLEM: . $file<br>";
					}

				}
			}
			// item not found
			else {
				print_r($matches);
				print "ITEM NOT FOUND: " . $matches[1] . ", " . $file . "<br>";
				print LOCAL_PATH."/library/" . $matches[1] . "<br>";
				FileSystem::removeDirRecursively(LOCAL_PATH."/library/".$matches[1]);

				// delete
			}
		}
	}

	print "END - MIDLIFE files check<br>";
}



// ITEMS TABLE - DONE
function fase_10() {

 	print "START - cleaning up ITEMS TABLE<br>";

	$query = new Query();
	$db = "supersonic_v1";
	$table = "items";
	$db_table = "`$db`.`$table`";

	// change table type
	$query->sql("ALTER TABLE $db_table ENGINE=InnoDB");


	// first rename columns
	if($query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'timestamp' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {
	 	if($query->sql("ALTER TABLE $db_table CHANGE `timestamp` `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP")) {
			print "CHANGE timestamp TO created_at<br>";
		}	
	}

	// check column order
	$query->sql("SHOW COLUMNS FROM $db_table");

	$results = $query->results();

	$correction_query = new Query();

	if($results[0]["Field"] != "id") {
		print "BAD COLUMN (0):" . $results[0]["Field"] . "<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN id int(11) NOT NULL AUTO_INCREMENT FIRST");
		print "COLUMN id, positioned correctly<br>";

		fase_10();
		exit();
	}

	if($results[1]["Field"] != "sindex") {
		print "BAD COLUMN (1):" . $results[1]["Field"] . "<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN sindex varchar(255) AFTER id");
		print "COLUMN sindex, positioned correctly<br>";

		fase_10();
		exit();
	}
	if($results[2]["Field"] != "status") {
		print "BAD COLUMN (2):" . $results[2]["Field"] . "<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN status int(11) AFTER sindex");
		print "COLUMN status, positioned correctly<br>";

		fase_10();
		exit();
	}
	if($results[3]["Field"] != "itemtype") {
		print "BAD COLUMN (3):" . $results[3]["Field"] . "<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN itemtype varchar(30) AFTER status");
		print "COLUMN itemtype, positioned correctly<br>";

		fase_10();
		exit();
	}
	if($results[4]["Field"] != "user_id") {
		print "BAD COLUMN (4):" . $results[4]["Field"] . "<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN user_id int(11) AFTER itemtype");
		print "COLUMN user_id, positioned correctly<br>";

		fase_10();
		exit();
	}
	if($results[5]["Field"] != "created_at") {
		print "BAD COLUMN (5):" . $results[5]["Field"] . "<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN created_at TIMESTAMP AFTER user_id");
		print "COLUMN created_at, positioned correctly<br>";

		fase_10();
		exit();
	}

	if(!isset($results[6])) {
		print "MISSING COLUMN - modified_at<br>";
		$correction_query->sql("ALTER TABLE $db_table ADD `modified_at` TIMESTAMP NULL AFTER `created_at`");

		fase_10();
		exit();
	}
	if($results[6]["Field"] != "modified_at") {
		print "BAD COLUMN (6):" . $results[6]["Field"] . "<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN modified_at TIMESTAMP AFTER created_at");
		print "COLUMN modified_at, positioned correctly<br>";

		fase_10();
		exit();
	}

	if(!isset($results[7])) {
		print "MISSING COLUMN - published_at<br>";
		$correction_query->sql("ALTER TABLE $db_table ADD `published_at` TIMESTAMP NULL AFTER `modified_at`");

		fase_10();
		exit();
	}
	if($results[7]["Field"] != "published_at") {
		print "BAD COLUMN (7):" . $results[7]["Field"] . "<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN published_at TIMESTAMP AFTER modified_at");
		print "COLUMN published_at, positioned correctly<br>";

		fase_10();
		exit();
	}


	// check column settings
	// id
	if($results[0]["Type"] != "int(11)" || $results[0]["Null"] != "NO" || $results[0]["Extra"] != "auto_increment") {
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN id int(11) NOT NULL AUTO_INCREMENT");
		print "id SETTING CORRECTED<br>";
	}
	// sindex
	if($results[1]["Type"] != "varchar(255)" || $results[1]["Null"] != "YES") {
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN sindex varchar(255) DEFAULT NULL");
		print "sindex SETTING CORRECTED<br>";
	}
	// status
	if($results[2]["Type"] != "int(11)" || $results[2]["Null"] != "NO") {
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN status int(11) NOT NULL");
		print "status SETTING CORRECTED<br>";
	}
	// itemtype
	if($results[3]["Type"] != "varchar(40)" || $results[3]["Null"] != "NO") {
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN itemtype varchar(40) NOT NULL");
		print "itemtype SETTING CORRECTED<br>";
	}
	// user_id
	if($results[4]["Type"] != "int(11)" || $results[4]["Null"] != "YES") {
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN user_id int(11) DEFAULT NULL");
		print "user_id SETTING CORRECTED<br>";
	}
	// created_at
	if($results[5]["Type"] != "timestamp" || $results[5]["Null"] != "NO" || $results[5]["Default"] != "CURRENT_TIMESTAMP") {
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP");
		print "created_at SETTING CORRECTED<br>";
	}
	// modified_at
	if($results[6]["Type"] != "timestamp" || $results[6]["Null"] != "YES") {
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN modified_at timestamp NULL");
		print "modified_at SETTING CORRECTED<br>";
	}
	// published_at
	if($results[7]["Type"] != "timestamp" || $results[7]["Null"] != "YES") {
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN published_at timestamp NULL");
		print "published_at SETTING CORRECTED<br>";
	}

	// SET KEYS
	$correction_query->sql("ALTER TABLE $db_table ADD PRIMARY KEY (id)");

 	print "END - cleaning up ITEMS TABLE<br>";

}

// TAGS - DONE
// rename tables, and prepare for using tags when updating following tables
function fase_11() {

	$query = new Query();
	$db = "supersonic_v1";

	$table_old = "item_tags";
	$table = "tags";

	$db_table = "`$db`.`$table`";

	// delete type column
 	print "START - cleaning up TAGS TABLE<br>";


	// rename table if not done already
	if($query->sql("SELECT table_name FROM information_schema.tables WHERE table_schema = '$db' AND table_name = '$table_old'")) {
		$query->sql("RENAME TABLE `$db`.`$table_old` TO `$db`.`$table`");
		print "RENAME item_tags TO tags<br>";
	}

	// change table type
	$query->sql("ALTER TABLE $db_table ENGINE=InnoDB");



	// first rename name to value
	if($query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'name' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {
	 	if($query->sql("ALTER TABLE $db_table CHANGE `name` `value` varchar(100) NOT NULL")) {
			print "CHANGE name TO value<br>";
		}	
	}

	// add context column
	if(!$query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'context' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {
		$query->sql("ALTER TABLE $db_table ADD `context` varchar(50) NOT NULL DEFAULT 'set' AFTER `id`");
	}


	// rename note to description
	if($query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'note' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {
	 	if($query->sql("ALTER TABLE $db_table CHANGE `note` `description` text NULL")) {
			print "CHANGE note TO description<br>";
		}	
	}


	// check column settings
	$correction_query = new Query();
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN id int(11) NOT NULL AUTO_INCREMENT");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN context varchar(50) NOT NULL");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN value varchar(100) NOT NULL");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN description text NULL");

	print "END - cleaning up TAGS TABLE<br>";

}

// ITEM_TAGS - DONE
// rename table
function fase_12() {

	$query = new Query();
	$db = "supersonic_v1";

	$table_old = "item_item_tags";
	$table = "taggings";

	$db_table = "`$db`.`$table`";

	// delete type column
 	print "START - cleaning up TAGGINGS TABLE<br>";


	// rename table if not done already
	if($query->sql("SELECT table_name FROM information_schema.tables WHERE table_schema = '$db' AND table_name = '$table_old'")) {
		$query->sql("RENAME TABLE `$db`.`$table_old` TO `$db`.`$table`");
		print "RENAME item_item_tags TO taggings<br>";
	}

	// change table type
	$query->sql("ALTER TABLE $db_table ENGINE=InnoDB");



	// clean up tags
	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();

	$query_check = new Query();

	foreach($results as $result) {
//		print $result["item_id"] . " -> " . $result["tag_id"] . "<br>";

		// check if tag_id exists
		if(!$query_check->sql("SELECT * FROM `$db`.`tags` WHERE id = " . $result["tag_id"])) {
			print "TAG ID not found<br>";
			// Delete tag
			$query_check->sql("DELETE FROM $db_table WHERE tag_id = " . $result["tag_id"]);
		}
		// check if item_id exists
		else if(!$query_check->sql("SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"])) {
			print "ITEM ID not found<br>";
			// Delete tag
			$query_check->sql("DELETE FROM $db_table WHERE item_id = " . $result["item_id"]);
		}
	}
	


	// check column order
	$query->sql("SHOW KEYS FROM $db_table");
	$results = $query->results();
	$correction_query = new Query();

	// ITEM_ID/ITEM_ID FOREIGN KEY
	$f_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "item_id" && $result["Non_unique"] == 1) {
			$f_key = true;
		}
	}
	if(!$f_key) {
//		print "ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id) ON UPDATE CASCADE ON DELETE CASCADE<br>";
		$correction_query->sql("ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id) ON UPDATE CASCADE ON DELETE CASCADE");
		print "ADD item_id CONTRAINT<br>";
	}

	// ITEM_ID/ITEM_ID FOREIGN KEY
	$f_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "tag_id" && $result["Non_unique"] == 1) {
			$f_key = true;
		}
	}
	if(!$f_key) {
//		print "ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (tag_id) REFERENCES tags(id) ON UPDATE CASCADE ON DELETE CASCADE<br>";
		$correction_query->sql("ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (tag_id) REFERENCES tags(id) ON UPDATE CASCADE ON DELETE CASCADE");
		print "ADD tag_id CONTRAINT<br>";
	}

	print "END - cleaning up TAGGINGS TABLE<br>";
}


// ITEM_NEWS - DONE
function fase_13() {

	$query = new Query();
	$query_update = new Query();
	$db = "supersonic_v1";
	$table = "item_news";
	$db_table = "`$db`.`$table`";

	// delete type column
 	print "START - cleaning up NEWS TABLE<br>";

	// change table type
	$query->sql("ALTER TABLE $db_table ENGINE=InnoDB");

	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();

	// move release_date info to items.published_at and modified_at

	foreach($results as $result) {
		if(isset($result["release_date"]) && $result["release_date"]) {
//			print "UPDATE `$db`.`items` SET published_at = '". $result["release_date"] ."' WHERE id = ". $result["item_id"]."<br>";
			$query_update->sql("UPDATE `$db`.`items` SET published_at = '". $result["release_date"] ."' WHERE id = ". $result["item_id"]);
			$query_update->sql("UPDATE `$db`.`items` SET modified_at = '". $result["release_date"] ."' WHERE id = ". $result["item_id"]);
			print "UPDATED published_at AND modified_at<br>";
//			print $result["release_date"]."<br>";
		}
	}


	// drop type
	deleteColumn("type", $table, $db);

	// drop release_date
	deleteColumn("release_date", $table, $db);

	// drop date
	deleteColumn("date", $table, $db);

	// drop image
	deleteColumn("image", $table, $db);


	// check column order
	$query->sql("SHOW COLUMNS FROM $db_table");
	$results = $query->results();
	$correction_query = new Query();

//	print_r($results);

	// check column settings
	// id
	if($results[0]["Type"] != "int(11)" || $results[0]["Null"] != "NO" || $results[0]["Extra"] != "auto_increment") {
		print "id SETTING WRONG<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN id int(11) NOT NULL AUTO_INCREMENT");

		print "id SETTING CORRECTED<br>";
	}
	// item_id
	if($results[1]["Type"] != "int(11)" || $results[1]["Null"] != "NO") {
		print "item_id SETTING WRONG<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN item_id int(11) NOT NULL");

		print "item_id SETTING CORRECTED<br>";
	}
	// name
	if($results[2]["Type"] != "varchar(255)" || $results[2]["Null"] != "NO") {
		print "name SETTING WRONG<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN name varchar(255) NOT NULL");

		print "name SETTING CORRECTED<br>";
	}
	// text
	if($results[3]["Type"] != "text" || $results[3]["Null"] != "NO") {
		print "text SETTING WRONG<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN text TEXT NOT NULL");

		print "text SETTING CORRECTED<br>";
	}

	// SET KEYS
	$correction_query->sql("ALTER TABLE $db_table ADD PRIMARY KEY (id)");

	
	$query->sql("SHOW KEYS FROM $db_table");
	$results = $query->results();

	// UNIQUE NAME
	$u_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "name" && $result["Non_unique"] == 0) {
			$u_key = true;
		}
	}
	if(!$u_key) {
		$correction_query->sql("ALTER TABLE $db_table ADD UNIQUE (`name`)");
	}

	// ID/ITEM_ID FOREIGN KEY
	$f_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "item_id" && $result["Non_unique"] == 1) {
			$f_key = true;
		}
	}
	if(!$f_key) {
//		print "ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id)<br>";
		$correction_query->sql("ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id) ON UPDATE CASCADE ON DELETE CASCADE");
	}


	// check all news items against items table and vice versa
	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();
	foreach($results as $result) {
		if(!$query_update->sql("SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"])) {
			print "INVALID ITEM DELETED<br>";
			$query_check->sql("DELETE FROM $db_table WHERE item_id = " . $result["item_id"]);
		}
	}

	$query->sql("SELECT * FROM `$db`.`items` WHERE itemtype = 'news'");
	$results = $query->results();
	foreach($results as $result) {
		if(!$query_update->sql("SELECT * FROM $db_table WHERE item_id = " . $result["id"])) {
			print "INVALID ITEM DELETED<br>";
			$query_check->sql("DELETE FROM `$db`.`items` WHERE id = " . $result["id"]);
		}
	}

	print "END - cleaning up NEWS TABLE<br>";

}

// ITEM_PEOPLE - DONE
function fase_14() {

	$query = new Query();
	$query_update = new Query();
	$db = "supersonic_v1";
	$table_old = "item_people";
	$table = "item_person";
	$db_table = "`$db`.`$table`";

	// delete type column
 	print "START - cleaning up PEOPLE TABLE<br>";



	// rename table if not done already
	if($query->sql("SELECT table_name FROM information_schema.tables WHERE table_schema = '$db' AND table_name = '$table_old'")) {
		$query->sql("RENAME TABLE `$db`.`$table_old` TO `$db`.`$table`");
		print "RENAME item_people TO item_person<br>";
	}


	// change table type
	$query->sql("ALTER TABLE $db_table ENGINE=InnoDB");


	// correct item type in items
	if($query->sql("SELECT * FROM `$db`.`items` WHERE itemtype = 'people'")) {
		$results = $query->results();
		foreach($results as $result) {
//			print "UPDATE `$db`.`items` SET itemtype = 'person' WHERE id = ". $result["id"]."<br>";
			$query->sql("UPDATE `$db`.`items` SET itemtype = 'person' WHERE id = ". $result["id"]);
		}
		print "UPDATED ITEMTYPE to PERSON<br>";
	}


	// move release_date info to items.published_at and modified_at
	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();
	foreach($results as $result) {
		if(isset($result["release_date"]) && $result["release_date"] && $result["release_date"] != "0000-00-00 00:00:00") {
//			print "UPDATE `$db`.`items` SET published_at = '". $result["release_date"] ."' WHERE id = ". $result["item_id"]."<br>";
			$query_update->sql("UPDATE `$db`.`items` SET published_at = '". $result["release_date"] ."' WHERE id = ". $result["item_id"]);
			$query_update->sql("UPDATE `$db`.`items` SET modified_at = '". $result["release_date"] ."' WHERE id = ". $result["item_id"]);
			print "UPDATED published_at AND modified_at FROM release_date<br>";
//			print $result["release_date"]."<br>";
		}
		else if(isset($result["release_date"])) {
//			print "SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"]."<br>";
			$query_update->sql("SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"]);
			$results_update = $query_update->results();
//			print "UPDATE `$db`.`items` SET published_at = '". $results_update[0]["created_at"] ."' WHERE id = ". $result["item_id"]."<br>";
			$query_update->sql("UPDATE `$db`.`items` SET published_at = '". $results_update[0]["created_at"] ."' WHERE id = ". $result["item_id"]);
			$query_update->sql("UPDATE `$db`.`items` SET modified_at = '". $results_update[0]["created_at"] ."' WHERE id = ". $result["item_id"]);
			print "UPDATED published_at AND modified_at FROM created_at<br>";
		}
		else {

			$query_update->sql("SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"]);
			$results_update = $query_update->results();
			if(!$results_update[0]["published_at"]) {
				$query_update->sql("UPDATE `$db`.`items` SET published_at = '". $results_update[0]["created_at"] ."' WHERE id = ". $result["item_id"]);
				print "UPDATED published_at FROM created_at<br>";
			}
			if(!$results_update[0]["modified_at"]) {
				$query_update->sql("UPDATE `$db`.`items` SET modified_at = '". $results_update[0]["created_at"] ."' WHERE id = ". $result["item_id"]);
				print "UPDATED modified_at FROM created_at<br>";
			}
//			print "UPDATE `$db`.`items` SET published_at = '". $results_update[0]["created_at"] ."' WHERE id = ". $result["item_id"]."<br>";
			
			
		}
	}

	// drop release_date
	deleteColumn("release_date", $table, $db);

	// drop image
	deleteColumn("image", $table, $db);


	// rename note to description
	if($query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'note' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {
	 	if($query->sql("ALTER TABLE $db_table CHANGE `note` `description` text NULL")) {
			print "CHANGE note TO description<br>";
		}	
	}

	// rename shortname to nickname
	if($query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'shortname' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {
	 	if($query->sql("ALTER TABLE $db_table CHANGE `shortname` `nickname` varchar(100) NOT NULL")) {
			print "CHANGE shortname TO nickname<br>";
		}	
	}

	// change order
	// check column order
	$query->sql("SHOW COLUMNS FROM $db_table");
	$results = $query->results();
	$correction_query = new Query();

	if($results[0]["Field"] != "id") {
		print "BAD COLUMN (0):" . $results[0]["Field"] . "<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN id int(11) NOT NULL AUTO_INCREMENT FIRST");
		print "COLUMN id, positioned correctly<br>";

		fase_14();
		exit();
	}

	if($results[1]["Field"] != "item_id") {
		print "BAD COLUMN (1):" . $results[1]["Field"] . "<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN item_id int(11) NOT NULL AFTER id");
		print "COLUMN item_id, positioned correctly<br>";

		fase_14();
		exit();
	}
	if($results[2]["Field"] != "name") {
		print "BAD COLUMN (2):" . $results[2]["Field"] . "<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN name varchar(100) NOT NULL AFTER item_id");
		print "COLUMN name, positioned correctly<br>";

		fase_14();
		exit();
	}
	if($results[3]["Field"] != "nickname") {
		print "BAD COLUMN (3):" . $results[3]["Field"] . "<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN nickname varchar(100) NOT NULL AFTER name");
		print "COLUMN nickname, positioned correctly<br>";

		fase_14();
		exit();
	}
	if($results[4]["Field"] != "email") {
		print "BAD COLUMN (4):" . $results[4]["Field"] . "<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN email varchar(100) NOT NULL AFTER nickname");
		print "COLUMN email, positioned correctly<br>";

		fase_14();
		exit();
	}
	if($results[5]["Field"] != "title") {
		print "BAD COLUMN (5):" . $results[5]["Field"] . "<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN title varchar(100) NULL AFTER email");
		print "COLUMN title, positioned correctly<br>";

		fase_14();
		exit();
	}
	if($results[6]["Field"] != "description") {
		print "BAD COLUMN (6):" . $results[6]["Field"] . "<br>";
		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN description text NULL AFTER title");
		print "COLUMN description, positioned correctly<br>";

		fase_14();
		exit();
	}


	// check column settings
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN id int(11) NOT NULL AUTO_INCREMENT");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN item_id int(11) NOT NULL");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN name varchar(100) NOT NULL");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN nickname varchar(100) NOT NULL");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN email varchar(100) NULL");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN title varchar(100) NULL");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN description text NULL");


	// check all news items against items table and vice versa
	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();
	foreach($results as $result) {
//		print "SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"]."<br>";
		if(!$query_update->sql("SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"])) {
			print "INVALID ITEM DELETED<br>";
			$query_check->sql("DELETE FROM $db_table WHERE item_id = " . $result["item_id"]);
		}
	}

	$query->sql("SELECT * FROM `$db`.`items` WHERE itemtype = 'person'");
	$results = $query->results();
	foreach($results as $result) {
//		print "SELECT * FROM $db_table WHERE item_id = " . $result["id"]."<br>";
		if(!$query_update->sql("SELECT * FROM $db_table WHERE item_id = " . $result["id"])) {
			print "INVALID ITEM DELETED<br>";
			$query_check->sql("DELETE FROM `$db`.`items` WHERE id = " . $result["id"]);
		}
	}


	// SET KEYS
	$correction_query->sql("ALTER TABLE $db_table ADD PRIMARY KEY (id)");


	$query->sql("SHOW KEYS FROM $db_table");
	$results = $query->results();

	// UNIQUE NAME - cannot be unique after all
	// $u_key = false;
	// foreach($results as $result) {
	// 	if($result["Column_name"] == "name" && $result["Non_unique"] == 0) {
	// 		$u_key = true;
	// 	}
	// }
	// if(!$u_key) {
	// 	$correction_query->sql("ALTER TABLE $db_table ADD UNIQUE (`name`)");
	// }

	// ID/ITEM_ID FOREIGN KEY
	$f_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "item_id" && $result["Non_unique"] == 1) {
			$f_key = true;
		}
	}
	if(!$f_key) {
//		print "ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id)<br>";
		$correction_query->sql("ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id) ON UPDATE CASCADE ON DELETE CASCADE");
	}


	print "END - cleaning up PEOPLE TABLE<br>";

}


// ITEM_DEPARTMENTS - DONE
function fase_15() {

	// convert departments and department tags
	$query = new Query();
	$query_update = new Query();
	$db = "supersonic_v1";
	$table = "item_departments";
	$db_table = "`$db`.`$table`";

 	print "START - cleaning up DEPARTMENTS TABLE<br>";

	// if departments table exists
	if($query->sql("SELECT table_name FROM information_schema.tables WHERE table_schema = '$db' AND table_name = '$table'")) {

		// convert all departments into tags
		$query->sql("SELECT * FROM $db_table");
		$results = $query->results();
		foreach($results as $result) {
			if(!$query_update->sql("SELECT * FROM `$db`.`tags` WHERE context = 'department' AND value = '".ucwords(strtolower($result["name"]))."'")) {
//				print "INSERT INTO `$db`.`tags` VALUES(DEFAULT, 'department', '".ucwords(strtolower($result["name"]))."', '')<br>";
				$query_update->sql("INSERT INTO `$db`.`tags` VALUES(DEFAULT, 'department', '".ucwords(strtolower($result["name"]))."', '')");
				print "ADD TAG: department:" . ucwords(strtolower($result["name"])) . "<br>"; 
			}
		}

	}

	// if departments relation table exists
	if($query->sql("SELECT table_name FROM information_schema.tables WHERE table_schema = '$db' AND table_name = 'item_item_departments'")) {

		// convert all departments relations into tags relations
		$query->sql("SELECT * FROM `$db`.`item_item_departments`");
		$results = $query->results();
		foreach($results as $result) {

			// get tag equivalent
			$query_update->sql("SELECT * FROM `$db`.`item_departments` WHERE id = ".$result["department_id"]);
			$department = $query_update->result(0, "name");

			$query_update->sql("SELECT * FROM `$db`.`tags` WHERE value = '".ucwords(strtolower($department))."'");
			$tag_id = $query_update->result(0, "id");

			if($tag_id && $query_update->sql("SELECT * FROM `$db`.`items` WHERE id = ".$result["item_id"])) {
				// check if relation already exists
				if(!$query_update->sql("SELECT * FROM `$db`.`taggings` WHERE item_id = ".$result["item_id"]." AND tag_id = $tag_id")) {
					print "INSERT INTO `$db`.`taggings` VALUES(DEFAULT, ".$result["item_id"].", ".$tag_id.")<br>";
					$query_update->sql("INSERT INTO `$db`.`taggings` VALUES(DEFAULT, ".$result["item_id"].", ".$tag_id.")");
					print "ADD TAG RELATION<br>";

				}
			}
		}
	}


	// delete item_item_departments table
	$query_update->sql("DROP TABLE `$db`.`item_item_departments`");
	// delete item_departments table
	$query_update->sql("DROP TABLE `$db`.`item_departments`");
	


	print "END - cleaning up DEPARTMENTS TABLE<br>";

}


// CREATE SETS AND SET ITEMS - DONE
function fase_16() {

	// convert departments and department tags
	$query = new Query();
	$query_update = new Query();
	$correction_query = new Query();

 	print "START - creating ITEM SETS TABLE<br>";

	$db = "supersonic_v1";
	$table = "item_set";
	$db_table = "`$db`.`$table`";

	// if item sets table exists
	if(!$query->sql("SELECT table_name FROM information_schema.tables WHERE table_schema = '$db' AND table_name = '$table'")) {
		$query->sql("CREATE TABLE $db_table (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `item_id` int(11) NOT NULL,
			  `name` varchar(50) NOT NULL,
			  `type` varchar(10) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

	}

	$query->sql("SHOW KEYS FROM $db_table");
	$results = $query->results();

	// UNIQUE NAME
	$u_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "name" && $result["Non_unique"] == 0) {
			$u_key = true;
		}
	}
	if(!$u_key) {
		$correction_query->sql("ALTER TABLE $db_table ADD UNIQUE (`name`)");
	}

	// ID/ITEM_ID FOREIGN KEY
	$f_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "item_id" && $result["Non_unique"] == 1) {
			$f_key = true;
		}
	}
	if(!$f_key) {
//		print "ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id)<br>";
		$correction_query->sql("ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id) ON UPDATE CASCADE ON DELETE CASCADE");
	}


	$db = "supersonic_v1";
	$table = "item_set_items";
	$db_table = "`$db`.`$table`";
	
	// if item sets table exists
	if(!$query->sql("SELECT table_name FROM information_schema.tables WHERE table_schema = '$db' AND table_name = '$table'")) {

		$query->sql("CREATE TABLE $db_table (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `item_id` int(11) NOT NULL,
		  `set_id` int(11) NOT NULL,
		  `position` int(11) DEFAULT '0',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
		
	}

	$query->sql("SHOW KEYS FROM $db_table");
	$results = $query->results();

	// ID/SET_ID FOREIGN KEY
	$f_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "set_id" && $result["Non_unique"] == 1) {
			$f_key = true;
		}
	}
	if(!$f_key) {
//		print "ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id)<br>";
		$correction_query->sql("ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (set_id) REFERENCES items(id) ON UPDATE CASCADE ON DELETE CASCADE");
	}

	// ID/ITEM_ID FOREIGN KEY
	$f_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "item_id" && $result["Non_unique"] == 1) {
			$f_key = true;
		}
	}
	if(!$f_key) {
//		print "ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id)<br>";
		$correction_query->sql("ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id) ON UPDATE CASCADE ON DELETE CASCADE");
	}


 	print "END - creating ITEM SETS TABLE<br>";

}


// ITEM_RADIO - DONE
function fase_17() {

	$query = new Query();
	$query_update = new Query();
	$correction_query = new Query();
	$db = "supersonic_v1";
	$table_old = "item_radio";
	$table = "item_audio";
	$db_table = "`$db`.`$table`";

	// delete type column
 	print "START - cleaning up RADIO TABLE<br>";


	// rename table if not done already
	if($query->sql("SELECT table_name FROM information_schema.tables WHERE table_schema = '$db' AND table_name = '$table_old'")) {
		$query->sql("RENAME TABLE `$db`.`$table_old` TO `$db`.`$table`");
		print "RENAME item_radio TO item_audio<br>";
	}

	// change table type
	$query->sql("ALTER TABLE $db_table ENGINE=InnoDB");


	// correct item type in items
	if($query->sql("SELECT * FROM `$db`.`items` WHERE itemtype = 'radio'")) {
		$results = $query->results();
		foreach($results as $result) {
//			print "UPDATE `$db`.`items` SET itemtype = 'audio' WHERE id = ". $result["id"]."<br>";
			$query->sql("UPDATE `$db`.`items` SET itemtype = 'audio' WHERE id = ". $result["id"]);
		}
		print "UPDATED ITEMTYPE to AUDIO<br>";
	}


	// move release_date info to items.published_at and modified_at
	// first rename columns
	// rename shortname to nickname
	if($query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'text' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {
	 	if($query->sql("ALTER TABLE $db_table CHANGE `text` `description` varchar(100) NOT NULL")) {
			print "CHANGE text TO description<br>";
		}	
	}

	if($query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'release_date' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {

		$query->sql("SELECT * FROM $db_table");
		$results = $query->results();
		foreach($results as $result) {
//			print isset($result["release_date"]) .", ". $result["release_date"] . "<br>";

			if(isset($result["release_date"]) && $result["release_date"] && $result["release_date"] != "0000-00-00 00:00:00") {
	//			print "UPDATE `$db`.`items` SET published_at = '". $result["release_date"] ."' WHERE id = ". $result["item_id"]."<br>";
				$query_update->sql("UPDATE `$db`.`items` SET published_at = '". $result["release_date"] ."' WHERE id = ". $result["item_id"]);
				$query_update->sql("UPDATE `$db`.`items` SET modified_at = '". $result["release_date"] ."' WHERE id = ". $result["item_id"]);
				print "UPDATED published_at AND modified_at FROM release_date<br>";
	//			print $result["release_date"]."<br>";
			}
			else {
	//			print "SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"]."<br>";
				$query_update->sql("SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"]);
				$results_update = $query_update->results();
	//			print "UPDATE `$db`.`items` SET published_at = '". $results_update[0]["created_at"] ."' WHERE id = ". $result["item_id"]."<br>";
				$query_update->sql("UPDATE `$db`.`items` SET published_at = '". $results_update[0]["created_at"] ."' WHERE id = ". $result["item_id"]);
				$query_update->sql("UPDATE `$db`.`items` SET modified_at = '". $results_update[0]["created_at"] ."' WHERE id = ". $result["item_id"]);
				print "UPDATED published_at AND modified_at FROM created_at<br>";
			}
		}

	}


	// create radio set
//	print "INSERT INTO `$db`.`items` VALUES(DEFAULT, DEFAULT, 1, 'set', DEFAULT, DEFAULT, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)<br>";
	if(!$query->sql("SELECT * FROM `$db`.`item_set` WHERE name = 'Radio'")) {
		$query->sql("INSERT INTO `$db`.`items` VALUES(DEFAULT, DEFAULT, 1, 'set', DEFAULT, DEFAULT, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
		$item_id = $query->lastInsertId();

		$query->sql("INSERT INTO `$db`.`item_set` VALUES(DEFAULT, $item_id, 'Radio', DEFAULT)");
		print "ADD RADIO SET<br>";
	}

	// add radio items and position to set
	if($query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'sequence' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {

		$query->sql("SELECT * FROM `$db`.`item_set` WHERE name = 'Radio'");

		$set_id = $query->result(0, "item_id");

//		print "SET ID: $set_id<br>";

		$query->sql("SELECT * FROM $db_table ORDER BY sequence ASC");
		$results = $query->results();
		$real_pos = 0;
		foreach($results as $result) {
			$item_id = $result["item_id"];
			if(!$query_update->sql("SELECT * FROM `$db`.`item_set_items` WHERE item_id = $item_id")) {
//				print "INSERT INTO `$db`.`item_set_items` VALUES(DEFAULT, $item_id, $set_id)<br>";
//				$position = $result["sequence"];
//				print $position . "->" . $real_pos++ . "<br>";
				$query_update->sql("INSERT INTO `$db`.`item_set_items` VALUES(DEFAULT, $item_id, $set_id, $real_pos)");
				$real_pos++;
				print "CREATE RELATION<br>";
			}


		}

	}

	// add tags
	if(!$query_update->sql("SELECT * FROM `$db`.`tags` WHERE context = 'category' AND value = 'Radio'")) {
		$query_update->sql("INSERT INTO `$db`.`tags` VALUES(DEFAULT, 'category', 'Radio', DEFAULT)");
	}

	$query_extra = new Query();
	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();

	// get radio category tag_id
	$query_update->sql("SELECT * FROM `$db`.`tags` WHERE context = 'category' AND value = 'Radio'");
	$radio_tag_id = $query_update->result(0, "id");

	foreach($results as $result) {
		$item_id = $result["item_id"];

//		print "ITEM:" . $item_id ."<br>";
//		print "SELECT * FROM `$db`.`taggings` WHERE item_id = $item_id<br>";

		// tags exist
		if(!$query_update->sql("SELECT * FROM `$db`.`taggings` WHERE item_id = $item_id")) {
			$query_update->sql("INSERT INTO `$db`.`taggings` VALUES(DEFAULT, $item_id, $radio_tag_id)");
			print "ADDED CATEGORY TAG<br>";
		}
	}

	// drop release_date
	deleteColumn("release_date", $table, $db);
	
	// drop file
	deleteColumn("file", $table, $db);
	
	// drop sequence
	deleteColumn("sequence", $table, $db);
	
	
		// check column settings
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN id int(11) NOT NULL AUTO_INCREMENT");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN item_id int(11) NOT NULL");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN name varchar(100) NOT NULL");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN description text NULL");


	// check all news items against items table and vice versa
	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();
	foreach($results as $result) {
//		print "SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"]."<br>";
		if(!$query_update->sql("SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"])) {
			print "INVALID ITEM DELETED<br>";
			$correction_query->sql("DELETE FROM $db_table WHERE item_id = " . $result["item_id"]);
		}
	}

	$query->sql("SELECT * FROM `$db`.`items` WHERE itemtype = 'audio'");
	$results = $query->results();
	foreach($results as $result) {
//		print "SELECT * FROM $db_table WHERE item_id = " . $result["id"]."<br>";
		if(!$query_update->sql("SELECT * FROM $db_table WHERE item_id = " . $result["id"])) {
			print "INVALID ITEM DELETED<br>";
			$correction_query->sql("DELETE FROM `$db`.`items` WHERE id = " . $result["id"]);
		}
	}


	// SET KEYS
	$correction_query->sql("ALTER TABLE $db_table ADD PRIMARY KEY (id)");


	$query->sql("SHOW KEYS FROM $db_table");
	$results = $query->results();

	// UNIQUE NAME
	$u_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "name" && $result["Non_unique"] == 0) {
			$u_key = true;
		}
	}
	if(!$u_key) {
		$correction_query->sql("ALTER TABLE $db_table ADD UNIQUE (`name`)");
	}

	// ID/ITEM_ID FOREIGN KEY
	$f_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "item_id" && $result["Non_unique"] == 1) {
			$f_key = true;
		}
	}
	if(!$f_key) {
//		print "ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id)<br>";
		$correction_query->sql("ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id) ON UPDATE CASCADE ON DELETE CASCADE");
	}



	print "END - cleaning up RADIO TABLE<br>";

}


// ITEM_ABOUT - DONE
function fase_18() {

	$query = new Query();
	$query_update = new Query();
	$correction_query = new Query();
	$db = "supersonic_v1";
	$table_old = "item_about";
	$table = "item_download";
	$db_table = "`$db`.`$table`";

	// delete type column
 	print "START - cleaning up ABOUT TABLE<br>";


	// rename table if not done already
	if($query->sql("SELECT table_name FROM information_schema.tables WHERE table_schema = '$db' AND table_name = '$table_old'")) {
		$query->sql("RENAME TABLE `$db`.`$table_old` TO `$db`.`$table`");
		print "RENAME item_about TO item_download<br>";
	}

	// change table type
	$query->sql("ALTER TABLE $db_table ENGINE=InnoDB");


	// correct item type in items
	if($query->sql("SELECT * FROM `$db`.`items` WHERE itemtype = 'about'")) {
		$results = $query->results();
		foreach($results as $result) {
//			print "UPDATE `$db`.`items` SET itemtype = 'audio' WHERE id = ". $result["id"]."<br>";
			$query->sql("UPDATE `$db`.`items` SET itemtype = 'download' WHERE id = ". $result["id"]);
		}
		print "UPDATED ITEMTYPE to DOWNLOAD<br>";
	}


	// move release_date info to items.published_at and modified_at
	// first rename columns
	if($query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'release_date' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {

		$query->sql("SELECT * FROM $db_table");
		$results = $query->results();
		foreach($results as $result) {
//			print isset($result["release_date"]) .", ". $result["release_date"] . "<br>";

			if(isset($result["release_date"]) && $result["release_date"] && $result["release_date"] != "0000-00-00 00:00:00") {
	//			print "UPDATE `$db`.`items` SET published_at = '". $result["release_date"] ."' WHERE id = ". $result["item_id"]."<br>";
				$query_update->sql("UPDATE `$db`.`items` SET published_at = '". $result["release_date"] ."' WHERE id = ". $result["item_id"]);
				$query_update->sql("UPDATE `$db`.`items` SET modified_at = '". $result["release_date"] ."' WHERE id = ". $result["item_id"]);
				print "UPDATED published_at AND modified_at FROM release_date<br>";
	//			print $result["release_date"]."<br>";
			}
			else {
	//			print "SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"]."<br>";
				$query_update->sql("SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"]);
				$results_update = $query_update->results();
	//			print "UPDATE `$db`.`items` SET published_at = '". $results_update[0]["created_at"] ."' WHERE id = ". $result["item_id"]."<br>";
				$query_update->sql("UPDATE `$db`.`items` SET published_at = '". $results_update[0]["created_at"] ."' WHERE id = ". $result["item_id"]);
				$query_update->sql("UPDATE `$db`.`items` SET modified_at = '". $results_update[0]["created_at"] ."' WHERE id = ". $result["item_id"]);
				print "UPDATED published_at AND modified_at FROM created_at<br>";
			}
		}

	}


	// create about set
//	print "INSERT INTO `$db`.`items` VALUES(DEFAULT, DEFAULT, 1, 'set', DEFAULT, DEFAULT, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)<br>";
	if(!$query->sql("SELECT * FROM `$db`.`item_set` WHERE name = 'About'")) {
		$query->sql("INSERT INTO `$db`.`items` VALUES(DEFAULT, DEFAULT, 1, 'set', DEFAULT, DEFAULT, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
		$item_id = $query->lastInsertId();

		$query->sql("INSERT INTO `$db`.`item_set` VALUES(DEFAULT, $item_id, 'About', DEFAULT)");
		print "ADD About SET<br>";
	}

	// add about items and position to set
	if($query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'sequence' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {

		$query->sql("SELECT * FROM `$db`.`item_set` WHERE name = 'About'");

		$set_id = $query->result(0, "item_id");

//		print "SET ID: $set_id<br>";

		$query->sql("SELECT * FROM $db_table ORDER BY sequence ASC");
		$results = $query->results();
		$real_pos = 0;
		foreach($results as $result) {
			$item_id = $result["item_id"];
			if(!$query_update->sql("SELECT * FROM `$db`.`item_set_items` WHERE item_id = $item_id")) {
//				print "INSERT INTO `$db`.`item_set_items` VALUES(DEFAULT, $item_id, $set_id)<br>";
//				$position = $result["sequence"];
//				print $position . "->" . $real_pos++ . "<br>";
				$query_update->sql("INSERT INTO `$db`.`item_set_items` VALUES(DEFAULT, $item_id, $set_id, $real_pos)");
				$real_pos++;
				print "CREATE RELATION<br>";
			}


		}

	}

	// update item status (has never been used for about files)
	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();
	foreach($results as $result) {
		$query_update->sql("UPDATE `$db`.`items` SET status = 1 WHERE id = ". $result["item_id"]);
	}


	// add new columns and parse info into these
	if(!$query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'extension' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {
		$correction_query->sql("ALTER TABLE $db_table ADD `extension` varchar(5) NOT NULL AFTER `name`");
	}
	if(!$query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'mimetype' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {
		$correction_query->sql("ALTER TABLE $db_table ADD `mimetype` varchar(60) NOT NULL AFTER `extension`");
	}


	if($query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'file' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {

		$query->sql("SELECT * FROM $db_table");
		$results = $query->results();
		foreach($results as $result) {
			
			if(!$result["extension"]) {

				$name = preg_replace("/ \(PDF\)| \(ZIP\)/", "", $result["name"]);
				$extension = substr($result["file"], -3);

				if($extension == "pdf") {
					$mimetype = "application/pdf";
				}
				else {
					$mimetype = "application/zip";
				}
//				print "UPDATE $db_table SET name = '$name', extension = '$extension', mimetype = '$mimetype' WHERE id = ". $result["id"]."<br>";
				$query_update->sql("UPDATE $db_table SET name = '$name', extension = '$extension', mimetype = '$mimetype' WHERE id = ". $result["id"]);
				print "UPDATE FILE INFO<br>";
			}
		}
	}

	// rename files in library
	if($query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'file' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {

		$query->sql("SELECT * FROM $db_table");
		$results = $query->results();
		foreach($results as $result) {
			$file = $result["file"];
			$extension = $result["extension"];

			if(file_exists(PRIVATE_FILE_PATH."/".$result["item_id"]."/".$file)) {
//				print PRIVATE_FILE_PATH."/".$result["item_id"]."/".$file." -> ". PRIVATE_FILE_PATH."/".$result["item_id"]."/".$extension . "<br>";
				copy(PRIVATE_FILE_PATH."/".$result["item_id"]."/".$file, PRIVATE_FILE_PATH."/".$result["item_id"]."/".$extension);
//				print "delete: " . PRIVATE_FILE_PATH."/".$result["item_id"]."/".$file."<br>";
				unlink(PRIVATE_FILE_PATH."/".$result["item_id"]."/".$file);
				print "FILE RENAMED<br>";
			}

		}
	}


	// drop release_date
	deleteColumn("release_date", $table, $db);

	// drop file
	deleteColumn("file", $table, $db);

	// drop sequence
	deleteColumn("sequence", $table, $db);


		// check column settings
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN id int(11) NOT NULL AUTO_INCREMENT");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN item_id int(11) NOT NULL");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN name varchar(100) NOT NULL");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN extension varchar(5) NOT NULL");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN mimetype varchar(50) NOT NULL");


	// check all news items against items table and vice versa
	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();
	foreach($results as $result) {
//		print "SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"]."<br>";
		if(!$query_update->sql("SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"])) {
			print "INVALID ITEM DELETED<br>";
			$correction_query->sql("DELETE FROM $db_table WHERE item_id = " . $result["item_id"]);
		}
	}

	$query->sql("SELECT * FROM `$db`.`items` WHERE itemtype = 'download'");
	$results = $query->results();
	foreach($results as $result) {
//		print "SELECT * FROM $db_table WHERE item_id = " . $result["id"]."<br>";
		if(!$query_update->sql("SELECT * FROM $db_table WHERE item_id = " . $result["id"])) {
			print "INVALID ITEM DELETED<br>";
			$correction_query->sql("DELETE FROM `$db`.`items` WHERE id = " . $result["id"]);
		}
	}


	// SET KEYS
	$correction_query->sql("ALTER TABLE $db_table ADD PRIMARY KEY (id)");


	$query->sql("SHOW KEYS FROM $db_table");
	$results = $query->results();

	// UNIQUE NAME
	$u_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "name" && $result["Non_unique"] == 0) {
			$u_key = true;
		}
	}
	if(!$u_key) {
		$correction_query->sql("ALTER TABLE $db_table ADD UNIQUE (`name`)");
	}

	// ID/ITEM_ID FOREIGN KEY
	$f_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "item_id" && $result["Non_unique"] == 1) {
			$f_key = true;
		}
	}
	if(!$f_key) {
//		print "ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id)<br>";
		$correction_query->sql("ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id) ON UPDATE CASCADE ON DELETE CASCADE");
	}


	print "END - cleaning up ABOUT TABLE<br>";
}

// ITEM_FRONTPAGE_TEXTS - DONE
function fase_19() {

	$query = new Query();
	$query_update = new Query();
	$correction_query = new Query();
	$db = "supersonic_v1";
	$table_old = "item_frontpage_texts";
	$table = "item_text";
	$db_table = "`$db`.`$table`";

	// delete type column
 	print "START - cleaning up ITEM_FRONTPAGE_TEXTS TABLE<br>";


	// rename table if not done already
	if($query->sql("SELECT table_name FROM information_schema.tables WHERE table_schema = '$db' AND table_name = '$table_old'")) {
		$query->sql("RENAME TABLE `$db`.`$table_old` TO `$db`.`$table`");
		print "RENAME item_frontpage_texts TO item_text<br>";
	}

	// change table type
	$query->sql("ALTER TABLE $db_table ENGINE=InnoDB");


	// correct item type in items
	if($query->sql("SELECT * FROM `$db`.`items` WHERE itemtype = 'frontpage_text'")) {
		$results = $query->results();
		foreach($results as $result) {
//			print "UPDATE `$db`.`items` SET itemtype = 'audio' WHERE id = ". $result["id"]."<br>";
			$query->sql("UPDATE `$db`.`items` SET itemtype = 'text' WHERE id = ". $result["id"]);
		}
		print "UPDATED ITEMTYPE to TEXT<br>";
	}



	// move release_date info to items.published_at and modified_at
	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();
	foreach($results as $result) {

		if($query->sql("SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"])) {
			$published_at = $query->result(0, "published_at");

			if(!$published_at) {
				$created_at = $query->result(0, "created_at");

//				print "UPDATE `$db`.`items` SET published_at = '". $created_at ."' WHERE id = ". $result["item_id"]."<br>";
				$query_update->sql("UPDATE `$db`.`items` SET published_at = '". $created_at ."' WHERE id = ". $result["item_id"]);
				$query_update->sql("UPDATE `$db`.`items` SET modified_at = '". $created_at ."' WHERE id = ". $result["item_id"]);
				print "UPDATED published_at AND modified_at FROM created_at<br>";
			}
		}
	}

	// update item status (has never been used for about files)
	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();
	foreach($results as $result) {
		$query_update->sql("UPDATE `$db`.`items` SET status = 1 WHERE id = ". $result["item_id"]);
	}


		// check column settings
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN id int(11) NOT NULL AUTO_INCREMENT");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN item_id int(11) NOT NULL");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN name varchar(100) NOT NULL");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN text text NOT NULL");


	// check all news items against items table and vice versa
	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();
	foreach($results as $result) {
//		print "SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"]."<br>";
		if(!$query_update->sql("SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"])) {
			print "INVALID ITEM DELETED<br>";
			$correction_query->sql("DELETE FROM $db_table WHERE item_id = " . $result["item_id"]);
		}
	}

	$query->sql("SELECT * FROM `$db`.`items` WHERE itemtype = 'text'");
	$results = $query->results();
	foreach($results as $result) {
//		print "SELECT * FROM $db_table WHERE item_id = " . $result["id"]."<br>";
		if(!$query_update->sql("SELECT * FROM $db_table WHERE item_id = " . $result["id"])) {
			print "INVALID ITEM DELETED<br>";
			$correction_query->sql("DELETE FROM `$db`.`items` WHERE id = " . $result["id"]);
		}
	}


	// SET KEYS
	$correction_query->sql("ALTER TABLE $db_table ADD PRIMARY KEY (id)");


	$query->sql("SHOW KEYS FROM $db_table");
	$results = $query->results();

	// UNIQUE NAME
	$u_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "name" && $result["Non_unique"] == 0) {
			$u_key = true;
		}
	}
	if(!$u_key) {
		$correction_query->sql("ALTER TABLE $db_table ADD UNIQUE (`name`)");
	}

	// ID/ITEM_ID FOREIGN KEY
	$f_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "item_id" && $result["Non_unique"] == 1) {
			$f_key = true;
		}
	}
	if(!$f_key) {
//		print "ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id)<br>";
		$correction_query->sql("ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id) ON UPDATE CASCADE ON DELETE CASCADE");
	}


	// add tags and clean up for old items
	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();
	foreach($results as $result) {
		$name = $result["name"];

		if(preg_match("/Radio|Music|About|Sound/", $name)) {
			print "VALID text $name<br>";
			if(!$query->sql("SELECT * FROM `$db`.`tags` WHERE value = '$name' AND context = 'page'")) {
				$query->sql("INSERT INTO `$db`.`tags` VALUES(DEFAULT, 'page', '$name', DEFAULT)");
				$tag_id = $query->lastInsertId();
			}
			else {
				$tag_id = $query->result(0, "id");
			}

			if(!$query->sql("SELECT * FROM `$db`.`taggings` WHERE tag_id = $tag_id AND item_id = " . $result["item_id"])) {
//				print "INSERT INTO `$db`.`taggings` VALUES(DEFAULT, ".$result["item_id"].", $tag_id)<br>";
				$query->sql("INSERT INTO `$db`.`taggings` VALUES(DEFAULT, ".$result["item_id"].", $tag_id)");
				print "CREATE TAG RELATION<br>";
			}

//			print "TAG_ID for $name:" . $tag_id ."<br>";
			
		}
		else {
//			print "DELETE FROM `$db`.`items` WHERE id = ". $result["item_id"]."<br>";
			$query->sql("DELETE FROM `$db`.`items` WHERE id = ". $result["item_id"]);
			print "INVALID text DELETED: $name<br>";

		}
	}


	print "END - cleaning up ITEM_FRONTPAGE_TEXTS TABLE<br>";

}


// ITEM_FRONTPAGE_IMAGES - DONE
function fase_20() {

	$query = new Query();
	$query_update = new Query();
	$correction_query = new Query();
	$db = "supersonic_v1";
	$table_old = "item_frontpage_images";
	$table = "item_image";
	$db_table = "`$db`.`$table`";

	// delete type column
 	print "START - cleaning up ITEM_FRONTPAGE_IMAGES TABLE<br>";


	// rename table if not done already
	if($query->sql("SELECT table_name FROM information_schema.tables WHERE table_schema = '$db' AND table_name = '$table_old'")) {
		$query->sql("RENAME TABLE `$db`.`$table_old` TO `$db`.`$table`");
		print "RENAME item_frontpage_images TO item_image<br>";
	}

	// change table type
	$query->sql("ALTER TABLE $db_table ENGINE=InnoDB");


	// delete duplet
	$query->sql("DELETE FROM `$db`.`items` WHERE id = 454");
	$query->sql("DELETE FROM $db_table WHERE item_id = 454");

	// correct item type in items
	if($query->sql("SELECT * FROM `$db`.`items` WHERE itemtype = 'frontpage_image'")) {
		$results = $query->results();
		foreach($results as $result) {
//			print "UPDATE `$db`.`items` SET itemtype = 'audio' WHERE id = ". $result["id"]."<br>";
			$query->sql("UPDATE `$db`.`items` SET itemtype = 'image' WHERE id = ". $result["id"]);
		}
		print "UPDATED ITEMTYPE to IMAGE<br>";
	}


	// move release_date info to items.published_at and modified_at
	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();
	foreach($results as $result) {

		if($query->sql("SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"])) {
			$published_at = $query->result(0, "published_at");

			if(!$published_at) {
				$created_at = $query->result(0, "created_at");

//				print "UPDATE `$db`.`items` SET published_at = '". $created_at ."' WHERE id = ". $result["item_id"]."<br>";
				$query_update->sql("UPDATE `$db`.`items` SET published_at = '". $created_at ."' WHERE id = ". $result["item_id"]);
				$query_update->sql("UPDATE `$db`.`items` SET modified_at = '". $created_at ."' WHERE id = ". $result["item_id"]);
				print "UPDATED published_at AND modified_at FROM created_at<br>";
			}
		}
	}


	// create name column
	if(!$query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'name' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {
		$correction_query->sql("ALTER TABLE $db_table ADD `name` varchar(50) NOT NULL AFTER `item_id`");
//		$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN name varchar(50) AFTER item_id");
		print "CREATE name COLUMN<br>";
	}

	// move filename into name
	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();
	foreach($results as $result) {

		$name = $result["name"];
		if(!$name) {
			$file = $result["file"];
			$new_name = preg_replace("/\_/", " ", preg_replace("/.jpg/", "", $file));
//			print "UPDATE $db_table SET name = '". $new_name ."' WHERE item_id = ". $result["item_id"]."<br>";
			$query_update->sql("UPDATE $db_table SET name = '". $new_name ."' WHERE item_id = ". $result["item_id"]);
			print "SET NAME: $new_name<br>";
		}
	}


	// drop file
	deleteColumn("file", $table, $db);


	// check column settings
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN id int(11) NOT NULL AUTO_INCREMENT");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN item_id int(11) NOT NULL");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN name varchar(50) NOT NULL");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN link varchar(255) NULL");



	// check all news items against items table and vice versa
	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();
	foreach($results as $result) {
//		print "SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"]."<br>";
		if(!$query_update->sql("SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"])) {
			print "INVALID ITEM DELETED<br>";
			$correction_query->sql("DELETE FROM $db_table WHERE item_id = " . $result["item_id"]);
		}
	}

	$query->sql("SELECT * FROM `$db`.`items` WHERE itemtype = 'image'");
	$results = $query->results();
	foreach($results as $result) {
//		print "SELECT * FROM $db_table WHERE item_id = " . $result["id"]."<br>";
		if(!$query_update->sql("SELECT * FROM $db_table WHERE item_id = " . $result["id"])) {
			print "INVALID ITEM DELETED<br>";
			$correction_query->sql("DELETE FROM `$db`.`items` WHERE id = " . $result["id"]);
		}
	}


	// SET KEYS
	$correction_query->sql("ALTER TABLE $db_table ADD PRIMARY KEY (id)");


	$query->sql("SHOW KEYS FROM $db_table");
	$results = $query->results();

	// UNIQUE NAME
	$u_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "name" && $result["Non_unique"] == 0) {
			$u_key = true;
		}
	}
	if(!$u_key) {
		$correction_query->sql("ALTER TABLE $db_table ADD UNIQUE (`name`)");
	}

	// ID/ITEM_ID FOREIGN KEY
	$f_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "item_id" && $result["Non_unique"] == 1) {
			$f_key = true;
		}
	}
	if(!$f_key) {
//		print "ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id)<br>";
		$correction_query->sql("ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id) ON UPDATE CASCADE ON DELETE CASCADE");
	}


	// add tags and clean up for old items
	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();
	foreach($results as $result) {
		$item_id = $result["item_id"];

		$query_update->sql("SELECT * FROM `$db`.`taggings` WHERE item_id = $item_id");
		$results_update = $query_update->results();
		foreach($results_update as $result_update) {
			$tag_id = $result_update["tag_id"];
			$taggings_id = $result_update["id"];

			$correction_query->sql("SELECT * FROM `$db`.`tags` WHERE id = $tag_id");

			$context = $correction_query->result(0, "context");
			$value = $correction_query->result(0, "value");

			// add correct tag

			if($context != "page") {

				if($correction_query->sql("SELECT * FROM `$db`.`tags` WHERE context = 'page' AND value = '$value'")) {

					$correct_tag_id = $correction_query->result(0, "id");
	//				print "INSERT INTO `$db`.`taggings` VALUES(DEFAULT, $item_id, $correct_tag_id)<br>";
					$correction_query->sql("INSERT INTO `$db`.`taggings` VALUES(DEFAULT, $item_id, $correct_tag_id)");
					$correction_query->sql("DELETE FROM `$db`.`taggings` WHERE id = $taggings_id");
					print "TAG CORRECTED<br>";

	//				print $result["item_id"] . ":" . $result["name"] . ":" . $correct_tag_id . "<br>";

				}
				else {
					$correction_query->sql("DELETE FROM `$db`.`taggings` WHERE id = $taggings_id");
					print "INVALID TAG DELETED<br>";
				}
				
			}
		}

	}


	print "END - cleaning up ITEM_FRONTPAGE_IMAGES TABLE<br>";

}


// ITEM_CLIPS - DONE
function fase_21() {

	$query = new Query();
	$query_update = new Query();
	$correction_query = new Query();
	$db = "supersonic_v1";
	$table_old = "item_clips";
	$table = "item_video";
	$db_table = "`$db`.`$table`";

	// delete type column
 	print "START - cleaning up ITEM_CLIPS TABLE<br>";


	// rename table if not done already
	if($query->sql("SELECT table_name FROM information_schema.tables WHERE table_schema = '$db' AND table_name = '$table_old'")) {
		$query->sql("RENAME TABLE `$db`.`$table_old` TO `$db`.`$table`");
		print "RENAME item_clips TO item_video<br>";
	}

	// change table type
	$query->sql("ALTER TABLE $db_table ENGINE=InnoDB");


	// correct item type in items
	if($query->sql("SELECT * FROM `$db`.`items` WHERE itemtype = 'clip'")) {
		$results = $query->results();
		foreach($results as $result) {
//			print "UPDATE `$db`.`items` SET itemtype = 'audio' WHERE id = ". $result["id"]."<br>";
			$query->sql("UPDATE `$db`.`items` SET itemtype = 'video' WHERE id = ". $result["id"]);
		}
		print "UPDATED ITEMTYPE to VIDEO<br>";
	}


	// move release_date info to items.published_at and modified_at
	// first rename columns
	if($query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'release_date' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {

		$query->sql("SELECT * FROM $db_table");
		$results = $query->results();
		foreach($results as $result) {
//			print isset($result["release_date"]) .", ". $result["release_date"] . "<br>";

			if(isset($result["release_date"]) && $result["release_date"] && $result["release_date"] != "0000-00-00 00:00:00") {
	//			print "UPDATE `$db`.`items` SET published_at = '". $result["release_date"] ."' WHERE id = ". $result["item_id"]."<br>";
				$query_update->sql("UPDATE `$db`.`items` SET published_at = '". $result["release_date"] ."' WHERE id = ". $result["item_id"]);
				$query_update->sql("UPDATE `$db`.`items` SET modified_at = '". $result["release_date"] ."' WHERE id = ". $result["item_id"]);
				print "UPDATED published_at AND modified_at FROM release_date<br>";
	//			print $result["release_date"]."<br>";
			}
			else {
	//			print "SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"]."<br>";
				$query_update->sql("SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"]);
				$results_update = $query_update->results();
	//			print "UPDATE `$db`.`items` SET published_at = '". $results_update[0]["created_at"] ."' WHERE id = ". $result["item_id"]."<br>";
				$query_update->sql("UPDATE `$db`.`items` SET published_at = '". $results_update[0]["created_at"] ."' WHERE id = ". $result["item_id"]);
				$query_update->sql("UPDATE `$db`.`items` SET modified_at = '". $results_update[0]["created_at"] ."' WHERE id = ". $result["item_id"]);
				print "UPDATED published_at AND modified_at FROM created_at<br>";
			}
		}

	}

	// drop vfxshowcase_height
	deleteColumn("release_date", $table, $db);

	// drop thumbnail
	deleteColumn("thumbnail", $table, $db);

	// drop screendump
	deleteColumn("screendump", $table, $db);

	// drop clip
	deleteColumn("clip", $table, $db);

	// drop clip_width
	deleteColumn("clip_width", $table, $db);

	// drop clip_height
	deleteColumn("clip_height", $table, $db);

	// drop prepost_screendump
	deleteColumn("prepost_screendump", $table, $db);

	// drop prepost
	deleteColumn("prepost", $table, $db);

	// drop prepost_width
	deleteColumn("prepost_width", $table, $db);

	// drop prepost_height
	deleteColumn("prepost_height", $table, $db);

	// drop vfxshowcase_screendump
	deleteColumn("vfxshowcase_screendump", $table, $db);

	// drop vfxshowcase
	deleteColumn("vfxshowcase", $table, $db);

	// drop vfxshowcase_width
	deleteColumn("vfxshowcase_width", $table, $db);

	// drop vfxshowcase_height
	deleteColumn("vfxshowcase_height", $table, $db);


	// first rename columns
	if($query->sql("SELECT DISTINCT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE column_name = 'text' AND TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db'")) {
	 	if($query->sql("ALTER TABLE $db_table CHANGE `text` `description` text NULL")) {
			print "CHANGE text TO description<br>";
		}	
	}

	// check column settings
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN id int(11) NOT NULL AUTO_INCREMENT");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN item_id int(11) NOT NULL");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN name varchar(50) NOT NULL");
	$correction_query->sql("ALTER TABLE $db_table MODIFY COLUMN description text NULL");


	// create Music set
	if(!$query->sql("SELECT * FROM `$db`.`item_set` WHERE name = 'Music'")) {
		$query->sql("INSERT INTO `$db`.`items` VALUES(DEFAULT, DEFAULT, 1, 'set', DEFAULT, DEFAULT, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
		$item_id = $query->lastInsertId();

		$query->sql("INSERT INTO `$db`.`item_set` VALUES(DEFAULT, $item_id, 'Music', DEFAULT)");
		print "ADD Music SET<br>";
	}
	// create Sound set
	if(!$query->sql("SELECT * FROM `$db`.`item_set` WHERE name = 'Commercial'")) {
		$query->sql("INSERT INTO `$db`.`items` VALUES(DEFAULT, DEFAULT, 1, 'set', DEFAULT, DEFAULT, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
		$item_id = $query->lastInsertId();

		$query->sql("INSERT INTO `$db`.`item_set` VALUES(DEFAULT, $item_id, 'Commercial', DEFAULT)");
		print "ADD Commercial SET<br>";
	}

	if(!$query_update->sql("SELECT * FROM `$db`.`tags` WHERE context = 'category' AND value = 'Music'")) {
		$query_update->sql("INSERT INTO `$db`.`tags` VALUES(DEFAULT, 'category', 'Music', DEFAULT)");
	}

	if(!$query_update->sql("SELECT * FROM `$db`.`tags` WHERE context = 'category' AND value = 'Commercial'")) {
		$query_update->sql("INSERT INTO `$db`.`tags` VALUES(DEFAULT, 'category', 'Commercial', DEFAULT)");
	}


	// get music category tag_id
	$query_update->sql("SELECT * FROM `$db`.`tags` WHERE context = 'category' AND value = 'Music'");
	$music_tag_id = $query_update->result(0, "id");

	// get Commercial category tag_id
	$query_update->sql("SELECT * FROM `$db`.`tags` WHERE context = 'category' AND value = 'Commercial'");
	$commercial_tag_id = $query_update->result(0, "id");

//	print $music_tag_id . ":" . $commercial_tag_id . "<br>";

	$query_extra = new Query();
	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();
	foreach($results as $result) {
		$item_id = $result["item_id"];
	
//		print "ITEM:" . $item_id ."<br>";
//		print "SELECT * FROM `$db`.`taggings` WHERE item_id = $item_id<br>";
	
		// tags exist
		if($query_update->sql("SELECT * FROM `$db`.`taggings` WHERE item_id = $item_id")) {
			$results_update = $query_update->results();

	
			foreach($results_update as $result_update) {
				$tag_id = $result_update["tag_id"];
	
//				print "TAG: $tag_id<br>";


				$query_extra->sql("SELECT * FROM `$db`.`tags` WHERE id = $tag_id");
				$context = $query_extra->result(0, "context");
				$value = $query_extra->result(0, "value");
				
				if($context != "category") {
					if($value == "Sound") {
						if(!$query_extra->sql("SELECT * FROM `$db`.`taggings` WHERE item_id = $item_id AND tag_id = $commercial_tag_id")) {
							$query_update->sql("INSERT INTO `$db`.`taggings` VALUES(DEFAULT, $item_id, $commercial_tag_id)");
							print "ADD COMMERCIAL CAT TAG<br>";
						}
					}
					if($value == "Music") {
						if(!$query_extra->sql("SELECT * FROM `$db`.`taggings` WHERE item_id = $item_id AND tag_id = $music_tag_id")) {
							$query_update->sql("INSERT INTO `$db`.`taggings` VALUES(DEFAULT, $item_id, $music_tag_id)");
							print "ADD MUSIC CAT TAG<br>";
						}
					}

					$query_update->sql("DELETE FROM `$db`.`taggings` WHERE item_id = $item_id AND tag_id = $tag_id");
					print "DELETE SET TAG<br>";
				}	
			}
		}
		else {
	
		}
	
	}


	$query_update->sql("DELETE FROM `$db`.`tags` WHERE context = 'set' AND value = 'Music'");
	$query_update->sql("DELETE FROM `$db`.`tags` WHERE context = 'set' AND value = 'Sound'");
	$query_update->sql("DELETE FROM `$db`.`tags` WHERE context = 'set' AND value = 'Front'");



	// check all news items against items table and vice versa
	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();
	foreach($results as $result) {
//		print "SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"]."<br>";
		if(!$query_update->sql("SELECT * FROM `$db`.`items` WHERE id = " . $result["item_id"])) {
			print "INVALID ITEM DELETED<br>";
			$correction_query->sql("DELETE FROM $db_table WHERE item_id = " . $result["item_id"]);
		}
	}

	$query->sql("SELECT * FROM `$db`.`items` WHERE itemtype = 'video'");
	$results = $query->results();
	foreach($results as $result) {
//		print "SELECT * FROM $db_table WHERE item_id = " . $result["id"]."<br>";
		if(!$query_update->sql("SELECT * FROM $db_table WHERE item_id = " . $result["id"])) {
			print "INVALID ITEM DELETED<br>";
			$correction_query->sql("DELETE FROM `$db`.`items` WHERE id = " . $result["id"]);
		}
	}


	// SET KEYS
	$correction_query->sql("ALTER TABLE $db_table ADD PRIMARY KEY (id)");


	$query->sql("SHOW KEYS FROM $db_table");
	$results = $query->results();

	// UNIQUE NAME
	$u_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "name" && $result["Non_unique"] == 0) {
			$u_key = true;
		}
	}
	if(!$u_key) {
		$correction_query->sql("ALTER TABLE $db_table ADD UNIQUE (`name`)");
	}

	// ID/ITEM_ID FOREIGN KEY
	$f_key = false;
	foreach($results as $result) {
		if($result["Column_name"] == "item_id" && $result["Non_unique"] == 1) {
			$f_key = true;
		}
	}
	if(!$f_key) {
//		print "ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id)<br>";
		$correction_query->sql("ALTER TABLE $db_table ADD CONSTRAINT FOREIGN KEY (item_id) REFERENCES items(id) ON UPDATE CASCADE ON DELETE CASCADE");
	}

	print "END - cleaning up ITEM_CLIPS TABLE<br>";


}

// DELETE UNUSED TABLES
function fase_22() {
	
	$query = new Query();
	$query_update = new Query();
	$db = "supersonic_v1";

	// delete type column
 	print "START - DELETING EMPTY TABLES<br>";
	
	
	$query_update->sql("DROP TABLE `$db`.`newsletter_signups`");
	$query_update->sql("DROP TABLE `$db`.`basics_itemtypes`");
	$query_update->sql("DROP TABLE `$db`.`basics_vat_rates`");
	$query_update->sql("DROP TABLE `$db`.`basics_currencies`");
	$query_update->sql("DROP TABLE `$db`.`basics_itemtype_mimetypes`");
	$query_update->sql("DROP TABLE `$db`.`basics_languages`");
	$query_update->sql("DROP TABLE `$db`.`basics_mimetypes`");
	$query_update->sql("DROP TABLE `$db`.`basics_countries`");
	$query_update->sql("DROP TABLE `$db`.`users`");
	$query_update->sql("DROP TABLE `$db`.`users_access_level_points`");
	$query_update->sql("DROP TABLE `$db`.`users_access_levels`");
	$query_update->sql("DROP TABLE `$db`.`users_access_points`");
	$query_update->sql("DROP TABLE `$db`.`users_log`");
	$query_update->sql("DROP TABLE `$db`.`users_menu`");
	$query_update->sql("DROP TABLE `$db`.`item_reels`");
	
	print "END - DELETING EMPTY TABLES<br>";
 	
}

// delete disabled items - and REEL items
function fase_23() {

	$query = new Query();
	$query_update = new Query();
	$correction_query = new Query();
	$db = "supersonic_v1";
	$table = "items";
	$db_table = "`$db`.`$table`";

	// delete type column
 	print "START - cleaning up disabled items<br>";


	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();
	foreach($results as $result) {

//		print "STATUS:" . $result["status"] . "<br>";

		if($result["status"] == 0 || $result["itemtype"] == "reel") {
			$item_id = $result["id"];
			$itemtype = $result["itemtype"];

			$query_update->sql("DELETE FROM $db_table WHERE id = $item_id");
			print "DELETE ITEM: $item_id ($itemtype)<br>";

			// delete orphan files
			// check both library/item_id and library/private/item_id
			if(file_exists(LOCAL_PATH."/library/".$item_id)) {
				FileSystem::removeDirRecursively(LOCAL_PATH."/library/".$item_id);
				print "OLD FILES DELETED: " . LOCAL_PATH."/library/".$item_id . "<br>";
			}
			if(file_exists(PRIVATE_FILE_PATH."/".$item_id)) {
				FileSystem::removeDirRecursively(PRIVATE_FILE_PATH."/".$item_id);
				print "NEW FILES DELETED: " . PRIVATE_FILE_PATH."/".$item_id . "<br>";
			}


		}
	}



 	print "END - cleaning up disabled items<br>";
	
}


// update sindex
function fase_30() {

	$query = new Query();
	$IC = new Item();

// 	// update sindex
	print "START - cleaning up sindex<br>";
// 	// rename original file to jpg
	if($query->sql("SELECT * FROM ".UT_ITE)) {
		$results = $query->results();
		foreach($results as $result) {
			print $result["id"]."<br>";
			$sindex = $IC->sindex($result["id"]);
			print $sindex."<br>";
// 			$IC->sindex($result["id"], $sindex);
		}
	}
 	print "END - cleaning up sindex<br>";


	// cannot set sindex to be UNIQUE with NULL allowed
//	$correction_query->sql("ALTER TABLE `supersonic_v1`.`items` ADD UNIQUE (`sindex`)");
}


// move music tags and check if all videos have commercial-tag
function fase_31() {

	$query = new Query();
	$query_update = new Query();
	$correction_query = new Query();
	$db = "supersonic_v1";
	$table = "item_video";
	$db_table = "`$db`.`$table`";


	print "START - moving music to commercials<br>";

	// get music category tag_id
	$query_update->sql("SELECT * FROM `$db`.`tags` WHERE context = 'category' AND value = 'Music'");
	$music_tag_id = $query_update->result(0, "id");

	// get Commercial category tag_id
	$query_update->sql("SELECT * FROM `$db`.`tags` WHERE context = 'category' AND value = 'Commercial'");
	$commercial_tag_id = $query_update->result(0, "id");


	$query->sql("SELECT * FROM $db_table");
	$results = $query->results();
	foreach($results as $result) {
		$item_id = $result["item_id"];

		if($query_update->sql("SELECT * FROM `$db`.`taggings` WHERE item_id = $item_id AND tag_id = $music_tag_id")) {
			$taggings_id = $query_update->result(0, "id");
			$correction_query->sql("DELETE FROM `$db`.`taggings` WHERE id = $taggings_id");
			print "DELETE MUSIC CAT TAG<br>";
		}

		if(!$query_update->sql("SELECT * FROM `$db`.`taggings` WHERE item_id = $item_id AND tag_id = $commercial_tag_id")) {
			$correction_query->sql("INSERT INTO `$db`.`taggings` VALUES(DEFAULT, $item_id, $commercial_tag_id)");
			print "ADD COMMERCIAL CAT TAG<br>";
		}

	}

	print "END - moving music to commercials<br>";

}

// rename sound to commercials
function fase_32() {

	$query = new Query();
	$query_update = new Query();
	$correction_query = new Query();
	$db = "supersonic_v1";
	$table = "item_text";
	$db_table = "`$db`.`$table`";

	print "START - rename sound-text to commercial-text<br>";

	if($query_update->sql("SELECT * FROM $db_table WHERE name = 'Sound'")) {
		$text_id = $query_update->result(0, "id");
		$query->sql("UPDATE $db_table SET name = 'Commercials' WHERE id = $text_id");
		print "SOUND text renamed to COMMERCIALS<br>";
	}

	print "END - rename sound-text to commercial-text<br>";

}

// delete downloads - not used on site
function fase_33() {
	$query = new Query();

	$IC = new Item();
	$items = $IC->getItems(array("itemtype" => "download"));

	foreach($items as $item) {
		print "delete:" . $item["id"] . "<br>";
		$IC->deleteItem($item["id"]);
	}


}
// delete unused tags
// convert media


// update links in item_image after sindex has been updated
// figure out how to keep links updated - sindex might change/clip might be deleted, link can also be external, so we can't use db-relation



// execute
//fase_1();		// move files from itemtype folders to id folders
//fase_2();		// news - files
//fase_3();		// people - files
//fase_4();		// about - files (keep file names)
//fase_5();		// clips - files - skipping vfx and prepost
//fase_6();		// frontpage - images
//fase_7();		// radio - files
//fase_8();		// STATUS on remaining files

// fase_10();	// ITEMS TABLE
// fase_11();	// TAGS
// fase_12();	// ITEM_TAGS
// fase_13();	// ITEM_NEWS
// fase_14();	// ITEM_PEOPLE
// fase_15();	// ITEM_DEPARTMENTS
// fase_16();	// CREATE SETS AND SET ITEMS
// fase_17();	// ITEM_RADIO
// fase_18();	// ITEM_ABOUT
// fase_19();	// ITEM_FRONTPAGE_TEXTS
// fase_20();	// ITEM_FRONTPAGE_IMAGES
// fase_21();	// ITEM_CLIPS
// fase_22();	// DELETE UNUSED TABLES
// fase_8();		// STATUS on remaining files
// fase_23();	// DELETE DISABLED FILES - and REEL items

// fase_30();	// update sindex
// fase_31();	// music tags to commercials
// fase_32();	// sound tags to commercials

// fase_33();		// delete download items


print "CLEANUP - delete empty folders<br>";

FileSystem::removeEmptyDirRecursively(LOCAL_PATH."/library", array("deny_folders" => "private"));
?>
	

</div>