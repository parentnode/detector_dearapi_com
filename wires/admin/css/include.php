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

// merge files into new include.css (possibly minified?)
$files[] = "framework/basics.css";
$files[] = "framework/typography.css";
$files[] = "framework/header.css";
$files[] = "framework/messageboard.css";
$files[] = "framework/columns.css";
$files[] = "framework/content.css";
$files[] = "framework/navigation.css";
$files[] = "framework/inputs.css";
$files[] = "framework/lists.css";
$files[] = "framework/tables.css";
$files[] = "framework/imagelists.css";
$files[] = "framework/modifiers.css";
$files[] = "framework/preview.css";
$files[] = "framework/design.css";
$files[] = "framework/print.css";

$_ = '';

$fp = fopen("include_test.css", "w+");

foreach($files as $file) {

	$lines = file("http://".$_SERVER["HTTP_HOST"]."/css/".$file);
	$switch = "off";

	foreach($lines as $line) {

		$minified = "";
		if(trim($line) != "") {
			print strpos(trim($line), "/*")."<br>";
			$switch = strpos(trim($line), "/*") === 0 ? "on" : (strpos(trim($line), "*/") != false ? "off" : $switch); 

			if($switch == "off") {
				$minified = $line;
			}

			$switch = strpos(trim($line), "*/") !== false ? "off" : $switch; 
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
print filesize("include_test.css");
print $_;

?>
</body>
</html>
