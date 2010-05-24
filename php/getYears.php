<?php

//turn on error reporting, set header
header('Content-Type: text/xml');
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

//pull variable
$artist = $_GET['artist'];

include('../db/db_login.php');

//connect with database
$con = mysql_connect($db_host, $db_username, $db_pass);

//if connection unsuccessful
if(!$con){
  //stop, and display error
  die('Could not connect: ' . mysql_error());
  }

//specify database
mysql_select_db($db_database, $con);
//end connecting to database

//declare placeholders
$years = "";
$year_array = array();

//request a listing of all songs grouped by artist and store results in $result
$result = mysql_query("SELECT * FROM artists WHERE abb = '$artist'");

//if no result
if(!$result){
  //stop, and display error message
  die(mysql_error());
}

//check if any results were returned
if(mysql_num_rows($result) > 0){
  //if yes, save 'years'
  while($row = mysql_fetch_array($result)){
    $years = $row['years'];
  }//end while
}//end if

else {
  echo "No results returned";
  }
    
$year_array = explode("+", $years);

//return the array of artist as xml
$dom = new DOMDocument();
$response = $dom->createElement('response');
$dom->appendChild($response);

//NOTE: the-1 behind count is to accomodate an empty element created because of a trailing plus mark left by 
//the script which compiles all the years. I should fix that script
for($j = 0; $j < count($year_array); $j++){
  if($year_array[$j]!== ""){
    $yearElement = $dom->createElement('element');
    $yearText = $dom->createTextNode($year_array[$j]);
    $yearElement->appendChild($yearText);

    $response->appendChild($yearElement);
  }//end if
}//end for

$xmlString = $dom->saveXML(); 
echo $xmlString;
?>
