<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");
include_once("classes/helpers/identify.class.php");

$action = $page->actions();


$ua = stringOr(getVar("ua"), isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "");

// include _include in path - for development
$dev = getVar("dev");


// Check for parameters via referer
if(isset($_SERVER["HTTP_REFERER"]) && $_SERVER["HTTP_REFERER"] && preg_match("/\?(.+)/", $_SERVER["HTTP_REFERER"], $param_string)) {
	$params = explode("&", $param_string[1]);
	foreach($params as $param) {
		list($key, $value) = explode("=", $param);

		if(!$dev && $key === "dev") {
			$dev = $value;
		}

		if($key === "segment") {
			$segment = $value;
		}

	}

	// Use referer as site, if site is not passed
	if(!isset($_GET["site"])) {
		$_GET["site"] = preg_replace("/(\?.+)/", "", $_SERVER["HTTP_REFERER"]);
	}

}

// Hardcoded params always wins


// general path - general path, if css and js follow same path pattern
$path = getVar("path");

// specific paths - if includes are not following recommended layout
$css_path = getVar("css_path");
$js_path = getVar("js_path");

// specific params - to be added to include names (can work as cache busters)
$css_param = getVar("css_param");
$js_param = getVar("js_param");

if(!isset($segment)) {

	// identify device
	$Identify = new Identify();
	$device = $Identify->identifyDevice($ua);
	$segment = $device["segment"];
	
}


// predefine file to be includes
// what file to include?
$file = ($dev ? "lib/" : "")."seg_".$segment.($dev ? "_include" : "");

header("Content-type: text/javascript; charset=UTF-8");
?>
<? if($css_path): ?>
document.write('<link type="text/css" rel="stylesheet" media="all" href="<?= $css_path ?>/<?= $file ?>.css<?= $css_param ? "?$css_param" : "" ?>" />');
<? else: ?>
document.write('<link type="text/css" rel="stylesheet" media="all" href="<?= $path ? $path : "" ?>/css/<?= $file ?>.css<?= $css_param ? "?$css_param" : "" ?>" />');
<? endif;


if($js_path): ?>
document.write('<script type="text/javascript" src="<?= $js_path ?>/<?= $file ?>.js<?= $js_param ? "?$js_param" : "" ?>"></script>');
<? else: ?>
document.write('<script type="text/javascript" src="<?= $path ? $path : "" ?>/js/<?= $file ?>.js<?= $js_param ? "?$js_param" : "" ?>"></script>');
<? endif; ?>
