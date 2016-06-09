<?php
ini_set("log_errors", 1);
ini_set("error_log", "php-error.log");
error_log( "Hello, errors!" );

include_once('simple_html_dom.php');
include_once('config.php');

$time_start = microtime(true);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medicaredb";

/*$servername = "10.0.0.1";
$username = "root";
$password = "seraph23";
$dbname = "medicare_pbs"; */


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

//reset database table
$sql = "TRUNCATE TABLE pbs_items";
if (!mysqli_query($conn, $sql)) {
	die("Error truncating table: " . mysqli_error($conn));
}  

if (file_exists("result.txt")) unlink("result.txt");
if (file_exists("failed.txt")) unlink("failed.txt"); 

$total = 0;
$total_loops = 1;
$total_loop_rows = 0;
foreach($locations as $loc=>$loc_name){
	foreach($atc3_codes2 as $atc3){
		
		//if( ($loc=='201' && $atc3=='L04A') || ($loc=='202' && $atc3=='L04A') || ($loc=='206' && $atc3=='L04A') || ($loc=='301' && $atc3=='L04A') || ($loc=='402' && $atc3=='L04A') || ($loc=='501' && $atc3=='L04A') || ($loc=='601' && $atc3=='V07A')) {
			//if($total_loops > 2) break 2;
			
			echo PHP_EOL.Date("M d, Y H:i:s")." Current Loop Number: ".$total_loops.", Current Location Code: ".$loc.", Current ATC3 Code: ".$atc3.", Previous Loop Rows : ".$total_loop_rows."\r";
			
			$url = "http://medicarestatistics.humanservices.gov.au/statistics/do.jsp?_PROGRAM=/statistics/mcl_pbs_item_report&WHERE=$loc&SCHEME=PBS&RPT_FMT=by%20time%20period&PTYPE=quarter&START_DT=$start_date&END_DT=$end_date&DRILL=on&GROUP=$atc3";
			// $url = "http://medicarestatistics.humanservices.gov.au/statistics/do.jsp?_PROGRAM=/statistics/mcl_mbs_item_report&WHERE=$loc&BTOS=99&SCHEME=MBS&RPT_FMT=without%20time%20period&PTYPE=quarter&START_DT=$start_date&END_DT=$end_date&DRILL=on&GROUP=$atc3";
			
			$html = file_get_html($url);
			
			$i=1;
			$th1 = "";
			$th2 = "";
			$th3 = "";
			$total_loop_rows = 0;
			
			if($html==false){
				//failed.txt
				$failedfile = fopen("failed.txt", "a+");
				fwrite($failedfile, PHP_EOL."getaddrinfo failed on Loop Number: ".$total_loops." : ".$url);
				fclose($failedfile);
			}
			
			$total_loops++;
			if($html!=false) foreach($html->find('table.table tr') as $row) {

				
				/* --------First 3 TD's------------- */
				if($i==2){
					$first_td1 = $row->find('td',0);
					$first_td2 = $row->find('td',1);
					$first_td3 = $row->find('td',2);
				}
				
				/* --------TH------------- */
				
				if($row->find('th',0)!=null && $row->find('th',1)!=null && $row->find('th',2)!=null && $row->find('th',3)!=null){ //4 th
					$th1 = $row->find('th',0);
					$th2 = $row->find('th',1);
					$th3 = $row->find('th',3);
				}elseif($row->find('th',0)!=null && $row->find('th',1)!=null && $row->find('th',2)!=null && $row->find('th',3)==null){ //3 th
					$th1 = $row->find('th',0);
					$th2 = $row->find('th',1);
					$th3 = $row->find('th',2);
				}elseif($row->find('th',0)!=null && $row->find('th',1)!=null && $row->find('th',2)==null && $row->find('th',3)==null){ //2 th
					$th2 = $row->find('th',0);
					$th3 = $row->find('th',1);
				}elseif($row->find('th',0)!=null && $row->find('th',1)==null && $row->find('th',2)==null && $row->find('th',3)==null){ //1 th
					$th3 = $row->find('th',0);
				}
				
				/* --------TD------------- */
				
				if($row->find('td',0)!=null){
					$td1 = $row->find('td',0);
				}else{
					$td1 = "";
				}
				if($row->find('td',1)!=null){
					$td2 = $row->find('td',1);
				}else{
					$td2 = "";
				}
				if($row->find('td',2)!=null){
					$td3 = $row->find('td',2);
				}else{
					$td3 = "";
				}
				
				if($i==3){
					//echo "Row $i ( Col 1 = $th1, Col 2 = $th2, Col 3 = $th3, Col 4 = $first_td1, Col 5 = $first_td2, Col 6 = $first_td3 )<br>";
					
					$sql = "INSERT INTO pbs_items (location_code,location,atc3_code,item_code,item_description,quarter,item,benefit,practitioner) VALUES('$loc','$loc_name','$atc3','".strip_tags($th1)."','".strip_tags($th2)."','".strip_tags($th3)."','".str_replace('*','0',str_replace(',','',strip_tags($first_td1)))."','".str_replace('*','0',str_replace(',','',strip_tags($first_td2)))."','".str_replace('*','0',str_replace(',','',strip_tags($first_td3)))."')";
					//echo $sql;
					if (!mysqli_query($conn, $sql)) {
						echo "Error inserting on table: " . mysqli_error($conn);
					} 
					$total++;
					$total_loop_rows++;
				}
				if($i>3){
					//echo "Row $i ( Col 1 = $th1, Col 2 = $th2, Col 3 = $th3, Col 4 = $td1, Col 5 = $td2, Col 6 = $td3 )<br>";
					
					$sql = "INSERT INTO pbs_items (location_code,location,atc3_code,item_code,item_description,quarter,item,benefit,practitioner) VALUES('$loc','$loc_name','$atc3','".strip_tags($th1)."','".strip_tags($th2)."','".strip_tags($th3)."','".str_replace('*','0',str_replace(',','',strip_tags($td1)))."','".str_replace('*','0',str_replace(',','',strip_tags($td2)))."','".str_replace('*','0',str_replace(',','',strip_tags($td3)))."')";
					//echo $sql;
					if (!mysqli_query($conn, $sql)) {
						echo "Error inserting on table: " . mysqli_error($conn);
					}
					$total++;
					$total_loop_rows++;
				}
				
				$i++;
			}
		//}
		
	}
}

$time_end = microtime(true);

//dividing with 60 will give the execution time in minutes other wise seconds
$execution_time = ($time_end - $time_start)/60;



//execution time of the script
$total_exe = 'Total Execution Time: '.$execution_time.' Mins, ';
$total_rows = 'Total Rows Transfered: '.$total.', ';
$total_loops = 'Total Loops: '.$total_loops;

echo PHP_EOL."Scraping process completed. See result.txt for details.";

$myfile = fopen("result.txt", "w");
fwrite($myfile, $total_exe);
fwrite($myfile, $total_rows);
fwrite($myfile, $total_loops);
fwrite($myfile, "\n");
fclose($myfile);
?>
