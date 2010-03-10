
<?php

//turn on error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
//header('Content-Type: text/xml');

/////////Functions
function abb($band) {
    /*this function takes in the user inputed band name and show date.
    It then produces an abbreviation from the band name.
    It then returns this abbreviation combined with the show date
    to create the show id*/

    //variable declarations
    $abb;//store the abbreviated band name
    //convert the band name to lower case for easier comparison
    $band = strtolower($band);
    //set abbreviation based on band name
    switch($band) {
        case "widespread panic" :
            $abb = "wsp";
            break;
        case "grateful dead" :
            $abb = "gd";
            break;
        case "phish" :
            $abb =  "ph";
            break;
        case "ryan adams" :
            $abb =  "ra";
            break;
        case "disco biscuits" :
            $abb = "tdb";
            break;
        case "moe." :
        	  $abb = "moe";
        	  break;
        case "warren zevon" :
            $abb = "wz";
            break;
        case "drive-by truckers" :
            $abb = "dbt";
            break;
        case "john mayer" :
            $abb = "jm";
            break;
        case "guster" :
            $abb = "gust";
            break;
        default:
        	  $abb = "unknown";
        	  break;
    }//end switch
    //return a combination of the band name abbreviation and the show date
    return $abb;
}//end showIdCreator()  

/////////////Main script
//pull variables
//need to do some error checking here
//$username = ($_GET['username']);

//connect with database
$con = mysql_connect("localhost","root","");
if(!$con){
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("musicneverstopped", $con);
//end connecting to database

//////////////////////////////////////////

//begin processing results
$result = mysql_query("SELECT * FROM songs WHERE unique_song_id LIKE 'ph2009-06-10%' AND length IS NULL");

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
    $trackDirectory = "../music_directory" . "/" . abb($row['artist']) . "/" . abb($row['artist']) . $row['date'] . "/" . $row['unique_song_id'] . '.mp3';
    //echo $trackDirectory . '</br>';
    $songString = $songString . $trackDirectory . '+';
    
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
