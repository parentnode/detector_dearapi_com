<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");
include_once("classes/identify.class.php");

$action = $page->actions();

$ua = stringOr(getVar("ua"), $_SERVER["HTTP_USER_AGENT"]);


// include _include in path - for development
$dev = getVar("dev");

// general path - general path, if css and js follow same path pattern
$path = getVar("path");

// specific paths - if includes are not following recommended layout
$css_path = getVar("css_path");
$js_path = getVar("js_path");

// specific params - to be added to include names (can work as cache busters)
$css_param = getVar("css_param");
$js_param = getVar("js_param");


// identify device
$Identify = new Identify();
$device = $Identify->identifyDevice($ua);

// predefine file to be includes
// what file to include?
$file = ($dev ? "lib/" : "")."seg_".$device["segment"].($dev ? "_include" : "");

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
