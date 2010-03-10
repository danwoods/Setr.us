<?php
/*
This file resets all artist's years


*/
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
header('Content-Type: text/xml');

//declare variables
$artist_array = array();

//connect to database
$con = mysql_connect("localhost","root","");
if(!$con){
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("musicneverstopped", $con);
//end connecting to database

//get array of all artist
$result = mysql_query("SELECT * FROM artists");

//if no result
if(!$result){
  //stop, and display error message
  die(mysql_error());
  }

//check if any results were returned
if(mysql_num_rows($result) > 0){
  //if yes cycle through all results, checking their artist
  while($row = mysql_fetch_array($result)){
    $artist_array[] = addslashes($row['name']);
    }//end while
  }//end if
  
//cycle through artists
foreach($artist_array as $artist){
  $years = "";
  for($i=2010; $i>1959; $i--){
    //query database for songs matching artist and year and store results in $result
    $result = mysql_query("SELECT * FROM songs WHERE artist = '$artist' AND date LIKE '%$i%'");
    
    //if no results
    if(!$result){
      //stop, and display errors
      die(mysql_error());
      }
      
    //check if any results were returned
    if (mysql_num_rows($result) > 0) {
      //store year in $artist_array
      $years = $years . $i . '+';
      }//end if
      //clean off trailing '+'
      //echo $years;
      //$years = substr($years, 0, -1);
    }//end for
    
  $sql= "UPDATE artists SET years = '".$years."' WHERE name = '".$artist."'";

				//check to see if the query went through
				if (!mysql_query($sql,$con)){
  					die('Error: ' . mysql_error());
          }
    
  }//end for
  
  
?>
