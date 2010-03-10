<?php

//turn on error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
//header('Content-Type: text/xml');

/////////////Main script
//pull variables
//need to do some error checking here
$trackname = ($_GET['trackname']);
$tracktime = ($_GET['tracktime']);

//remove leading/following track information
$trackname = str_replace('../music_directory/ph/ph2009-06-10/', '', $trackname);
$trackname = str_replace('.mp3', '', $trackname);
$trackname = ltrim($trackname);
//echo $trackname;

//connect with database
$con = mysql_connect("localhost","root","");
if(!$con){
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("musicneverstopped", $con);
//end connecting to database

//////////////////////////////////////////

//update given song time
$sql = "UPDATE songs SET length = '".$tracktime."' WHERE unique_song_id = '".$trackname."'";
echo $sql;
mysql_query($sql);

echo mysql_error();

//error check
//if(!$attempt){
  //die(mysql_error());
//}

//////////////////////////////////////////

//close database connection
mysql_close($con);//close mysql connection
 
    
?>
