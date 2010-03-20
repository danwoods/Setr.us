<?php
//turn on error reporting, set header
header('Content-Type: text/xml');
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

//pull variables
//need to do some error checking here
$group = fix_spaces($_GET['group']);
$artist = fix_spaces($_GET['artist']);
$expandTo = fix_spaces($_GET['expandTo']);

//if expanding a favorite song/show/year/artist, also pull username
if($expandTo == 'fav_artist' || $expandTo == 'fav_years' || $expandTo == 'fav_shows' || $expandTo == 'fav_songs'){
  $username = $_GET['username'];
  }

include('db/db_login.php');

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
$artist_array = array();
$song_array = array();

////////////////////////////////////////////////////
#Create, run, and evaluate query based on $expandTo
////////////////////////////////////////////////////

//expandTo == 'artist'
if($expandTo == "artist"){

  //request a listing of all songs grouped by artist and store results in $result
  $result = mysql_query("SELECT * FROM songs ORDER BY artist");

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
        if(!in_array($row['artist'], $artist_array)){
  	      //if no, store this artist in $artist_array
  	      $artist_array[] = $row['artist'];
          }//end if
        }//end while
    }//end if
  }//end if($expandTo == "artist")
//////////////////////////////////////////

//if expandTo == 'fav_artist'
elseif($expandTo == "fav_artist"){
  
  //request a listing of all users favorite songs and store results in $result
  $result = mysql_query("SELECT * FROM user_fav_songs WHERE username = '$username'");

  //if no results
  if(!$result){
    //stop, and display error message
    die(mysql_error());
    }

  //check if any results were returned
  if(mysql_num_rows($result) > 0){
    //if yes cycle through all results, checking their artist
    while($row = mysql_fetch_array($result)){
      //check if the current song is in the song array
      if(!in_array($row['unique_song_id'], $song_array)){
  	    //if no, store this song in $song_array
  	    $song_array[] = $row['unique_song_id'];
        }//end if
      }//end while
    }//end if

  //loop through all the songs stored in $song_array, and query the database to gather
  //more information about each of them
  foreach($song_array as $song){
    //query the database for an individual song and store result in $result
    $result = mysql_query("SELECT * FROM songs WHERE unique_song_id = '$song' ORDER BY artist");
    
    //if no results returned
    if(!$result){
      //stop, and display error
      die(mysql_error());
      }

    //check if any results were returned
    if(mysql_num_rows($result) > 0){
      //if yes cycle through all results, checking their artist
      while($row = mysql_fetch_array($result)){
        //check if the current songs artist is in the artist array
        if(!in_array($row['artist'], $artist_array)){
          //if no, store this artist in $artist_array
  	      $artist_array[] = $row['artist'];
          }//end if
        }//end while
      }//end if
      
    }//end foreach

  }//end if($expandTo == "fav_artist")
//////////////////////////////////////////

//if expandTo == 'years'
elseif($expandTo == "years"){
  //loop through years 2010 > 1959
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
      $artist_array[] = $i;
      }//end if
      
    }//end for
    
  }//end elseif($expandTo == "years")
//////////////////////////////////////////

//expandTo == 'fav_years'
elseif($expandTo == "fav_years"){
  
  //request a listing of all user's favorite songs
  $result = mysql_query("SELECT * FROM user_fav_songs WHERE username = '$username'");

  //if no results
  if(!$result){
    //stop, and display error
    die(mysql_error());
    }

  //check if any results were returned
  if(mysql_num_rows($result) > 0){
    //if yes cycle through all results, checking their artist against artist already in the $artist_array
    while($row = mysql_fetch_array($result)){
      //check if the current songs artist is in the artist array
      if(!in_array($row['unique_song_id'], $song_array)){
  	    //if yes, store this artist in $artist_array
  	    $song_array[] = $row['unique_song_id'];
        }//end if
      }//end while
    }//end if

  //loop through all the songs in $song_array
  foreach($song_array as $song){
    
    //for each song, loop through years 2010 > 1959
    for($i=2010; $i>1959; $i--){
    
      //query to see what year the current favorite song was played in
      $result = mysql_query("SELECT * FROM songs WHERE unique_song_id = '$song' AND artist = '$artist' AND date LIKE '%$i%'");// ORDER BY date");
      
      //if no results returned
      if(!$result){
        //stop, and displaay error
        die(mysql_error());
        }
      
      //check if any results were returned
      if (mysql_num_rows($result) > 0) {
        //if yes, see if that year is in $artist_array
        if(!in_array($i, $artist_array)){
          //if no, add year to array
          $artist_array[] = $i;
          }//end if
        }//end if
 
      }//end for
      
    }//end foreach

  }//end if($expandTo == "fav_years")
//////////////////////////////////////////

//if expandTo == 'shows'
elseif($expandTo == "shows"){
  
  //query all songs for $artist and $group
  $result = mysql_query("SELECT * FROM songs WHERE artist = '$artist' AND unique_song_id LIKE '$group%'ORDER BY date DESC");// ORDER BY date");
  
  //if no results
  if(!$result){
    //stop, and display error
    die(mysql_error());
    }

  //check if any results were returned
  if(mysql_num_rows($result) > 0){
    //if yes cycle through all results, checking their artist
    while($row = mysql_fetch_array($result)){
      //check if the current songs artist is in the artist array
      if(!in_array($row['date'], $artist_array)){
        //if no store this artist in $artist_array
        $artist_array[] = $row['date'];
        }//end if
      }//end while
    }//end if
  }//end elseif(expandTo == "shows"
//////////////////////////////////////////

//expandTo == 'fav_shows'
elseif($expandTo == "fav_shows"){
  
  //normalize $group
  $group = preg_replace('/fav_/', '', $group);
  
  //query user_fav_songs based on $username and $group
  $result = mysql_query("SELECT * FROM user_fav_songs WHERE username = '$username' AND unique_song_id LIKE '$group%'");
  
  //check if results were returned
  if(!$result){
    //if no, stop and display error
    die(mysql_error());
    }

  //check if any results were returned
  if(mysql_num_rows($result) > 0){
    //if yes cycle through all results, checking their artist
    while($row = mysql_fetch_array($result)){
      //check if the current songs artist is in the artist array
        if(!in_array($row['unique_song_id'], $song_array)){
  	    //if no, store this artist in $artist_array
  	    $song_array[] = $row['unique_song_id'];
        }//end if
      }//end while
    }//end if

  //cycle through the unqiue_song_ids in $song_array
  foreach($song_array as $song){
    //query the song
    $result = mysql_query("SELECT * FROM songs WHERE unique_song_id = '$song'");
  
    //if no results
    if(!$result){
      //stop, and display error
      die(mysql_error());
      }

    //check if any results were returned
    if(mysql_num_rows($result) > 0){
      //if yes cycle through all results, checking their artist
      while($row = mysql_fetch_array($result)){
        //check if the current songs artist is in the artist array
        if(!in_array($row['date'], $artist_array)){
  	      //if no, store this artist in $artist_array
  	      $artist_array[] = $row['date'];
          }//end if
        }//end while
      }//end if
      
    }//end foreach

}//end elseif(expandTo == "fav_shows"
//////////////////////////////////////////
//expandTo == 'songs'
elseif($expandTo == "songs"){
  //create a variable to hold # of sets and current set number
  $numOfSets = 1;
  $currentSet = '';
   
  //query based on $artist and $group and put songs in order 
  $result = mysql_query("SELECT * FROM songs WHERE artist = '$artist' AND unique_song_id LIKE '$group%' ORDER BY unique_song_id");
  
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
      if($row['set_num'] != $currentSet){
        //set $currentSet to current song's set
        $currentSet = $row['set_num'];
        //flag that this is a new set
        $newSetTest = 'true';
        
        //if $currentSet is 'e' (encore)
        if($currentSet == 'e'){
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
         
      //attach song to current set   
      $newSet->appendChild($songElement);
      //}//end while($row['set_num'] == $currentSet)
      if($newSetTest =='true'){
        $response->appendChild($newSet);
        }
      
    //  }//end //if($row['set_num'] != $currentSet){
      //
      
      //check if the current songs artist is in the artist array
      if(!in_array($row['name'], $artist_array)){
  	    //if yes store this artist in $artist_array
  	    $artist_array[] = $row['name'];
        }//end if
      }//end while
    }//end if(rows $result)
$xmlString = $dom->saveXML(); 
echo $xmlString;
}//elseif($expandTo == "songs")

//////////////////////////////////////////

//////////////////////////////////////////
//expandTo == 'fav_songs'
elseif($expandTo == "fav_songs"){
  $group = preg_replace('/fav_/', '', $group);
  //echo $group;
  $result = mysql_query("SELECT * FROM user_fav_songs WHERE username = '$username' AND unique_song_id LIKE '$group%' ORDER BY unique_song_id");
//$result = mysql_query("SELECT * FROM songs ORDER BY artist");

//error check - see if this is safe to remove later
if(!$result){
  die(mysql_error());
  }

//check if any results were returned
if(mysql_num_rows($result) > 0){
  //if yes cycle through all results, checking their artist
  $i = 0;
  while($row = mysql_fetch_array($result)){
    //check if the current songs artist is in the artist array
      if(!in_array($row['unique_song_id'], $song_array)){
  	  //if yes store this artist in $artist_array
  	  //echo $row['unique_song_id'];
  	  $song_array[] = $row['unique_song_id'];
  	  //echo $row['unique_song_id'];
  	  //$artist = $row['artist'];
  	  //echo $i;
  	  $i++;
      }//end if
  }//end while
}//end if

  //create a variable to hold # of sets and current set number
  $numOfSets = 1;
  $currentSet = '';
  
  //$dom = new DOMDocument();
    //$response = $dom->createElement('response');
    //$dom->appendChild($response);
    
  /*echo '<?xml version="1.0" encoding="ISO-8859-1"?>';*/
  echo '<response>';
    
  foreach($song_array as $song){
    //echo "    entered foreach, song = " . $song;
  $result = mysql_query("SELECT * FROM songs WHERE unique_song_id = '$song'");
  
  //error check - see if this is safe to remove later
  if(!$result){
  die(mysql_error());
  }
    
  while($row = mysql_fetch_array($result)){
    
    //echo " entered while(row = mysql_fetch_array(result)) ";
   if($row['set_num'] != $currentSet){
     $currentSet = $row['set_num'];
     //echo $currentSet;
     $newSetTest = 'true';
    }
    else{$newSetTest = 'false';}
    
    if(isset($oldSet)){
    if($oldSet != $row['set_num']){
      if($oldSet == 'e'){
        echo '</encore>';
      }
      else{ echo '</set>';}
      
      if($row['set_num'] == 'e'){
        echo '<encore>';
      }
      else{ echo '<set>';}
      $oldSet = $row['set_num'];
      //echo $currentSet;
      //$newSetTest = 'true';
    }//end if($oldSet != $row['set_num'])
  }// endif(isset($oldSet))
  else{
    if($row['set_num'] == 'e'){
        echo '<encore>';
        $oldSet = $row['set_num'];
      }
      else{ echo '<set>';}
      $oldSet = $row['set_num'];
    }
     
      
       //echo $newSetTest . ' ';
       //$songElement = $dom->createElement('song');
     echo '<song>';
     
       //$idElement = $dom->createElement('song_id');
       //$idText = $dom->createTextNode($row['unique_song_id']);
       echo '<song_id>' .  $row['unique_song_id'] . '</song_id>';
       //$idElement->appendChild($idText);
       //$songElement->appendChild($idElement);
       
       //$nameElement = $dom->createElement('song_name');
       //$nameText = $dom->createTextNode($row['name']);
       echo '<song_name>' .  $row['name'] . '</song_name>';
       //$nameElement->appendChild($nameText);
       //$songElement->appendChild($nameElement);
       
       //$lengthElement = $dom->createElement('song_time');
       //$lengthText = $dom->createTextNode('length');
       echo '<song_time>' .  'length' . '</song_time>';
       //$lengthElement->appendChild($lengthText);
       //$songElement->appendChild($lengthElement);
       
       echo '<song_info>' .  $row['notes'] . '</song_info>';
         echo '</song>';
         
         if($newSetTest == 'true'){
           if(isset($newSet)){
             //$response->appendChild($newSet);
             if($oldSet == 's'){
              //echo '</set>';
            }
            //else{echo '</encore>';}
           }
           
           if($currentSet == 'e'){
             //$newSet = $dom->createElement('encore');
             //echo '<encore>';
             //$oldSet = 'e';
           }
      
           else{
             //$newSet = $dom->createElement('set');
             //echo '<set>';
             //$oldSet = 's';
           }
           
           //$newSet->appendChild($songElement);
           //echo'</song>';
         }
         
         else{
           //$newSet->appendChild($songElement);
           //echo '</song>';
         }
    
    //$oldSet = $row['set_num'];
  }//end while($row = mysql_fetch_array($result)
}//end foreach

if($oldSet == 'e'){
        echo '</encore>';
      }
      else{ echo '</set>';}
echo '</response>';


}//elseif($expandTo == "fav_songs")

//////////////////////////////////////////

elseif($expandTo == "playlist"){
  //$group = preg_replace('/fav_/', '', $group);
  //echo $group;
  $result = mysql_query("SELECT * FROM playlist WHERE username = '$username'");
//$result = mysql_query("SELECT * FROM songs ORDER BY artist");

//error check - see if this is safe to remove later
if(!$result){
  die(mysql_error());
  }

//check if any results were returned
if(mysql_num_rows($result) > 0){
  //if yes cycle through all results, checking their artist
  $i = 0;
  while($row = mysql_fetch_array($result)){
    //check if the current songs artist is in the artist array
      if(!in_array($row['name'], $artist_array)){
  	  //if yes store this artist in $artist_array
  	  //echo $row['unique_song_id'];
  	  $artist_array[] = $row['name'];
  	  //$artist = $row['artist'];
  	  //echo $i;
  	  $i++;
      }//end if
  }//end while
}//end if

}//end elseif(expandTo == "fav_shows"
//////////////////////////////////////////

elseif($expandTo == "pl_songs"){
  //$group = preg_replace('/fav_/', '', $group);
  $group = preg_replace('/pl_/', '', $group);
  $result = mysql_query("SELECT * FROM playlist WHERE username = '$username' AND name = '$group'");
//$result = mysql_query("SELECT * FROM songs ORDER BY artist");

//error check - see if this is safe to remove later
if(!$result){
  die(mysql_error());
  }

//check if any results were returned
if(mysql_num_rows($result) > 0){
  //if yes split the string of songs based on '.'
  while($row = mysql_fetch_array($result)){
    //check if the current songs artist is in the artist array
      $song_array = preg_split("/ /", $row['songs']);
      //}//end if
  }//end while
}//end if

echo "<response>";
foreach($song_array as $song){
    //echo "    entered foreach, song = " . $song;
  $result = mysql_query("SELECT * FROM songs WHERE unique_song_id = '$song'");
  
  //error check - see if this is safe to remove later
  if(!$result){
  die(mysql_error());
  }
    
  while($row = mysql_fetch_array($result)){
    echo '<song>';
     
       //$idElement = $dom->createElement('song_id');
       //$idText = $dom->createTextNode($row['unique_song_id']);
       echo '<song_id>' .  $row['unique_song_id'] . '</song_id>';
       //$idElement->appendChild($idText);
       //$songElement->appendChild($idElement);
       
       //$nameElement = $dom->createElement('song_name');
       //$nameText = $dom->createTextNode($row['name']);
       echo '<song_name>' .  $row['name'] . '</song_name>';
       //$nameElement->appendChild($nameText);
       //$songElement->appendChild($nameElement);
       
       //$lengthElement = $dom->createElement('song_time');
       //$lengthText = $dom->createTextNode('length');
       echo '<song_time>' .  'length' . '</song_time>';
       //$lengthElement->appendChild($lengthText);
       //$songElement->appendChild($lengthElement);
         echo '</song>';
       }//end while
     }//end for each
     echo "</response>";
//echo count($artist_array);
}//end elseif(expandTo == "fav_shows"
//////////////////////////////////////////

//close database connection
mysql_close($con);//close mysql connection
 
//////////////////////////////////////////////////////
#Functions
//////////////////////////////////////////////////////
 function fix_spaces($phrase){
  for($i = 0; $i < count($phrase); $i++){
    if($phrase[$i] == '_'){
      $phrase[$i] = ' ';
    }
  }
  return $phrase;
}
//////////////////////////////////////////////////////
#DOM manipulation
//////////////////////////////////////////////////////
if($expandTo != 'songs' && $expandTo !== 'fav_songs' && $expandTo !== 'pl_songs'){
$dom = new DOMDocument();
$response = $dom->createElement('response');
$dom->appendChild($response);


for($j = 0; $j < count($artist_array); $j++){
$artistElement = $dom->createElement('artist');
$artistText = $dom->createTextNode($artist_array[$j]);
$artistElement->appendChild($artistText);

$response->appendChild($artistElement);
}//end for

$xmlString = $dom->saveXML(); 
echo $xmlString;
}//end if ($expandTo != 'songs')

?>
