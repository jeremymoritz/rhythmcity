<?php

////	DIRECT PEOPLE TO THE NEW REVIEWS PAGE

header("Location: /reviews.php");


/////







$title = 'Rhythm City Reviews: Here is what people are saying about Rhythm City!';
$h1 = 'Rhythm City Reviews';
$h2 = 'Read what people are saying about Rhythm City!';
include('includes/rc_all.php');

$quotesxml = simplexml_load_file('includes/quotes.xml');
$aud = $quotesxml->group[0];
$cast = $quotesxml->group[1];
$vid_width = 340;	//	original movie is 400px wide
$vid_height = 191;	//	original movie is 224px tall

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
	<h2>Director, Audience and Cast Reviews</h2>
	<p>Here's what others are saying about...</p>
	<img src='images/rhythm_city_logo-250.png' alt='Rhythm City'>
	<blockquote>
		<p>"It is everything a high school musical should be.... I bet it becomes the most produced high school musical in America once it becomes known."</p>
		<footer>&tilde; Susan Steffan, Theatre Director of Marengo Community High School (Marengo, IL)</footer>
	</blockquote>
	<blockquote>
		<p>"I would like to thank you for putting together a wonderful show.  My students loved the choreography and the concept of a city where singing and dancing is the norm.  We had a blast working on it....  It will be a show that I will definitely recommend to my colleagues from other middle schools."</p>
		<footer>&tilde; Sandy Carlson-Wood, Theatre Director of Murray MS (Stuart, FL)</footer>
	</blockquote>
	<blockquote>
		<p>"I've been directing musicals for 30 years now; I wish I'd found this one sooner."</p>
		<footer>&tilde; Kathy Breeden, Director of Theatre at Leawood Middle School (Leawood, KS)</footer>
	</blockquote>
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
