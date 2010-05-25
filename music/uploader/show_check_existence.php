
<?php

//allow out buffering
ob_start();

//turn on error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

//pull variables
$show_id = $_GET['show_id'];

//connect with database
$con = mysql_connect("localhost","root","");

//if no connection
if(!$con){
  //stop, and display error
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("musicneverstopped", $con);
//end connecting to database

//request a listing of all shows with same id as the one about to be uploaded
$result = mysql_query("SELECT * FROM shows WHERE unique_show_id = '$show_id'");

//if no result
if(!$result){
  //stop, and display error message
  die(mysql_error());
}

//check if any results were returned
if(mysql_num_rows($result) > 0)
  echo 1;
else
  echo 0;
    
?>
