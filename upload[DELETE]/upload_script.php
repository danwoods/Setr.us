<?php session_start(); #File uploading script

//////////////////////////////////////////////////////////////////
//File: upload_script.php
//Author: Daniel Woodson (w/ help from gevans from phpfreaks.org)
//Purpose: Upload multiple files
//Last update: 1-21-09
//Comment: intergrating show locations
//////////////////////////////////////////////////////////////////

//begin session



////////////////functions////////////////////
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

////////////////end of functions////////////////////

////////////////begin lead-in php///////////////////
   

   
//turn on error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

//print html header
printHead(); #print html head

//Initailize and assign values to starter variables.

  $username = $_SESSION['username'];
  $sets = $_SESSION['sets'];
  $setlist_set = $_SESSION['setlist_set'];

	$abb = $_SESSION['abb'];
	//$abb = abb($band);
	//$year = $_SESSION['year'];
	//$month = $_SESSION['month'];
	//$day = $_POST['day'];
	$date = $_SESSION['date'];
	$year = $_SESSION['year'];
	$city = $_SESSION['city'];
	$state = $_SESSION['state'];
	$venue = $_SESSION['venue'];
	$numOfSets = $_SESSION['numOfSets'];
	//echo $numOfSets;
	//for($i = 0; $i < $numOfSets; $i++){
	  //$curr_recieving_set = 's' . ($i+1);
	  //echo $curr_recieving_set;
	  //$sets[] = $_POST[$curr_recieving_set];
	  //$setlist_set[] = $_POST[$curr_recieving_set];
	  
  //}
	//$sets[] = $_POST['s1'];
	//$sets[] = $_POST['s2'];
	$sets[] = $_SESSION['Encore'];
	//$setlist_set[] = $_POST['Encore'];
	//var_dump($sets);
	$Encore = $_SESSION['Encore'];
	$taper = $_SESSION['taper'];
	$transfered_by = $_SESSION['transfered_by'];
	$source = $_SESSION['source'];
	$lineage = $_SESSION['lineage'];
	//$numOfEncores = $_POST['numOfEncores'];
	$showNotes = $_SESSION['showNotes'];
	$mic_loc = $_SESSION['mic_loc'];
	
	
	/*
	 *TESTING THE USE OF  SESSION VARIABLES
	 */
  /*
  $_SESSION['sets'] = array();
  $_SESSION['setlist_set'] = array();

	$_SESSION['abb'] = $_POST['band'];
	//$abb = abb($band);
	$_SESSION['year'] = $_POST['year'];
	$_SESSION['month'] = $_POST['month'];
	$_SESSION['day'] = $_POST['day'];
	$_SESSION['date'] = create_date($year, $month, $day);
	$_SESSION['city'] = $_POST['city'];
	$_SESSION['state'] = $_POST['state'];
	$_SESSION['venue'] = $_POST['venue'];
	$_SESSION['num0fSets'] = $_POST['numOfSets'];
	//echo $numOfSets;
	for($i = 0; $i < $_SESSION['num0fSets']; $i++){
	  $curr_recieving_set = 's' . ($i+1);
	  //echo $curr_recieving_set;
	  $_SESSION['sets'][] = $_POST[$curr_recieving_set];
	  $_SESSION['setlist_set'][] = $_POST[$curr_recieving_set];
	  
  }
	//$sets[] = $_POST['s1'];
	//$sets[] = $_POST['s2'];
	$_SESSION['sets'][] = $_POST['Encore'];
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
	
	*/
	
	//get song count
$total_song_count = 0;
for($i = 0; $i < count($sets); $i++){
  $total_song_count += count($sets[$i]);
}

//create a unique show id
$unique_show_id = $abb . $date;

//resolve where the files should be stored
$band_dir = "/opt/lampp/htdocs/musicneverstopped/music_directory/" . $abb;
$file_dir = "/opt/lampp/htdocs/musicneverstopped/music_directory/" . $abb . "/" . $unique_show_id . "/";

//check the directory for saving the files
//THIS DOESN'T WORK!!
if(!is_dir($band_dir)){
  mkdir($band_dir, '757');
}
if(!is_dir($file_dir)){
	mkdir($file_dir, '757');
}
	

//create an array of songs based on set on and save it to an array of the same name
for($i = 0; $i < count($sets); $i++){
  if(($i) > $numOfSets){
    $setNumString = 'e';
  }
  else{
    $setNumString = ($i + 1 + '0');
  }
  /*I commented out the following line because $sets[$i] is already an array of song information*/
  //$sets[$i] = create_song_array($sets[$i], $unique_show_id, $setNumString);
  
  //var_dump($sets);
}


//check to see if file has been submitted
if(isset($_POST['submitted'])){
  //recive other information posted when song files are submitted
  $_SESSION['songNotes'] = $_POST['song_notes'];
  
	//connect to database
	$con = mysql_connect("localhost","root","");
	if (!$con){
  		die('Could not connect: ' . mysql_error());
  	}

	mysql_select_db("musicneverstopped", $con);
	//end connecting to database
	
	//set actual full band name
	$result = mysql_query("SELECT * FROM artists WHERE abb = '$abb'");

//error check - see if this is safe to remove later
if(!$result){
  die(mysql_error());
  }

//check if any results were returned
if(mysql_num_rows($result) > 0){
  
  while($row = mysql_fetch_array($result)){
    $band =  $row['name'];
  	  }//end while

  }        
	
    //begin looping through files to upload
    //declare var to hold current song number
    $song_num = 0;
    
    //start a loop to move through all the files
    for($i = 0; $i < count($sets); $i++){
      for($t = 0; $t < count($sets[$i]); $t++){
        
        //create an array of allowed types (###NOTE: need MIME type for Flac###)
        $allowed = array('audio/mpeg');
        
        //now evaluate current file
        //check to see if current file's type is in the array of allowed types
        if(in_array($_FILES['upload']['type'][$song_num], $allowed)){
            //If true, begin to pull song information
            if(($i + 1) >= $numOfSets){
              $song_set_num = 'e';
              }
            else{
              $song_set_num = ($i + 1 + '0');
              }
            $song_set_pos = ($t + 1 + '0');
            $song_id = $sets[$i][$t][0];
            $song_file_loc = $file_dir . $sets[$i][$t][0] . '.mp3';
            $song_name = $sets[$i][$t][2];
          
            $file_path = $file_dir . $song_id . ".mp3";
            
            //try to move the file
            if(move_uploaded_file($_FILES['upload']['tmp_name'][$song_num], $file_path)){
            	
            	//if successful//            	
            	//send information to database
            	//$sql= "INSERT INTO songs (unique_song_id, file_location, artist, date, city, state, name, set_num, set_position, notes)
				//VALUES('$song_id','$song_file_loc','$band','$date','$city','$state','$song_name','$song_set_num','$song_set_pos', '$_SESSION[\'songNotes\'][$song_num]')";
				
				//the same sql statement as above w/o song notes
				$sql= "INSERT INTO songs (unique_song_id, file_location, artist, date, city, state, name, set_num, set_position)
				VALUES('$song_id','$song_file_loc','$band','$date','$city','$state','$song_name','$song_set_num','$song_set_pos')";

				//check to see if the query went through
				if (!mysql_query($sql,$con)){
  				die('Error: ' . mysql_error());
        }
  				
  			/*display results*/
  			//remove slashes, '\'
        $song_name = stripslashes($song_name);
        //display results of upload
        echo '<p><em>' . $song_name . ' has been uploaded! Unique song id is ' . $song_id . '</em></p>';
        //Delete file if if still exist
        if(file_exists($_FILES['upload']['tmp_name'][$song_num]) && is_files($_FILES['upload']['tmp_name'][$song_num])){
          unlink($_FILES['upload']['tmp_name'][$song_num]);
        }
      }//end if(move_uploaded_file...
      
      //if the upload was not successful, check for errors
      else {
        if($_FILES['upload']['error'][$song_num] > 0){
         echo '<p class="error">The file could not be uploaded because: <strong>';
        //print message based on error
        switch($_FILES['upload']['error'][$song_num]){
        case 1:
          print 'The file exceeds the upload_max_filesize settiing in php.ini';
          break;
        case 2:
          print 'The file exceeds the MAX_FILE_SIZE setting in the html form';
          break;
        case 3:
          print 'The file was only partially uploaded';
          break;
        case 4:
          print 'No file was uploaded';
          break;
        case 6:
          print 'No temporary folder was available';
          break;
        case 7:
          print 'Unable to write to the disk';
          break;
        case  8:
          print 'File upload was stopped';
          break;
        default:
          print 'A system error occured';
          break;
        }//end of switch
        print '</strong></p>';
      }//end of error if
    }//end else
  } else {//invaild type
      echo '<p class="error">Error: Wrong filetype. Must be an mp3</p>';
    }//end else
    
    //increment song_num
    $song_num++;
    
  }//end inner (songs) for loop
  
}//end outer (sets) for loop
    
    //create show
    $sql = "INSERT INTO shows (unique_show_id, artist, year, date, city, state, venue, taper, transfered_by, source, lineage, show_notes, uploaded_by, uploaded_on, mic_loc)
      VALUES('$unique_show_id', '$band', '$year', '$date', '$city', '$state', '$venue', '$taper', '$transfered_by', '$source', '$lineage', '$showNotes', '$username', NOW(), '$mic_loc')";

//echo $sql + "\n";

    //check to see if the query went through
		if (!mysql_query($sql,$con)){
  		die('Error: ' . mysql_error());
  	}
    
    
    
    mysql_close($con);//close mysql connection
    //provide ability to upload another file
    echo "<a href=\"archiver.html\">Upload another show?!?!? -></a>";
}//end isset

////////////////begin actual page/////////////////// 
/*
else{
  //display upload form
//display show information
echo "The band is: " . $band . "<br>";
echo "The show date is: " . $date . "<br>";
echo "Unique show id: " . $unique_show_id . "<br><br>";
//var_dump($sets[0]);

//begin form header
echo "<form  enctype=\"multipart/form-data\" name=\"upload_script.php\" action=\"upload_script.php\" method=\"post\">";


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


//use a for loop to produce multiple upload boxes
for($i = 0; $i < count($sets); $i++){
  //title the fieldsets
  if(($i) > $numOfSets){
    echo "<fieldset><legend>Encore</legend>";
  }
  else{
    echo "<fieldset><legend>Set " . ($i + 1) . "</legend>";
  }
  for($t = 0; $t < count($sets[$i]); $t++){
?>   
<input type ="hidden" name = "MAX_FILE_SIZE" value = "524288000" />

<fieldset><legend><?php echo $sets[$i][$t][2] ?></legend>

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
}//end else*/
?>
</body>
</html>
