<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="da" lang="da">
<head>
	<!-- All material protected by copyrightlaws (as if you didnt know) //-->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>-- ANYTHINK --</title>
	<style type="text/css">
	* {
		font-family: monaco;
		font-size: 10px;
	}
	.good {
		color: green;
	}
	.bad {
		color: red;
	}
	</style>
</head>
<body>
	<?php

// merge files into new include.js (possibly minified?)


$files[] = "framework/util.js";
$files[] = "framework/proto_string.js";
$files[] = "framework/proto_array.js";
$files[] = "framework/util_basic.js";
$files[] = "framework/util_interface.js";
$files[] = "framework/util_event.js";
$files[] = "framework/util_dom.js";
$files[] = "framework/util_position.js";
$files[] = "framework/util_debug.js";
$files[] = "framework/util-obj_default_input_value.js";
$files[] = "framework/util-obj_list.js";
$files[] = "framework/util-obj_table.js";
$files[] = "framework/util-obj_table_search.js";
$files[] = "framework/util-obj_table_incremental.js";
$files[] = "framework/util-obj_table_arrange.js";
$files[] = "framework/util-obj_input.js";
$files[] = "framework/util-obj_button.js";
$files[] = "framework/util-obj_form.js";
$files[] = "framework/util-obj_autocomplete.js";
$files[] = "framework/obj_util_ajax.js";
//$files[] = "framework/obj_util_obstructions.js";
$files[] = "framework/util_init_onload.js";

$_ = '';

$fp = fopen("include_test.js", "w+");

foreach($files as $file) {

	$lines = file("http://".$_SERVER["HTTP_HOST"]."/js/".$file);

	foreach($lines as $line) {

		$minified = "";
		if(strpos(trim($line), "//") !== 0 && trim($line) != "") {
			$minified = $line;

//			$minified = trim($minified);
			/*
			$minified = str_replace(" element", " e", $minified);
			$minified = str_replace("element ", "e ", $minified);
			$minified = str_replace("\(element", "(e", $minified);
			$minified = str_replace("element\)", "e)", $minified);
			$minified = str_replace("element\.", "e.", $minified);

			$minified = str_replace(" string", " s", $minified);
			$minified = str_replace("string ", "s ", $minified);
			$minified = str_replace("\(string", "(s", $minified);
			$minified = str_replace("string\)", "s)", $minified);
			$minified = str_replace("string\.", "s.", $minified);
			*/
//			$minified = str_replace("document.getElementById", "U", $minified);

			fwrite($fp, $minified);
		}
		//$minified = $line;
		//fwrite($fp, $minified);

		if($line == $minified) {
			$_ .= '<div style="color:green"><code>' . htmlentities($minified) . '</code></div>';
			
		}
		else {
			$_ .=  '<div><span style="color:red;">' . htmlentities($line) . '</span><span class="good">' . htmlentities($minified) . '</span></div>';
		}
	}

}
print filesize("include_test.js");
print $_;

?>
</body>
</html>
