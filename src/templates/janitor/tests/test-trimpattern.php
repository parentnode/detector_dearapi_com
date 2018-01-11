<?php
global $action;
global $IC;
global $itemtype;
global $model;


//include_once("classes/identify.class.php");
$Identify = new Identify();


$useragents[] = 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_2_1 like Mac OS X) AppleWebKit/602.4.6 (KHTML, like Gecko) Mobile/14D27 [FBAN/FBIOS;FBAV/83.0.0.38.70;FBBV/51754296;FBDV/iPhone8,4;FBMD/iPhone;FBSN/iOS;FBSV/10.2.1;FBSS/2;FBCR/TELIA;FBID/phone;FBLC/da_DK;FBOP/5;FBRV/52433023]';
$useragents[] = 'Mozilla/5.0 (Linux; Android 6.0.1; SM-G920F Build/MMB29K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/56.0.2924.87 Mobile Safari/537.36 [FB_IAB/MESSENGER;FBAV/109.0.0.23.70;]';
$useragents[] = 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_2_1 like Mac OS X) AppleWebKit/602.4.6 (KHTML, like Gecko) Mobile/14D27 Instagram 10.12.0 (iPhone7,2; iOS 10_2_1; en_GB; en-GB; scale=2.00; gamut=normal; 750x1334)';
$useragents[] = 'Mozilla/5.0 (compatible; MSIE 11.0; Windows NT 6.2; Trident/7.0; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E) ';
$useragents[] = 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.116 Safari/537.36 Yandex.Translate';
$useragents[] = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-us) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5,gzip(gfe) (via translate.google.com)';
$useragents[] = 'Mozilla/5.0 (Windows NT 10.0.14393.1066; osmeta 9.3.55667173) AppleWebKit/602.1.1 (KHTML, like Gecko) Version/9.0 Safari/602.1.1 osmeta/9.3.55667173 Build/55667173 Instagram 10.16.0 (Windows Device; osmeta/Windows 9_3_55667173; scale=1.25; gamut=normal; 1920x990)';
$useragents[] = 'Mozilla/5.0 (Windows NT 10.0.15063.540; osmeta 9.3.1081) AppleWebKit/602.1.1 (KHTML, like Gecko) Version/9.0 Safari/602.1.1 osmeta/9.3.1081 Build/1081 Instagram 10.20.3 (Windows Device; osmeta/Windows 9_3_1081; scale=1.00; gamut=normal; 1920x1008)';
$useragents[] = 'Mozilla/5.0 (Windows NT 10.0.15063.608; osmeta 10.3.2132) AppleWebKit/602.1.1 (KHTML, like Gecko) Version/9.0 Safari/602.1.1 osmeta/10.3.2132 Build/2132 Instagram 10.32.0 (Windows Device; osmeta/Windows 10_3_2132; scale=1.00; gamut=normal; 1366x696)';
$useragents[] = 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_0_2 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Mobile/14A456 Instagram 19.0.0.27.91 (iPad4,1; iOS 10_0_2; scale=2.00; gamut=normal; 640x960)';
$useragents[] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/600.8.9 (KHTML, like Gecko) Version/8.0.8 Safari/600.8.9,gzip(gfe)';
$useragents[] = 'Mozilla/5.0 (en-us) AppleWebKit/525.13 (KHTML, like Gecko; Google Wireless Transcoder) Version/3.1 Safari/525.13';
$useragents[] = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; da-dk) AppleWebKit/523.15.1 (KHTML, like Gecko) Version/3.0.4 Safari/523.15';
$useragents[] = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; da-DK) AppleWebKit/525.28 (KHTML, like Gecko) Version/3.2.2 Safari/525.28.1';
$useragents[] = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_7;) AppleWebKit/528.16 (KHTML, like Gecko) Version/4.0 Safari/528.21';
$useragents[] = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12.10240 QQBrowser/8.2.4258.400';
$useragents[] = 'Mozilla/5.0 (compatible; MSIE 10.6; Windows NT 6.1; Trident/5.0; InfoPath.2; SLCC1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 2.0.50727) 3gpp-gba UNTRUSTED/1.0';
$useragents[] = 'Mozilla/5.0 (X11; U; Linux i686 (x86_64); en-US; rv:1.8.1.6) Gecko/20070817 IceWeasel/2.0.0.6-g3';

$useragents[] = 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_0_1 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Mobile/14A403 Instagram 10.0.1 (iPhone9,3; iOS 10_0_1; ko-Kore_KR; scale=2.00; 750x1334)';

$useragents[] = 'Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X; en-us) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11A465 Safari/9537.53';
$useragents[] = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 10.0; Win64; x64; Trident/7.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729; InfoPath.3; Tablet PC 2.0; ms-office)';
$useragents[] = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.2; WOW64; Trident/6.0; Touch; .NET4.0E; .NET4.0C; .NET CLR 3.5.30729; .NET CLR 2.0.50727; .NET CLR 3.0.30729; Tablet PC 2.0; CMNTDFJS; InfoPath.3; MS-RTC LM 8)';
$useragents[] = 'Mozilla/5.0 (Linux; Android 4.4.2; LaVieTab PC-TE510S1 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/30.0.0.0 Safari/537.36';
$useragents[] = 'Mozilla/5.0 (Linux; Android 6.0.1; SM-T580 Build/MMB29K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/54.0.2840.85 Safari/537.36 Instagram 9.8.0 Android (23/6.0.1; 240dpi; 1200x1920; samsung; SM-T580; gtaxlwifi; samsungexynos7870; en_US)';
$useragents[] = 'Mozilla/5.0 (Linux; Android 4.1.2; LG-LG730 Build/JZO54K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.99 Mobile Safari/537.36';
$useragents[] = 'Mozilla/5.0 (SAMSUNG; SAMSUNG-GT-S5380D/1.0; U; Bada/2.0; en-us) AppleWebKit/534.20 (KHTML, like Gecko) Dolfin/3.0 Mobile HVGA SMM-MMS/1.2.0 OPN-B';
$useragents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; GTB7.3; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; MASN),gzip(gfe) (via translate.google.com)';
?>
<div class="scene defaultList purgeUseragentRegex tests">
	<h1>Testing trim patterns</h1>

	<div class="all_items i:defaultList filters">

<?		if($useragents): ?>
		<ul class="items">
<?			foreach($useragents as $useragent):

				$org_useragent = $useragent;
				$trimmed_useragent = $useragent;
				$diff_useragent = $useragent;

				// loop trim patterns
				foreach($Identify->trimming_patterns as $pattern):
					$diff_useragent = preg_replace("/(".$pattern.")/", "<span class=\"trimmed\">$1</span>", $diff_useragent);
					$trimmed_useragent = preg_replace("/".$pattern."/", "", $trimmed_useragent);


				endforeach; ?>
			<li class="item ua_id:<?= $item["id"] ?>">
				<h3><strong>BEFORE:</strong><br /><?= $org_useragent ?></h3>
				<h3><strong>AFTER:</strong><br /><?= $trimmed_useragent ?></h3>
				<h3><strong>DIFF:</strong><br /><?= $diff_useragent ?></h3>
			 </li>
<?			endforeach; ?>
		</ul>

<?		endif;?>

	</div>

</div>
