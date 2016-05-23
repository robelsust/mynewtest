<?php
session_start();

include_once('/usr/local/cpanel/base/frontend/paper_lantern/rscommon/common-encoded-141114-133939.php');

//GET DATABASE
$dbname = $_GET['dbname'];
if($dbname!=''){
	$dbname = base64_decode($dbname);
}
if(!$dbname){
	header("Location:dbs.php");
	exit;
}

$dbowner= trim($_ENV['REMOTE_USER']);

pg_connect("host=".PG_HOST." user=".PG_DBUSER." password=".PG_DBPASS." dbname=".$dbname) or die('can not connect to server');

//Now get all  tables and their size for a user
$sql="SELECT relname, n_tup_ins, n_tup_upd, n_tup_del, last_vacuum, last_analyze, last_autovacuum, last_autoanalyze FROM pg_stat_user_tables";

$result = pg_query($sql);









?>
<?php include('/usr/local/cpanel/base/frontend/paper_lantern/rspsql/header.php'); ?>

<div id="content" class="container">

<h1>
<span id="icon-postgresql_mini" class="spriteicon_img_mini"></span>
<span id="pageHeading">PostgreSQL Reports</span>
</h1>

<p id="descMysql" class="description">
    Table History
</p>





<table id="sql_db_tbl" class="sortable table table-striped">
		<thead>
            <tr>
                <th class="cell" scope="col">Table</th>
	<th class="cell" scope="col">Inserts</th>
	<th class="cell" scope="col">Updates</th>
        <th class="cell" scope="col">Delete</th>
        <th class="cell" scope="col">Last Vac</th>
        <th class="cell" scope="col">Last Analyze</th> 
        <th class="cell" scope="col">Last Auto Vac</th>
        <th class="cell" scope="col">Last Auto Analyze</th>

                            </tr>
		</thead>
  <tbody>
  
  
  
  
  


	<?php
if($result){
	while ($row = pg_fetch_row($result)) {
		echo '<tr>';
		echo '<td>'.$row[0].'</td><td>'.$row[1].'</td><td>'.$row[2].'</td><td>'.$row[3].'</td>';	
		echo '<td>'.$row[4].'</td>';
		echo '<td>'.$row[5].'</td>';
		echo '<td>'.$row[6].'</td>';
		echo '<td>'.$row[7].'</td>';
		echo '</tr>';
	}
}else{
	echo "<tr td colspan='3'>No Database Exists</tr>";
}
?>

		</tbody>
		
	</table>

<div class="return-link"><a href="index.php">&larr; <cptext "Go Back to Database List"></a></div>                                       
                                	
<div class="return-link"><a href="../index.html">&larr; <cptext "Go Back"></a></div>
</div>
<?php include('/usr/local/cpanel/base/frontend/paper_lantern/rspsql/footer.php'); ?>