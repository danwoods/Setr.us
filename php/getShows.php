<?php

//turn on error reporting, set header
header('Content-Type: text/xml');
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

include('db/db_login.php');

//pull variables
$artistYear = $_GET['artistYear'];
$artistAbb = substr($artistYear, 0, -4);;
$year = substr($artistYear, -4);
$show_array = array();

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

//request a listing of all songs grouped by artist and store results in $result
$result = mysql_query("SELECT * FROM artists WHERE abb = '$artistAbb'");
//if no result
if(!$result){
  //stop, and display error message
  die(mysql_error());
}

//check if any results were returned
if(mysql_num_rows($result) > 0){
  //if yes cycle through all results, checking their artist
  while($row = mysql_fetch_array($result)){
    //check if the current songs artist is in the artist array
    $artist = mysql_real_escape_string($row['name']);
        }//end while
    }//end if
    
//request a listing of all songs grouped by artist and store results in $result
$result = mysql_query("SELECT * FROM shows WHERE artist = '$artist' AND year = '$year'");
//if no result
if(!$result){
  //stop, and display error message
  die(mysql_error());
}

//check if any results were returned
if(mysql_num_rows($result) > 0){
  //if yes cycle through all results, checking their artist
  while($row = mysql_fetch_array($result)){
    //check if the current songs artist is in the show array
    $show_array[] = $row['date'];
    //echo $row['unique_show_id'];
        }//end while
    }//end if

//return the array of artist as xml
$dom = new DOMDocument();
$response = $dom->createElement('response');
$dom->appendChild($response);

//NOTE: the-1 behind count is to accomodate an empty element created because of a trailing plus mark left by 
//the script which compiles all the years. I should fix that one
for($j = 0; $j < count($show_array); $j++){
$showElement = $dom->createElement('element');
$showText = $dom->createTextNode($show_array[$j]);
$showElement->appendChild($showText);

$response->appendChild($showElement);
}//end for

$xmlString = $dom->saveXML(); 
echo $xmlString;
?>
