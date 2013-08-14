<?php
$access_item = false;

if(isset($read_access) && $read_access) {
	return;
}

header("Content-type: text/html; charset=UTF-8");

function import_database_tables($db_name, $names, $folder) {
	global $global_db_name;
	if($db_name) {
//		global $db_hostname, $username, $password;
		$data = null;
		$result = null;
		$status = 'not initialized';
		$command = null;
//		$conn = mysql_connect($db_hostname, $username, $password) OR DIE("No connection to MySQL");
		if(!mysql_select_db($db_name)){
			mysql_query("CREATE DATABASE `$db_name` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		} 
		mysql_select_db($db_name) OR DIE("No connection to Database");
		mysql_query("SET NAMES utf8");
		mysql_query("SET CHARACTER SET utf8");
	
		// For each database table
		for ($i = 0; $i < count($names); $i++) {
		
			print '<div class="group">';
			print "<h1>Processing $db_name/$names[$i]</h1>";
			//print $_SERVER["GLOBAL_PATH"].'/config/db/'.$db_name.'/' . $names[$i] . '.sql';
			// find file
			if(file_exists($_SERVER["LOCAL_PATH"].'/config/db/' . $names[$i] . '.sql')) {
				$filename = $_SERVER["LOCAL_PATH"].'/config/db/' . $names[$i] . '.sql';
			}
			else if(file_exists($_SERVER["REGIONAL_PATH"].'/config/db/'.$db_name.'/' . $names[$i] . '.sql')) {
				$filename = $_SERVER["REGIONAL_PATH"].'/config/db/'.$db_name.'/' . $names[$i] . '.sql';
			}
			else if(file_exists($_SERVER["REGIONAL_PATH"].'/config/db/' . $names[$i] . '.sql')) {
				$filename = $_SERVER["REGIONAL_PATH"].'/config/db/' . $names[$i] . '.sql';
			}
			else if(file_exists($_SERVER["GLOBAL_PATH"].'/config/db/'.$db_name.'/' . $names[$i] . '.sql')) {
				$filename = $_SERVER["GLOBAL_PATH"].'/config/db/'.$db_name.'/' . $names[$i] . '.sql';
			}
			else if(file_exists($_SERVER["GLOBAL_PATH"].'/config/db/' . $names[$i] . '.sql')) {
				$filename = $_SERVER["GLOBAL_PATH"].'/config/db/' . $names[$i] . '.sql';
			}
			else {
				$filename = $_SERVER["FRAMEWORK_PATH"].'/config/db/' . $names[$i] . '.sql';
				
			}
			print "<p>Filename: $filename</p>";
		
			// Load contents of file
			$data = file($filename);
			//$data = file($folder . $names[$i] . '.sql');
		
			// For each line in file collect commands in temp var
			for ($j = 0; $j < count($data); $j++) {
				if ($data != '') {
					$command .= $data[$j];
				}
			}
		
			// Remove new lines
			$command = str_replace("GLOBAL_DB", $global_db_name, $command);
			$command = str_replace("\n", "", $command);
			$command = str_replace("LOCAL_PATH", LOCAL_PATH, $command);
			$command = str_replace("REGIONAL_PATH", REGIONAL_PATH, $command);
			$command = str_replace("GLOBAL_PATH", GLOBAL_PATH, $command);
			$command = str_replace("FRAMEWORK_PATH", FRAMEWORK_PATH, $command);
		
			// Split command at ;
			$all_commands = explode(";", $command);
		
			// Execute commands
			for ($k = 0; $k < count($all_commands); $k++) {
			
				// Ignore comments
				if ($all_commands[$k] != '' && substr($all_commands[$k], 0, 2) != '/*') {
					$result = mysql_query($all_commands[$k]);
					$status = ($result != 1) ? 'bad' : 'ok';
					print '<div class="' . $status . '">' . $all_commands[$k];
					if ($status == 'bad') {
						print '<div class="bad_inner">- ' . mysql_error() . '</div>';
					}
					print '</div>';
				}
			
				$result = null;
				$status = null;
			}
		
			$data = null;
			$command = null;
		 		
			print "</div>";
		}
	
		//mysql_close();
	}
}

function remove_database_tables($db_name, $names, $type="") {
	if($db_name) {
//		global $db_hostname, $username, $password;
		$result = null;
		$status = 'not initialized';
		$command = null;
//		$conn = mysql_connect($db_hostname, $username, $password) OR DIE("No connection to MySQL");
		if(mysql_select_db($db_name)){
			mysql_query("SET NAMES utf8");
			mysql_query("SET CHARACTER SET utf8");

			print '<div class="group">';
			print '<h1>Emptying ' . $type . " " . $db_name . '</h1>';

			// For each database table in reverse order
			for ($i = (count($names) - 1); $i >= 0; $i--) {
				$command = 'DROP TABLE IF EXISTS `' . $names[$i] . '`';
				$result = mysql_query($command);
				$status = ($result != 1) ? 'bad' : 'ok';
				print '<div class="' . $status . '">' . $command;
				if ($status == 'bad') {
					print '<div class="bad_inner">- ' . mysql_error() . '</div>';
				}
				print '</div>';
				$command = null;
			}

			print "</div>";

			//mysql_close();
		} //OR DIE("No connection to Database");
		else {
			print "No connection to Database ".$db_name."<br/>";
		}
	}
}

$import = true;
if(isset($_GET["import"]) && $_GET["import"] == "false") {
	$import = false;
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Reseting databases...</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<style type="text/css">
	body {
		margin: 0;
		font-family: monaco, sans-serif;
		font-size: 10px;
		color: #000;
	}
	
	.group, .bad_inner {
		border: 1px solid #3875D7;
		background-color: #E7EDF8;
		padding: 4px;
		margin: 4px;
	}
	
	.group h1 {
		margin: 0;
		font-family: monaco, sans-serif;
		font-size: 14px;
		color: #3875D7;
		font-weight: normal;
	}
	.group p {
		margin: 0;
		font-family: monaco, sans-serif;
		font-size: 10px;
		color: #3875D7;
		font-weight: normal;
	}
	
	.ok {
		border: 1px solid #22A84B;
		color: #22A84B;
		background-color: #D6F7D6;
		padding: 2px 2px 0 2px;
		margin-bottom: 4px;
	}
	
	.bad {
		border: 1px solid #CC0000;
		color: #CC0000;
		background-color: #ECD8D8;
		padding: 2px 2px 0 2px;
		margin-bottom: 4px;
	}
	
	.bad_inner {
		color: #3875D7;
	}
	</style>
</head>
<body>
<div id="page">

	<?php

	remove_database_tables($local_db_name, $local_db, "local");
	foreach($regional_db_names as $key => $regional_db_name) {
		remove_database_tables($regional_db_name, $regional_db, "regional");
	}
	remove_database_tables($global_db_name, $global_db, "global");

	if($import) {
		import_database_tables($global_db_name, $global_db, $_SERVER["GLOBAL_PATH"].'/config/db/'.$global_db_name.'/');
		foreach($regional_db_names as $key => $regional_db_name) {
			import_database_tables($regional_db_name, $regional_db, $_SERVER["GLOBAL_PATH"].'/config/db/'.$regional_db_name.'/');
		}
		import_database_tables($local_db_name, $local_db, $_SERVER["GLOBAL_PATH"].'/config/db/'.$local_db_name.'/');
	}

	?>

</div>
</body>
</html>