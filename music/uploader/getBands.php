<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
header('Content-Type: text/xml');

//connect with database
$con = mysql_connect("localhost","root","");
if(!$con){
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("musicneverstopped", $con);
//end connecting to database

//declare placeholders

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
