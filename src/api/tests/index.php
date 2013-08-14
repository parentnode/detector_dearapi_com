<?php
header("Content-type: text/html; charset=UTF-8");

$access_item = array();
$access_default = "page";

$access_item = false;

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["PAGE_PATH"]."/page.class.php");
include_once("class/devices/device.core.class.php");

include_once("class/system/performance.class.php");
$perf = new Performance();


$ua = array();
$ua[] = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X; it-it) AppleWebKit/523.12 (KHTML, like Gecko) Version/3.0.4 Safari/523.12";
$ua[] = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; GTB7.2; chromeframe/16.0.912.75; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET4.0C)";
$ua[] = "Mozilla/5.0 (Linux; U; Android 2.3.4; da-dk; LT18i Build/4.0.2.A.0.42) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1";
$ua[] = "Mozilla/5.0 (Linux; U; Android 2.2.2; en-dk; Desire_A8181 Build/FRG83G) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1";
$ua[] = "Mozilla/5.0 (compatible; Konqueror/3.5; FreeBSD) KHTML/3.5.10 (like Gecko)";
$ua[] = "Mozilla/5.0 (compatible; MSIE 9.0; Windows Phone OS 7.5; Trident/5.0; IEMobile/9.0; HTC; Radar C110e)";
$ua[] = "Opera/9.80 (Windows NT 5.1; U; Edition Campaign 21; de) Presto/2.9.168 Version/11.52";
$ua[] = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; fr-fr) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1";
$ua[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:10.0) Gecko/20100101 Firefox/10.0";
$ua[] = "Mozilla/5.0 (Android; Linux armv7l; rv:9.0) Gecko/20111216 Firefox/9.0 Fennec/9.0";
$ua[] = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; HTC_EVO3D_X515m; da-dk) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16";
$ua[] = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; chromeframe/17.0.963.79)";
$ua[] = "Mozilla/5.0 (iPad; CPU OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9B176";


$ua[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/534.55.3 (KHTML, like Gecko) Version/5.1.5 Safari/534.55.3;";
$ua[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/534.55.3 (KHTML, like Gecko) Version/5.1.5 Safari/534.55.3;";
$ua[] = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; chromeframe/19.0.1084.52; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET4.0C);";
$ua[] = "Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.8.0.11) Gecko/20070312 Firefox/1.5.0.11; CollapsarTEXT;";
$ua[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_4) AppleWebKit/534.56.5 (KHTML, like Gecko) Version/5.1.6 Safari/534.56.5;";
$ua[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/534.55.3 (KHTML, like Gecko) Version/5.1.5 Safari/534.55.3;";
$ua[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_4) AppleWebKit/534.56.5 (KHTML, like Gecko) Version/5.1.6 Safari/534.56.5;";
$ua[] = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.52 Safari/536.5;";
$ua[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/534.55.3 (KHTML, like Gecko) Version/5.1.5 Safari/534.55.3;";
$ua[] = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; chromeframe/19.0.1084.52; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729);";
$ua[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/534.55.3 (KHTML, like Gecko) Version/5.1.5 Safari/534.55.3;";
$ua[] = "Mozilla/5.0 (compatible; AcoonBot/4.10.8; +http://www.acoon.de/robot.asp);";
$ua[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/534.55.3 (KHTML, like Gecko) Version/5.1.5 Safari/534.55.3;";
$ua[] = "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; chromeframe/19.0.1084.46);";
$ua[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/534.55.3 (KHTML, like Gecko) Version/5.1.5 Safari/534.55.3;";
$ua[] = "Mozilla/5.0 (Linux; U; Android 2.3.4; en-gb; HTC_Sensation Build/GRJ22) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1;";
$ua[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_4) AppleWebKit/534.57.2 (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2;";
$ua[] = "Mozilla/5.0 (Linux; U; Android 4.0.3; da-dk; HTC_One_X Build/IML74K) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30;";
$ua[] = "Mozilla/5.0 (X11; Linux i686 on x86_64; rv:7.0.1) Gecko/ /7.0.1;";
$ua[] = "Opera/9.80 (Windows NT 5.1; U; Edition Indonesian Local; en) Presto/2.10.229 Version/11.62;";
$ua[] = "Mozilla/5.0 (compatible; MJ12bot/v1.4.3; http://www.majestic12.co.uk/bot.php?+);";
$ua[] = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0;";
$ua[] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; chromeframe/18.0.1025.168; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; SiteKiosk 6.2 Build 51);";
$ua[] = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; GTB7.3; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; InfoPath.2);";
$ua[] = "Mozilla/4.0 (compatible; MSIE8.0; Windows NT 6.0) .NET CLR 2.0.50727);";
$ua[] = "Mozilla/5.0 (compatible; MSIE 9.0; Windows Phone OS 7.5; Trident/5.0; IEMobile/9.0; SAMSUNG; GT-I8350);";
$ua[] = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; GTB7.3; chromeframe/18.0.1025.152; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; eSobiSubscriber 2.0.4.16; InfoPath.2; .NET CLR 3.5.30729; .NET CLR 3.0.30618; .NET4.0C);";
$ua[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/534.57.2 (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2;";


$ua[] = "Mozilla/5.0 (iPhone; U; CPU iPhone OS 5_1_1 like Mac OS X; da-dk) AppleWebKit/534.46.0 (KHTML, like Gecko) CriOS/19.0.1084.60 Mobile/9B206 Safari/7534.48.3";

$ua[] = "SAMSUNG-GT-M760XXXL/1.0 SHP/VPP/R5 NetFront/3.5 NexPlayer/2.9.1 SMM-MMS/1.2.0 profile/MIDP-2.1 configuration/CLDC-1.1";
$ua[] = "SAMSUNG-SGH-A76XXX/A766UXIA4 SHP/VPP/R5 NetFront/3.4 SMM-MMS/1.2.0 profile/MIDP-2.0 configuration/CLDC-1.1";
$ua[] = "SonyEricssonZ310XXX/R1KC Browser/NetFront/3.3 Profile/MIDP-2.0 Configuration/CLDC-1.1";

$ua[] = "BlackBerry1234/5.0.0.732 Profile/MIDP-2.1 Configuration/CLDC-1.1 VendorID/102 ips-agent";

//$ua[] = "";


print "Direct<br>";

$deviceClass = new DeviceCore();

$perf->mark("ID - direct", true);

foreach($ua as $useragent) {

	$identification = $deviceClass->identifyDevice($useragent);
	if($identification && isset($identification["device_id"])) {
		$device = $deviceClass->getDeviceBase($identification["device_id"]);
	}
	else {
		$device = array("brand" => "unknown", "model" => "unknown", "display_width" => 0, "display_height" => 0, "segment" => "desktop_light");
	}

	// $device_id = $deviceClass->identifyDevice($useragent);
	// $device = $deviceClass->getDeviceBase($device_id);

	$perf->mark("IDed");

	print $useragent."<br>".$device["brand"].", ".$device["model"].", ".$device["segment"]."<br><br>";
}
//print $perf->result();



print "HTTP/xml<br>";

$perf->mark("ID - HTTP/xml", true);

foreach($ua as $useragent) {

	$device_id = file_get_contents("http://devices.dearapi.com/xml?ua=".urlencode($useragent)."&site=".urlencode($_SERVER["HTTP_HOST"])."&file=".urlencode($_SERVER["SCRIPT_NAME"]));
//	$device_id = file_get_contents("http://devices.local/xml?ua=".urlencode($_SERVER["HTTP_USER_AGENT"])."&site=".urlencode($_SERVER["HTTP_HOST"])."&file=".urlencode($_SERVER["SCRIPT_NAME"]));
	$device = simplexml_load_string($device_id);
	if($device) {
		$perf->mark("IDed - $useragent");
//		$device["segment"] = (string) $device->segment;
		print $useragent."<br>".$device->brand.", ".$device->model.", ".$device->segment."<br><br>";

	}
	else {
		$perf->mark("NOT IDed");
		print $useragent."<br><br>";
	}

}
print $perf->result();

//print $deviceClass->perf->result();
?>
