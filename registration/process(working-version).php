<?php

//Retrieve form data. 
//GET - user submitted data using AJAX
//POST - in case user does not support javascript, we'll use POST instead
$name = ($_GET['name']) ? $_GET['name'] : $_POST['name'];
$email = ($_GET['email']) ?$_GET['email'] : $_POST['email'];
$pass = ($_GET['pass2']) ?$_GET['pass2'] : $_POST['pass2'];

//echo "name = " . $name . " email = " . $email;

//flag to indicate which method it uses. If POST set it to 1
if ($_POST) $post=1;

//Simple server side validation for POST data, of course, you should validate the email
if (!$name) $errors[count($errors)] = 'Please enter your username.';
if (!$email) $errors[count($errors)] = 'Please enter your email.'; 
if (!$pass) $errors[count($errors)] = 'Please enter your password.'; 

//if the errors array is empty, send the mail
if (!$errors) {

	//recipient
	$to = 'woodson.dan@gmail.com';	
	//sender
	$from = $name . ' <' . $email . '>';
	
	//subject and the html message
	$subject = 'Comment from ' . $name;	
	$message = '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head></head>
	<body>
	<table>
		<tr><td>Name</td><td>' . $name . '</td></tr>
		<tr><td>Email</td><td>' . $email . '</td></tr>
		<tr><td>Website</td><td>Set.r</td></tr>
		<tr><td>Comment</td><td>New User!</td></tr>
	</table>
	</body>
	</html>';

	//send the mail
	$result = insert_new_user($name, $pass, $email);
	
	//if POST was used, display the message straight away
	if ($_POST) {
		if ($result) echo 'Thank you! We have received your message.';
		else echo 'Sorry, unexpected error. Please try again later';
		
	//else if GET was used, return the boolean value so that 
	//ajax script can react accordingly
	//1 means success, 0 means failed
	} else {
		echo $result;	
	}

//if the errors array has values
} else {
	//display the errors message
	for ($i=0; $i<count($errors); $i++) echo $errors[$i] . '<br/>';
	echo '<a href="registration.php">Back</a>';
	exit;
}


//Simple mail function with HTML header
function sendmail($to, $subject, $message, $from) {
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	$headers .= 'From: ' . $from . "\r\n";
	
	$result = mail($to, $subject, $message, $headers);
	
	if ($result) return 1;
	else return 0;
}

function insert_new_user($username, $password, $email){
  //set standard user variables
  $group = 'member';
  
  //connect with database
  $con = mysql_connect("localhost","root","");
  
  //check connection
  if(!$con){
    die('Could not connect: ' . mysql_error());
    }

  mysql_select_db("musicneverstopped", $con);
  //end connecting to database

  $test = mysql_query("SELECT * FROM users WHERE username = '$username'");
  
  if(!$test){
    echo 'bad test';
    die(mysql_error());
    }
   
  if(mysql_num_rows($test) > 0){
    echo "Username already taken. Sorry";
    //close mysql connection
    mysql_close($con);
    return 0;
    }
  elseif(mysql_num_rows($test) == 0){
    if(mysql_query("INSERT INTO users (username, password, email, privlege, join_date)
    VALUES('$username','$password','$email','$group', NOW())")){    
      
      //log user in
      session_start();
      
      $_SESSION['username'] = $username;
      $_SESSION['privilege'] = $group;
        
      //close mysql connection
      mysql_close($con);
      return 1;
      }//end if
    else{
      echo "insert failed";
      return 0;
      }//end else
    }//end elseif

  }//end function	  
    
?>
