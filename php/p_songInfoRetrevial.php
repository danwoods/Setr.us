<?php
###CURRENTLY WORKING ON RETURNING MULTIPLE SETS OF SONGS###
//remove comment qoutes and test with xml.php
//turn on error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
header('Content-Type: text/xml');

//pull variables
//need to do some error checking here
$elementId = ($_GET['elementId']);//selection = ($_GET['selection']);
$encoreSongCount = 0; //debugging

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

//declare placeholders

////////////////////////////////////////////////////
#Create, run, and evaluate query based on $expandTo
////////////////////////////////////////////////////


  $result = mysql_query("SELECT * FROM songs WHERE unique_song_id LIKE '$elementId%' ORDER BY unique_song_id ASC");

//error check - see if this is safe to remove later
if(!$result){
  die(mysql_error());
  }

//check if any results were returned
if(mysql_num_rows($result) > 0){
  //echo "song found";
  //if so set up the xml
  $dom = new DOMDocument();
    $response = $dom->createElement('response');
    $encore = $dom->createElement('encoreSongs');
    $dom->appendChild($response);
    $previousDate = "";
  //if yes cycle through all results, checking their artist
  
  while($row = mysql_fetch_array($result)){
    //check if the current songs artist is in the artist array
  	  $song_name = $row['name'];
  	  $song_artist = $row['artist'];
  	  $song_date = $row['date'];
  	  $song_city = $row['city'];
  	  $song_state = $row['state'];
  	  $song_location = $song_city . ', ' . $song_state;
  	  $song_id = $row['unique_song_id'];
  	  $song_segue = $row['part_of_a_sugue'];
    
    
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
      echo "::New song name::" . $row['name'] . "::New song name::";
      /*if the previous song had a different date,
        assume that the previous group of encores went with the previous show
        and append the encores before this new song/show */
      if(isset($previous) && $previous['date'] != $song_date){
        echo " >>> entering previous if on " . $row['name'] . " >>> ";
        //affix encore songs to 'response'
        $encore_songs = $encore->getElementsByTagName('song');
        echo " @@@ number of encore songs = " . count($encoreSongCount) . " @@@";
        //echo $encore_songs;
        $i = 0;
        foreach($encore_songs as $a){
          $i++;
          echo $i . "songs added to response";
          $response->appendChild($a);
        }
        //reset encore variable
        $encore = $dom->createElement('encoreSongs'); 
        echo " !!! encore element cleared !!! "; 
        $encoreSongCount = 0;
      }
      $encore->appendChild($song_info);
      $encoreSongCount++;
      
      echo " \\\ " . $row['name'] . " added to encores, song" . $encoreSongCount . " \\\ ";
      //var_dump($encore);
      
    }
      
    else{
      //attach new song
      $response->appendChild($song_info);
    }
    
    $previous = $row;
  }//end while
     
  //affix any remaining encore songs
  $encore_songs = $encore->getElementsByTagName('song');
  foreach($encore_songs as $a){
    $response->appendChild($a);
  }        

$xmlString = $dom->saveXML(); 
echo $xmlString;
}//end if
//////////////////////////////////////////

//close database connection
mysql_close($con);//close mysql connection
 


?>
