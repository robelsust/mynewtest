<?php

require_once('../../../../wp-config.php');

$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db(DB_NAME);

$login_email = mysql_real_escape_string($_COOKIE['user_email']);
$login_password = mysql_real_escape_string($_COOKIE['user_password']);

if ((strlen($login_email) < 3) || (strlen($login_password) < 3)) { echo 'User not found'; exit; }

$r = mysql_query("SELECT uid FROM `user_info` WHERE email='$login_email' AND password='$login_password' LIMIT 1");
if (mysql_num_rows($r) == 0) { echo 'User not found'; exit; }



 $uid = mysql_result($r, 0, 'uid');
$email = $_POST['email'];
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	$err++;
	$err_msg .= "- Invalid E-Mail address.\r\n";
}
$last_name = $_POST['last_name'];
if (strlen($last_name) < 2) {
	$err++;
	$err_msg .= "- Last name too short.\r\n";
}
$first_name = $_POST['first_name'];
if (strlen($first_name) < 2) {
	$err++;
	$err_msg .= "- First name too short.\r\n";
}

$phone = $_POST['phone'];
$age = intval($_POST['age']);
$traveltime = $_POST['traveltime'];
$lang_nl = $_POST['check1a'] == 'true' ? true : false;
$lang_fr = $_POST['check1b'] == 'true' ? true : false;
$lang_en = $_POST['check1c'] == 'true' ? true : false;

if($lang_nl=='true') $nl1 ='NL'; 
if($lang_fr=='true') $nl2 ='FR';
if($lang_en=='true') $nl3='ENG';

$lang=$nl1.''.$nl2.', '.$nl3; 
$dates = explode(',', $_POST['cals']);
$stap1 = $_POST['check2a'] == 'true' ? true : false;
$stap2 = $_POST['check2b'] == 'true' ? true : false;
$stap3 = $_POST['check2c'] == 'true' ? true : false;
$stap4 = $_POST['check2d'] == 'true' ? true : false;

$rs_st = array();
if($stap1=='true') { $st1 ='1'; $rs_st[]=1; }
if($stap2=='true') { $st2 ='2'; $rs_st[]=2; }
if($stap3=='true') { $st3 ='3'; $rs_st[]=3; }
if($stap4=='true') { $st4 ='4'; $rs_st[]=4; }
$stap=implode(',',$rs_st); 

$priveles = $_POST['check2e'] == 'true' ? true : false;
$Stap_TMP1= $_POST['radio1']== 'true' ? true : false;
$Stap_TMP2= $_POST['radio2']== 'true' ? true : false;
$Stap_TMP3= $_POST['radio3']== 'true' ? true : false;
$Stap_TMP4= $_POST['radio4']== 'true' ? true : false;
if($Stap_TMP1=='true') $StapB1=1;
if($Stap_TMP2=='true') $StapB2=1;
if($Stap_TMP3=='true') $StapB3=1;
if($Stap_TMP4=='true') $StapBW=1;

if($Stap_TMP1=='true') $B='B1';
if($Stap_TMP2=='true') $B='B2';
if($Stap_TMP3=='true') $B='B3';
if($Stap_TMP4=='true') $B='BW';


$bongobon = $_POST['check2f'] == 'true' ? true : false;
if($bongobon == false){
	$StapB1=$StapB2=$StapB3=$StapBW=0;
	$B='';
}
if($bongobon) if (empty($StapB1) & empty($StapB2) &  empty($StapB3) &  empty($StapBW)){
    $err++;
        $err_msg .= "- Please select one of the 4 Bongobon options.\r\n";
}
if ($bongobon) $bcode = $_POST['bongobon'];
else $bcode = '';
if (($bongobon) && (strlen($bcode) < 3)) {
	$err++;
	$err_msg .= "- Bongobon too short.\r\n";
}
if ($bongobon) $acode = $_POST['acode'];
else $acode = '';
if (($bongobon) && (strlen($acode) < 3)) {
	$err++;
	$err_msg .= "- Activation Code too short.\r\n";
}
$message = $_POST['message'];

if ($err > 0) echo $err_msg;
else {
	$sql_email = mysql_real_escape_string($email);
	$sql_last_name = mysql_real_escape_string($last_name);
	$sql_first_name = mysql_real_escape_string($first_name);
	$sql_phone = mysql_real_escape_string($phone);
	$sql_traveltime = mysql_real_escape_string($traveltime);
	$sql_bcode = mysql_real_escape_string($bcode);
	$sql_acode = mysql_real_escape_string($acode);
	$sql_message = mysql_real_escape_string($message);
        $r = mysql_query("UPDATE `user_info` SET email='$email', last_name='$last_name', first_name='$first_name', phone='$phone', age='$age', traveltime='$traveltime', lang_nl='$lang_nl', lang_fr='$lang_fr', lang_en='$lang_en', stap1='$stap1', stap2='$stap2', stap3='$stap3', stap4='$stap4', StapB1='$StapB1', StapB2='$StapB2', StapB3='$StapB3', StapBW='$StapBW', priveles='$priveles', bcode='$bcode', acode='$acode', message='$message', register_date=register_date WHERE uid=$uid");
        $r = mysql_query("DELETE FROM `user_availability` WHERE user=$uid");
	if (is_array($dates)) {
		$dates = array_unique($dates);
		foreach ($dates as $date) {
			$date_sql = intval($date);
			if ($date_sql > 0) $r = mysql_query("INSERT INTO `user_availability` (`user`, `available_date`) VALUES ('$uid', '$date_sql')");
		}
	}
	echo 'Bedankt voor uw reservatie, uw beschikbare datums zijn naar ons doorgestuurd, dit verzekert u van een plaats in onze planning! 2 dagen op voorhand wordt u door ons gecontacteerd om de les te bevestigen indien er wind is. Vragen? Bel 0470/01.48.69';
        
}

$aval=mysql_query("SELECT `available_date` FROM user_availability WHERE user=$uid ORDER BY available_date ASC");
$avail_data = array(); 
while($val=mysql_fetch_array($aval)){
  $month = date('M', strtotime($val['available_date']));
  $date= date('d', strtotime($val['available_date']));
  $avail_data[$month][]=$date;
}

$txt_m = '';
foreach($avail_data as $mon=>$rsval){
  $txt_m.=$mon.' '.implode(',', $rsval)." ";
}

mysql_close($db);

// New UPDATE Mail(Admin)     
        $date= date("m/d/y");  
        $subject_ad = "Reservatie - update";  
        $message_ad.="Volgende student heeft zijn profiel aangepast:\n\n";
		$message_ad.="Voorneem: $sql_first_name\n";
        $message_ad.="Achternaam: $sql_last_name\n";
        if($B!=''){
        	$message_ad.="Stap:$stap,$B\n";
    	}else{
    		$message_ad.="Stap:$stap\n";
    	}
        $message_ad.="Gsm nummer: $sql_phone\n";
        $message_ad.="Beschikbaarheid: $txt_m\n";
        $message_ad.="Taal: $lang\n";
		$message_ad.="Email: $sql_email\n";
		$message_ad.="Reistijd naar club: $sql_traveltime\n";
        $message_ad.="Gewicht: $age\n";
        $message_ad.="Opmerking: $sql_message\n\n";
        $headers = "From: booking@kitesurf.be\r\nReply-To: booking@kitesurf.be\r\n";
        //mail('booking@kitesurf.be', $subject_ad, $message_ad, $headers,$date);
        mail('booking@kitesurf.be', $subject_ad, $message_ad, $headers);

 //echo 'OK';

?>