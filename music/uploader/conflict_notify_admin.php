<?php
//turn on error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
//pull variables
$show_id = $_GET['show_id'];
$mess = $_GET['mess'];
$uploader = $_GET['uploader'];

//connect with database
$con = mysql_connect("localhost","root","");

//if no connection
if(!$con){
  //stop, and display error
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("admin", $con);
//end connecting to database

//create record of conflict
$sql = "INSERT INTO upload_conflict (datetime, `show`, message, user) VALUES (NOW(), '$show_id', '$mess', '$uploader')";

//check to see if the query went through
  if (!mysql_query($sql, $con)){
    echo "query fail";
  	die('Error: ' . mysql_error());
  }
  
mysql_close($con);//close mysql connection
?>
