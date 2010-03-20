<?php

//turn on error reporting, set header
header('Content-Type: text/xml');
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

include('db/db_login.php');

//pull variables
$show = $_GET['artistShow'];
$artistAbb = substr($show, 0, -10);;
//$year = substr($artist, -4);
$show_array = array();

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

//create a variable to hold # of sets, current set number, and current set type
  $numOfSets = 1;
  $currentSet = '';
  $setType = '';
   
//request a listing of all songs grouped by artist and store results in $result
$result = mysql_query("SELECT * FROM artists WHERE abb = '$artistAbb'");
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
    $artist = $row['name'];
        }//end while
    }//end if   
  //query based on $artist and $show and put songs in order 
  $result = mysql_query("SELECT * FROM songs WHERE unique_song_id LIKE '$show%' ORDER BY unique_song_id");
  //artist = '$artist' AND 
  //if no results
  if(!$result){
    //stop, and display error
    die(mysql_error());
    }

  //check if any results were returned
  if(mysql_num_rows($result) > 0){
    
    //if yes setup xml structure
    $dom = new DOMDocument();
    $response = $dom->createElement('response');
    $dom->appendChild($response);
    
    //while there are results, cycle through them
    while($row = mysql_fetch_array($result)){
      //if current songs set number if different than what is saved in $currentSet 
      if($row['set_num'] != $currentSet || $row['setOrEncore'] != $setType){
        //set $currentSet and $setType to current song's set and set type
        $currentSet = $row['set_num'];
        $setType = $row['setOrEncore'];
        //flag that this is a new set
        $newSetTest = 'true';
        
        //if $currentSet is 'e' (encore)
        if($setType == 'encore'){
          //create a new xml element 'encore'
          $newSet = $dom->createElement('encore');
          }
        //otherwise  
        else{
          //create new xml element 'set'
          $newSet = $dom->createElement('set');
          }
        }//end if($row['set_num'] != $currentSet
      
      //create xml element 'song' 
      $songElement = $dom->createElement('song');
     
      //create xml element 'song_id'
      $idElement = $dom->createElement('song_id');
      //create text node and give it the value of current song's unique_song_id
      $idText = $dom->createTextNode($row['unique_song_id']);
      //append text node to 'song_id'
      $idElement->appendChild($idText);
      //append 'song_id' to 'song'
      $songElement->appendChild($idElement);
         
      $nameElement = $dom->createElement('song_name');
      $nameText = $dom->createTextNode($row['name']);
      $nameElement->appendChild($nameText);
      $songElement->appendChild($nameElement);
       
      $lengthElement = $dom->createElement('song_time');
      $lengthText = $dom->createTextNode($row['length']);
      $lengthElement->appendChild($lengthText);
      $songElement->appendChild($lengthElement);
       
      $infoElement = $dom->createElement('song_info');
      $infoText = $dom->createTextNode($row['notes']);
      $infoElement->appendChild($infoText);
      $songElement->appendChild($infoElement);
      
      $segueElement = $dom->createElement('segue');
      $segueText = $dom->createTextNode($row['part_of_a_sugue']);
      $segueElement->appendChild($segueText);
      $songElement->appendChild($segueElement);
         
      //attach song to current set   
      $newSet->appendChild($songElement);
      
      //if this is a new set, attach it to the xml
      if($newSetTest =='true'){
        $response->appendChild($newSet);
        }
   
      }//end while
    }//end if(rows $result)
$xmlString = $dom->saveXML(); 
echo $xmlString;


?>

