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
if(isset($_GET['username'])){
  $username = $_GET['username'];
}
$song_array = array();

//connect with database
$con = mysql_connect("localhost","root","");
if(!$con){
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("musicneverstopped", $con);
//end connecting to database

//declare placeholders

////////////////////////////////////////////////////
#Create, run, and evaluate query based on $expandTo
////////////////////////////////////////////////////


  $result = mysql_query("SELECT * FROM user_fav_songs WHERE username = '$username' AND unique_song_id LIKE '$elementId%' ORDER BY unique_song_id ASC");
//error check - see if this is safe to remove later
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
  echo '<response>';
  $encore_array = array();
  $count = 0;
  //echo " more than one result!";
  
  //if yes cycle through all results, checking their artist
  while($row = mysql_fetch_array($result)){
    //check if the current songs artist is in the artist array
      if(!in_array($row['unique_song_id'], $song_array)){
  	  //if yes store this artist in $artist_array
  	  $song_array[] = $row['unique_song_id'];
  	  //echo $song_array[0];
      }//end if
  }//end while
}//end if



foreach($song_array as $song){
//  echo " entering secondary query ";
$result = mysql_query("SELECT * FROM songs WHERE unique_song_id = '$song'");

//error check - see if this is safe to remove later
if(!$result){
  die(mysql_error());
  }

//check if any results were returned
if(mysql_num_rows($result) > 0){
  
  //echo " secondary query returned results! ";
  
  while($row = mysql_fetch_array($result)){
    //check if the current songs artist is in the artist array
    
    $pattern = '/e....$\b/';
    if(preg_match($pattern, $row['unique_song_id'])){
      $encore_array[$count]['song_id'] = $row['unique_song_id'];
      $encore_array[$count]['song_name'] = $row['name'];
  	  $encore_array[$count]['song_artist'] = $row['artist'];
  	  $encore_array[$count]['song_date'] = $row['date'];
  	  $encore_array[$count]['song_city'] = $row['city'];
  	  $encore_array[$count]['song_state'] = $row['state'];
  	  $encore_array[$count]['song_location'] = $encore_array[$count]['song_city'] . ', ' . $encore_array[$count]['song_state'];
  	  
  	  $count++;
    }
    else{
    $song_id = $row['unique_song_id'];
    $song_name = $row['name'];
  	$song_artist = $row['artist'];
  	$song_date = $row['date'];
  	$song_city = $row['city'];
    $song_state = $row['state'];
  	$song_location = $song_city . ', ' . $song_state;
  	
    
    echo '<song>';
    echo '<song_id>' . $song_id . '</song_id>';
    echo '<song_name>' . $song_name . '</song_name>';
    echo '<song_artist>' . $song_artist . '</song_artist>';
    echo '<song_date>' . $song_date . '</song_date>';
    echo '<song_location>' . $song_location . '</song_location>';
    echo '</song>';
    
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
    
    
  }//end else(is a 'set' song)
  }//end while
}//end if(mysql_num_rows($result) > 0)
}//end foreach
   
  //affix encore songs to 'response'
  $encore_songs = $encore->getElementsByTagName('song');
  foreach($encore_array as $a){
    echo '<song>';
    echo '<song_id>' . $a['song_id'] . '</song_id>';
    echo '<song_name>' . $a['song_name'] . '</song_name>';
    echo '<song_artist>' . $a['song_artist'] . '</song_artist>';
    echo '<song_date>' . $a['song_date'] . '</song_date>';
    echo '<song_location>' . $a['song_location'] . '</song_location>';
    echo '</song>';
  }  
   
      

$xmlString = $dom->saveXML(); 
echo '</response>';
//end if
//////////////////////////////////////////

//close database connection
mysql_close($con);//close mysql connection
 


?>
