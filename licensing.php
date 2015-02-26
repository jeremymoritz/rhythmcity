<?php
$title = 'Rhythm City Licensing Information';
include('includes/rc_all.php');

//	constants
define('MAX_PERF_NUMBER',50);	//	char limit
define('MAX_PERF_DATES',50);	//	char limit
define('NUM_TICKET_PRICES',4);	//	# of iterations for ticket prices

if(filter_input(INPUT_POST, 'submit')) {
	//	They've filled out the form and submitted it.  Now let's insert their info into the database!
	$name = stripslashes(apiSnag('name'));
	$company = stripslashes(apiSnag('company'));
	$email = filter_var(strtolower(apiSnag('email')), FILTER_SANITIZE_EMAIL);
	$email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : 'INVALID_EMAIL (' . addslashes($email) . ')';
	$phone = stripslashes(apiSnag('phone'));
	$address = stripslashes(apiSnag('address'));
	$city = stripslashes(apiSnag('city'));
	$state = stripslashes(apiSnag('state'));
	$zip = stripslashes(apiSnag('zip'));
	$website = stripslashes(apiSnag('website'));
	$version = stripslashes(apiSnag('version'));	//	"full" or "jr"
	$perf_dates = stripslashes(apiSnag('perf_dates'));
	$perf_number = stripslashes(apiSnag('perf_number'));
	$seat_capacity = stripslashes(apiSnag('seat_capacity'));
	$seat_average = stripslashes(apiSnag('seat_average'));
	$price_1 = stripslashes(apiSnag('price_1'));
	$price_2 = stripslashes(apiSnag('price_2'));
	$price_3 = stripslashes(apiSnag('price_3'));
	$price_4 = stripslashes(apiSnag('price_4'));
	$percent_1 = stripslashes(apiSnag('percent_1'));
	$percent_2 = stripslashes(apiSnag('percent_2'));
	$percent_3 = stripslashes(apiSnag('percent_3'));
	$percent_4 = stripslashes(apiSnag('percent_4'));
	$comments = stripslashes(apiSnag('comments'));
	$ip = $_SERVER['REMOTE_ADDR'];
	$date = date('Y-m-d H:i:s',strtotime('-1 hour'));	//	this accounts for our Central timezone

	$likelySpam = false;
	if(apiPost('questions') || apiPost('referral') || apiPost('color') !== 'http://') {	//	if any of these bot-trap (hidden) fields are filled, this is almost certainly spam
		$likelySpam = true;
	}

	if(!$likelySpam) {
		//	Insert data into database
		$query = "
			INSERT INTO rc_licensing (
				name,
				company,
				email,
				phone,
				address,
				city,
				state,
				zip,
				website,
				version,
				perf_dates,
				perf_number,
				seat_capacity,
				seat_average,
				price_1,
				price_2,
				price_3,
				price_4,
				percent_1,
				percent_2,
				percent_3,
				percent_4,
				comments,
				ip,
				date
			) VALUES (
				:name,
				:company,
				:email,
				:phone,
				:address,
				:city,
				:state,
				:zip,
				:website,
				:version,
				:perf_dates,
				:perf_number,
				:seat_capacity,
				:seat_average,
				:price_1,
				:price_2,
				:price_3,
				:price_4,
				:percent_1,
				:percent_2,
				:percent_3,
				:percent_4,
				:comments,
				:ip,
				:date
			)";
		$sth = $dbh->prepare($query);

		$sth->bindParam(':name', $name, PDO::PARAM_STR);
		$sth->bindParam(':company', $company, PDO::PARAM_STR);
		$sth->bindParam(':email', $email, PDO::PARAM_STR);
		$sth->bindParam(':phone', $phone, PDO::PARAM_STR);
		$sth->bindParam(':address', $address, PDO::PARAM_STR);
		$sth->bindParam(':city', $city, PDO::PARAM_STR);
		$sth->bindParam(':state', $state, PDO::PARAM_STR);
		$sth->bindParam(':zip', $zip, PDO::PARAM_STR);
		$sth->bindParam(':website', $website, PDO::PARAM_STR);
		$sth->bindParam(':version', $version, PDO::PARAM_STR);
		$sth->bindParam(':perf_dates', $perf_dates, PDO::PARAM_STR);
		$sth->bindParam(':perf_number', $perf_number, PDO::PARAM_STR);
		$sth->bindParam(':seat_capacity', $seat_capacity, PDO::PARAM_STR);
		$sth->bindParam(':seat_average', $seat_average, PDO::PARAM_STR);
		$sth->bindParam(':price_1', $price_1, PDO::PARAM_STR);
		$sth->bindParam(':price_2', $price_2, PDO::PARAM_STR);
		$sth->bindParam(':price_3', $price_3, PDO::PARAM_STR);
		$sth->bindParam(':price_4', $price_4, PDO::PARAM_STR);
		$sth->bindParam(':percent_1', $percent_1, PDO::PARAM_INT);
		$sth->bindParam(':percent_2', $percent_2, PDO::PARAM_INT);
		$sth->bindParam(':percent_3', $percent_3, PDO::PARAM_INT);
		$sth->bindParam(':percent_4', $percent_4, PDO::PARAM_INT);
		$sth->bindParam(':comments', $comments, PDO::PARAM_STR);
		$sth->bindParam(':ip', $ip, PDO::PARAM_STR);
		$sth->bindParam(':date', $date, PDO::PARAM_STR);
		$sth->execute();

		//	Send a mailer to me
		$to = $config['licensing_email'];
		$message = "<b>Rhythm City Licensing Quote Inquiry:</b><br><br>\n"
			. "<b>Name:</b> $name<br>\n"
			. "<b>Company:</b> $company<br>\n"
			. "<b>Email:</b> $email<br>\n"
			. "<b>Phone:</b> $phone<br>\n"
			. "<b>Address:</b> $address<br>\n"
			. "&nbsp; &nbsp; &nbsp; $city, $state $zip<br>\n"
			. "<b>Website:</b> $website<br>\n"
			. "<b>Version:</b> $version<br>\n"
			. "<b>Performance Dates:</b> $perf_dates<br>\n"
			. "<b>No. of Performances:</b> $perf_number<br>\n"
				. ($version == 'full' ?
					"<b>Seat Capacity:</b> $seat_capacity<br>\n"
					. "<b>Seat Average:</b> $seat_average<br>\n"
					. "<b>Tickets 1:</b> \$$price_1 ($percent_1%)<br>\n"
					. "<b>Tickets 2:</b> \$$price_2 ($percent_2%)<br>\n"
					. "<b>Tickets 3:</b> \$$price_3 ($percent_3%)<br>\n"
					. "<b>Tickets 4:</b> \$$price_4 ($percent_4%)<br>\n"
				: "")
			. "<b>Comments:</b> $comments<br>\n"
			. "<b>Date:</b> $date<br>\n"
			. "<b>I.P.:</b> $ip";
		$subject = "RC Licensing Quote: $name";
		$headers = "From: RC Licensing <" . $config['licensing_email'] . ">\n"
			. "Reply-To: $name <$email>\n"
			. "MIME-Version: 1.0\n"
			. "Content-type: text/html; charset=iso-8859-1\n"
			. "bcc: rhythmcity@gmail.com";
		$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);	// Fix any bare linefeeds in the message to make it RFC821 Compliant
		$headers = preg_replace('#(?<!\r)\n#si', "\r\n", $headers); 	// Make sure there are no bare linefeeds in the headers
		mail($to,$subject,$message,$headers);
	}
	$completedform = true;
}

if(apiGet('prefill')) {
	$pfName = " value='" . apiGet('name') . "'";
	$pfCompany = " value='" . apiGet('company') . "'";
	$pfEmail = " value='" . apiGet('email') . "'";
	$pfPhone = " value='" . apiGet('phone') . "'";
	$pfAddress = " value='" . apiGet('address') . "'";
	$pfCity = " value='" . apiGet('city') . "'";
	$pfState = apiGet('state');
	$pfZip = " value='" . apiGet('zip') . "'";
} else {
	$pfName = "";
	$pfCompany = "";
	$pfEmail = "";
	$pfPhone = "";
	$pfAddress = "";
	$pfCity = "";
	$pfState = "";
	$pfZip = "";
}
?>

<!DOCTYPE html>
<html>
<head>
	<?=$head;?>
</head>
<body>
<?=$header;?>
<section id='licensing' class='main'>
	<h2>Rhythm City Licensing Information</h2>
	<section id='content'>
		<h2 class='visible'><img src='images/rhythm_city_logo-125.png' alt='Rhythm City'> Licensing Information
			<img src='images/rhythm_city_logo_junior-125.png' alt='Rhythm City Junior'></h2>
		<section id='form'>
			<h2>License Form</h2>
<?php
if(isset($completedform)) {
	echo "<h3>Thank you for your interest in <em>Rhythm City" . ($want_jr ? " Junior" : "") . "</em>!<br>
		You will receive a quote via email.</h3>\n";
} else {
	echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='post' id='licenseform'>
		<h3>Licensing Quote Request Form</h3>
		<table>
			<tr><th>Name:</th><td><input type='text' name='name' id='name'$pfName></td></tr>
			<tr><th>School or Theatre Co:</th><td><input type='text' name='company'$pfCompany></td></tr>
			<tr><th>Email:</th><td><input type='text' name='email' id='email'$pfEmail></td></tr>
			<tr><th>Phone:</th><td><input type='text' name='phone' id='phone'$pfPhone></td></tr>
			<tr><th>Address:</th><td><input type='text' name='address' id='address'$pfAddress></td></tr>
			<tr><th>City, ST Zip:</th><td><input type='text' name='city' id='city'$pfCity>, <select name='state' id='state'>
				<option value='--'>--</option>\n";
	foreach($states_l2s as $st) {
		echo "<option value='" . $st . "'" . ($st == $pfState ? " selected='selected'" : "") . ">" . $st . "</option>\n";
	}
	echo "
			</select> <input type='text' name='zip' id='zip'$pfZip></td></tr>
			<tr><th>Website:</th><td><input type='text' name='website'></td></tr>
			<tr><th>Version:</th>
				<td id='version'><input type='radio' name='version' id='version_full' value='full'><label for='version_full'>Rhythm City</label><br>
					<input type='radio' name='version' id='version_jr' value='jr'><label for='version_jr'>Rhythm City Junior</label></td></tr>
			<tr><th>Performance Dates*:</th><td><input type='text' name='perf_dates' id='perf_dates' maxlength='" . MAX_PERF_DATES . "'></td></tr>
			<tr><th>No. of Performances*:</th><td><input type='text' name='perf_number' id='perf_number' maxlength='" . MAX_PERF_NUMBER . "'></td></tr>
			<tr class='full_only'><th>Seating Capacity*:</th><td><input type='text' name='seat_capacity' id='seat_capacity'></td></tr>
			<tr class='full_only'><th>Seating Average*:</th><td><input type='text' name='seat_average' id='seat_average'></td></tr>
			<tr class='full_only'><th>Ticket Price(s)*:</th>
				<td><table id='price_table'>
						<tr><th>Price</th><th>% of Total</th></tr>";
	for ($i = 1; $i <= NUM_TICKET_PRICES; $i++) {
		echo "		<tr><td>$<input type='text' name='price_$i' class='price'></td><td><input type='text' name='percent_$i' class='percent' value='"
			. ($i == 1 ? "100" : "0") . "' maxlength='3' onfocus='select()'>%</td></tr>\n";
	}
	echo "			<tr class='bold'><td>TOTAL:</td><td>100%</td></tr>
					</table></td></tr>
			<tr><th>Comments or Questions:</th><td><textarea name='comments' rows='4' cols='23'></textarea></td></tr>
			<tr><th>Printed Materials?:</th><td><input type='checkbox' name='printed_materials' id='printed_materials'><label for='printed_materials'>Check this box if you prefer to have physical scripts &amp; scores mailed instead of downloading PDFs (extra fee).</label></td></tr>
			<!-- BOT-TRAP: THIS PART IS MEANT TO STOP SPAM SUBMISSIONS FROM ROBOTS (if you see this section, don't change these fields) -->
				<tr class='bot-trap'><th>Robot Questions</th><td><textarea name='questions' rows='6' cols='40'></textarea></td></tr>
				<tr class='bot-trap'><th>Robot Referral</th><td><input type='text' name='referral' value=''></td></tr>
				<tr class='bot-trap'><th>Robot Color</th><td><input type='text' name='color' value='http://'></td></tr>
			<!-- /BOT-TRAP -->
			<tr><th></th><td><input type='submit' value='Submit' name='submit'></td></tr>
		</table>
		<p>*If unknown, give approximate ranges</p>
		</form>";
}
?>
		</section>
		<iframe id='choreo_dvd' src='http://player.vimeo.com/video/23181749?title=0&amp;byline=0&amp;portrait=0'></iframe>
		<blockquote>
			"This information is better than anything I have ever received for any play or musical I have directed in my 30 years of directing! Your Director's Package is awesome! Thank you so very much for saving my manager and me so much work and time."<br>
			<cite>~Cor Jesu Academy, St. Louis, MO</cite>
		</blockquote>
		<section id='resources'>
			<h2>What Rhythm City has to Offer</h2>
			<h3>License Rhythm City for FREE!</h3>
			<p>Rhythm City is now ROYALTY-FREE, including download of all standard materials for a full-scale production!  If you would like more information about producing <em>Rhythm City</em> or <em>Rhythm City Junior</em> at your school or theater, please fill out this form, and you will receive an email with detailed <strong>licensing Information</strong>.</p>
			<p>In addition to the standard materials (scripts, vocal books, conductor's score, piano-vocal book, and <a class='tooltip-html' title='<div class="custom-tooltip"><b>Rhythm City:</b><br>Bass<br>Bassoon<br>Clarinet<br>Drums<br>Flute<br>Guitar<br>Horn<br>Keyboard<br>Oboe<br>Percussion<br>Piano<br>Trombone<br>Trumpet<br>Violin<br><br><b>Rhythm City Jr:</b><br>Tracks Only</div>'>orchestra parts</a>), <em>Rhythm City</em> and <em>Rhythm City Junior</em> also offer valuable money-saving resources such as:</p>
			<ul id='resourcelist'>
				<li>
					<img src='images/rc-orch-cd_125.png' alt='Rhythm City Orchestration CD'>
					A professionally-recorded, flawless <strong>Orchestration CD</strong> with all music for the entire show (includes rights to use for all performances as well as rehearsals).
				</li>
				<li>
					<img src='images/rc-choreo-dvd_125.png' alt='Rhythm City Choreography DVD'>
					<em>The Steps of Rhythm City</em>: A <strong>Choreography DVD</strong> in which the original choreographer teaches step-by-step instructions for all choreography in the original production. <span>(see preview below)</span>
				</li>
				<li>
					<img src='images/rc-dir-pkg_125.png' alt='Rhythm City Director Package'>
					A <strong>Director's Package</strong> data CD containing:
					<ul>
						<li>Blocking Script <span>(with original director's blocking notes)</span></li>
						<li>Line Memorization Recordings <span>(for each character)</span></li>
						<li>Props List</li>
						<li>Illustrated Costume Breakdown</li>
						<li>Set Design Sketches</li>
						<li>Callback Cuts</li>
						<li>Sample Rehearsal Schedule</li>
						<li>Editable Mic Plot</li>
						<li>And much more!</li>
					</ul>
				</li>
			</ul>

			<!-- <div id='resourcelist'>
				<img src='images/rc-orch-cd_125.png' alt='Rhythm City Orchestration CD'>
				<p>
					A professionally-recorded, flawless <strong>Orchestration CD</strong> with all music for the entire show (includes rights to use for all performances as well as rehearsals).
				</p>
				<img src='images/rc-choreo-dvd_125.png' alt='Rhythm City Choreography DVD'>
				<p>
					<em>The Steps of Rhythm City</em>: A <strong>Choreography DVD</strong> in which the original choreographer teaches step-by-step instructions for all choreography in the original production. <span>(see preview below)</span>
				</p>
				<img src='images/rc-dir-pkg_125.png' alt='Rhythm City Director Package'>
				<p>
					A <strong>Director's Package</strong> data CD containing:
					<ul>
						<li>Blocking Script <span>(with original director's blocking notes)</span></li>
						<li>Line Memorization Recordings <span>(for each character)</span></li>
						<li>Props List</li>
						<li>Illustrated Costume Breakdown</li>
						<li>Set Design Sketches</li>
						<li>Callback Cuts</li>
						<li>Sample Rehearsal Schedule</li>
						<li>Editable Mic Plot</li>
						<li>And much more!</li>
					</ul>
				</p>
			</div> -->
			<p>If you would like further information, please feel free to <a href='contact.php'>Contact Us</a> at any time.</p>
		</section>
		<p class='clear'></p>
	</section>
</section>
<?=$footer;?>
</body>
</html>
