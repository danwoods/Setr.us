<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Set.r > Registration</title>

<script type="text/javascript" src="js/jquery-1.3.1.min.js"></script>
<script type="text/javascript">
//This code is my take on the examples given at
//http://www.queness.com/post/160/create-a-ajax-based-form-submission-with-jquery
$(document).ready(function() {
	
	//if submit button is clicked
	$('#submit').click(function () {		
		
		//Get the data from all the fields
		var name = $('input[name=name]');
		var email = $('input[name=email]');
		var pass1 = $('input[name=pass1]');
		var pass2 = $('input[name=pass2]');

		//Simple validation to make sure user entered something
		//If error found, add hightlight class to the text field
		if (name.val()=='') {
			name.addClass('hightlight');
			return false;
		} else name.removeClass('hightlight');
		
		if (email.val()=='') {
			email.addClass('hightlight');
			return false;
		} else email.removeClass('hightlight');
		
		if (pass1.val()=='') {
			pass1.addClass('hightlight');
			return false;
		} else pass1.removeClass('hightlight');
		
		if (pass2.val()=='') {
			pass2.addClass('hightlight');
			return false;
		} else pass2.removeClass('hightlight');
		
		//organize the data properly
		var data = 'name=' + name.val() + '&email=' + email.val() + '&pass2=' + 
		pass2.val();
		
		alert("process(working-version).php?"+data);
		
		//disabled all the text fields
		$('.text').attr('disabled','true');
		
		//show the loading sign
		$('.loading').show();
		
		//debug
		//alert("process(working-version).php?" + data);
		
		//start the ajax
		$.ajax({
			//this is the php file that processes the data and send mail
			url: "process(working-version).php",	
			
			//GET method is used
			type: "GET",

			//pass the data			
			data: data,		
			
			//Do not cache the page
			cache: false,
			
			//success
			success: function (html) {				
				//if process.php returned 1/true (send mail success)
				if (html==1) {					
					//hide the form
					$('.form').fadeOut('slow');					
					
					//show the success message
					$('.done').fadeIn('slow');
					
					setTimeout(function (){window.location = '../index.php';}, 1000);
					
				//if process.php returned 0/false (send mail failed)
				} else alert('Sorry, unexpected error. Please try again later.');				
			}		
		});
		
		//cancel the submit button default behaviours
		return false;
	});	
	
	//clear highlight if user is fixing problem
	$('input[type="text"]').focus(
    function(){
      // only select if the text has not changed
      $(this).removeClass('hightlight');
      }
    );
    
  //validator triage
  $('input[type="text"]').keyup(function(){
    
    //switch based on calling element's id
    switch($(this).attr('id')){
      case "name":
        //alert('hey');
        validator_name();
        break;
      case 'pass1':
        validator_pass1();
        break;
      case 'pass2':
        validator_pass2();
        break;
      case 'email':
        validator_email();
        break;
      default:
        break;
      }

  });
  
});	

function validator_name(){
  //alert('hello');
  if($('input[name=name]').val().length > 4){
    
    //set up ajax parameters
    var data = "username=" + $('input[name=name]').val();
    //alert(data);
    //ajax
    $.ajax({
			//this is the php file that processes the data and send mail
			url: "php/check_username_avail.php",	
			
			//GET method is used
			type: "GET",

			//pass the data			
			data: data,		
			
			//Do not cache the page
			cache: false,
			
			//success
			success: function (html) {				
				//if process.php returned 1/true (send mail success)
				if (html==1) {					
					//display success image
					$("#name_mess_icon").css({ "background-image": "url('validYes.png')" });
					}
        else if(html == 0){
          $("#name_mess_icon").css({ "background-image": "url('validNo.png')" });		
          }		
        }//end success
      });
    }
  }

function validator_pass1(){
  var pattern = /[0-9]/;
  var user_sub_val = $('input[name=pass1]').val();
  
  if(user_sub_val.match(pattern) && user_sub_val.length < 9 && user_sub_val.length > 5){
    $("#pass1_mess_icon").css({ "background-image": "url('validYes.png')" });
    }
  else{
    $("#pass1_mess_icon").css({ "background-image": "url('validNo.png')" });
    }
  
  }

function validator_pass2(){
  //compare to pass1 value
  if($('input[name=pass2]').val() === $('input[name=pass1]').val()){
    $("#pass2_mess_icon").css({ "background-image": "url('validYes.png')" });
    }
  else{
    $("#pass2_mess_icon").css({ "background-image": "url('validNo.png')" });
    }
}

function validator_email(){
  var pattern = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
  var testAgainst = $('input[name=email]').val();
  //alert(testAgainst);
  //$("#email_mess_icon").css({ "background-image": "url('validYes.png')" });
  if(testAgainst.match(pattern)){
    $("#email_mess_icon").css({ "background-image": "url('validYes.png')" });
    }
  else{
    $("#email_mess_icon").css({ "background-image": "url('validNo.png')" });
    }
}
</script>

<style>
/*******
The styles below are my take 
on the example given at
http://www.sitepoint.com/article/fancy-form-design-css/
*******/
body {
  text-align:center;
}

.clear {
	clear:both
}
#header {
  position: absolute;
  top: 0px;
  left: 0px;
  height: 60px;
}
#content {
  position: absolute;
  top: 50px;
}
.block {
	width:400px;
	margin:0 auto;
	text-align:left;
}
fieldset {  
  position: relative;  
  float: left;  
  clear: both;  
  width: 100%;  
  margin: 0 0 -1em 0;  
  padding: 0 0 1em 0;  
  border-style: none;  
  border-top: 1px solid #BFBAB0;  
  border-left: 1px solid #BFBAB0;  
  border-right: 1px solid #BFBAB0;  
  background-color: #F2EFE9;
} 
legend {  
  padding: 0;  
  color: #545351;  
  font-weight: bold;
}
legend span {  
  position: absolute;  
  left: 0.74em;  
  top: 0;  
  margin-top: 0.5em;  
  font-size: 135%;
}
fieldset ol {  
  padding: 3.5em 1em 0 1em;  
  list-style: none;
}
fieldset li {  
  float: left;  
  clear: left;  
  width: 100%;  
  padding-bottom: 1em;
}
fieldset.submit {  
  float: none;  
  width: auto;  
  padding-top: 1.5em;  
  padding-left: 9em;  
  padding-right: 10.40em;
  border-style: none;
  border-top: 1px solid #BFBAB0;  
  background-color: #FFFFFF;
}
label {  
  float: left;  
  width: 10em;  
  margin-right: 1em;  
  text-align: left;
}
label strong {
  font-size: 10px;
}
#name_mess_icon{  
margin-top: 4px;
margin-left: 9px;
position: absolute;
width: 16px;
height: 16px;
}

#pass1_mess_icon{
  margin-top: 4px;
  margin-left: 9px;
  position: absolute;
  width: 16px;
  height: 16px;
}

#pass2_mess_icon{
  margin-top: 4px;
  margin-left: 9px;
  position: absolute;
  width: 16px;
  height: 16px;
}

#email_mess_icon{
  margin-top: 4px;
  margin-left: 9px;
  position: absolute;
  width: 16px;
  height: 16px;
}

.hightlight {
	border:2px solid #9F1319;
	background:url(iconCaution.gif) no-repeat 2px
}
.loading {
	float:right; 
	background:url(ajax-loader.gif) no-repeat 1px; 
	height:28px; 
	width:28px; 
	display:none;
}
.done {
	background:url(iconIdea.gif) no-repeat 2px; 
	padding-left:20px;
	font-family:arial;
	font-size:12px; 
	width:70%; 
	margin:20px auto; 
	display:none
}
</style>
<!--[if lte IE 7]>  
<style type="text/css" media="all">  
@import "css/fieldset-styling-ie.css";  
</style>
<![endif]-->
</head>
<body>

<div id="header">
  <div id="logo"><img src="../images/logo_banner.png" /></div>
  </div>

<div id='content'>
<div class="block">

<div class="done">
<b>Thank you !</b> Your account has been created. 
</div>

	<div class="form">
	<form method="post" action="process(working-version).php">
	
<fieldset>  
<legend>  
<span>User Information</span>
</legend>  
<ol>  
<li>  
<label for="name">Username:</label>  
<input id="name" name="name" class="text" type="text" /> 
<span id='name_mess_icon' class='message_icon'></span><span class='message'></span>
</li>  
<li>  
<label for="pass1">Password:</label>  
<input id="pass1" name="pass1" class="text" type="text" />
<span id='pass1_mess_icon' class='pass1_message_icon'></span><span class='message'></span>  
</li> <li>  
<label for="pass2">Password <strong>(re-enter)</strong>:</label>  
<input id="pass2" name="pass2" class="text" type="text" />
<span id='pass2_mess_icon' class='message_icon'></span><span class='message'></span>  
</li> 
<li>  
<label for="email">Email address:</label>  
<input id="email" name="email" class="text" type="text" />
<span id='email_mess_icon' class='message_icon'></span><span class='message'></span>  
</li>  
 </ol>  
</fieldset>  

<fieldset class="submit">  
<input id='submit' class="submit" type="submit"  
value="Sign me up!" />  
</fieldset>

</form>
	</div>
</div>

<div class="clear"></div>
</div>


</body>
</html>
