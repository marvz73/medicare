<?php




$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medicaredb"; 


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// //reset database table
// $sql = "TRUNCATE TABLE pbs_items";
// if (!mysqli_query($conn, $sql)) {
// 	die("Error truncating table: " . mysqli_error($conn));
// } 


$sql = "SELECT * FROM pbs_items_etx";
				
$query = mysqli_query($conn, $sql) or die(mysql_error());


while ( $row = mysqli_fetch_array($query) ) {


// $sql = "INSERT INTO pbs_items (location_code,location,atc3_code,item_code,item_description,quarter,item,benefit,practitioner)"

// $sql = "INSERT INTO pbs_items (location_code,location,atc3_code,item_code,item_description,quarter,item,benefit,practitioner) VALUES('$loc','$loc_name','$atc3','".strip_tags($th1)."','".strip_tags($th2)."','".strip_tags($th3)."','".str_replace('*','0',str_replace(',','',strip_tags($first_td1)))."','".str_replace('*','0',str_replace(',','',strip_tags($first_td2)))."','".str_replace('*','0',str_replace(',','',strip_tags($first_td3)))."')";

$location_code = $row['location_code'];
$location = $row['location'];
$atc3_code = $row['atc3_code'];
$item_code = $row['item_code'];
$item_description = $row['item_description'];
$quarter = $row['quarter'];
$item = $row['item'];
$benefit = $row['benefit'];
$practitioner = $row['practitioner'];

$sql = "INSERT INTO pbs_items_main (location_code, location, atc3_code, item_code, item_description, quarter, item, benefit, practitioner) VALUES('$location_code', '$location', '$atc3_code', '$item_code', '$item_description', '$quarter', '$item', '$benefit', '$practitioner')";



// $sql = "INSERT INTO test_table (location_code, location_name)  VALUES('$location_code','$location')";



if (!mysqli_query($conn, $sql)) {
	echo "Error inserting on table: " . mysqli_error($conn);
}


}






// $filename = 'failed.txt';
// if ($filename) {


// function in_array_r($needle, $haystack, $strict = false) {
//     foreach ($haystack as $item) {
//         if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
//             return true;
//         }
//     }

//     return false;
// }

// $contents = file($filename);
// $faildScrap = array();
// foreach($contents as $line) {


// 	array_push($faildScrap, array(substr(trim($line), -105, 3), substr(trim($line), -4)));

// }

// 	if(in_array_r("402", $faildScrap))
// 	{
// 		echo "Found <br>";
// 	}

// print_r($faildScrap);

// }