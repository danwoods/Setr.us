<?php

#THIS DOES NOT CHECK FOR ERRORS!!!#

//turn on error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
header('Content-Type: text/xml');

//pull variables
//need to do some error checking here
$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$group = "member";

//connect with database
$con = mysql_connect("localhost","root","");
if(!$con){
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("musicneverstopped", $con);
//end connecting to database

$sql= "INSERT INTO users (username, password, email, firstname, lastname, privlege)
				VALUES('$username','$password','$email','$firstname','$lastname', '$group')";

				//check to see if the query went through
				if (!mysql_query($sql,$con)){
  					die('Error: ' . mysql_error());
          }
          
          mysql_close($con);//close mysql connection
?>
