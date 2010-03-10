<?php

//turn on error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
header('Content-Type: text/xml');

//pull variables
//need to do some error checking here
$songId = $_GET['songId'];
$time = $_GET['time'];

//connect with database
$con = mysql_connect("localhost","root","");
if(!$con){
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("musicneverstopped", $con);
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
