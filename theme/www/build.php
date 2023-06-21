<?php
$access_item = false;
if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/init.php");


$action = $page->actions();
$IC = new Items();
$itemtype = "device";
$model = $IC->typeObject($itemtype);


$page->bodyClass("build");
$page->pageTitle("Build detection script");


$language = getVar("language");
$grouping = prepareForHTML(getVar("grouping"));

if($language == "php") {

	print $model->createPHPDetection($grouping);
	
}
else if($language == "javascript") {

	print $model->createJavaScriptDetection($grouping);
	
}
else if($language == "java") {

	print $model->createJavaDetection($grouping);
	
}
else {

	print "invalid language";
	
}

?>
