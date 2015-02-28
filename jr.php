<?php
$title = 'Rhythm City Junior :: A New Musical';
$preload_images = array('plot-music-jr_over.png', 'movie-jr_over.png');
include('includes/rc_all.php');
?>

<!DOCTYPE html>
<html>
<head>
	<?=$head;?>
</head>
<body>
<?=$header;?>
<section id='jr' class='main'>
	<h2>Rhythm City Junior</h2>
	<section id='content'>
		<h2>Information and Resources for Rhythm City Junior the Musical</h2>
		<section id='intro'>
			<img src='images/rhythm_city_logo_junior-250.png' alt='Rhythm City Junior'>
			<h2 class='visible'>Rhythm City Junior</h2>
			<h3 class='hidden'>By Jeremy Moritz</h3>
			<p>With simplified songs &amp; dialogue and a run-time of only one hour, <strong>Rhythm City Junior</strong> retains the charm and fun of the full musical <strong>Rhythm City</strong> at a scale that is crafted specifically for Middle School and Junior High performers!  You can find performances of this delightful musical in middle schools, high schools, and community theatres nationwide.</p>
		</section>
		<section id='resources'>
			<h2>Watch Rhythm City Junior online.</h2>
			<h3>Get to know Rhythm City Junior!</h3>
			<table id='jrlinks'>
				<tr>
					<td><a href='movie_jr.php'><img src='images/movie-jr.png' alt='Plot Synopsis' class='mouseover'><br>Watch the full production online!</a></td>
					<td class='narrow'>OR</td>
					<td><a href='plot_jr.php'><img src='images/plot-music-jr.png' alt='Reviews' class='mouseover'><br>Read the plot synopsis and listen to the songs!</a></td>
				</tr>
			</table>
		</section>
		<section id='videos'>
			<h2>Videos</h2>
			<h3>30-Second Trailer for Rhythm City</h3>
			<!--[if IE 9]><div class='hidden'><![endif]-->
			<video width='400' height='225' controls preload='auto' poster='images/rctrailer-poster.png'>
				<source src='videos/rhythmcity-trailer.mp4' type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"' />
				<source src='videos/rhythmcity-trailer.webm' type='video/webm; codecs="vp8, vorbis"' />
				<source src='videos/rhythmcity-trailer.ogv' type='video/ogg; codecs="theora, vorbis"' />
			<!--[if IE 9]></video></div><![endif]-->
				<!--Fallback to Vimeo-->
				<iframe src='http://player.vimeo.com/video/36436175?title=0&amp;byline=0&amp;portrait=0' width='440' height='330'></iframe>
			<!--[if IE 9]><div class='hidden'><video class='hidden'><![endif]-->
			</video>
			<!--[if IE 9]></div><![endif]-->
		</section>
		<section>
			<h2>Connect with Rhythm City</h2>
			<p class='weak acenter narrow'><a href='http://www.facebook.com/RhythmCityMusical'><img src='/images/facebook_button.jpg' alt='Rhythm City on Facebook'></a></p>
		</section>
	</section>
</section>
<?=$footer;?>
</body>
</html>