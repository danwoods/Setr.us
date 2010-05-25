<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
header('Content-Type: text/xml');

//include database credentials
include('../../db/db_login.php');

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

////////////////////////////////////////////////////
//Retrive band info
////////////////////////////////////////////////////


$result = mysql_query("SELECT * FROM artists ORDER BY name ASC");

//error check - see if this is safe to remove later
if(!$result){
  die(mysql_error());
  }

//check if any results were returned
if(mysql_num_rows($result) > 0){
  
  $response = "";
  //echo $response;
  
  while($row = mysql_fetch_array($result)){
    $response = $response . "<option value=\"" . $row['abb'] . "\">" . $row['name'] . "</option>";
  	  }//end while

  }        

echo $response;
//end if
//////////////////////////////////////////

//close database connection
mysql_close($con);//close mysql connection
 


?>
