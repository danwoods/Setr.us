<?php

#THIS DOES NOT CHECK FOR ERRORS!!!#

//turn on error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
header('Content-Type: text/xml');

//pull variables
//need to do some error checking here
$username = $_GET['username'];
$playlistOldName = $_GET['oldPlaylistTitle'];
$playlist = $_GET['playlist'];
$playlistNewName = $_GET['newPlaylistTitle'];
$dateTime = getdate();
$mySqlDateTime = $dateTime['year'] . '-' . $dateTime['month'] . '-' . $dateTime['mday'] . ' ' . $dateTime['hours'] . ':' . $dateTime['minutes'] . ':' . $dateTime['seconds'];

//$playlistOldName = $username . '_' . $playlistOldName;
//$playlistNewName = $username . '_' . $playlistNewName;

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

$sql= "INSERT INTO playlist (username, name, songs, dateTime)
				VALUES('$username','$playlistNewName','$playlist', NOW())";

				//check to see if the query went through
				if (!mysql_query($sql,$con)){
  					die('Error: ' . mysql_error());
          }
          
          mysql_close($con);//close mysql connection
?>
