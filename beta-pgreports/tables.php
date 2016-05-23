<?php
session_start();


include_once('/usr/local/cpanel/base/frontend/paper_lantern/rscommon/common-encoded-141114-133939.php');
//GET DATABASE
$dbname = $_GET['dbname'];
if($dbname!=''){
	$dbname = base64_decode($dbname);
}
if(!$dbname){
	header("Location: newtables.php");
	exit;
}

$dbowner= trim($_ENV['REMOTE_USER']);

pg_connect("host=".PG_HOST." user=".PG_DBUSER." password=".PG_DBPASS." dbname=".$dbname) or die('can not connect to server');

//Now get all  tables and their size for a user
$sql="select table_schema, table_name, pg_size_pretty(pg_relation_size( quote_ident( table_schema ) || '.' || quote_ident( table_name ) )) as size, pg_size_pretty(pg_total_relation_size( quote_ident( table_schema ) || '.' || quote_ident( table_name ) )) as total_size  from information_schema.tables where table_type = 'BASE TABLE' and table_schema not in ('information_schema', 'pg_catalog') order by pg_relation_size( quote_ident( table_schema ) || '.' || quote_ident( table_name ) ) desc, table_schema, table_name";

$result = pg_query($sql);
?>

<?php include('/usr/local/cpanel/base/frontend/paper_lantern/rspsql/header.php'); ?>

<div id="content" class="container">

<h1>
<span id="icon-postgresql_mini" class="spriteicon_img_mini"></span>
<span id="pageHeading">PostgreSQL</span>
</h1>

<p id="descMysql" class="description">
    Table Information
</p>






<table id="sql_db_tbl" class="sortable table table-striped">
		<thead>
            <tr>
                <th class="cell" scope="col">Table Schema</th>
	<th class="cell" scope="col">Table Name</th>
	<th class="cell" scope="col">Size</th>
        <th class="cell" scope="col">Total Size</th>

                            </tr>
		</thead>
  <tbody>


	<?php
if($result){
	while ($row = pg_fetch_row($result)) {
		echo '<tr>';
		echo '<td>'.$row[0].'</td><td>'.$row[1].'</td><td>'.$row[2].'</td><td>'.$row[3].'</td>';	
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