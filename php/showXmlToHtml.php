<?php

//turn on error reporting, set header
header('Content-Type: text/xml');
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

//pull variables
$show = $_GET['show'];
$show_html = "";

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

//request a listing of all songs grouped by artist and store results in $result
$result = mysql_query("SELECT * FROM shows WHERE unique_show_id = '$show'");
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
    $show_xml = ($row['show_xml']);
        }//end while
    }//end if


//$xml = simplexml_load_string($show_xml);
//echo '<span>';

//$test = $xml->getName();

//echo '<div id="returned"><p>'.$test.'<br /><span>Span2</span></p></div>';

echo $show_xml;

//echo '</span>';
//echo $show_xml;
?>
