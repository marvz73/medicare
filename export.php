<?php
$fp = fopen('pbs_items.csv', 'w');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medicaredb"; 
/* $servername = "localhost";
$username = "root";
$password = "abc321";
$dbname = "medicare"; */


// Create connection
$conn = mysql_connect($servername, $username, $password) or die(mysql_error());
$db = mysql_select_db($dbname) or die(mysql_error());


$sql = "SELECT location_code,item_code,quarter,item,benefit,practitioner FROM pbs_items";
				
$query = mysql_query($sql) or die(mysql_error());

//add csv heading
$header = array('ML_CODE','PBS_CODE','Quarter','Services','Benefits','Prescribers');
fputcsv($fp, $header, ";");

//add database rows to db				
while ( $row = mysql_fetch_array($query) ) {
	$data = array('ML'.$row['location_code'],$row['item_code'],$row['quarter'],$row['item'],$row['benefit'],$row['practitioner']);
    fputcsv($fp, $data, ";");
}
fclose($fp);
?>