
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

mysql_query("UPDATE Persons SET Age = '36'
WHERE FirstName = 'Peter' AND LastName = 'Griffin'");

mysql_close($con);

$sql= mysql_query("UPDATE playlist SET name = '$playlistNewName'
				WHERE name = '$playlistOldName' AND username = '$username'");

				//check to see if the query went through
				if (!mysql_query($sql,$con)){
  					die('Error: ' . mysql_error());
          }
          
          mysql_close($con);//close mysql connection
?>
