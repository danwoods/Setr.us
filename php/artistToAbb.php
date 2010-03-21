<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
header('Content-Type: text/plain'); 

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
$artist = $_GET['artist'];

$result = mysql_query("SELECT * FROM artists WHERE name = '$artist'");

//error check - see if this is safe to remove later
if(!$result){
  die(mysql_error());
  }

//check if any results were returned
if(mysql_num_rows($result) > 0){
  
  while($row = mysql_fetch_array($result)){
    $response =  $row['abb'];
  	  }//end while

  }        

echo $response;

//close database connection
mysql_close($con);//close mysql connection
 

?>
