<?php
global $action;
global $IC;
global $itemtype;
global $model;

//print_r($all_items);
?>
<div class="scene defaultList tests">
	<h1>Testing specific regexp</h1>


<?

//$useragent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.1.0.0 Safari/537.36";

// Chrome specific (limit scope for extensive search)
// if(preg_match("/Chrome/", $useragent) && !preg_match("/phone|mobile|chromeframe|android/i", $useragent)) {
// 	print "chrome";
// 
// 	// Chrome for iPad >= version 19
// 	if(preg_match("/AppleWebKit\/53[3-7]{1}[^$]+Gecko[^$]+Chrome\/([0-9]{1,2}).0[^$]+Safari\/53[3-7]{1}/", $useragent, $matches)) {
// 
// 		print_r($matches);
// 
// 	}
// }

// 
// $useragent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_5) AppleWebKit/537.73.11 (KHTML, like Gecko) Version/6.1.1 Safari/537.73.11";
// 
// 
// // Desktop Safari specific (limit scope for extensive search)
// if(preg_match("/AppleWebKit\/[0-9]{3}/", $useragent) && !preg_match("/htc|mobile|iphone|ipod|ipad|android|symbian|blackberry|trident/i", $useragent)) {
// 
// 	print "safari?";
// 
// 	// Safari 5-7
// 	if(preg_match("/AppleWebKit\/53[3-7]{1}[^$]+Gecko[^$]+Version\/([5-7]{1})[^$]+Safari\/53[3-7]{1}/", $useragent, $matches)) {
// 		print_r($matches);
// 	}
// 
// 	// Separate 4 from 4.1
// 	// Safari 4.1
// 	if(preg_match("/AppleWebKit\/533[^$]+Gecko[^$]+Version\/4.1[^$]+Safari\/533/", $useragent, $matches)) {
// 		print_r($matches);
// 	}
// 	// Safari 3-4
// 	if(preg_match("/AppleWebKit\/[4-5]{1}[0-9]{2}[^$]+Gecko[^$]+Version\/([3-4]{1})[^$]+Safari\/[4-5]{1}[0-9]{2}/", $useragent, $matches)) {
// 		print_r($matches);
// 	}
// 
// }


//$useragent = "Mozilla/5.0 (iPhone; CPU iPhone OS 7_0_4 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) Mobile/11B554a [FBAN/MessengerForiOS;FBAV/3.1.2;FBBV/1027309;FBDV/iPhone6,2;FBMD/iPhone;FBSN/iPhone OS;FBSV/7.0.4;FBSS/2; FBCR/3DK;FBID/phone;FBLC/da_DK;FBOP/1]";
// $useragent = "Mozilla/5.0 (iPad; CPU OS 7_0_4 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) Mobile/11B554a [FBAN/FBIOS;FBAV/6.8;FBBV/745892;FBDV/iPad3,4;FBMD/iPad;FBSN/iPhone OS;FBSV/7.0.4;FBSS/2; FBCR/;FBID/tablet;FBLC/da_DK;FBOP/1]";
// $useragent = "Mozilla/5.0 (iPhone; CPU iPhone OS 7_0_4 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) Mobile/11B554a [FBAN/FBIOS;FBAV/6.8;FBBV/745892;FBDV/iPhone6,2;FBMD/iPhone;FBSN/iPhone OS;FBSV/7.0.4;FBSS/2; FBCR/TELIA;FBID/phone;FBLC/da_DK;FBOP/5]";
// $useragent = "Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Mobile/10A403 [FBAN/FBIOS;FBAV/5.0;FBBV/47423;FBDV/iPad2,2;FBMD/iPad;FBSN/iPhone OS;FBSV/6.0;FBSS/1; FBCR/3DK;FBID/tablet;FBLC/da_DK]";
// $useragent = "Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Mobile/10A403 [FBAN/FBIOS;FBAV/5.0;FBBV/47423;FBDV/iPhone3,1;FBMD/iPhone;FBSN/iPhone OS;FBSV/6.0;FBSS/2; FBCR/3SE;FBID/phone;FBLC/en_US]";
// $useragent = "Mozilla/5.0 (iPhone; U; CPU iPhone OS 5_1 like Mac OS X; da_DK) AppleWebKit (KHTML, like Gecko) Mobile [FBAN/FBForIPhone;FBAV/4.1;FBBV/4100.0;FBDV/iPhone2,1;FBMD/iPhone;FBSN/iPhone OS;FBSV/5.1;FBSS/1; FBCR/Sonofon;FBID/phone;FBLC/da_DK;FBSF/1.0]";
//
// // Facebook iOS specific (limit scope for extensive search)
// if(preg_match("/^Mozilla\/5.0[^$]+FBAN\/(FBIOS|FBForIPhone)/", $useragent) && !preg_match("/android/i", $useragent)) {
//
// 	print "MATCHING FACEBOOK\n";
// 	// Facebook iPad app
// 	if(preg_match("/iPad[^$]+AppleWebKit\/53[4-7]{1}[^$]+Gecko[^$]+FBSV\/([0-9]{1})./", $useragent, $matches)) {
// 		print_r($matches);
// 	}
//
// 	// Facebook iPod app
// 	if(preg_match("/iPod[^$]+AppleWebKit\/53[4-7]{1}[^$]+Gecko[^$]+FBSV\/([0-9]{1})./", $useragent, $matches)) {
// 		print_r($matches);
// 	}
//
// 	// Facebook iPhone app
// 	if(preg_match("/iPhone[^$]+AppleWebKit\/53[4-7]{1}[^$]+Gecko[^$]+FBSV\/([0-9]{1})./", $useragent, $matches)) {
// 		print_r($matches);
// 	}
//
// }

// $useragent = "Mozilla/5.0 (iPhone; CPU iPhone OS 8_1_3 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) CriOS/40.0.2214.69 Mobile/12B466 Safari/600.1.4";
// $useragent = "Mozilla/5.0 (iPhone; CPU iPhone OS 8_1_3 like Mac OS X) AppleWebKit/537.1.4 (KHTML, like Gecko) CriOS/40.0.2214.69 Mobile/12B466 Safari/600.1.4";
//
// // CriOS - webkit 600
// if(preg_match("/iPhone[^$]+AppleWebKit\/(53[4-7]{1}|600)[^$]+Gecko[^$]+CriOS\/([0-9]{2}).0/", $useragent, $matches)) {
// 	print_r($matches);
// }


// $useragent = "Mozilla/5.0 (Macintosh; Intel Mac OS X) AppleWebKit/0600.1.25 (KHTML, like Gecko) Version/8.0 Safari/0600.1.25";
// $useragent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/600.2.2 (KHTML, like Gecko) Version/8.0 Safari/600.1.25";
// //$useragent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10) AppleWebKit/537.16 (KHTML, like Gecko) Version/8.0 Safari/537.16";
//
//
// if(preg_match("/AppleWebKit\/(53[4-7]{1}|600)[^$]+Gecko[^$]+Version\/([5-8]{1})/", $useragent, $matches)) {
// 	print_r($matches);
// }


// $useragent = "Mozilla/5.0 (Linux; U; en-gb; KFTT Build/IML74K) AppleWebKit/535.19 (KHTML, like Gecko) Silk/3.16 Safari/535.19 Silk-Accelerated=true";
//
// if(preg_match("/Mozilla\/5.0[^$]+Linux[^$]+Android/", $useragent) && !preg_match("/crios|ipod|ipad|symbian|blackberry|firefox/i", $useragent)) {
//
// 	if(preg_match("/Kindle Fire|KF[^$]+Silk/", $useragent, $matches)) {
// 		print_r($matches);
// 	}
// }

//$useragent = "Mozilla/5.0 (Linux; Android 4.2.2; A1-810 Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.141 Safari/537.36";
//$useragent = "Mozilla/5.0 (Linux; Android 4.2.2; A1-810 Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.141 Safari/537.36";
//$reg_exp_pos = "A1-(8)[0-9]{2} b";
//$reg_exp_neg = "^(?!.*(chrome\/[3-9][0-9]))"; //"msie";


// $useragent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; SV1; Maxthon; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)";
// $reg_exp_pos = "msie[ ]?[0-8]\b";
// $reg_exp_neg = "phone|mobile|windows[ ]?ce|midp|wap|brew|\d\d\dx\d\d\d|opera|nokia|symbian|motorola|NetFront|Palm";

//hp-tablet[^$]+bkit\/                              (53(7\.[3-9]|[8-9]))[^$]+TouchPad
//Linux; U; (?!Android)[^$]+(; KF[A-Z]+ )[^$]+bkit\/(53([0-6]|7\.[0-2]))[^$]+(silk)
$useragent = "Mozilla/5.0 (Linux; Android 5.1; Vodafone Tab grand 6 Build/LMY47I) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.89 Safari/537.36";
$reg_exp_pos = "Linux[^$]+Android[^$]+Vodafone Tab grand[^$]+bkit\/(53(7\.[3-9]|[8-9]))";
$reg_exp_neg = false; 

//"msie";
//$reg_exp_neg = "^(?!.*(chrome\/[3-9][0-9]))"; //"msie";

// preg_match("/".$reg_exp_neg."/i", $useragent, $matches);
// print_r($matches);

if(preg_match("/".$reg_exp_pos."/i", $useragent) && (!$reg_exp_neg || !preg_match("/".$reg_exp_neg."/i", $useragent))) {

	print "<p>passed</p>";
}
else {

	print "<p>failed</p>";
}
 ?>

</div>

