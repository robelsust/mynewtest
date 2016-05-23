<?php

if ($_POST['sulik'] != 'wau55UGCy75VbiuUN5X586bkacCNuLYy') exit;

$from = intval($_POST['sulfd']);
$to = intval($_POST['sultd']);
$v = intval($_POST['sulv']);
$u = intval($_POST['sulu']);

if (($from == 0) || ($to == 0)) exit;

require_once('../../../../wp-config.php');

$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db(DB_NAME);

	$r = mysql_query("DELETE FROM `user_availability` WHERE available_date<SUBDATE(NOW(), INTERVAL 1 DAY)");

if ($v == 1) {
	$r = mysql_query("DELETE FROM `user_availability` WHERE user=$u");
	$r = mysql_query("DELETE FROM `user_info` WHERE uid=$u LIMIT 1");
}

if ($from > $to) list($from, $to) = array($to, $from);

$r = mysql_query("SELECT `user`, UNIX_TIMESTAMP(`available_date`), `user_info`.age FROM `user_availability`,`user_info` WHERE `available_date`>=$from AND `available_date`<=$to and `user_info`.uid=`user_availability`.user ORDER BY `available_date` asc, stap1 DESC,StapB1 DESC,StapB2 DESC,StapB3 DESC,stap2 DESC,stap3 DESC,stap4 DESC,StapBW DESC");
while ($row = mysql_fetch_row($r)) {
	if (is_array($dates[$row[1]])) { if (!in_array($row[0], $dates[$row[1]])) $dates[$row[1]][] = $row[0]; }
	else $dates[$row[1]][] = $row[0];
	if (is_array($users)) { if (!in_array($row[0], $users)) $users[] = $row[0]; }
	else $users[] = $row[0];
}

if (is_array($users)) {
	$users_sql = implode(',', $users);
	$r = mysql_query("SELECT * FROM  `user_info` WHERE uid IN ($users_sql) ORDER BY stap1 DESC,StapB1 DESC,StapB2 DESC,StapB3 DESC,stap2 DESC,stap3 DESC,stap4 DESC,StapBW DESC");
	while ($row = mysql_fetch_row($r)) {
		$user_info[$row[0]]['email'] = $row[1];
		$user_info[$row[0]]['password'] = $row[2];
		$user_info[$row[0]]['last_name'] = $row[4];
		$user_info[$row[0]]['first_name'] = $row[5];
		$user_info[$row[0]]['phone'] = $row[6];
		$user_info[$row[0]]['age'] = $row[7];
		$user_info[$row[0]]['traveltime'] = $row[8];
		$user_info[$row[0]]['message'] = nl2br(trim($row[23]));
		$user_info[$row[0]]['register_date'] = $row[24];
		unset($languages);
		if ($row[9]) $languages[] = 'NL';
		if ($row[10]) $languages[] = 'FR';
		if ($row[11]) $languages[] = 'EN';
		if (is_array($languages)) $user_info[$row[0]]['languages'] = implode(' - ', $languages);
		unset($steps);
		if ($row[12]) $steps[] = '1';
                if ($row[16]) $steps[] = 'B1';
		if ($row[17]) $steps[] = 'B2';
		if ($row[18]) $steps[] = 'B3';
		if ($row[13]) $steps[] = '2';
		if ($row[14]) $steps[] = '3';
		if ($row[15]) $steps[] = '4';
                if ($row[19]) $steps[] = 'BW';
		if ($row[20]) $steps[] = 'P';
		//if ($row[21]) $steps[] = '<span title="'.$row[21].'+'.$row[22].'">B</span>';
		if (is_array($steps)) $user_info[$row[0]]['steps'] = implode(' - ', $steps);
	}
}
mysql_close($db);

if (is_array($dates)) foreach ($dates as $date => $val) {
$fdate = date('l, dS F', $date+43200);
echo <<<USERLIST
<div class="login-section">
<div class="login-section-title">
$fdate
</div>
</div>
<table class="login-ls">
<tr><td>Name</td>
    <td>Mail address</td>
    <td>Phone</td>
    <td>Age</td>
    <td>Distance</td>
    <td>Steps</td>
    <td>Languages</td></tr>
USERLIST;
foreach ($val as $user) echo <<<USER
<tr>
    <td><span onclick="login('{$user_info[$user]['email']}', 'wau55UGCy75VbiuUN5X586bkacCNuLYy{$user_info[$user]['password']}');">{$user_info[$user]['last_name']}, {$user_info[$user]['first_name']}</span> <input type="button" value="X" onclick="if (confirm('Delete {$user_info[$user]['last_name']}, {$user_info[$user]['first_name']}?')) { document.getElementById('sulv').value='1'; document.getElementById('sulu').value='$user'; sul_form(); document.getElementById('sulv').value='0'; }"></td>
            <td>{$user_info[$user]['email']}</td>
            <td>{$user_info[$user]['phone']}</td>
            <td>{$user_info[$user]['age']}</td>
            <td>{$user_info[$user]['traveltime']}</td>
            <td>{$user_info[$user]['steps']}</td>
            <td>{$user_info[$user]['languages']}</td>
</tr>
<tr><td colspan="7"><font style="color:#aaa;"><i>{$user_info[$user]['register_date']}</i></font><br>{$user_info[$user]['message']}</td></tr>
USER;
echo <<<USERLIST
</table>
</div>
</div>
</br>
USERLIST;
}

?>