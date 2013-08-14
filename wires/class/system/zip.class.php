<?php

class Zip {
	/**
	* Unpack .zip file
	*
	* @param String $file filename (including path)
	* @param bool $folders true if file is to be unpacked in folders, false if flat 
	* @return string|false Item path and filename to unpacked filestructure false on error
	*/
	function unpackZip($file, $folders = false) {
		if($zipfile = zip_open($file)) {
			if($zipfile) {
				// create folder
				$dir = str_replace(".zip", "", $file);
				mkdir($dir);

				// read entries
				while($zip_entry = zip_read($zipfile)) {
					// check if file is valid
					if(substr(zip_entry_name($zip_entry),0,1) == "_" || substr(zip_entry_name($zip_entry),0,1) == ".") {
					}
					// system file
					else {
						// start reading file
						if(zip_entry_open($zipfile,$zip_entry,"r")) {
							$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
							// check if file is in folder
							$dir_name = dirname(zip_entry_name($zip_entry));

							if($dir_name != ".") {
								// check structure
								$structure = $dir . "/";
								foreach(explode("/",$dir_name) as $folder) {
									// only create folders if we have to unpack in folders
									if($folders) {
										$structure .= $folder;
										$structure = utf8Encode($structure);
										if(is_file($structure)) {
											unlink($structure);
										}
										if(!is_dir($structure)) {
											mkdir($structure);
										}
										$structure .= "/";
									}
									// else delete folders agian
									else {
										if(file_exists($dir . "/" . $folder)) {
											unlink($dir . "/" . $folder);
										}
									}
								}
							}
							// should we unpack in folders
							if($folders) {
								// empty folder
								if($buf == "" && !is_dir($dir."/".zip_entry_name($zip_entry)) && substr(zip_entry_name($zip_entry), -1) == "/") {
									mkdir(utf8Encode($dir."/".zip_entry_name($zip_entry)));
									$fp = false;
								}
								else {
									$fp = fopen(utf8Encode($dir . "/" . zip_entry_name($zip_entry)),"w");
								}
							}
							// or just flat
							else {
								$fp = fopen($dir . "/" . basename(zip_entry_name($zip_entry)),"w");
							}
							if($fp) {
								fwrite($fp,$buf);
								fclose($fp);
							}
							zip_entry_close($zip_entry);
						}
						else {
							return false;
						}
					}
				}
				zip_close($zipfile);
			}
		}
		else {
			return false;
		}
		return $dir;
	}

}

?>