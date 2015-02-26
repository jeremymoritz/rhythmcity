<?php
$title = 'Rhythm City Survey';
include('includes/rc_all.php');

//	SURVEY QUESTIONS
$questions = array(
	"",	//	NOTE: This is left blank because there is no Question #0
	"1. What is your age range?",
	"2. Was this your first experience of producing a musical in a week?",
	"3. What was your overall impression of the whole experience?",
	"4. What was your <strong>favorite</strong> aspect about the week?",
	"5. What was your <strong>least favorite</strong> aspect about the week?",
	"6. What thing(s) can we improve or change to make it a better experience next year?",
	"7. If we produce another musical during the week after Christmas 2012, how likely are you to be involved again?",
	"8. If you could choose, what musical(s) would you be most interested in for next year?",
	"9. Any further comments you can add about your experience with us are greatly appreciated:"
	);

if(filter_input(INPUT_POST, 'submit')) {
	//	They've filled out the form and submitted it.  Now let's insert their info into the database!
	$age = apiPost('age');
	$first = apiPost('first');
	$impression = (apiPost('impression') == 'Other') ? 'Other: ' . apiPost('imp_other') : apiPost('impression');
	$fav = apiPost('fav');
	$least_fav = apiPost('least_fav');
	$change = apiPost('change');
	$involved = apiPost('involved');
	$musical = apiPost('musical');
	$comments = apiPost('comments');
	$ip = $_SERVER['REMOTE_ADDR'];
	$date = date('Y-m-d H:i:s',strtotime('-1 hour'));	//	this accounts for our Central timezone
	//	Send a mailer to me
	$to = $config['info_email'];
	$message = "<b>New Rhythm City Survey Response:</b><br /><br />\n"
		. $questions[1] . "<br /><b>$age</b><br /><br />\n"
		. $questions[2] . "<br /><b>$first</b><br /><br />\n"
		. $questions[3] . "<br /><b>$impression</b><br /><br />\n"
		. $questions[4] . "<br /><b>$fav</b><br /><br />\n"
		. $questions[5] . "<br /><b>$least_fav</b><br /><br />\n"
		. $questions[6] . "<br /><b>$change</b><br /><br />\n"
		. $questions[7] . "<br /><b>$involved</b><br /><br />\n"
		. $questions[8] . "<br /><b>$musical</b><br /><br />\n"
		. $questions[9] . "<br /><b>$comments</b><br /><br />\n"
		. "Date: $date<br />\n"
		. "I.P.: $ip";
	$subject = "RCJr Survey ($age)";
	$headers = "From: RCJr Survey <" . $config['info_email'] . ">\n"
		. "Reply-To: RCJr Survey <" . $config['info_email'] . ">\n"
		. "MIME-Version: 1.0\n"
		. "Content-type: text/html; charset=iso-8859-1\n"
		. "bcc: rhythmcity@gmail.com";
	$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);	// Fix any bare linefeeds in the message to make it RFC821 Compliant
	$headers = preg_replace('#(?<!\r)\n#si', "\r\n", $headers); 	// Make sure there are no bare linefeeds in the headers
	mail($to,$subject,$message,$headers);

	$completedform = true;
}
?>

<!DOCTYPE html>
<html>
<head>
	<?=$head;?>
</head>
<body>
<?=$navbar;?>
<div id='survey' class='main'>
	<img src='images/rhythm_city_logo_junior-250.png' alt='Rhythm City Junior' />
	<h1 class='hidden'>Rhythm City Junior Survey</h1>
	<h2>Rhythm City Junior Anonymous Survey</h2>
<?php
if(isset($completedform)) {
	echo("<h3>Thank you for your filling out the survey!  We appreciate you!</h3>\n");
} else {
?>
	<p>Exclusively for the cast and parents of cast members in <em>Rhythm City Junior</em> Winter 2011</p>
	<p class='tiny'>NOTE: If you wish to elaborate on any answers, please use the "Comments" block at the bottom of the page.</p>
	<div id='form'>
		<form action='<?=$_SERVER['PHP_SELF'];?>' method='post'>
			<table>
				<tr class='question'><td><?=$questions[1];?></td></tr>
					<tr class='answer radio'><td>
						<input type='radio' name='age' id='age_12' value='12 &amp; under' /><label for='age_12'>12 &amp; under</label><br />
						<input type='radio' name='age' id='age_13' value='13-14' /><label for='age_13'>13-14</label><br />
						<input type='radio' name='age' id='age_15' value='15-17' /><label for='age_15'>15-17</label><br />
						<input type='radio' name='age' id='age_18' value='18+' /><label for='age_18'>18+</label><br />
						<input type='radio' name='age' id='age_parent' value='Parent' /><label for='age_parent'>Parent</label></td></tr>
				<tr class='question'><td><?=$questions[2];?></td></tr>
					<tr class='answer radio'><td>
						<input type='radio' name='first' id='first_y' value='Yes' /><label for='first_y'>Yes</label><br />
						<input type='radio' name='first' id='first_n' value='No' /><label for='first_n'>No</label></td></tr>
				<tr class='question'><td><?=$questions[3];?></td></tr>
					<tr class='answer radio'><td>
						<input type='radio' name='impression' id='impression_loved' value='Totally loved it!  So glad I did it!' /><label for='impression_loved'>Totally loved it!  So glad I did it!</label><br />
						<input type='radio' name='impression' id='impression_enjoyed' value='I enjoyed it. It was a good experience.' /><label for='impression_enjoyed'>I enjoyed it. It was a good experience.</label><br />
						<input type='radio' name='impression' id='impression_okay' value='It was okay.' /><label for='impression_okay'>It was okay.</label><br />
						<input type='radio' name='impression' id='impression_slept' value="Wish I would've done something else for Christmas break." /><label for='impression_slept'>Wish I would've done something else for Christmas break.</label><br />
						<input type='radio' name='impression' id='impression_other' value='Other' /><label for='impression_other'>Other:</label> <input type='text' name='imp_other' id='imp_other' onchange='document.getElementById("impression_other").checked=true' /></td></tr>
				<tr class='question'><td><?=$questions[4];?></td></tr>
					<tr class='answer'><td><input type='text' name='fav' id='fav' /></td></tr>
				<tr class='question'><td><?=$questions[5];?></td></tr>
					<tr class='answer'><td><input type='text' name='least_fav' id='least_fav' /></td></tr>
				<tr class='question'><td><?=$questions[6];?></td></tr>
					<tr class='answer'><td><input type='text' name='change' id='change' /></td></tr>
				<tr class='question'><td><?=$questions[7];?></td></tr>
					<tr class='answer'><td>
						<input type='radio' name='involved' id='involved_a' value='Absolutely!' /><label for='involved_a'>Absolutely!</label><br />
						<input type='radio' name='involved' id='involved_vl' value='Very likely.' /><label for='involved_vl'>Very likely.</label><br />
						<input type='radio' name='involved' id='involved_p' value='Possibly.' /><label for='involved_p'>Possibly.</label><br />
						<input type='radio' name='involved' id='involved_n' value='Not very likely.' /><label for='involved_n'>Not very likely.</label><br />
						<input type='radio' name='involved' id='involved_d' value='Depends on the musical.' /><label for='involved_d'>Depends on the musical.</label></td></tr>
				<tr class='question'><td><?=$questions[8];?></td></tr>
					<tr class='answer text'><td><input type='text' name='musical' id='musical' /></td></tr>
				<tr class='question'><td><?=$questions[9];?></td></tr>
					<tr class='answer'><td><textarea name='comments' rows='6' cols='60'></textarea></td></tr>
				<tr class='question'><td><input type='submit' value='Submit' name='submit' /></td></tr>
			</table>
			<p>Thank you for your helping us serve you better!</p>
		</form>
<?php
}	//	ending the ELSE statement above
?>
	</div>
</div>
<?=$navbar;?>
</body>
</html>




				<!--
				die("Dead! " . __LINE__);
				<tr class='question'><td></td></tr>
					<tr class='answer'><td></td></tr>
				<tr class='question'><td></td></tr>
					<tr class='answer'><td></td></tr>
				<tr class='question'><td></td></tr>
					<tr class='answer'><td></td></tr>
				<tr class='question'><td></td></tr>
					<tr class='answer'><td></td></tr>
				<tr class='question'><td></td></tr>
					<tr class='answer'><td></td></tr>
				<tr class='question'><td></td></tr>
					<tr class='answer'><td></td></tr>
					<input type='radio' name='' id='' value='' /><label for=''></label><br />-->
