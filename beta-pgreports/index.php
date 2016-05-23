<?php include('/usr/local/cpanel/base/frontend/paper_lantern/rspsql/header.php'); ?>

<div id="content" class="container">

<h1>
<span id="icon-postgresql_mini" class="spriteicon_img_mini"></span>
<span id="pageHeading">PostgreSQL Reports</span>
</h1>

<p id="descMysql" class="description">
    Below are some basic PostgreSQL database and table reports. More comprehensive reports can be run via phpPgAdmin as well as via PgSQL.
</p>






<?php

//include_once('/usr/local/cpanel/base/frontend/paper_lantern/rscommon/common-encoded-141114-133939.php');

define('PG_DBUSER','postgres');
define('PG_DBPASS', 'Dafna1836');
define('PG_HOST','127.0.0.1');
					
//Now process directory and check backups
$uname = $_ENV['REMOTE_USER'];

//Set Environment
putenv('PGPASSWORD='.PG_DBPASS);
putenv('PGUSER='.PG_DBUSER);
putenv('PGHOST='.PG_HOST);


$dbowner= trim($_ENV['REMOTE_USER']);

pg_connect("host=".PG_HOST." user=".PG_DBUSER." password=".PG_DBPASS) or die('can not connect to server');

//Now get all database and their size for a user
$sql = "SELECT pg_database.datname, pg_size_pretty(pg_database_size(pg_database.datname)) FROM pg_database, pg_user WHERE pg_database.datdba = pg_user.usesysid AND pg_user.usename = '$dbowner'";

$result = pg_query($sql);
?>

<p class="description"></p><br />



<table id="sql_db_tbl" class="sortable table table-striped">
		<thead>
            <tr>
                <th class="cell" scope="col">Database</th>
                <th class="cell" scope="col">Size on Disk</th>
                <th class="cell sorttable_nosort" scope="col">Table Info</th>
	        <th class="cell sorttable_nosort" scope="col">Table I/O</th>
	        <th class="cell sorttable_nosort" scope="col">Cache Ratio</th>
	        <th class="cell sorttable_nosort" scope="col">History</th>

                            </tr>
		</thead>
  <tbody>


	<?php
if($result){
	while ($row = pg_fetch_row($result)) {
		echo '<tr>';
		echo '<td>'.$row[0].'</td><td>'.$row[1].'</td><td align="center"><a href="tables.php?dbname='.base64_encode($row[0]).'"><img src="images/icons/plix-16/black/graph3-16.png"></a></td>';	
		
		echo '<td align="center"><a href="tables-drill.php?dbname='.base64_encode($row[0]).'"><img src="images/icons/plix-16/black/graph3-16.png"></a></td>';
		//echo '<td><a href="current-queries.php?dbname='.base64_encode($row[0]).'"><img src="data/images/icons/icon_search.png"></a></td>';
		echo '<td align="center"><a href="cache-ratio.php?dbname='.base64_encode($row[0]).'"><img src="images/icons/plix-16/black/graph3-16.png"></a></td>';
		
		//echo '<td><a href="processes.php?dbname='.base64_encode($row[0]).'"><img src="data/images/icons/icon_search.png"></a></td>';
		
		echo '<td align="center"><a href="table-history.php?dbname='.base64_encode($row[0]).'"><img src="images/icons/plix-16/black/graph3-16.png"></a></td>';
		
		echo '</tr>';
	}
}else{
	echo "<tr td colspan='3'>No Database Exists</tr>";
}
?>

		</tbody>
		
	</table>

                                       
                                	
<div class="return-link"><a href="../index.html">&larr; <cptext "Go Back"></a></div>
</div>
<?php include('/usr/local/cpanel/base/frontend/paper_lantern/rspsql/footer.php'); ?>