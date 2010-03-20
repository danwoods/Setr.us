
<?php
//need to preform error checking when adding songs to make sure there's  not two instances
//of the same song
//header('Content-Type: text/xml');
//turn on error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);


//pull variables
//need to do some error checking here
$username = ($_GET['username']);
$songId = ($_GET['songId']);
$incORdec = ($_GET['incORdec']);

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

////////////////////////////////////////////////////
    
if($incORdec == 'inc'){
  //need to check and make sure the record doesn't already exist first
  $result = mysql_query("INSERT INTO user_fav_songs (username, unique_song_id) VALUES ('$username','$songId')");
  if (!$result){
    die('Error: ' . mysql_error());
    }//end if
  }//end if
    
  else{
    $result = mysql_query("DELETE FROM user_fav_songs WHERE username = '$username' AND unique_song_id = '$songId'");
    if (!$result){
      die('Error: ' . mysql_error());
      }//end if   
    }//end else
  	  
//////////////////////////////////////////

//close database connection
mysql_close($con);//close mysql connection

echo "sucsess";
 
    
?>
