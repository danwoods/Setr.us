
<?php

//turn on error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
//header('Content-Type: text/xml');


/////////////Main script
//pull variables
//need to do some error checking here
$username = ($_GET['username']);

//connect with database
$con = mysql_connect("localhost","root","");
if(!$con){
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("musicneverstopped", $con);
//end connecting to database

//////////////////////////////////////////

//begin processing results
$result = mysql_query("SELECT * FROM users WHERE username = '$username'");

//error check
if(!$result){
  die(mysql_error());
}

//check if any results were returned
if(mysql_num_rows($result) > 0){
  echo 0;
}//end if
else{
  echo 1;
}
    
//////////////////////////////////////////

//close database connection
mysql_close($con);//close mysql connection
 
    
?>
