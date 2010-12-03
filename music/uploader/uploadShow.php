<?php

//allow out buffering
ob_start();
//begin session
session_start();

//turn on error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

//required parameters
$showId = $_POST['showId'];
$artist = $_POST['artist'];
$abb = $_POST['abb'];
$showDate = $_POST['showDate'];
$year = $_POST['year'];
$city = $_POST['city'];
$state = $_POST['state'];
$venue = $_POST['venue'];
$showText = $_POST['showText'];
$show_xml = $_POST['showXml'];
$uploader = $_SESSION['username'];

//optional parameters
/*
if($_POST['venue'] == "")
  $venue = NULL;
else*/
  $venue = $_POST['venue'];
  /*
if($_POST['taper'] == "")
  $taper = NULL;
else*/
  $taper = $_POST['taper'];
  /*
if($_POST['transferer'] == "")
  $transferer = NULL;
else*/
  $transferer = $_POST['transferer'];
  /*
if($_POST['source'] == "")
  $source = NULL;
else*/
  $source = $_POST['source'];
  /*
if($_POST['mic_loc'] == "")
  $mic_loc = NULL;
else*/
  $mic_loc = $_POST['mic_loc'];
  /*
if($_POST['lineage'] == "")
  $lineage = NULL;
else*/
  $lineage = $_POST['lineage'];
  /*
if($_POST['showNotes'] == "")
  $show_notes = NULL;
else*/
  $show_notes = $_POST['showNotes'];
  
  //connect to database
  $con = mysql_connect("localhost","root","");
  if (!$con){
    die('Could not connect: ' . mysql_error());
  }
  
  mysql_select_db("setr.us_db", $con);
  //end connecting to database
    
  //add show information to database
  $sql = "INSERT INTO shows (unique_show_id, artist, date, year, city, state, venue, taper, transfered_by, source, mic_loc, lineage, uploaded_by, uploaded_on, show_notes, show_xml)
				VALUES('$showId', '$artist', '$showDate', '$year', '$city', '$state', '$venue', '$taper', '$transferer', '$source', '$mic_loc', '$lineage', '$uploader', NOW(), '$show_notes', '$show_xml')";
				
  //check to see if the query went through
  if (!mysql_query($sql,$con)){
    echo "query fail";
  	die('Error: ' . mysql_error());
  }
        
  //add show to artist years
  $years;
  $year_array = array();

  //request a listing of all songs grouped by artist and store results in $result
  $result = mysql_query("SELECT * FROM artists WHERE abb = '$abb'");

  //if no result
  if(!$result){
    //stop, and display error message
    die(mysql_error());
  }

  //check if any results were returned
  if(mysql_num_rows($result) > 0){
    //if yes cycle through all results, checking their artist
    while($row = mysql_fetch_array($result)){
      //check if the current songs artist is in the artist array
      $years = $row['years'];
      $numOfShows = $row['numOfShows'];
    }//end while
  }//end if
    
  $year_array = explode("+", $years);
  
  $year_exist = false;
  
  for($i = 0; $i < count($year_array); $i++){
    if($year_array[$i] == $year){
      $year_exist = true;
      break 2;
    }
  }
  
  if(!$year_exist)
    $year_array[] = $year;
    
  sort($year_array);
  
  $newYearArray = implode("+", $year_array);
  
  $numOfShows++;
  
  //update database
  $sql = "UPDATE artists SET years = '$newYearArray', numOfShows = $numOfShows WHERE abb = '$abb'";
  
  if (!mysql_query($sql, $con)){
				  echo "query fail";
  				die('Error: ' . mysql_error());
        }
        
  mysql_close($con);//close mysql connection        

?>
