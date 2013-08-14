<?php
error_reporting(E_ALL);

$access_item = array();
$access_default = "page,list";

$access_item = false;

if(isset($read_access) && $read_access) {
	return;
}

$file_include[] = "seg_basic_include.css";
$file_output[] = "../seg_basic.css";

$file_include[] = "seg_mobile_light_include.css";
$file_output[] = "../seg_mobile_light.css";

$file_include[] = "seg_mobile_include.css";
$file_output[] = "../seg_mobile.css";

$file_include[] = "seg_mobile_touch_include.css";
$file_output[] = "../seg_mobile_touch.css";

$file_include[] = "seg_tablet_include.css";
$file_output[] = "../seg_tablet.css";

$file_include[] = "../lib/seg_desktop_include.css";
$file_output[] = "../seg_desktop.css";

$file_include[] = "../lib/seg_desktop_ie_include.css";
$file_output[] = "../seg_desktop_ie.css";

$file_include[] = "../lib/seg_desktop_light_include.css";
$file_output[] = "../seg_desktop_light.css";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="da" lang="da">
<head>
	<!-- All material protected by copyrightlaws (as if you didnt know) //-->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>-- parse css --</title>
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
	.notminified {
		color: green; font-weight: normal;
	}
	.file {
		color: black; font-weight: bold;
	}
	.file div {display: none;}
	.open div {display: block;}
	</style>
</head>
<body>

<?php

$_ = '';

foreach($file_include as $index => $source) {

//	print $source .":".realpath($source);
	$fp = fopen($source, "r");
	$includes = file($source);

	// read include file
	if(!$includes) {
		$_ .= $source . " -> " . $file_output[$index] . "<br />";
		$_ .= "No include file<br /><br /><hr />";
	}
	else {

		$files = array();

		foreach($includes as $include) {
			//@import url(framework/header.css);
			
			if(preg_match("/url\(([a-zA-Z\.\/_-]+)\)/i", $include, $matches)) {
				$filepath = "http://".$_SERVER["HTTP_HOST"].$matches[1];
				$files[] = $filepath;
			}
		}
		// write compiled js
		$fp = fopen($file_output[$index], "w+");

		$include_size = 0;
		$a = '';

		foreach($files as $file) {

			fwrite($fp, "/*".basename($file)."*/\n");

			$a .= '<div class="file" onclick="this.className = this.className.match(/open/) ? \'file\' : \'file open\'">' . $file;

			// calculate pre filesize
			$file_size = strlen(join('', file($file)));
			$include_size += $file_size ? $file_size : 0;

			$lines = file($file);
			$switch = false;
			foreach($lines as $linenumber => $line) {

				$minified = "";
				// not empty line
				if(trim($line)) {

					$tline = trim($line);
					if(!$switch && strpos($line, "/*") !== false) {
						$com_line = $index;
						$com_s_pos = strpos($line, "/*");
						$switch = true;
					}

					if(!$switch && trim($line)) {
						$minified = $line;
					}
					else if($switch && strpos($line, "*/") !== false) {

						$com_e_pos = strpos($line, "*/");
						$switch = false;
						$comment = substr($line, $com_s_pos, ($com_e_pos-$com_s_pos+2));
						$minified = str_replace(substr($line, $com_s_pos, ($com_e_pos-$com_s_pos+2)), "", $line);
					}

					$com_s_pos = 0;

					/*
					if(strpos($minified, "{\n") !== false) {
						$minified = trim($minified);
					}
					if(strpos($minified, "\t") !== false) {
						$minified = trim($minified);
					}
					if(trim($minified) == "}") {
						$minified = trim($minified)."\n";
					}
					*/

					if(trim($minified)) {
						fwrite($fp, $minified);
					}
				}

				if($line == $minified) {
					$a .= '<div class="notminified"><code>'.$linenumber.':'.$minified.'</code></div>';
				}
				else {
					$a .=  '<div class="minified"><span class="bad">'.$linenumber.':'.$line.'</span><span class="good">' . htmlentities($minified) . '</span></div>';
				}

			}
			fwrite($fp, "\n");

			$a .=  '</div>';
		}

		$_ .= $source . " ($include_size bytes) -> " . $file_output[$index] . " (".filesize($file_output[$index])." bytes)<br />";
		$_ .= count($includes) . " include files<br /><br />";

		$_ .= $a."<br /><br /><hr />";
	}

}

print $_;

?>
</body>
</html>
