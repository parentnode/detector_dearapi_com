testing regexp
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


$useragent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_5) AppleWebKit/537.73.11 (KHTML, like Gecko) Version/6.1.1 Safari/537.73.11";

// Desktop Safari specific (limit scope for extensive search)
if(preg_match("/AppleWebKit\/[0-9]{3}/", $useragent) && !preg_match("/htc|mobile|iphone|ipod|ipad|android|symbian|blackberry|trident/i", $useragent)) {

	print "safari?";

	// Safari 5-7
	if(preg_match("/AppleWebKit\/53[3-7]{1}[^$]+Gecko[^$]+Version\/([5-7]{1})[^$]+Safari\/53[3-7]{1}/", $useragent, $matches)) {
		print_r($matches);
	}

	// Separate 4 from 4.1
	// Safari 4.1
	if(preg_match("/AppleWebKit\/533[^$]+Gecko[^$]+Version\/4.1[^$]+Safari\/533/", $useragent, $matches)) {
		print_r($matches);
	}
	// Safari 3-4
	if(preg_match("/AppleWebKit\/[4-5]{1}[0-9]{2}[^$]+Gecko[^$]+Version\/([3-4]{1})[^$]+Safari\/[4-5]{1}[0-9]{2}/", $useragent, $matches)) {
		print_r($matches);
	}

}



 ?>



