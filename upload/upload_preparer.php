<?php
//allow out buffering
ob_start();
//begin session
session_start();

//turn on error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

//begin session
//session_start();
$username = $_SESSION['username'];
  $_SESSION['sets'] = array();
  $_SESSION['setlist_set'] = array();

	$_SESSION['abb'] = $_POST['band'];
	//$abb = abb($band);
	$_SESSION['year'] = $_POST['year'];
	$_SESSION['month'] = $_POST['month'];
	$_SESSION['day'] = $_POST['day'];
	$_SESSION['date'] = create_date($_SESSION['year'], $_SESSION['month'], $_SESSION['day']);
	//create a unique show id
$unique_show_id = $_SESSION['abb'] . $_SESSION['date'];
	$_SESSION['city'] = $_POST['city'];
	$_SESSION['state'] = $_POST['state'];
	$_SESSION['venue'] = $_POST['venue'];
	$_SESSION['numOfSets'] = $_POST['numOfSets'];
  $_SESSION['numOfEncores'] = $_POST['numOfEncores'];
	
	//echo $numOfSets;
	for($i = 0; $i < $_SESSION['numOfSets']; $i++){
	  $curr_recieving_set = 's' . ($i+1);
	  //echo $curr_recieving_set;
	  //$_SESSION['sets'][$i] = $_POST[$curr_recieving_set];
	  $curr_set_string = $_POST[$curr_recieving_set];
	  $_SESSION['sets'][$i] = create_song_array($curr_set_string, $unique_show_id, $i+1);
	  $_SESSION['setlist_set'][] = $_POST[$curr_recieving_set];
	  
  }
	//$sets[] = $_POST['s1'];
	//$sets[] = $_POST['s2'];
	for($i = 0; $i < $_SESSION['numOfEncores']; $i++){
	  $curr_recieving_encore = 'e' . ($i+1);
	  //echo $curr_recieving_set;
	  //$_SESSION['sets'][$i] = $_POST[$curr_recieving_set];
	  $curr_set_string = $_POST['Encore'];
	  $_SESSION['sets'][] = create_song_array($curr_set_string, $unique_show_id, 'e');
	  $_SESSION['setlist_set'][] = $_POST[$curr_recieving_set];
	  
  }
	//$_SESSION['sets'][] = $_POST['Encore'];
	//$setlist_set[] = $_POST['Encore'];
	//var_dump($sets);
	$_SESSION['Encore'] = $_POST['Encore'];
	$_SESSION['taper'] = $_POST['taper'];
	$_SESSION['transfered_by'] = $_POST['transfered_by'];
	$_SESSION['source'] = $_POST['source'];
	$_SESSION['lineage'] = $_POST['lineage'];
	//$numOfEncores = $_POST['numOfEncores'];
	$_SESSION['showNotes'] = $_POST['notes'];
	$_SESSION['mic_loc'] = $_POST['mic_loc'];
	
	


  //display upload form
//display show information
printHead();
echo "The band is: " . $band . "<br>";
echo "The show date is: " . $date . "<br>";
echo "Unique show id: " . $unique_show_id . "<br><br>";
//var_dump($sets[0]);

//begin form header
echo "<form  enctype=\"multipart/form-data\" name=\"upload_script.php\" action=\"upload_script.php\" method=\"post\">";

/*
//create hidden values to be passed to the next page
echo "<input type =\"hidden\" name =\"band\" value =\"" . $band . "\" />";
echo "<input type =\"hidden\" name =\"year\" value =\"" . $year . "\" />";
echo "<input type =\"hidden\" name =\"month\" value =\"" . $month . "\" />";
echo "<input type =\"hidden\" name =\"day\" value =\"" . $day . "\" />";
echo "<input type =\"hidden\" name =\"city\" value =\"" . $city . "\" />";
echo "<input type =\"hidden\" name =\"state\" value =\"" . $state . "\" />";
echo "<input type =\"hidden\" name =\"venue\" value =\"" . $venue . "\" />";
echo "<input type =\"hidden\" name =\"taper\" value =\"" . $taper . "\" />";
echo "<input type =\"hidden\" name =\"source\" value =\"" . $source . "\" />";
echo "<input type =\"hidden\" name =\"lineage\" value =\"" . $lineage . "\" />";
echo "<input type =\"hidden\" name =\"transfered_by\" value =\"" . $transfered_by . "\" />";
echo "<input type =\"hidden\" name =\"numOfSets\" value =\"" . $numOfSets . "\" />";
for($i = 0; $i < count($setlist_set); $i++){
  echo "<input type =\"hidden\" name =\"s" . ($i + 1). "\" value =\"" . $setlist_set[$i] . "\" />";
}
echo "<input type =\"hidden\" name =\"Encore\" value =\"" . $Encore . "\" />";
echo "<input type =\"hidden\" name =\"notes\" value =\"" . $showNotes . "\" />";
//echo "<input type =\"hidden\" name =\"song_notes\" value =\"" . $songNotes . "\" />";
*/


//use a for loop to produce multiple upload boxes
for($i = 0; $i < count($_SESSION['sets']); $i++){
  //title the fieldsets
  if(($i) >= $_SESSION['numOfSets']){
    echo "<fieldset><legend>Encore</legend>";
  }
  else{
    echo "<fieldset><legend>Set " . ($i + 1) . "</legend>";
  }
  for($t = 0; $t < count($_SESSION['sets'][$i]); $t++){
    //this is to locate the appropriate element
    //echo $t;
    //echo $_SESSION['sets'][$i][$t];
?>   
<input type ="hidden" name = "MAX_FILE_SIZE" value = "524288000" />

<fieldset><legend><?php echo $_SESSION['sets'][$i][$t][2] ?></legend>

<?php
	//find and echo file/song info
	
	
	 //echo $band . " : " . $date . " : " . $sets[$i][$t][2];
	  ?>
<p><b>file:</b><input type="file" name ="upload[]" /></p>
<p><b>song notes:</b><input type="text" name ="song_notes[]" /></p>

</fieldset>

<?php
}//end inner for loop
echo "</fieldset>";
}//end outer for loop
//create submit button and end <form>
echo "<div align=\"center\"><input type=\"submit\" name=\"submit\" value=\"submit\" /></div>";
echo "<input type=\"hidden\" name=\"submitted\" value=\"TRUE\" />";
echo "</form>";
echo "</body>";
echo "</html>";
//end else

function printHead(){
	/*This function  prints an html header*/
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
       
            <html xmlns="http://www.w3.org/1999/xhtml"
            xml:lang="en" lang="en">

            <head>
       <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
       
       <title>File Uploader</title>
       <style type="text/css" title ="text/css" media="all">
       .error {
          font-weight: bold;
          color: #C00
       }
       </style>
       </head>
       <body>';
}//end printHead()

function create_date($year, $month, $day){
	//function combines year, month, and day into a yyyy-mm-dd format
	if($month < 10){
		$month = '0' . $month;
	}
	if($day < 10){
		$day = '0' . $day;
	}
	return $year . "-" . $month . "-" . $day;
}//end create_date()

function create_song_array($set, $unique_show_id, $set_num) {
    /*function takes in show.set object, splits that object into
    an array, and then makes it multidiminsional. It then creates and
    assigns song ids to the first row of the array and song titles
    to the third row */

    //split the object($set) into a dummy array based on ',' and '>', using perl-type regex
    $dummy_array = preg_split("/(,|>)/", $set);

    //use the trim() function to remove leading/trailing white spaces
    for($i=0;$i<count($dummy_array);$i++){
        $dummy_array[$i] = trim($dummy_array[$i]);
    }

    //then create the real array as a copy of dummy array
    $song_array = array(count($dummy_array));


    //make the real array multidiminsional and create eight
    //rows to hold the song's various properties
    //[0] = unique song name/key
    //[1] =
    //[2] = song title
    for($i = 0; $i < count($dummy_array); $i++){
        $song_array[$i] = array(8);
    }

    //assign song title, set_one_songs[2] = dummy_array[1]
    for($m = 0; $m < count($dummy_array); $m++){
        $song_array[$m][2] = $dummy_array[$m];
    }
    
    // remove slashes before apostrophes
    for($m = 0; $m < count($song_array); $m++){
    	$song_array[$m][2] = stripslashes($song_array[$m][2]);
	}
      
    //assign unique song ids (###NOTE: $n+1 should return a value which is in a 2 digit form###)
    for($n = 0; $n < count($dummy_array); $n++) {
      //set wether the song beleongs to a set or encore
      if($set_num == "e"){
        //this is assuming there will be only one encore
        $song_array[$n][0] = $unique_show_id . "e" . '0' . "s";
      }
      else{
        $song_array[$n][0] = $unique_show_id . "s" . $set_num . "s";
      }
      
      //set where the song is in the set/encore
      if(($n+1) < 10){
       	$song_array[$n][0] = $song_array[$n][0] . '0' . ($n+1);
		    }
		  else{
			  $song_array[$n][0] = $song_array[$n][0] . ($n+1);
			  }
		    
	    }
    //return newly created array
    return $song_array;
}//create_song_array()


?>
