<?php

//turn on error reporting, set header
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
header('Content-Type: text/xml');

include('../db/db_login.php');

//pull variables
//Need to do some error checking here
$username = ($_GET['name']) ? $_GET['name'] : $_POST['name'];
$password = ($_GET['pass']) ?$_GET['pass'] : $_POST['pass'];

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

//query database for user-submitted username and store result in $result
$result = mysql_query("SELECT * FROM users WHERE username = '$username'");

//if no results returned
if(!$result){
  //stop and display error
  die(mysql_error());
  }

//check if a single result was returned
if(mysql_num_rows($result) == 1){
  //if true, set the returned results to $row
  $row = mysql_fetch_array($result);
  //check if password from user matches password from database
  if($password == $row['password']){
    //if true, begin session
    session_start();
    //assign session variables
    $_SESSION['username'] = $row['username'];
    $_SESSION['privilege'] = $row['privlege'];
    //send user to index page
    //header('Location: http://localhost/musicneverstopped');
    //mysql_close($con);//close mysql connection
    echo 1;
    }
  else{
    //mysql_close($con);//close mysql connection
    //if false, send user to login page
    echo '0';
    }
  mysql_close($con);
  }//end if(mysql_num_rows($result) == 1)
else{
  return '0';
  }
?>
