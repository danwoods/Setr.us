<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Create a Ajax based form submission with jQuery</title>

<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	
	//if submit button is clicked
	$('#submit').click(function () {		
		
		//Get the data from all the fields
		var name = $('input[name=name]');
		var email = $('input[name=email]');
		var website = $('input[name=website]');
		var comment = $('textarea[name=comment]');

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
		
		if (comment.val()=='') {
			comment.addClass('hightlight');
			return false;
		} else comment.removeClass('hightlight');
		
		//organize the data properly
		var data = 'name=' + name.val() + '&email=' + email.val() + '&website=' + 
		website.val() + '&comment='  + encodeURIComponent(comment.val());
		
		//disabled all the text fields
		$('.text').attr('disabled','true');
		
		//show the loading sign
		$('.loading').show();
		
		//start the ajax
		$.ajax({
			//this is the php file that processes the data and send mail
			url: "process.php",	
			
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
					
				//if process.php returned 0/false (send mail failed)
				} else alert('Sorry, unexpected error. Please try again later.');				
			}		
		});
		
		//cancel the submit button default behaviours
		return false;
	});	
});	
</script>
<style>
body{text-align:center;}
.clear {
	clear:both
}
.block {
	width:400px;
	margin:0 auto;
	text-align:left;
}
.element * {
	padding:5px; 
	margin:2px; 
	font-family:arial;
	font-size:12px;
}
.element label {
	float:left; 
	width:75px;
	font-weight:700
}
.element input.text {
	float:left; 
	width:270px;
	padding-left:20px;
}
.element .textarea {
	height:120px; 
	width:270px;
	padding-left:20px;
}
.element .hightlight {
	border:2px solid #9F1319;
	background:url(iconCaution.gif) no-repeat 2px
}
.element #submit {
	float:right;
	margin-right:10px;
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
<body>

<div id="header">
  <div id="logo"><img src="../images/logo_banner.png" /></div>
  </div>

<div class="block">
<div class="done">
<b>Thank you !</b> We have received your message. 
</div>
	<div class="form">
	<form method="post" action="process(working-version.php">
	<div class="element">
		<label>Name</label>
		<input type="text" name="name" class="text" />
	</div>
	<div class="element">
		<label>Email</label>
		<input type="text" name="email" class="text" />
	</div>
	<div class="element">
		<label>Website</label>
		<input type="text" name="website" class="text" />
	</div>
	<div class="element">
		<label>Comment</label>
		<textarea name="comment" class="text textarea" /></textarea>
	</div>
	<div class="element">
		
		<input type="submit" id="submit"/>
		<div class="loading"></div>
	</div>
	</form>
	</div>
</div>

<div class="clear"></div>



</body>
</html>
