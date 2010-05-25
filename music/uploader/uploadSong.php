<?php

//allow out buffering
ob_start();
//begin session
session_start();

//turn on error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

$showId = $_POST['showId'];
$artist = $_POST['artist'];
$abb = $_POST['abb'];
$showDate = $_POST['showDate'];
$city = $_POST['city'];
$state = $_POST['state'];
$venue = $_POST['venue'];
$setOrEncore = $_POST['setOrEncore'];
$setNum = $_POST['setNum'];
$songNum = $_POST['songNum'];
$songId = $_POST['songId'];
$partOfASeuge = $_POST['partOfASegue'];
$songName = $_POST['songName'];
$addInfo = $_POST['addInfo'];

//resolve where the file should be stored
$band_dir = "/opt/lampp/htdocs/musicneverstopped/music_directory/" . $abb;
$file_dir = "/opt/lampp/htdocs/musicneverstopped/music_directory/" . $abb . "/" . $showId . "/";

//check if the neccessary directory exist, if not create it
if(!is_dir($band_dir)){
  mkdir($band_dir, '757');
}
if(!is_dir($file_dir)){
	mkdir($file_dir, '757');
}

$file_path = $file_dir . $songId . ".mp3";

if ($_FILES['upload']['error'] > 0)
    {
    echo "Return Code: " . $_FILES['upload']['error'] . "<br />";
    }

//if file uploads correctly
if(move_uploaded_file($_FILES['upload']['tmp_name'], $file_path)){
  //echo success and add song to database
  echo "success";
  
  //connect to database
  $con = mysql_connect("localhost","root","");
  if (!$con){
    die('Could not connect: ' . mysql_error());
  }
  
  mysql_select_db("musicneverstopped", $con);
  //end connecting to database
    
  //add show information to database
  $sql= "INSERT INTO songs (unique_song_id, file_location, artist, date, city, state, name, set_num, set_position, part_of_a_sugue, setOrEncore, notes)
				VALUES('$songId','$file_path','$artist','$showDate','$city','$state','$songName','$setNum','$songNum', $partOfASeuge, '$setOrEncore', '$addInfo')";
				
  //check to see if the query went through
	if (!mysql_query($sql,$con)){
	  echo "query fail";
  	die('Error: ' . mysql_error());
  }
        
  mysql_close($con);//close mysql connection
        
}
else{
  echo "fail";
}

?>
