<?php
//start the session
session_start();

//check to make sure the session variable is registered
if(isset($_SESSION['username'])){

//if true, the user is ready to logout
//remove session variables
session_unset();
//destroy session
session_destroy();
//redirect user - need to create logout page
header('Location: http://localhost/Setr.us/login_html.php');

}
//if false
else{
//the session variable isn't registered, the user shouldn't even be on this page
//redirect them to the login page
header('Location: http://localhost/Setr.us/login_html.php');
}
?> 
