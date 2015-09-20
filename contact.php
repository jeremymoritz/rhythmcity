<?php
$title = 'Rhythm City Contact Form';
include('includes/rc_all.php');

if(filter_input(INPUT_POST, 'submit')) {
	//	They've filled out the form and submitted it.  Now let's insert their info into the database!
	$name = apiPost('name');
	$email = filter_var(apiPost('email'), FILTER_SANITIZE_EMAIL);
	$phone = apiPost('phone');
	$comments = apiPost('comments');
	$ip = $_SERVER['REMOTE_ADDR'];
	$date = date('Y-m-d H:i:s',strtotime('-1 hour'));	//	this accounts for our Central timezone

	//	Insert data into database
	$query = "
		INSERT INTO rc_contacts (
			name,
			email,
			phone,
			comments,
			ip,
			date
		) VALUES (
			:name,
			:email,
			:phone,
			:comments,
			:ip,
			:date
		)";
	$sth = $dbh->prepare($query);
	$sth->bindParam(':name', $name, PDO::PARAM_STR);
	$sth->bindParam(':email', $email, PDO::PARAM_STR);
	$sth->bindParam(':phone', $phone, PDO::PARAM_STR);
	$sth->bindParam(':comments', $comments, PDO::PARAM_STR);
	$sth->bindParam(':ip', $ip, PDO::PARAM_STR);
	$sth->bindParam(':date', $date, PDO::PARAM_STR);
	$sth->execute();

	//	Send a mailer to me
		//	first adjust these variables to make sure there's no funnybusiness
	$name = get_magic_quotes_gpc() ? $name : addslashes($name);
	$email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : 'INVALID_EMAIL (' . addslashes($email) . ')';
	$phone = get_magic_quotes_gpc() ? $phone : addslashes($phone);
	$comments = get_magic_quotes_gpc() ? $comments : addslashes($comments);

	$to = $config['personal_email'];
	$message = "<b>New Rhythm City Contact:</b><br><br>\n"
		. "Name: $name<br>\n"
		. "Email: $email<br>\n"
		. "Phone: $phone<br>\n"
		. "Comments: $comments<br>\n"
		. "Date: $date<br>\n"
		. "I.P.: $ip";
	$subject = "RC Contact: $name";
	$headers = "From: RC Info <" . $config['info_email'] . ">\n"
		. "Reply-To: $name <$email>\n"
		. "MIME-Version: 1.0\n"
		. "Content-type: text/html; charset=iso-8859-1\n"
		. "bcc: rhythmcity@gmail.com";
	$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);	// Fix any bare linefeeds in the message to make it RFC821 Compliant
	$headers = preg_replace('#(?<!\r)\n#si', "\r\n", $headers); 	// Make sure there are no bare linefeeds in the headers


	$likelySpam = false;
	function setAndEmpty($postValue) {
		return isset($_POST[$postValue]) && empty($_POST[$postValue]);
	}
	function setAndEmptyArray($postValuesArr) {
		foreach($postValuesArr as $val) {
			if(!setAndEmpty($val)) {return false;}
		}
		return true;
	}
	if(!setAndEmptyArray(array('fname', 'questions', 'referral')) || apiPost('website') !== 'http://') {	//	if any of these bot-trap (hidden) fields are filled, this is almost certainly spam
		$likelySpam = true;
	}


	if(!$likelySpam) {
		mail($to,$subject,$message,$headers);
	}

	$completedform = true;
}
?>

<!DOCTYPE html>
<html>
<head>
	<?=$head;?>
</head>
<body>
<?=$header;?>
<section id='contact' class='main'>
	<h2>Rhythm City Contact Information</h2>
	<section id='content'>
		<h2>Main Content</h2>
		<img src='images/rhythm_city_logo-250.png' alt='Rhythm City'>
		<h2>Rhythm City Contact Information</h2>
		<section id='form'>
			<h2>Fill out this form to request information about Rhythm City</h2>
<?php
if(isset($completedform)) {
	echo "<h3>Thank you for your interest in <em>Rhythm City</em>!</h3>
		<h3>We'll be in touch.</h3>\n";
} else {
	echo "
		<form action='" . $_SERVER['PHP_SELF'] . "' method='post'>

					<!-- BOT-TRAP: THIS PART IS MEANT TO STOP SPAM SUBMISSIONS FROM ROBOTS (if you see this section, don't change these fields) -->
						<p class='bot-trap'><label>Robot Questions</label><textarea name='questions' rows='6' cols='40'></textarea></p>
						<p class='bot-trap'><label>Robot Referral</label><input type='text' name='referral' value=''></p>
						<p class='bot-trap'><label>Robot Name</label><input type='text' name='fname' value=''></p>
						<p class='bot-trap'><label>Robot Website</label><input type='text' name='website' value='http://'></p>
					<!-- /BOT-TRAP -->

			<table>
				<tr><th>Name</th><td><input type='text' name='name'></td></tr>
				<tr><th>Email</th><td><input type='text' name='email'></td></tr>
				<tr><th>Phone</th><td><input type='text' name='phone'></td></tr>
				<tr><th>Comments or Questions</th><td><textarea name='comments' rows='6' cols='23'></textarea></td></tr>
				<tr><th></th><td><input type='submit' value='Submit' name='submit'></td></tr>
			</table>
			<h3>Please feel free to contact us anytime!</h3>
			<address>
				<strong>Phone:</strong> <span><a href='tel://913-980-5376' title='Give us a call!'>(913) 980-5376</a></span><br>
				<strong>Email:</strong> <em>" . disguiseMail("<a href='mailto:" . $config['info_email'] . "'>" . $config['info_email'] . "</a>") . "</em><br>
				<strong>Postal Mail:</strong>5502 Oakview<br>
				&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Shawnee, KS 66216
			</address>
		</form>";
}
?>
		</section>
		<p>If you would like more information about <em>Rhythm City</em> or <em>Rhythm City Junior</em>, please let us know, and we will contact you back shortly.</p>
		<p class='clear'><small>Please Note: Your information will be kept strictly confidential.</small></p>
	</section>
</section>
<?=$footer;?>
</body>
</html>
