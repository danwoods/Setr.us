<?php
if(isset($_SESSION['username'])){
  header('Location: index.php');
  }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Setr.us > Login</title>

<script type="text/javascript" src="javascript/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
//This code is my take on the examples given at
//http://www.queness.com/post/160/create-a-ajax-based-form-submission-with-jquery
$(document).ready(function() {
	
  //set focus to username
  $('#name').focus();
  
	//if submit button is clicked
	$('#submit').click(function () {		
		
		//Get the data from all the fields
		var name = $('input[name=name]');
		//var email = $('input[name=email]');
		var pass = $('input[name=pass]');
		//var pass2 = $('input[name=pass2]');

		//Simple validation to make sure user entered something
		//If error found, add hightlight class to the text field
		if (name.val()=='') {
			name.addClass('hightlight');
			return false;
		} else name.removeClass('hightlight');
		
		if (pass.val()=='') {
			pass.addClass('hightlight');
			return false;
		} else pass.removeClass('hightlight');
		
		//organize the data properly
		var data = 'name=' + name.val() + '&pass=' + pass.val();
		
		//disabled all the text fields
		$('.text').attr('disabled','true');
		
		//show the loading sign
		$('.loading').show();
		
		//start the ajax
		$.ajax({
			//this is the php file that processes the data and send mail
			url: "php/login.php",	
			
			//GET method is used
			type: "GET",

			//pass the data			
			data: data,		
			
			//Do not cache the page
			cache: false,
			
			dataType: "html",
			
			//success
			success: function (html) {				
				//if process.php returned 1/true (send mail success)
				if (html==1) {					
					//hide the form
					$('.form').fadeOut('slow');					
					
					//show the success message
					$('.done').fadeIn('slow');
					
					setTimeout(function (){window.location = 'index.php';}, 1000);
														  
				  }
				  
				  //if process.php returned 0/false (not a registered user)
				  else if(html == 0){				    
				    //lower message
				    $('.fail').fadeIn('slow');
				    
				    //set timer for message
				    setTimeout(function (){$('.fail').fadeOut('slow');}, 1000);
				    
				    //set longer timer for enabling text feilds to account for the message's slow fade out
				    setTimeout(function (){$('.text').removeAttr('disabled');$('input[name="pass"]').val('');}, 1750);
		        

            }
            
          //else system error
          else alert('Sorry, unexpected error. Please try again later.' + html);				
        },
        
        error:function (XMLHttpRequest, textStatus, errorThrown){
          alert('textStatus = ' + textStatus + '\n' + 
                'errorThrown = ' + errorThrown);
          }       
        //alert(html);
        
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
    
 
  
});	


</script>
  
  <link rel="stylesheet" type="text/css" href="stylesheets/index.css" />
  </head>
  
  <body>

<div id="header">
  <div id="logo"><img src="images/logo_banner.png" /></div>
  </div>

<div id='content'>
<div class="block">

<div class="done">
<b>Thank you !</b> You're now logged in. 
</div>

<div class='fail'>
<b>Sorry, impropper username or password</b>
</div>

<div class="form">
	<form method="post" action="php/login.php">
	
<fieldset>  
<legend>  
<span>User Login</span>
</legend>  
<ol>  
<li>  
<label for="name">Username:</label>  
<input id="name" name="name" class="text" type="text" /> 
<span id='name_mess_icon' class='message_icon'></span><span class='message'></span>
</li>  
<li>  
<label for="pass">Password:</label>  
<input id="pass" name="pass" class="text" type="password" />
<span id='pass_mess_icon' class='pass_message_icon'></span><span class='message'></span>  
</li> 
 </ol>  
</fieldset>  

<fieldset class="submit">  
<input id='submit' class="submit" type="submit"  
value="Login!" />  
</fieldset>

</form>
	</div>
</div>

<div class="clear"></div>
</div>


</body>
</html>

