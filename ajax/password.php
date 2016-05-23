<?php

require_once('../../../../wp-config.php');

$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db(DB_NAME);

if ($_POST['a'] == 'req') {
	$alnum = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	for ($i=0; $i<32; $i++) $alnumrand .= $alnum[mt_rand(0, strlen($alnum)-1)];
	$email = $_POST['email'];
	$sql_email = mysql_real_escape_string($email);
	$r = mysql_query("UPDATE `user_info` SET reset_pass='$alnumrand' WHERE email='$sql_email' LIMIT 1");
	$users = mysql_affected_rows();
	if ($users > 0) {
		$subject = 'New password';
		$message = 'We have received a request to reset your account password at the kitesurf.be . If you believe you have received this message in error, you may disregard it.

To reset your password, click the link below or copy and paste the link into your browser location bar:

http://kitesurf.be/wp-content/themes/risen/ajax/password.php?email='.urlencode($email).'&vc='.urlencode($alnumrand).'&a=csc

Regards,
Kitesurf.be';
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= "Content-type:text/html; charset=UTF-8\r\n";
		$headers .= "From: info@kitesurf.be\r\nReply-To: info@kitesurf.be\r\n";

		mail($email, $subject, $message, $headers);
	}
	echo 'OK';
}
elseif ($_GET['a'] == 'csc') {
	setcookie('reset_email', $_GET['email'], time()+7200);
	setcookie('reset_value', $_GET['vc'], time()+7200);
	header('Location: http://kitesurf.be/password');
}
elseif ($_POST['a'] == 'stp') {
	$password = $_POST['password'];
	if (strlen($password) < 6) {
		$err++;
		$err_msg .= "- Password too short.\r\n";
	}
	$repassword = $_POST['repassword'];
	if ($password != $repassword) {
		$err++;
		$err_msg .= "- Passwords do not match.\r\n";
	}
	if ($err > 0) echo $err_msg;
	else {
		require_once('../../../../wp-includes/class-phpass.php');
		$phash = new PasswordHash(8, true);
		$password = $phash->HashPassword($password);
		$sql_password = mysql_real_escape_string($password);
		$email = $_COOKIE['reset_email'];
		$sql_email = mysql_real_escape_string($email);
		$r = mysql_query("UPDATE `user_info` SET password='$sql_password' WHERE email='$sql_email' LIMIT 1");
		echo $_COOKIE['reset_email'];
	}
}

mysql_close($db);

?>