<?php
error_reporting(E_ALL ^ E_NOTICE);
$email = $_POST['email'];
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	$err++;
	$err_msg .= "Invalid E-Mail address.\r\n";
}
$last_name = $_POST['last_name'];
if (strlen($last_name) < 2) {
	$err++;
	$err_msg .= "Last name too short.\r\n";
}

$first_name = $_POST['first_name'];
if (strlen($first_name) < 2) {
	$err++;
	$err_msg .= "First name too short.\r\n";
}

$phone=$_POST['phone'];
if (strlen($phone) < 2) {
	$err++;
	$err_msg .= "Phone number is too short.\r\n";
}


$password = $_POST['password'];
if (strlen($password) < 6) {
	$err++;
	$err_msg .= "Password is too short.\r\n";
}
$repassword = $_POST['repassword'];
if ($password != $repassword) {
	$err++;
	$err_msg .= "Password does not match.\r\n";
}
$terms = $_POST['check3a'] == 'true' ? true : false;
if (!$terms) {
	$err++;
	$err_msg .= "Please read and accept our terms in order to proceed.\r\n";
}
$mailchimp = $_POST['check3b'] == 'true' ? true : false;

if ($err > 0) echo $err_msg;
else {
	if ($mailchimp) {
		include_once('MailChimp.php');

		$MailChimp = new MailChimp('5e4349ac4ac2db3335665a42a2f7d87d-us7');
		$result = $MailChimp->call('lists/subscribe', array(
			'id' => '10d1db9f55',
			'email' => array('email'=>$email),
			'merge_vars' => array('FNAME'=>$first_name, 'LNAME'=>$last_name),
			'double_optin' => false,
			'update_existing' => true,
			'replace_interests' => false,
			'send_welcome' => false,
		));
	}
	require_once('../../../../wp-config.php');

	$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	mysql_select_db(DB_NAME);

	$sql_email = mysql_real_escape_string($email);
	$sql_last_name = mysql_real_escape_string($last_name);
	$sql_first_name = mysql_real_escape_string($first_name);
	$sql_phone = mysql_real_escape_string($phone);
	require_once('../../../../wp-includes/class-phpass.php');
	$phash = new PasswordHash(8, true);
	$password = $phash->HashPassword($password);
	$sql_password = mysql_real_escape_string($password);

	$r = mysql_query("INSERT INTO `user_info`(`email`, `last_name`, `first_name`, `phone`,`password`) VALUES ('$sql_email', '$sql_last_name', '$sql_first_name', '$sql_phone','$sql_password')");
	$user = mysql_insert_id();
	if (is_array($dates)) foreach ($dates as $date) {
		mysql_query("INSERT INTO `user_availability`(`user`, `available_date`) VALUES ('$user', '$date')");
	}

	mysql_close($db);

		$subject = "Confirmation of your booking at Surf and Fly";
		$message = "Hi $first_name,
		
Thanks for your reservation!
We'll contact you 2 days in advance if the conditions are looking good. You can change your profile page whenever you want (don't forget to update your profile after each lesson). Since we depend on the weather, we can't confirm your lesson more then 2 days in advance. If you made a last minute booking. Please give us a call!

If you have questions, don't hesitate to mail us or to give us a call (if we don't respond, we're in the water, in this case text us).
Please be on the spot 15 minutes in advance and take traffic into account.

Address: De wandelaar 5 - 8301 DUINBERGEN

Kind regards,
Kitesurf.be - 0472/94.99.71";

		$headers = "From: info@kitesurf.be\r\nReply-To: info@kitesurf.be\r\n";

		mail($email, $subject, $message, $headers);
// New Mail(Admin)         
            $subject_ad = "Nieuwe reservatie";
            $message_ad = "  
     $first_name heeft een account aangemaakt.\n
	Naam : $first_name
	E-mail: $email
	GSM nummer: $phone
        ";
       $headers = "From: info@kitesurf.be\r\nReply-To: info@kitesurf.be\r\n";
     mail('info@kitesurf.be', $subject_ad, $message_ad, $headers);

 echo 'OK';
}

?>