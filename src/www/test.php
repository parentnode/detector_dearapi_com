testing regexp
<?

$useragent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.1.0.0 Safari/537.36";

// Chrome specific (limit scope for extensive search)
if(preg_match("/Chrome/", $useragent) && !preg_match("/phone|mobile|chromeframe|android/i", $useragent)) {
	print "chrome";

	// Chrome for iPad >= version 19
	if(preg_match("/AppleWebKit\/53[3-7]{1}[^$]+Gecko[^$]+Chrome\/([0-9]{1,2}).0[^$]+Safari\/53[3-7]{1}/", $useragent, $matches)) {

		print_r($matches);

	}
}


 ?>



