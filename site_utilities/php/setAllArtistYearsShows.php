<?php
/*
This file resets all artist's years


*/
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
header('Content-Type: text/xml');

//declare variables
$artist_array = array();
$show_array = array();;
$year_array = array();

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
    $year_array = explode("+", $row['years']);
    foreach($year_array as $year){
      
    /*$artist_array[] = addslashes($row['name']);
    }//end while
  }//end if
  
//cycle through artists
foreach($artist_array as $artist){
  $years = "";
  $years_array = new array();
  
  $result = mysql_query("SELECT * FROM artists WHERE name = '$artist'");

//if no result
if(!$result){
  //stop, and display error message
  die(mysql_error());
  }

//check if any results were returned
if(mysql_num_rows($result) > 0){
  //if yes cycle through all results, checking their artist
  while($row = mysql_fetch_array($result)){
    $years = $row['years'];
  }//end while
  $years_array = explode('+', $years);  
}//end if
  
    
  $sql= "UPDATE artists SET years = '".$years."' WHERE name = '".$artist."'";

				//check to see if the query went through
				if (!mysql_query($sql,$con)){
  					die('Error: ' . mysql_error());
          }
    
  }//end for
  
  
?>
