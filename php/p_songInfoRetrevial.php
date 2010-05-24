<?php
//turn on error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
header('Content-Type: text/xml');

//pull variables
$elementId = $_GET['elementId'];

//include database credentials
include('../db/db_login.php');

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

//attempt query
$result = mysql_query("SELECT * FROM songs WHERE unique_song_id LIKE '$elementId%' ORDER BY unique_song_id ASC");

//error check - if query was unsuccessful stop and display warning
if(!$result){
  die(mysql_error());
}

//check if any results were returned
if(mysql_num_rows($result) > 0){
  //if so set up the xml
  $dom = new DOMDocument();
  $response = $dom->createElement('response');
  $encore = $dom->createElement('encoreSongs');
  $dom->appendChild($response);
  //and set some variables specific to xml
  $encore_songs = array(); 
  
  while($row = mysql_fetch_array($result)){
    //save song info to variables
  	$song_name = $row['name'];
  	$song_artist = $row['artist'];
  	$song_date = $row['date'];
  	$song_city = $row['city'];
  	$song_state = $row['state'];
  	$song_location = $song_city . ', ' . $song_state;
  	$song_id = $row['unique_song_id'];
  	$song_segue = $row['part_of_a_sugue'];
    
    //create song element in xml
    $song_info = $dom->createElement('song');
    
    $idElement = $dom->createElement('song_id');
    $idText = $dom->createTextNode($song_id);
    $idElement->appendChild($idText);
    $song_info->appendChild($idElement);
    
    $nameElement = $dom->createElement('song_name');
    $nameText = $dom->createTextNode($song_name);
    $nameElement->appendChild($nameText);
    $song_info->appendChild($nameElement);
    
    $artistElement = $dom->createElement('song_artist');
    $artistText = $dom->createTextNode($song_artist);
    $artistElement->appendChild($artistText);
    $song_info->appendChild($artistElement);
    
    $dateElement = $dom->createElement('song_date');
    $dateText = $dom->createTextNode($song_date);
    $dateElement->appendChild($dateText);
    $song_info->appendChild($dateElement);
    
    $locationElement = $dom->createElement('song_location');
    $locationText = $dom->createTextNode($song_location);
    $locationElement->appendChild($locationText);
    $song_info->appendChild($locationElement);
    
    $segueElement = $dom->createElement('song_segue');
    $segueText = $dom->createTextNode($song_segue);
    $segueElement->appendChild($segueText);
    $song_info->appendChild($segueElement);
    
    //if the song is part of an encore, save it for later
    if($row['setOrEncore'] == 'encore'){
      /*if the previous song had a different date,
        assume that the previous group of encores went with the previous show
        and append the encores before this new song/show */
      if(isset($previous) && $previous['date'] != $song_date){
        
        //dump encore songs into a DOMNodeList object
        $encore_songs_objs = $encore->getElementsByTagName('song');
        
        //copy songs into array
        foreach($encore_songs_objs as $encore_songs_obj)
          $encore_songs[] = $encore_songs_obj;
        
        //append encore songs to $response
        foreach($encore_songs as $a)
          $response->appendChild($a);
        
        //reset encore variables
        $encore = $dom->createElement('encoreSongs'); 
        $encore_songs = array();
        
      }//end if(isset)
      
      $encore->appendChild($song_info);
      
    }//end if($row['setOrEncore'] == 'encore')
      
    else{
      //else just attach song to xml
      $response->appendChild($song_info);
    }
    
    //set $previous to help test for new shows
    $previous = $row;
  }//end while
     
  //affix any remaining encore songs
  //dump encore songs into a DOMNodeList object
  $encore_songs_objs = $encore->getElementsByTagName('song');
        
  //copy songs into array
  foreach($encore_songs_objs as $encore_songs_obj)
    $encore_songs[] = $encore_songs_obj;
        
  //append encore songs to $response
  foreach($encore_songs as $a)
    $response->appendChild($a);

  //output xml
  $xmlString = $dom->saveXML(); 
  echo $xmlString;
  
}//end if(mysql_num_rows($result) > 0)

//close database connection
mysql_close($con);//close mysql connection
 
?>
