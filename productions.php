<?php
$title = 'Productions of Rhythm City and Rhythm City Junior';
include('includes/rc_all.php');

//	initialize variables
$photos = "";	//	store the table rows here
$captions = "";
$framesPerRow = 3;
$curFrame = 1;	//	current frame in the row
$maxPics = 5;	//	max number of pics to show of each production
$maxShows = 6;	//	max number of shows to display pics for
$numShows = 0;	//	initialize var for number of shows we've displayed yet

$shows_arr = array(
	"Immeasurable Productions|ip|rcjr|23",	//	there are 23 images in total, but only up to $maxPics number of randomly-chosen images will be loaded at any one time
	"Blue Valley High School|bvhs|rcjr|7",
	"Leawood Middle School|lms|rcjr|12",
	"South Valley Middle School|svms|rcjr|13",
	"Trilogy Cultural Arts|tca|rc|7",
	"Murray Middle School|mms|rcjr|12",
	"Utica High School|uhs|rc|19",
	"Philomath High School|phs|rc|21",
	"East Carteret High School|echs|rc|7",
	"Cor Jesu Academy|cja|rc|19",
	"Maranatha Christian Academy|mca|rcjr|14"
);
shuffle($shows_arr);	//	randomize shows

$version_name = array(
	"rc" => "Rhythm City",
	"rcjr" => "Rhythm City Junior"
);

foreach($shows_arr as $key => $co_abbr_ver_pics) {
	list($co, $abbr, $ver, $numpics) = explode("|", $co_abbr_ver_pics);	//	make 4 variables out of this one

	$photos .= (($curFrame % $framesPerRow == 1) ? "<tr class='photos'>" : "") .	//	if first frame in the row, open tr
		"<td id='$abbr' data-showpic='$key' title='{$version_name[$ver]} at $co'>\n";

	$photoNumbers = range(1,$numpics);	//	initialize array of photos for this show
	shuffle($photoNumbers);	//	randomize
	for($i=1; $i<=$maxPics; $i++) {
		$picNum = array_pop($photoNumbers);	//	grab a pic (don't use any pic twice)
		$photos .= "<div" . ($i == 1 ? " class='current'" : "") . "><img src='images/photos/$abbr-" . str_pad($picNum, 2, 0, STR_PAD_LEFT) . ".jpg' alt='" . $version_name[$ver] . " at $co (photo $picNum)'></div>\n";
	}
	$photos .= "</td>\n" .
		(($curFrame++ % $framesPerRow == 0) ? "</tr>" : "");		//	if last frame in row, close tr

	if(++$numShows >= $maxShows) {	//	stop displaying shows if we've reached our quota
		break;
	}
}

$showpics = "
	<button onclick='replaceShowPics()'>Change Photos</button>
	<table id='showpics'>
		<tbody>
			$photos
		</tbody>
	</table>";
?>

<!DOCTYPE html>
<html>
<head>
	<?=$head;?>
</head>
<body>
<?=$header;?>
<section id='productions' class='main'>
	<h2>Rhythm City and Rhythm City Junior are now being performed around the country.</h2>
	<section id='content'>
		<h2>Schools and Community Groups that have performed Rhythm City</h2>
		<aside class='left'>
			<h2>Images and Quotes about Rhythm City</h2>
			<img src='images/rhythm_city_logo-125.png' alt='Rhythm City'>
		</aside>
		<aside class='right'>
			<h2>Quotes and Images involving Rhythm City</h2>
			<img src='images/rhythm_city_logo_junior-125.png' alt='Rhythm City Junior'>
		</aside>
		<section id='verbiage'>
			<h2>Rhythm City Around the Country</h2>
			<h3>A sampling of schools and community theatres that have recently produced <strong>Rhythm City</strong> and <strong>Rhythm City Junior</strong>:</h3>
			<ul id='venues-list'>
			</ul>
			<script>
				var venues = [
					'Trilogy Cultural Arts Centre|Olathe, KS',
					'Biloxi High School|Biloxi, MS',
					'Lompoc High School|Lompoc, CA',
					// 'Clyde High School|Clyde, OH',
					'Murray Middle School|Stuart, FL',
					'Geneseo High School|Geneseo, IL',
					'Kickapoo High School|Springfield, MO',
					'Utica High School|Utica, MI',
					'Blue Valley High School|Overland Park, KS',
					'Immeasurable Productions|Kansas City, MO',
					'Marengo Community High School|Marengo, IL',
					'Leawood Middle School|Leawood, KS',
					'Philomath High School|Philomath, OR',
					'South Valley Middle School|Liberty, MO',
					'East Carteret High School|Beaufort, NC',
					'Maranatha Christian Academy|Shawnee, KS',
					'St Edward High School|Elgin, IL',
					'Cor Jesu Academy|St. Louis, MO',
					'Greater Atlanta Christian School|Atlanta, GA'
				];
				$.each(venues, function eachVenue(index, venue) {
					var company = venue.split('|')[0];
					var city = venue.split('|')[1];

					$('#venues-list').append('<li>' + company + ' (' + city + ')</li>');
				});
			</script>
		</section>
		<?=$showpics;?>
	</section>
	<br class='clear'>
</section>
<?=$footer;?>
</body>
</html>
