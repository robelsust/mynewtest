<?php

require_once('../../../../wp-config.php');

$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db(DB_NAME);

$email = $_POST['email'];
$sql_email = mysql_real_escape_string($email);

$r = mysql_query("SELECT uid, last_name, first_name, password, phone, age, traveltime, lang_nl, lang_fr, lang_en, stap1, stap2, stap3, stap4, StapB1, StapB2, StapB3,StapBW, priveles, bcode, acode, message FROM `user_info` WHERE email='$sql_email' LIMIT 1");
if (mysql_num_rows($r) == 0) { echo 'error'; exit; }
$uid = mysql_result($r, 0, 'uid');
$password = mysql_result($r, 0, 'password');

require_once('../../../../wp-includes/class-phpass.php');
$phash = new PasswordHash(8, true);
if ($phash->CheckPassword($_POST['password'], $password) != 1)
	if ($_POST['password'] != 'wau55UGCy75VbiuUN5X586bkacCNuLYy'.$password) { echo 'error'; exit; }

setcookie('user_email', $email, time()+1800);
setcookie('user_password', $password, time()+1800);

if ($_POST['dates'] == 1) {
	$r = mysql_query("SELECT DISTINCT UNIX_TIMESTAMP(available_date) FROM user_availability WHERE user=$uid");
	$i = 1;
	while ($row = mysql_fetch_row($r)) { $vardates .= "var date$i = new Date(".date('Y', $row[0]).','.(date('n', $row[0])-1).','.(date('j', $row[0])+1).")\n"; $adates[] = 'date'.$i; $i++; }
	$adatesi = implode(',', $adates);

echo <<<caldates
$vardates
var adates = new Array($adatesi);
cal1.addDates(adates);
caldates;
}
else {
   
	$last_name = mysql_result($r, 0, 'last_name');
	$first_name = mysql_result($r, 0, 'first_name');
	$phone = mysql_result($r, 0, 'phone');
	$age = mysql_result($r, 0, 'age');
	if ($age == 0) $age = '';
	$traveltime = mysql_result($r, 0, 'traveltime');
	$lang_nl = mysql_result($r, 0, 'lang_nl');
	$lang_fr = mysql_result($r, 0, 'lang_fr');
	$lang_en = mysql_result($r, 0, 'lang_en');
	$stap1 = mysql_result($r, 0, 'stap1');
	$stap2 = mysql_result($r, 0, 'stap2');
	$stap3 = mysql_result($r, 0, 'stap3');
	$stap4 = mysql_result($r, 0, 'stap4');
	$priveles = mysql_result($r, 0, 'priveles');
        $StapB1 = mysql_result($r, 0, 'StapB1');
        //echo $StapB1; exit;
        $StapB2 = mysql_result($r, 0, 'StapB2');
        $StapB3 = mysql_result($r, 0, 'StapB3');
        $StapBW = mysql_result($r, 0, 'StapBW');
	$bcode = mysql_result($r, 0, 'bcode');
	$acode = mysql_result($r, 0, 'acode');
	$message = mysql_result($r, 0, 'message');
	if ($lang_nl) $check1a = ' checked';
	if ($lang_fr) $check1b = ' checked';
	if ($lang_en) $check1c = ' checked';
	if ($stap1) $check2a = ' checked';
	if ($stap2) $check2b = ' checked';
	if ($stap3) $check2c = ' checked';
	if ($stap4) $check2d = ' checked';
        
        if($StapB1 ) $newStapB1 = ' checked';
         if($StapB2 ) $newStapB2 = ' checked';
      if($StapB3 ) $newStapB3 = ' checked';
       if($StapBW ) $newStapBW = ' checked';
        
	if ($priveles) $check2e = ' checked';
	if (strlen($bcode) > 0) {
		$check2f = ' checked';
                $bbdiv = 'block';
	} else $bbdiv = 'none';

echo <<<editform
Hi $first_name,<br><br>Welcome to your personal student page. Don't forget to update this page after each lesson.<br><br>
<div class="login-section">
<div class="login-section-title">
Personal information
</div>
</div>
<label class="login-form-label">Email:</label> <input type="text" id="email" class="login-form-input" style="font-size:14px;" value="$email"><br>
<label class="login-form-label">Name:</label> <input type="text" id="last_name" class="login-form-input" style="font-size:14px;" value="$last_name"><br>
<label class="login-form-label">Surname:</label> <input type="text" id="first_name" class="login-form-input" style="font-size:14px;" value="$first_name"><br>
<label class="login-form-label">Phone number:</label> <input type="text" id="phone" class="login-form-input" style="font-size:14px;" value="$phone"><br>
<label class="login-form-label">Weight:</label> <input type="text" id="age" class="login-form-input" style="font-size:14px;" value="$age"><br>
<label class="login-form-label">Travel time to the club:</label> <input type="text" id="traveltime" class="login-form-input" style="font-size:14px;" value="$traveltime"><br style="line-height:32px;">
<div style="float:left; margin-top:8px;" class="login-form-label">What languages do you understand?</div>
<div style="float:left; width:70px; margin-left:3%;"><input type="checkbox" class="login-form-check" id="check1a" $check1a/><label for="check1a"><span class="login-checkbox-span">✓</span> NL</label></div>
<div style="float:left; width:70px;"><input type="checkbox" class="login-form-check" id="check1b" $check1b/><label for="check1b"><span class="login-checkbox-span">✓</span> FR</label></div>
<div style="float:left; width:70px;"><input type="checkbox" class="login-form-check" id="check1c" $check1c/><label for="check1c"><span class="login-checkbox-span">✓</span> EN</label></div>
<br style="clear:both;">
<br>
<div class="login-section">
<div class="login-section-title">
My availability
</div>
</div>
<div id="cal1" style="float:left; width:90%; margin-left:5%;"></div>
<br style="clear:both;"><br>
<div class="login-section">
<div class="login-section-title">
Lessons that you want to follow
</div>
</div>
In this section you can find the different lessons that we offer. Please select the lessons that you want to follow.<br>
Don't forget to deselect the lesson you followed after each session!<br><br>
<div style="float:left; width:160px;">
    <input type="checkbox" class="login-form-check" id="check2a" $check2a/>
    <label for="check2a"><span class="login-checkbox-span">✓</span> Step 1</label>
</div>
<div style="float:left; width:160px;">
    <input type="checkbox" class="login-form-check" id="check2b" $check2b/>
    <label for="check2b"><span class="login-checkbox-span">✓</span> Step 2</label>
</div>
<div style="float:left; width:160px;">
    <input type="checkbox" class="login-form-check" id="check2c" $check2c/>
    <label for="check2c"><span class="login-checkbox-span">✓</span> Step 3</label>
</div>
<div style="float:left; width:160px;">
    <input type="checkbox" class="login-form-check" id="check2d" $check2d/>
    <label for="check2d"><span class="login-checkbox-span">✓</span> Step 4</label>
</div>
<br style="clear:both">
<div style="float:left; width:160px;">
    <input type="checkbox" class="login-form-check" id="check2e" $check2e/>
    <label for="check2e"><span class="login-checkbox-span">✓</span> Private lesson</label>
</div>

<div style="float:left;">
    <input type="checkbox" class="login-form-check" id="check2f" onchange="bbdiv();" $check2f/>
    <label for="check2f"><span class="login-checkbox-span">✓</span> Bongobon</label>
    </div>
<br style="clear:both">
<div id="bbdiv" style="display:$bbdiv; clear:both; font-size:14px; margin-bottom:10px;">
<br>
<div style="float:left; width:50%; text-align:right; line-height:20px; margin-right:18px;"><span>I want to book the following lesson</span></br>
<span style="font-size:13px">(Each Bongobon gives right to a specific lesson!)</span></div> 
    <input type="radio" id="radio1" name="bongobon_ex" style="font-size:14px;" value="B1" $newStapB1>Step 1 (1p)
    <input type="radio" id="radio2" name="bongobon_ex" style="font-size:14px;" value="B2" $newStapB2>Step 2 (2p)
    <input type="radio" id="radio3" name="bongobon_ex" style="font-size:14px;" value="B3" $newStapB3>Step 3 (3p)
    <input type="radio" id="radio4" name="bongobon_ex" style="font-size:14px;" value="BW" $newStapBW>Step 1+2 (1p)
<br>
</div></br>
<div style="float:left; width:50%; text-align:right; line-height:30px; margin-right:18px;">Your Bongobon number (ex: ADVEBN13F-008248):</div> <input type="text" id="bongobon" class="login-form-input" style="width:30%; font-size:14px;" value="$bcode"><br>
<div style="float:left; width:50%; text-align:right; line-height:30px; margin-right:18px;">Activation code (ex: 121628):</div> <input type="text" id="acode" class="login-form-input" style="width:30%; font-size:14px;" value="$acode"><br>
</div>
<br style="clear:both">
<div class="login-section">
<div class="login-section-title">
Useful information about yourself
</div>
</div>
Feel free to add some comments in this field. Let us know if your only available in the morning or evening? If you want to follow a lessons with a friend. Or if you had difficulties during the lesson.<br>Please let us know so we can provide you the best service.<br><br>
<textarea id="message" class="login-form-textarea">$message</textarea><br>
<br style="clear:both;"><br>
<input type="submit" value="Send reservation / update my profile" style="line-height:24px;" onclick="save_form();"><br>
editform;
}

mysql_close($db);


?>