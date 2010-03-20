<?php

//turn on error reporting, set header
header('Content-Type: text/xml');
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

include('db/db_login.php');

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
$artist_array = array();

//request a listing of all songs grouped by artist and store results in $result
$result = mysql_query("SELECT * FROM artists ORDER BY name ASC");

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
    if(!in_array($row['name'], $artist_array)){
      //if no, store this artist in $artist_array
  	      $artist_array[] = $row['name'];
          }//end if
        }//end while
    }//end if

//return the array of artist as xml
$dom = new DOMDocument();
$response = $dom->createElement('response');
$dom->appendChild($response);


for($j = 0; $j < count($artist_array); $j++){
$artistElement = $dom->createElement('element');
$artistText = $dom->createTextNode($artist_array[$j]);
$artistElement->appendChild($artistText);

$response->appendChild($artistElement);
}//end for

$xmlString = $dom->saveXML(); 
echo $xmlString;
?>
