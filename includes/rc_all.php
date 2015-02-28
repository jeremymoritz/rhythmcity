<?php	//	Rhythm City all includes
date_default_timezone_set('America/Chicago');	//	Sets the timezone to be Central
ob_start();	//	starts output buffer (allows for setting php headers after declaring output, without errors)
ini_set('display_errors', 1);	//	show errors
$isLocal = $_SERVER['REMOTE_ADDR'] === '127.0.0.1' ? true : false;

//	variables we'll use on many pages (we still have the ability to initialize these differently per page)
$title = isset($title) ? $title : "Rhythm City the Musical";
$h1 = isset($h1) ? "<h1>$h1</h1>" : "<h1>$title</h1>";	//	If no H1 is set, then use the title
$h2 = isset($h2) ? "<h2>$h2</h2>" : "";	//	If no H2 is set, use nothing
$footer_h2 = isset($footer_h2) ? "<h2>$footer_h2</h2>" : "<h2>Footer Information</h2>";
$meta_desc = isset($meta_desc) ? $meta_desc : "Rhythm City the Musical, by Jeremy Moritz";
$meta_keywords = isset($meta_keywords) ? $meta_keywords : "Rhythm City, musical, high school musical, middle school musical, junior, Rhythm City Junior, middle school, rhythm, dance, music, Jeremy Moritz";

//	preload images in footer (if there are any set by the page)
$preload = "";
if(isset($preload_images)) {
	$preload = "<div class='hidden'><!--PRELOAD IMAGES FOR JAVASCRIPT-->\n";
	foreach($preload_images as $img) {
		$preload .= "<img src='images/$img' alt=''>\n";
	}
	$preload .= "</div>";
}

$analytics = "
	<script>
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-23088628-1']);
		_gaq.push(['_trackPageview']);

		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>";

$head = "
	<meta charset='UTF-8'>
	<meta name='description' content='$meta_desc'>
	<meta name='keywords' content='$meta_keywords'>
	<title>$title</title>
	<link rel='shortcut icon' href='favicon.ico'>
	<link rel='stylesheet' href='http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css'>
	<link rel='stylesheet' href='includes/rc.css'>
	<script src='//code.jquery.com/jquery-1.11.0.min.js'></script>
	<script src='//code.jquery.com/ui/1.10.4/jquery-ui.js'></script>
	<!--[if lt IE 9]>
		<link rel='stylesheet' href='includes/ie8.css'>
		<script src='http://html5shim.googlecode.com/svn/trunk/html5.js'></script>
	<![endif]-->
	$analytics\n";

$navbar = "
	<nav>
		<h2>Site Navigation</h2>
		<table>
			<tr>
				<td><a href='index.php'>HOME</a></td>
				<td><a href='plot.php'>MUSIC/PLOT</a></td>
				<td><a href='reviews.php'>REVIEWS</a></td>
				<td><a href='order.php'>ORDER DVD</a></td>
				<td><a href='productions.php'>PRODUCTIONS</a></td>
				<td><a href='licensing.php'>LICENSING</a></td>
				<td><a href='contact.php'>CONTACT US</a></td>
			</tr>
		</table>
	</nav>";

$header = "
	<header>
		$h1
		$h2
		$navbar
	</header>";

$footer = "
	<footer>
		$footer_h2
		$navbar
		<p><small>all content &copy;copyright " . date("Y") . " Immeasurable Productions</small></p>
		<script src='includes/rc.js'></script>
		$preload
	</footer>
	";

//	ARRAYS
require('config.php');

####	CONNECT TO THE DATABASE		######
try {
	$dbh = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['db'], $config['username'], $config['password'], array(PDO::ATTR_PERSISTENT => true));
} catch (PDOException $e) {
	die($e->getMessage() . "\n Please contact us to tell us about this error... info@rhythmcity.org");
}

#takes a pdo statement handle and returns an array of row objects
function sthFetchObjects($sth) {
	$out = array();
	while($o = $sth->fetchObject()) {
		$out[] = $o;
	}
	return $out;
}

//	Array of all 50 states + 12 Provinces and some other territories
$states_s2l = array(
	'AL' => 'Alabama',					'AK' => 'Alaska',							'AZ' => 'Arizona',					'AR' => 'Arkansas',
	'CA' => 'California',				'CO' => 'Colorado',							'CT' => 'Connecticut',				'DC' => 'District Of Columbia',
	'DE' => 'Delaware',					'FL' => 'Florida',							'GA' => 'Georgia',					'HI' => 'Hawaii',
	'ID' => 'Idaho',					'IL' => 'Illinois',							'IN' => 'Indiana',					'IA' => 'Iowa',
	'KS' => 'Kansas',					'KY' => 'Kentucky',							'LA' => 'Louisiana',				'ME' => 'Maine',
	'MD' => 'Maryland',					'MA' => 'Massachusetts',					'MI' => 'Michigan',					'MN' => 'Minnesota',
	'MS' => 'Mississippi',				'MO' => 'Missouri',							'MT' => 'Montana',					'NE' => 'Nebraska',
	'NV' => 'Nevada',					'NH' => 'New Hampshire',					'NJ' => 'New Jersey',				'NM' => 'New Mexico',
	'NY' => 'New York',					'NC' => 'North Carolina',					'ND' => 'North Dakota',				'OH' => 'Ohio',
	'OK' => 'Oklahoma',					'OR' => 'Oregon',							'PA' => 'Pennsylvania',				'RI' => 'Rhode Island',
	'SC' => 'South Carolina',			'SD' => 'South Dakota',						'TN' => 'Tennessee',				'TX' => 'Texas',
	'UT' => 'Utah',						'VT' => 'Vermont',							'VA' => 'Virginia',					'WA' => 'Washington',
	'WV' => 'West Virginia',			'WI' => 'Wisconsin',						'WY' => 'Wyoming',					'--' => '--',
	'AA' => 'Military (Americas)',		'AE' => 'Military (Europe)',				'AP' => 'Military (Pacific)',		'PR' => 'Puerto Rico',
	'==' => '==',						'AB' => 'Alberta',							'BC' => 'British Columbia', 		'MB' => 'Manitoba',
	'NB' => 'New Brunswick',			'NL' => 'Newfoundland and Labrador',		'NT' => 'Northwest Territories',	'NS' => 'Nova Scotia',
	'NU' => 'Nunavut',					'ON' => 'Ontario',							'PE' => 'Prince Edward Island',		'QC' => 'Quebec',
	'SK' => 'Saskatchewan',				'YT' => 'Yukon');
$states_l2s = array_flip($states_s2l);

$coupon_codes = array(
	'SCHOOL'	=>	'FREEDVD',
	'TEACHER'	=>	'FREEDVD',
	'JUNIOR'	=>	'FREEJUNIOR',
	'DOLLAR'	=>	'DOLLAR'
);

	/***********************
	*	Functions			  *
	***********************/

//	Checks if an email is valid
function isValidEmail($email) {
	return preg_match("/^[a-zA-Z]\w+(\.\w+)*\@\w+(\.[0-9a-zA-Z]+)*\.[a-zA-Z]{2,4}$/", $email);
}

	/***********************
	*	API Functions		  *
	***********************/

//	THESE REPLACE THE $_GET, $_POST, etc.
function apiGet($key) {return filter_input(INPUT_GET, $key) ? filter_input(INPUT_GET, $key) : false;}
function apiPost($key) {return filter_input(INPUT_POST, $key) ? filter_input(INPUT_POST, $key) : false;}
function apiCookie($key) {return filter_input(INPUT_COOKIE, $key) ? filter_input(INPUT_COOKIE, $key) : false;}
function apiSession($key) {return isset($_SESSION[$key]) ? $_SESSION[$key] : false;}
	//	Check to see if a parameter has been set and if so, return it
function apiSnag($key) {
	if(apiGet($key))				{return apiGet($key);
	} elseif(apiPost($key))		{return apiPost($key);
	} elseif(apiCookie($key))	{return apiCookie($key);
	} elseif(apiSession($key))	{return apiSession($key);
	} else							{return false;
	}
}

	//	converts "<a href='mailto:abc@example.com'>abc@example.com</a>" to "<a href='mailt&#111;:abc&#64;example.c&#111;m'>abc&#64;example.c&#111;m</a>" to hide from spamBots
function disguiseMail($mail) {
	return str_replace('@','&#64;',str_replace('o','&#111;',$mail));
}
	//	converts "abc@example.com" to "<a href='mailt&#111;:abc&#64;example.c&#111;m'>abc&#64;example.c&#111;m</a>" to hide from spamBots
function disguiseMailLink($simpleEmailAddress) {
	return disguiseMail("<a href='mailto:$simpleEmailAddress'>$simpleEmailAddress</a>");
}

	//	determines if the IP is on the blacklist
function isBlacklistedIP($ip) {
	//	Check to be sure their IP is not on the blacklist before submitting it
	$blackIPs = array();
	$blacklistJSON = json_decode(file_get_contents('http://jeremymoritz.com/support/ip_blacklist.json'));
	$ipIsBlacklisted = false;	//	innocent until proven guilty
	foreach($blacklistJSON->blacklist as $bIP) {
		if($ip === $bIP) {
			$ipIsBlacklisted = true;
			break;
		}
	}
	// $ipParts = explode('.', $ip);
	// foreach($blackIPs as $blackIP) {
	// 	$blackIPParts = explode('.', $blackIP);
	// 	if($ipParts[0] === $blackIPParts[0] && $ipParts[1] === $blackIPParts[1]) {
	// 		$ipIsBlacklisted = true;	//	if the first 2 parts of IP address are the on the blacklist, then kick this guy out!
	// 	}
	// }

	return $ipIsBlacklisted;	//	function returns true if blacklisted, else false
}
?>
