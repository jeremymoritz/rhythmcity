<?php
$title = 'Rhythm City Reviews: Here is what people are saying about Rhythm City!';
$h1 = 'Rhythm City Reviews';
$h2 = 'Read what people are saying about Rhythm City!';
include('includes/rc_all.php');

$quotesxml = simplexml_load_file('includes/quotes.xml');
$dir = $quotesxml->group[0];
$aud = $quotesxml->group[1];
$cast = $quotesxml->group[2];
$vid_width = 340;	//	original movie is 400px wide
$vid_height = 191;	//	original movie is 224px tall

function blockQuoteLister($xml_quotes) {
	$list = "";	//	start the list
	$q_arr = array();	//	new array of quotes (for shuffling purposes)
	foreach($xml_quotes as $q) {
		$q_arr[] = $q;
	}
		//	shuffle($q_arr);	//	shuffle the order of the quotes (currently not used on block quotes)
	foreach($q_arr as $q) {
		$list .= "
			<blockquote>
				<p>&quot;$q->statement&quot;</p>
				<footer>&tilde; $q->from</footer>
			</blockquote>";
	}
	
	return $list;
}

function quoteLister($xml_quotes, $altrow=true) {
	$list = "<ul>\n";	//	start the list
	$q_arr = array();	//	new array of quotes (for shuffling purposes)
	foreach($xml_quotes as $q) {
		$q_arr[] = $q;
	}
	shuffle($q_arr);	//	shuffle the order of the quotes
	foreach($q_arr as $q) {
		$altrow = !$altrow;	//	change alternating row colors
		$list .= "<li" . ($altrow ? " class='altrow'" : "") . ">&quot;$q->statement&quot;</li>\n";
	}
	$list .= "</ul>\n";	//	close list
	
	return $list;
}

	//	director content
$d = "
	<section class='blockquotes' id='directors'>
		<h2>School Theatre Directors</h2>"
		. blockQuoteLister($dir->quotes->quote) . "
	</section>";

	//	audience content
$a = "
	<section class='quotes' id='audience'>
		<h3>Audience Exit Interviews</h3>
		<hr>
		<iframe title='Audience Exit Interviews' src='http://player.vimeo.com/video/22803135?title=0&amp;byline=0&amp;portrait=0' width='$vid_width' height='$vid_height'></iframe>"
		. quoteLister($aud->quotes->quote) . "
	</section>";
	
	//	cast content
$c = "
	<section class='quotes' id='cast'>
		<h3>Cast Interviews</h3>
		<hr>
		<iframe title='Audience Exit Interviews' src='http://player.vimeo.com/video/22750487?title=0&amp;byline=0&amp;portrait=0' width='$vid_width' height='$vid_height'></iframe>"
		. quoteLister($cast->quotes->quote, false) . "	
	</section>";
?>

<!DOCTYPE html>
<html>
<head>
	<?=$head;?>
</head>
<body>
<?=$header;?>
<section id='reviews' class='main'>
	<h2>Directors, Audience, and Cast Reviews</h2>
	<p>Here's what others are saying about...</p>
	<img src='images/rhythm_city_logo-250.png' alt='Rhythm City'>
	<?=$d;?>
	<br>
	<!--Audience Interviews-->
	<?=$a;?>
	<!--Cast Interviews-->
	<?=$c;?>
</section>
<?=$navbar;?>
<?=$hidlinks;?>
</body>
</html>
