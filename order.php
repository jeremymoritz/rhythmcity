<?php
$title = "Rhythm City CD/DVD Order Form";
include('includes/rc_all.php');

//important variables & arrays:
$price_dvd = 20;
$price_dvdjr = 20;
$price_cd = 10;
$price_combo = 25;
$max_qty = 10;

//	Max lengths
$max_name = 50;
$max_company = 70;
$max_email = 50;
$max_phone = 25;
$max_address = 70;
$max_city = 50;
$max_state = 2;
$max_zip = 11;
$max_coupon = 10;
$max_comments = 1000;

if(filter_input(INPUT_POST, 'submit')) {
	//	They've filled out the form and submitted it.  Now let's insert their info into the database!
	$name = stripslashes(substr(apiPost('name'),0,$max_name));
	$company = stripslashes(substr(apiPost('company'),0,$max_name));
	$email = substr(strtolower(apiPost('email')),0,$max_email);
	$phone = stripslashes(substr(apiPost('phone'),0,$max_phone));
	$address = stripslashes(substr(apiPost('address'),0,$max_address));
	$city = stripslashes(substr(apiPost('city'),0,$max_city));
	$state = substr(apiPost('state'),0,$max_state);
	$zip = stripslashes(substr(apiPost('zip'),0,$max_zip));
	$want_jr = apiPost('want_jr') ? true : false;
	$dvd_qty = apiPost('dvd_qty') + apiPost('combo_qty');	//	add combos + DVDs to get total DVDs
	$dvdjr_qty = apiPost('dvdjr_qty');
	$cd_qty = apiPost('cd_qty') + apiPost('combo_qty');	//	add combos + CDs to get total CDs
	$combo_qty = $cd_qty <= $dvd_qty ? $cd_qty : $dvd_qty;	//	equal to lesser of the quantities in the order (zero if only one item is ordered)
	//$dvd_qty = ($combo_qty + $dvd_qty + $cd_qty + dvdjr_qty == 0) ? 1 : $dvd_qty;	//	if nothing is ordered at all, we'll assume they've ordered one DVD
	$dvd_ind_qty = $dvd_qty - $combo_qty;
	$cd_ind_qty = $cd_qty - $combo_qty;
	$total = ($combo_qty * $price_combo) + ($dvd_ind_qty * $price_dvd) + ($cd_ind_qty * $price_cd) + ($dvdjr_qty * $price_dvdjr);

	$coupon = strtoupper(trim(substr(apiPost('coupon'),0,$max_coupon),'"\\ '));
	$comments = stripslashes(substr(apiPost('comments'),0,$max_comments));
	$ip = $_SERVER['REMOTE_ADDR'];
	$date = date('Y-m-d, g:ia');

	if($total == 0) {
		$order = "NONE ORDERED<br>\n";
	} else {
		$order = "<b>DVDs:</b> $dvd_qty<br>\n"
			. "<b>RCJr DVDs:</b> $dvdjr_qty<br>\n"
			. "<b>CDs:</b> $cd_qty";
	}

	$likelySpam = false;
	if(apiPost('questions') || apiPost('referral') || apiPost('website') !== 'http://') {	//	if any of these bot-trap (hidden) fields are filled, this is almost certainly spam
		$likelySpam = true;
	} elseif($coupon && !in_array($coupon, $coupon_codes) && strstr(trim($name), ' ')) {
		$likelySpam = true;
	}

	if($total > 0 && !isBlacklistedIP($ip) && !$likelySpam) {
		if($coupon && $coupon_codes[$coupon] === 'FREEDVD') {	//	check to see if this coupon is in the coupon_codes array
			$total = max($total - $price_dvd,0);	//	subtracts price of DVD, but never lower than zero.
			$total = ($total == 5 ? 0 : $total);	//	if total is $5, reduce it to free
			$coupon_success = true;
		} elseif($coupon && $coupon_codes[$coupon] === 'DOLLAR') {
			$total = 1;	//	this is just a hack to charge myself exactly $1 (because if i don't use my debit card every month, i'm charged a $14 fee)
			$coupon_success = true;
		} else {
			$coupon_success = false;
		}

		$what_ordered = $combo_qty > 0 ? "DVD & CD" : ($dvd_qty + $dvdjr_qty > 0 ? "DVD" : "CD");

		//	Send a mailer to me
		$to = $config['order_email'];
		if($coupon && $coupon_codes[$coupon] === 'DOLLAR') {
			$to = 'jeremy@rhythmcity.org';	//	send mailer only to me
		}
		$message = "$name<br>$company<br>$address<br>$city, $state $zip<br><br><br>\n"
			. "<b>New Rhythm City $what_ordered Order:</b><br><br>\n"
			. "<b>Name:</b> $name<br>\n"
			. "<b>Company/School:</b> $company<br>\n"
			. "<b>Email:</b> $email<br>\n"
			. "<b>Phone:</b> $phone<br>\n"
			. "<b>Address:</b> $address<br>\n"
			. "         $city, $state $zip<br>\n<br>\n"
			. "<b>Order...</b><br>\n"
			. "$order<br>\n"
			. "Total Price: \$" . $total . "<br>\n"
			. "Coupon Code: " . $coupon . "<br>\n"
			. ($want_jr ? "NOTE: Junior Version Preferred<br>\n" : "")
			. "<b>Comments:</b> $comments<br>\n"
			. "<b>Date:</b> $date<br>\n"
			. "<b>I.P.:</b> $ip";
		$subject = "Rhythm City " . ($want_jr || $dvdjr_qty ? "Jr. " : "") . "$what_ordered Order: $name";
		$headers = "From: RC Media Sales <" . $config['order_email'] . ">\n"
			. "Reply-To: $name <$email>\n"
				// TEMPORARY EMAIL //
			. "Reply-To: 'Davey' <david@rhythmcity.org>\n"
			. "MIME-Version: 1.0\n"
			. "Content-type: text/html; charset=iso-8859-1\n"
			. "cc: rob@rhythmcity.org\n"
			. "cc: david@rhythmcity.org\n"
			. "bcc: rhythmcity@gmail.com";

		$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);	// Fix any bare linefeeds in the message to make it RFC821 Compliant
		$headers = preg_replace("#(?<!\r)\n#si", "\r\n", $headers); 	// Make sure there are no bare linefeeds in the headers
		mail($to,$subject,$message,$headers);	//	send mailer

		$sql = "
			INSERT INTO rc_order (
				name,
				company,
				email,
				phone,
				address,
				city,
				state,
				zip,
				price,
				dvd,
				dvdjr,
				cd,
				jr,
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
				:total,
				:dvd_qty,
				:dvdjr_qty,
				:cd_qty,
				:want_jr,
				:comments,
				:ip,
				:date
			)";

		$sth = $dbh->prepare($sql);
		$sth->bindParam(':name', $name, PDO::PARAM_STR);
		$sth->bindParam(':company', $company, PDO::PARAM_STR);
		$sth->bindParam(':email', $email, PDO::PARAM_STR);
		$sth->bindParam(':phone', $phone, PDO::PARAM_STR);
		$sth->bindParam(':address', $address, PDO::PARAM_STR);
		$sth->bindParam(':city', $city, PDO::PARAM_STR);
		$sth->bindParam(':state', $state, PDO::PARAM_STR);
		$sth->bindParam(':zip', $zip, PDO::PARAM_STR);
		$sth->bindParam(':total', $total, PDO::PARAM_STR);
		$sth->bindParam(':dvd_qty', $dvd_qty, PDO::PARAM_STR);
		$sth->bindParam(':dvdjr_qty', $dvdjr_qty, PDO::PARAM_STR);
		$sth->bindParam(':cd_qty', $cd_qty, PDO::PARAM_STR);
		$sth->bindParam(':want_jr', $want_jr, PDO::PARAM_INT);
		$sth->bindParam(':comments', $comments, PDO::PARAM_STR);
		$sth->bindParam(':ip', $ip, PDO::PARAM_STR);
		$sth->bindParam(':date', $date, PDO::PARAM_STR);
		$sth->execute();

		$completedform = true;
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<?=$head;?>
</head>
<body>
<?=$header;?>
<section id='order' class='main'>
	<h2>Order the Rhythm City DVD!</h2>
	<section>
		<h2>About the CD &amp; DVD</h2>
		<img src='images/rhythm_city_logo-250.png' alt='Rhythm City'>
		<p class='center'>Order the Original Cast Recording of Rhythm City along with the full-length professional DVD movie, complete with bonus features such as Director's Commentary, Cast Interviews, and more!</p>
	</section>
	<section id='form'>
		<h3><img src='images/dvd_icon_100.png' alt='Rhythm City DVD'>
			Rhythm City DVD/CD Order Form
			<img src='images/cd_icon_100.png' alt='Rhythm City CD'></h3>
<?php
if(isset($completedform)) {
	if($total > 0 || $coupon_success) {
		$coupon_statement = $coupon_success ? "<br>Your coupon code has been approved!<br><br>\n" : '';

		echo "
			<section id='review'>
				<h3>Review Your Order:</h3>\n"
				. "<p><strong>Name:</strong> $name<br>\n"
				. "<strong>Company/School:</strong> $company<br>\n"
				. "<strong>Email:</strong> $email<br>\n"
				. "<strong>Phone:</strong> $phone<br>\n"
				. "<strong>Address:</strong> $address<br>\n"
				. "         $city, $state $zip<br>\n"
				. "<strong>Comments:</strong> $comments<br><br>\n"
				. "Order...<br>\n"
				. "$order<br>\n"
				. ($want_jr ? "*JUNIOR* version<br>\n" : "")
				. $coupon_statement
				. "<strong>TOTAL COST: \$" . $total . "</strong></p>"
				. "<p>NOTE: Shipping is free to US addresses.</p>";

		if ($total > 0) {
			echo "<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
						<input type='hidden' name='cmd' value='_xclick'>
						<input type='hidden' name='business' value='" . $config['order_email'] . "'>
						<input type='hidden' name='item_name' value='Rhythm City DVD/CD Order'>
						<input type='hidden' name='currency_code' value='USD'>
						<input type='hidden' name='return' value='http://" . $_SERVER['SERVER_NAME'] . "/order_thankyou.php'>
						<input type='hidden' name='cancel_return' value='http://" . $_SERVER['SERVER_NAME'] . "/order_cancel.php'>
						<input type='hidden' name='amount' value='$total'>
						<input id='buynow' type='image' src='images/buynow_btn.gif' name='submit' alt='Click Here to submit payment with VISA, MasterCard, AmEx, Discover, or PayPal'>
					</form>";
		} else {
			echo "<form action='order_thankyou.php' method='post'>
					<input type='submit' value='Submit Order' id='submit_free_order'>
				</form>";
		}

		echo "<p class='center'><a href='" . $_SERVER['PHP_SELF'] . "'>Start Over</a></p>
			</section>";
	}
} else {
	//Prep the select options in the form
	$statesopt = "<option value='--'>--</option>";
	foreach($states_l2s as $st) {
		$statesopt .= "<option value='" . $st . "'>" . $st . "</option>\n";
	}

	$qty_opt = "<option value='0'>0</option>\n";
	for($i=1; $i<=$max_qty; $i++) {
		$qty_opt .= "<option value='$i'>$i</option>\n";
	}

	echo "
		<form action='" . $_SERVER['PHP_SELF'] . "' method='post' id='orderform' name='orderform'>
			<table>
				<tr><th><label for='name'>*Name:</label></th><td><input type='text' name='name' id='name' maxlength='$max_name'></td></tr>
				<tr><th><label for='company'>Company/School:</label></th><td><input type='text' name='company' id='company' maxlength='$max_company'></td></tr>
				<tr><th><label for='email'>*Email:</label></th><td><input type='text' name='email' id='email' maxlength='$max_email'></td></tr>
				<tr><th><label for='phone'>*Phone:</label></th><td><input type='text' name='phone' id='phone' maxlength='$max_phone'></td></tr>
				<tr><th><label for='address'>*Address:</label></th><td><input type='text' name='address' id='address' maxlength='$max_address'></td></tr>
				<tr><th><label for='city'>*City, ST Zip</label></th><td><input type='text' name='city' id='city' maxlength='$max_city'>,
					<select name='state' id='state'>$statesopt</select>
					<input type='text' name='zip' id='zip' maxlength='$max_zip'></td></tr>
				<tr><td colspan='2'>
					<table id='media'>
						<thead>
							<tr><th class='item'>Item</th>
								<th>Price</th>
								<th>Quantity</th></tr>
						</thead>
						<tbody>
							<tr class='hidden'><td>Combo Pack (Rhythm City DVD &amp; CD)</td>
								<td>\$$price_combo each</td>
								<td><select name='combo_qty'>$qty_opt</select></td></tr>
							<tr><td>Rhythm City DVD</td>
								<td>\$$price_dvd each</td>
								<td><select name='dvd_qty'>$qty_opt</select></td></tr>
							<tr><td><span style='color:#ff0;font-weight:bold'>NEW! </span>Rhythm City Junior DVD</td>
								<td>\$$price_dvdjr each</td>
								<td><select name='dvdjr_qty'>$qty_opt</select></td></tr>
							<tr><td>Rhythm City CD</td>
								<td>\$$price_cd each</td>
								<td><select name='cd_qty'>$qty_opt</select></td></tr>
						</tbody>
					</table>
				</td></tr>
				<!-- BOT-TRAP: THIS PART IS MEANT TO STOP SPAM SUBMISSIONS FROM ROBOTS (if you see this section, don't change these fields) -->
					<tr class='bot-trap'><th>Robot Questions</th><td><textarea name='questions' rows='6' cols='40'></textarea></td></tr>
					<tr class='bot-trap'><th>Robot Referral</th><td><input type='text' name='referral' value=''></td></tr>
					<tr class='bot-trap'><th>Robot Website</th><td><input type='text' name='website' value='http://'></td></tr>
				<!-- /BOT-TRAP -->
				<tr><th><label for='coupon'>Coupon Code:</label></th><td><input type='text' name='coupon' id='coupon' maxlength='$max_coupon'><img src='http://admin.citiesunlimited.com/images/icons/16/actions/button_accept.png' alt='ACCEPTED!' id='checkmark' title='Coupon Code Accepted!'><img src='http://admin.citiesunlimited.com/images/icons/16/actions/button_cancel.png' alt='INVALID COUPON CODE' id='redx' title='Invalid Coupon Code'></td></tr>
				<tr><th>Comments</th><td><textarea name='comments' rows='6' cols='40'></textarea></td></tr>
				<tr id='jr_row'>
					<th><label for='want_jr'><img src='images/rhythm_city_logo_junior-125.png' alt='Rhythm City Junior'></label></th>
					<td><input type='checkbox' name='want_jr' id='want_jr' value='1'> <label for='want_jr'>Check this box if you are primarily interested in <strong>Rhythm City Junior</strong>, the 1-hour, simplified version of <em>Rhythm City</em> available for Middle School and Youth Theatre.</label></td></tr>
				<tr><th></th><td><input type='submit' value='Review Your Order' name='submit' id='submit'></td></tr>
			</table>
		</form>";
}
?>
	</section>
</section>
<?=$footer;?>
</body>
</html>
