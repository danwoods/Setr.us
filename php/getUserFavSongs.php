<?php

//turn on error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
header('Content-Type: text/xml');

include('../db/db_login.php');

//pull variables
//need to do some error checking here
$username = ($_GET['username']);

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

//////////////////////////////////////////

//begin processing results
$result = mysql_query("SELECT * FROM user_fav_songs WHERE username = '$username'");

//error check
if(!$result){
  die(mysql_error());
}

//check if any results were returned
if(mysql_num_rows($result) > 0){
  
  $songString = '';
  
  //if so set up the xml
  //$dom = new DOMDocument();
  //$response = $dom->createElement('response');
  //$dom->appendChild($response);
    
  while($row = mysql_fetch_array($result)){
   //echo $row['unique_song_id'];
    $songString = $songString . $row['unique_song_id'] . '.';
    
    //create song node    
    //$song = $dom->createElement('song');
    //$idText = $dom->createTextNode($row['unique_song_id']);
    //$song->appendChild($idText);
    //$response->appendChild($song);
  }//end while
  
  //save and echo xml
  //$xmlString = $dom->saveXML(); 
  //echo $xmlString;
  echo $songString;
}//end if
    
//////////////////////////////////////////

//close database connection
mysql_close($con);//close mysql connection
 
    
?>
