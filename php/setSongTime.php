<?php

//turn on error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
header('Content-Type: text/xml');

//pull variables
//need to do some error checking here
$songId = $_GET['songId'];
$time = $_GET['time'];

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

$result = mysql_query("SELECT * FROM songs WHERE unique_song_id = '$songId' AND length = 'NULL'");

//error check - see if this is safe to remove later
if(!$result){
  die(mysql_error());
  }

//check if a single result was returned
if(mysql_num_rows($result) == 1){
  
  //echo " secondary query returned results! ";
  mysql_query("UPDATE songs SET length = '$time'
  WHERE unique_song_id = '$songId' AND length = 'NULL'");
  }
?>
