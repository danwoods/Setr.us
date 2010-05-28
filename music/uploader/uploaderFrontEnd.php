<?php 
session_start();
if(!isset($_SESSION['username'])){
  header('Location: http://localhost/musicneverstopped/login_html.php');
  }
   ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
            xml:lang="en" lang="en">
<head>
  <title>Setr.us > Upload</title>
  <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
  <script type="text/javascript" src="javascript/jquery-1.3.2.min.js"></script>
  <script type="text/javascript" src="javascript/jquery-ui-1.7.2.custom.min.js"></script>
  <script type="text/javascript" src="javascript/jquery.qtip-1.0.0-rc3.js"></script>
  <link rel=StyleSheet href="jquery-ui-1.7.2.custom.css" type="text/css" media=screen>
  <link rel=StyleSheet href="uploaderFrontEnd.css" type="text/css" media=screen>
  <script type="text/javascript">
  <? echo "var username = '" . $_SESSION['username'] . "';";?>
  $(document).ready(function() {
    
    //variables      
    var numOfSets = 1;
    var numOfEncores = 0;
    var uploadSuccessful = false;
    var uploadFinished = false;
    
    /*set inital modal dialog warning*/
    $(function() {
		$("#user_agreement").dialog({
			height: 375,
			modal: true,
			buttons: {
			  "I understand...": function() {
			    $(this).dialog("close"); } }
		});

	});
    
    //ajax for band names
		$.ajax({
			//getBands.php returns an html snippet in the following format:
			//<option value=\"" . $row['abb'] . "\">" . $row['name'] . "</option>
			url: "getBands.php",	
			type: "GET",
			cache: false,			
			dataType: "html",
			success: function (html) {				
				//if success, append to band select
        //alert(html);
				$("#band_default_selection").after(html);
      },
      error:function (XMLHttpRequest, textStatus, errorThrown){
          alert('textStatus = ' + textStatus + '\n' + 
                'errorThrown = ' + errorThrown);
          }       
        
		});		
			  
	  /*FIRST LOAD SRCIPTING*/
	  
	  /*text input styling*/
	  $('input[type=text]').css('color', 'grey');
	  $('input[type=text]').focus(function(){
	    $(this).val("").css('color', 'black');
	    
	    });
	  
	  /*add date checker functionality*/
	  $("#required_inputs .date_input select").bind("change", function() {
	    check_date($("#required_inputs .date_input [name='year'] option:selected").text(),
                 $("#required_inputs .date_input [name='month'] option:selected").text(),
                 $("#required_inputs .date_input [name='day'] option:selected").text());
    });
    
    /*optional info fade in/out functionality*/
	  $('#opt_info_header').bind("click", function() {
      if($('#optional_inputs').hasClass('hidden')){
        $('#optional_inputs').fadeIn("slow");
        $('#optional_inputs').removeClass("hidden");
        $('#optional_inputs').addClass("visible");
        $('#opt_info_header').text("Optional Information -");
      }
      else{
        $('#optional_inputs').fadeOut("slow");
        $('#optional_inputs').removeClass("visible");
        $('#optional_inputs').addClass("hidden");
        $('#opt_info_header').text("Optional Information +");
      }
    });  
	    
    /*add song functionality*/
		$('p.add_song').bind("click",function() {
		  addSong($(this).parent());
	  });
    
    /*remove song functionality*/
    $('p.remove_song').bind("click", function(){
		    var numOfChild = $(this).parents('.set').children('.song').length;
		    if(numOfChild == 1)
		      alert("Can't remove the only song from set one");
        else
		      //$(this).parents('.song').remove();	
		      $(this).parents('.song').fadeOut(500, function() {
          $(this).addClass('garbage').removeClass('song');
          $(this).removeAttr('id');
          $(this).remove();          
          refresh();
        });
	  });
	  
	  /*add sugue arrow clickability*/
		$('.segue_indic').click(function(){
		  if($(this).hasClass("segue")){
          $(this).removeClass("segue");
          $(this).attr('src', 'images/ul_segue_hollow.png');
        }
        else{
          $(this).addClass("segue");
          $(this).attr('src', 'images/ul_segue_darkGreen.png');          
        }
    });
    
    /*seuge tool-tip*/
    $('.segue_indic').qtip({
          content: "is this song a segue?",
          position: {
            corner: {
              target: 'topMiddle',
              tooltip: 'bottomLeft'
            }
          },
          style: { 
            tip: 'bottomLeft',
            name: 'blue',
            'font-size': 12 
          },   
          show: 'mouseover',
          hide: {
            when: 'mouseout',
            fixed: true
          }
        }); //end creating tool tip
		
		/*add set functionality*/
		$('p.add_set').bind("click",function() {
		  //clone first set
		  var setElmClone = $('#set1').clone();
		  //increment numOfSets then set set id
		  numOfSets++;
		  $(setElmClone).attr('id', 'set' + numOfSets);
		  //clean out all songs
		  $(setElmClone).children( '.song' ).remove();
		  //fix title
		  $('h2', setElmClone).text("Set " + numOfSets);
		  //add song
		  addSong($(setElmClone));
		  //add "add song" functionality
		  /*add song functionality*/
		  $('p.add_song', setElmClone).bind("click",function() {
		    addSong($(setElmClone));
	    });
	    /*remove set functionality*/
	    $('p.remove_set', setElmClone).click(function() {
        $(this).parents('.set').fadeOut("slow", function() {
          numOfSets--;
          $(this).addClass('garbage').removeClass('set');
          $(this).removeAttr('id');
          $(this).remove();          
          refresh();
        });
      });
      
      //make set initially invisable
      $(setElmClone).css('display', 'none');
      
		  //insert new set above "add set" button
		  $(setElmClone).insertBefore($(this));		
		  
		  //reveal new set
		  $(setElmClone).fadeIn("slow");
    });
    
    /*add encore functionality*/
    $('p.add_encore').bind("click", function() {
      //clone first set
		  var setElmClone = $('#set1').clone();
		  //increment numOfSets then set set id
		  numOfEncores++;
		  $(setElmClone).attr('id', 'encore' + numOfEncores);
		  //change class from set to encore
		  $(setElmClone).removeClass('set');
		  $(setElmClone).addClass('encore');		  
		  //clean out all songs
		  $(setElmClone).children( '.song' ).remove();
		  /*fix title and other text areas*/
		  //title
		  if(numOfEncores > 1)
		    $('h2', setElmClone).text("Encore (" + numOfEncores + ")");
      else
        $('h2', setElmClone).text("Encore");
      //remove 'set'
      $('p.remove_set', setElmClone).text("remove encore -");		  
		  //add song
		  addSong($(setElmClone));
		  //add "add song" functionality
		  $('p.add_song', setElmClone).bind("click",function() {
		    addSong($(setElmClone));
	    });
	    /*remove set functionality*/
	    $('p.remove_set', setElmClone).click(function() {
        $(this).parents('.encore').fadeOut(500, function() {
          numOfEncores--;
          $(this).addClass('garbage').removeClass('encore');
          $(this).removeAttr('id');
          $(this).remove();          
          refresh();
          });
      });
		  //insert new set above "add set" button
		  $(setElmClone).insertBefore($(this));		
    });
    
    /*submit functionality*/
    $("#submit").bind('click', function(){
      
      //variables
      var error = false;
      var error_msg = "";
      
      /*Initial validation*/
      //pull required show information and check values
      var band_abb = $("#required_inputs .artist_input option:selected").val();
      
      var year = $(":input[name=year]").val();
      var month = $(":input[name=month]").val();
      
      var day = $(":input[name=day]").val();
      
      var city = $(":input[name=city]").val();
      var state = $(":input[name=state]").val();
      var venue = $(":input[name=venue]").val();
      
      //check values
      if(band_abb == ""){
        error = true;
        error_msg += "Missing Band<br />";
      }
      if(year == 'Year'){
        error = true;
        error_msg += "Missing Year<br />";
      }
      if(month == 'Month'){
        error = true;
        error_msg += "Missing Month<br />";
      }
      if(day == 'Day'){
        error = true;
        error_msg += "Missing Day<br />";
      }
      if(city == 'City'){
        error = true;
        error_msg += "Missing City<br />";
      }
      if(state == 'State'){
        error = true;
        error_msg += "Missing State<br />";
      }
      
      //check individual songs
      $('.set', '#show').each(function(index) {
        var setNum = (index + 1);      
        $('.song', this).each(function(index) {
          if($(".song_name_input", this).val() == "") {
            error_msg += "Set " + setNum + " song " + (index + 1) + " missing name<br />";
            error = true;
          }
          if($(".song_loc_input", this).val() == ""){
            error_msg += "Set " + setNum + " song " + (index + 1) + " missing file location<br />";
            error = true;
          }
          else if(!($(".song_loc_input", this).val()).match(/.mp3$/i)){
            error_msg += "Set " + setNum + " song " + (index + 1) + " wrong filetype<br />";
            error = true;
          }
        });
      });
      
      //if an error is found alert uploader then return to system
      if(error){
        $('#submit_dialog p').html(error_msg);
        $("#submit_dialog").dialog({
			    height: 300,
			    width: 450,
			    title: "Check yo'self...",
			    modal: true,
			    buttons: {
			      "OK": function() {
              $(this).dialog("destroy");
              } }
		    });
		    
		    return;
      }
      
      //create setlist
      var show_text = ""; //show text to display to the  user for final inspection
      var add_info_text = ""; //additional infomation about particular songs
      var add_info_count = 0; //how many songs currently have additional info
      
      //build show date and unique show id  
      if(month < 10)
        month = '0' + month;
      if(day < 10)
        day = '0' + day;
      var date = year + '-' + month + '-' + day;      
      var showId = band_abb + date;
      
      var full_band_name = $("#required_inputs .artist_input option:selected").text();
      show_text = full_band_name + "<br />";
      show_text += year + '-' + month + '-' + day + "<br />";
      show_text += city + ', ' + state + "<br /><br />";
      
      //cycle through sets
      $('.set', '#show').each(function(index) {
        var curSet = $(this);
        var setNum = (index + 1);     
        show_text += "Set " + setNum + "<br />"; 

        $('.song', this).each(function(index) {
          //get song name
          show_text += $(".song_name_input", this).val();
          //get any add info
          if($('.add_info_input', this).val() !== ""){
            add_info_count++;
            
            for(var i = 0; i < add_info_count; i++){
              add_info_text += '*';
              show_text += '*';
            }
              
            add_info_text += " " + $('.add_info_input', this).val() + "<br />";
          }
          //check if segue or last song
          if($(this).attr('id') == $('.song:last', curSet).attr('id')){
          }
          else if($('.segue_indic', this).hasClass('segue'))
            show_text += " > ";
          else
            show_text += ", ";
            
        });
        show_text += "<br /><br />";
      });
      
      //cycle through encore
      $('.encore', '#show').each(function(index) {
        var curSet = $(this);
        var setNum = (index + 1);   
        if(setNum < 2)
          show_text += "Encore<br />"; 
        else  
          show_text += "Encore " + setNum + "<br />"; 
        $('.song', this).each(function(index) {
          //get song name
          show_text += $(".song_name_input", this).val();
          //get any add info
          if($('.add_info_input', this).val() !== ""){
            add_info_count++;
            
            for(var i = 0; i < add_info_count; i++){
              add_info_text += '*';
              show_text += '*';
            }
              
            add_info_text += " " + $('.add_info_input', this).val() + "<br />";
          }
          //check if segue or last song
          if($(this).attr('id') == $('.song:last', curSet).attr('id')){
          }
          else if($('.segue_indic', this).hasClass('segue'))
            show_text += " > ";
          else
            show_text += ", ";
            
        });
      });
      
      //combine show and additiional info text
      show_text += '<br />' + add_info_text;
      
      //create submit dialog for final user check
      $('#submit_dialog p').html(show_text);
      $("#submit_dialog").dialog({
			  height: 300,
			  width: 450,
			  title: "Final Check",
			  modal: true,
			  buttons: {
          "Check!": function() {
            $(this).dialog("destroy");
            //begin uploading files
            
      //get any additional show information
      var taper = $(":input[name=taper]").val();
      var transferer = $(":input[name=transfered_by]").val();
      var source = $(":input[name=source]").val();
      var mic_loc = $(":input[name=mic_loc]").val();
      var lineage = $(":input[name=lineage]").val();
      var showNotes = $(":input[name=notes]").val();
      
      /*begin uploading songs*/
      //variables
      var uploaded_sets = 0;
      var uploaded_encores = 0;
      var setOrEncore = "";
      var show_xml = "<show "; //xml to be passed to the database      
      
      show_xml += "id='" + showId + "' >";
      show_xml += "<artist abb='" + band_abb + "'>" + full_band_name + "</artist>";
      show_xml += "<date><year>" + year + "</year><month>" + month + "</month><day>" + day + "</day></date>";
      show_xml += "<location><city>" + city + "</city><state>" + state + "</state></location>";
      
      //move though all the sets/encores
      $(".set, .encore", "#show").each(function (i) {
        
        //sets
        if($(this).hasClass('set')){
          uploaded_sets++;
          setOrEncore = "set";
          show_xml += "<set id='";
          if (uploaded_sets < 10)
            var setId = showId + "s0" + uploaded_sets;
          else
            var setId = showId + "s" + uploaded_sets;
        }
        
        //encores
        else{
          uploaded_encores++;
          setOrEncore = "encore";
          show_xml += "<encore id='";
          if (uploaded_encores < 10)
            var setId = showId + "e0" + uploaded_encores;
          else
            var setId = showId + "e" + uploaded_encores;
        }          
          
        show_xml += setId + "' >";
          
        //move through the current set's songs
        $(".song", this).each(function (i) {
          var songNum = (i + 1);
          if ( songNum < 10)
            var songId = setId + "s0" + songNum;
          else
            var songId = setId + "s" + songNum;          
            
          var songName = $(".song_name_input", this).val();
          
          var addInfo = $(".add_info_input", this).val();
            
          var partOfASeuge;
          if($(".segue_indic", this).hasClass("segue"))          
            partOfASeuge = 1;
          else
            partOfASeuge = 0;
          
          show_xml += "<song id='" + songId + "' partOfASeuge='" + partOfASeuge + "' ><song_name>" + songName + "</song_name><song_addInfo>" + addInfo + "</song_addInfo></song>";
            
          /*create an "invisible" form to take advantage of php's '$_FILES' object*/
          var form_id = "UpId" + songId;
          //wrap <form> tags around existing file upload input
          $('.song_loc_input', this).wrap('<form action=\"uploadSong.php\" method=\"POST\" class=\"upload_form\" enctype = \"multipart/form-data\" target=\"' + form_id + '\"></form>');
            
          //create form body
          var upload_form = "<input type=\"hidden\" name=\"showId\" value=\"" + showId + "\" />";
          upload_form += "<input type=\"hidden\" name=\"artist\" value=\"" + full_band_name + "\" />";
          upload_form += "<input type=\"hidden\" name=\"abb\" value=\"" + band_abb + "\" />";
          upload_form += "<input type=\"hidden\" name=\"showDate\" value=\"" + date + "\" />";
          upload_form += "<input type=\"hidden\" name=\"city\" value=\"" + city + "\" />";
          upload_form += "<input type=\"hidden\" name=\"state\" value=\"" + state + "\" />";
          upload_form += "<input type=\"hidden\" name=\"venue\" value=\"" + venue + "\" />";            
          upload_form += "<input type=\"hidden\" name=\"setOrEncore\" value=\"" + setOrEncore + "\" />";
          upload_form += "<input type=\"hidden\" name=\"songNum\" value=\"" + songNum + "\" />";
          upload_form += "<input type=\"hidden\" name=\"songId\" value=\"" + songId + "\" />";
          upload_form += "<input type=\"hidden\" name=\"showId\" value=\"" + showId + "\" />";
          upload_form += "<input type=\"hidden\" name=\"songName\" value=\"" + songName + "\" />";
          upload_form += "<input type=\"hidden\" name=\"partOfASegue\" value=\"" + partOfASeuge + "\" />";
          upload_form += "<input type=\"hidden\" name=\"addInfo\" value=\"" + addInfo + "\" />";
          if(setOrEncore == "set")
            upload_form += "<input type=\"hidden\" name=\"setNum\" value=\"" + uploaded_sets + "\" />";
          else
            upload_form += "<input type=\"hidden\" name=\"setNum\" value=\"" + uploaded_encores + "\" />";
            
          //insert form body into form before existing file input
          $('.song_loc_input', this).before(upload_form);
            
          //change the file input's name
          $('.song_loc_input', this).attr('name', 'upload');
            
          //now that the form is complete create target
          $('body').append('<iframe id="' + form_id + '"></iframe>');
            
          //submit form
          $('form.upload_form', this).submit();
            
          //check if done and results
          check_done($(this).attr('id'), form_id);
        });
          show_xml += "</" + setOrEncore + ">";
        });
        
        show_xml += "</show>";
                
        $.ajax({
          url: 'uploadShow.php',
          type: "POST",
          data: {
            'showId': showId,
            'artist': full_band_name,
            'abb': band_abb,
            'showDate': date,
            'year': year,
            'city': city,
            'state': state,
            'showText': show_text,
            'showXml': show_xml,
            'venue': venue,
            'taper': taper,
            'transferer': transferer,
            'source': source,
            'mic_loc': mic_loc,
            'lineage': lineage,
            'showNotes': showNotes
          },
          success: function (data) {
            //alert(data);
          }
        });
        
        $('#submit_dialog p').html('Done!');
        $("#submit_dialog").dialog({
			    height: 300,
			    width: 450,
			    title: "Upload Completed!",
          modal: true,
			    buttons: {
			      "Upload more!": function() {
              location.reload();
            },
            "Nope, that's it...": function() {
              $(this).dialog("destroy");
              window.close();
            }
          }
        });            
            
          },
          "Wait, that's not right...": function() {
            $(this).dialog("destroy");
          }
        }
      });
    });
    
    /*general functions*/
    function addSong(set){
      
      //clone the original song element
		  var songElmClone = $('#s01s01').clone();
		  
		  //clear all previous values
		  $(":input", songElmClone).attr('value', "");
		  
		  $(".segue_indic", songElmClone).removeClass("segue");
		  
		  /*setup new cloned and cleaned song element*/
		  //set id
		  $(songElmClone).attr('id', 'newDiv');
		  
		  //set remove song functionality
		  $('p.remove_song', songElmClone).bind("click", function(){
		    var numOfChild = $(this).parents('.set').children('.song').length;
		    //make sure they're not creating an empty set
		    if(numOfChild == 1)
		      alert("Can't remove the only song from the set, try removing the set instead");
        else
		      $(this).parents('.song').fadeOut("slow", function() {
          numOfSets--;
          $(this).addClass('garbage').removeClass('song');
          $(this).removeAttr('id');
          $(this).remove(); 
                
          refresh();
        });
      });
      
      //set segue image source
      $('.segue_indic', songElmClone).attr('src', 'images/ul_segue_hollow.png');
      
      //set segue functionality
      $('.segue_indic', songElmClone).bind("click", function(){
        if($(this).hasClass("segue")){
          $(this).removeClass("segue");
          $(this).attr('src', 'images/ul_segue_hollow.png');
        }
        else{
          $(this).addClass("segue");
          $(this).attr('src', 'images/ul_segue_darkGreen.png');          
        }
      });
      
      //set segue tool-tip
      $('.segue_indic', songElmClone).qtip({
          content: "is this song a segue?",
          position: {
            corner: {
              target: 'topMiddle',
              tooltip: 'bottomLeft'
            }
          },
          style: { 
            tip: 'bottomLeft',
            name: 'blue',
            'font-size': 12 
          },   
          show: 'mouseover',
          hide: {
            when: 'mouseout',
            fixed: true
          }
        }); //end creating tool tip
              
      //make it invisable at first
      $(songElmClone).css('display', 'none');
      
      //add new song element
		  $(songElmClone).insertBefore($(set).children('p.add_song'));
		  
		  //display it with ui effects
		  $(songElmClone).fadeIn("slow");

		  refresh();		  
	 }
    
    function refresh(){
      $('.set').each(function(index){
        
        var setNum = index + 1;
                  
        $(this).attr('id', 'set' + setNum);
        
        $('h2', this).text('Set ' + setNum);
        
        //iterate through current set's songs
        $('.song', this).each(function(index){
          if(setNum < 10)
            setId = "0" + setNum;
          else
            var setId = setNum;
          
          var songNum = index + 1;
          if(songNum < 10)
            $(this).attr('id', 's' + setId + 's0' + songNum);
          else
            $(this).attr('id', 's' + setId + 's' + songNum);
        });
      });
      
      //now check encores
      $('.encore').each(function(index){
        var encoreNum = index + 1;
        $(this).attr('id', 'encore' + encoreNum);
        if(encoreNum > 1)
		      $('h2', this).text("Encore (" + encoreNum + ")");
        else
          $('h2', this).text("Encore");
        //iterate through current set's songs
        $('.song', this).each(function(index){
          var songNum = index + 1;
          if(songNum < 10)
            $(this).attr('id', 'e' + encoreNum + 's0' + songNum);
          else
            $(this).attr('id', 'e' + encoreNum + 's' + songNum);
        });
      });
    }    
		
		function check_done(origElm_id, form_id){

      setTimeout(function() {
        if ($('#' + form_id).contents().find('body').text() == "" ){ 
          check_done(origElm_id, form_id);
        }
        else{
        /*  $('body').append('<div id="upload_progress_dialog"><p></p></div>');
          $('#upload_progress_dialog p').html("Done! Uploaded song " + $('#' + form_id).contents().find('body').text());
          //alert("Done! Uploaded song " + $('#' + form_id).contents().find('body').text());   
          $("#upload_progress_dialog").dialog({
			      height: 300,
			      width: 450,
			      title: "Uploading",
			      modal: true
          });
          */  //begin uploading files
          finish_upload(origElm_id, form_id);
        }               
      },
      500);
      
    }
      
    function finish_upload(origElm_id, form_id){
      //remove form and header    
      $('form.upload_form', '#' + origElm_id).remove();
      $('.song_loc_div h6', '#' + origElm_id).remove();
    
      //get server response
      var serverResponse = $('#' + form_id).contents().find('body').text();
    
      //remove iframe
      $('#' + form_id).remove();
      
      //$('#upload_progress_dialog').dialog("destroy");
        
      //replace with upload results
      $('.song_loc_div', '#' + origElm_id).append('<p class="song_upload_results">Upload ' + serverResponse + '!</p>')
        
    }
		
		function check_date(testY, testM, testD){
		  //check if all dates are there
		  if(testY !== 'Year' && testM !== 'Month' && testD !== 'Day' && 
		    $("#required_inputs .artist_input option:selected").val() !== 'Band'){
		      
		    //normalize day and month
		    if(testM < 10)
          testM = '0' + testM
        if(testD < 10)
          testD = '0' + testD
          
        $.ajax({
          type: 'GET',
          url: 'show_check_existence.php',
          data : {
            'show_id': $("#required_inputs .artist_input option:selected").val() + testY + '-' + testM + '-' + testD
          },
          success: function(html){
            if(html == 1){              
              msg = "Sorry, our records show that we already have a show for " +
                $("#required_inputs .artist_input option:selected").text() +
                " on that date (" + testY + '-' + testM + '-' + testD + "). If you think the copy you'd like to upload is higher" +
                " quality than the one we currently have please let us know so we can" +
                " continue to stream the best possible version.";
              $('#submit_dialog p').html(msg);
              $("#submit_dialog").dialog({
			          height: 300,
			          width: 650,
			          title: "Show already exist...",
			          modal: true,
			          buttons: { "Nevermind then": function() {
			                        $(this).dialog("destroy");
                              window.close(); },
			                     "I got the date wrong :(": function() { $(this).dialog("close"); },
                           "Mine's better!": function() { 
                             mess = "Get off your ass and add a message field";

                              //notify me  
                              $.ajax({
                                type: 'GET',
                                url: 'conflict_notify_admin.php',
                                data: {
                                  'show_id': $("#required_inputs .artist_input option:selected").val() + testY + '-' + testM + '-' + testD,
                                  'uploader': username,
                                  'mess': mess
                                },
                                success: function(html){
                                    
                                  }
                                });
                              $('#submit_dialog p').html('Admins have been notified and you should be contacted shortly');
                              $(this).dialog('option', 'buttons', { "Okay, I'm done": function() { window.close(); }, "Upload more": function() {$("#required_inputs .artist_input select[name='band']").attr('value', ""); $(this).dialog("close");} }); }                         
                           }
              });
              
              /*
              
                */
              $("#required_inputs .date_input select[name='year']").attr('value', 'Year');
              $("#required_inputs .date_input select[name='month']").attr('value', 'Month');
              $("#required_inputs .date_input select[name='day']").attr('value', 'Day');
            }//end if
          }
        });
      }
    }
		
  });
		
  </script>
  
</head>
<body>
  <div id="header">
  <div id="logo"><img src="images/logo_banner.png" /></div>
  </div>
  
    <!--Starting modal dialog box-->
    <div id="user_agreement" title="User Upload Agreement">
	<p><em>Reminder:</em> Never upload any material which is copyrighted or you think may be copyrighted. Setr.us is only interested in audience recorded audio or audio which has been officially released by the artist to be publicly traded.</p>
</div>
  
    <h1 class="title">Enter Show Information:</h1>
    
    <div id="required_inputs">
    <div class="input_option">
      <p class="artist_heading input_header">Band:</p>
      <div class="artist_input input">
        <select name="band">
          <option id="band_default_selection" value="">Band</option>
        </select>
      </div>    
    </div>
    
    <div class="input_option">
      <p class="date_heading input_header">When:</p>
      <div class="date_input input">
        <select name="year">
          <option>Year</option>
          <option>2010</option>
          <option>2009</option>
          <option>2008</option>
          <option>2007</option>
          <option>2006</option>
          <option>2005</option>
          <option>2004</option>
          <option>2003</option>
          <option>2002</option>
          <option>2001</option>
          <option>2000</option>
          <option>1999</option>
          <option>1998</option>
          <option>1997</option>
          <option>1996</option>
          <option>1995</option>
          <option>1994</option>
          <option>1993</option>
          <option>1992</option>
          <option>1991</option>
          <option>1990</option>
          <option>1989</option>
          <option>1988</option>
          <option>1987</option>
          <option>1986</option>
          <option>1985</option>
          <option>1984</option>
          <option>1983</option>
          <option>1982</option>
          <option>1981</option>
        </select>
      
        <select name="month">
          <option>Month</option>
          <option>1</option>
          <option>2</option>
          <option>3</option>
          <option>4</option>
          <option>5</option>
          <option>6</option>
          <option>7</option>
          <option>8</option>
          <option>9</option>
          <option>10</option>
          <option>11</option>
          <option>12</option>
        </select>
      
        <select name="day">
          <option>Day</option>
          <option>1</option>
          <option>2</option>
          <option>3</option>
          <option>4</option>
          <option>5</option>
          <option>6</option>
          <option>7</option>
          <option>8</option>
          <option>9</option>
          <option>10</option>
          <option>11</option>
          <option>12</option>
          <option>13</option>
          <option>14</option>
          <option>15</option>
          <option>16</option>
          <option>17</option>
          <option>18</option>
          <option>19</option>
          <option>20</option>
          <option>21</option>
          <option>22</option>
          <option>23</option>
          <option>24</option>
          <option>25</option>
          <option>26</option>
          <option>27</option>
          <option>28</option>
          <option>29</option>
          <option>30</option>
          <option>31</option>
        </select>
      </div>
    </div>
    
    <div class="input_option">
      <p class="show_loc_heading input_header">Where:</p>
      <div class="show_loc_input input">
        <input id="city_input" type="text" name="city" value="City" />
        <select name="state">
          <option>State</option>
          <option>AL</option>
          <option>AK</option>
          <option>AZ</option>
          <option>CA</option>
          <option>CO</option>
          <option>CT</option>
          <option>DC</option>
          <option>DE</option>
          <option>FL</option>
          <option>GA</option>
          <option>HI</option>
          <option>ID</option>
          <option>IN</option>
          <option>IL</option>
          <option>IA</option>
          <option>KS</option>
          <option>KY</option>
          <option>LA</option>
          <option>ME</option>
          <option>MD</option>
          <option>MA</option>
          <option>MI</option>
          <option>MN</option>
          <option>MS</option>
          <option>MO</option>
          <option>MT</option>
          <option>NE</option>
          <option>NV</option>
          <option>NH</option>
          <option>NJ</option>
          <option>NM</option>
          <option>NY</option>
          <option>NC</option>
          <option>ND</option>
          <option>OH</option>
          <option>OK</option>
          <option>OR</option>
          <option>PA</option>
          <option>RI</option>
          <option>SC</option>
          <option>SD</option>
          <option>TN</option>
          <option>TX</option>
          <option>UT</option>
          <option>VT</option>
          <option>VA</option>
          <option>WA</option>
          <option>WV</option>
          <option>WI</option>
          <option>WY</option>
        </select>

        <input id="venue_input" type="text" name="venue" value="Venue (may be left blank)" />
      </div>
    </div>
    </div>
    
    <p id="opt_info_header">Optional Information +</p>
    
    <div id="optional_inputs" class="hidden">
    <div class="input_option">
      <p class="input_header">Taped by:</p>
      <div class="input">
        <input type="text" name="taper" value="" class="opt" />
      </div>
    </div>

    <div class="input_option">
      <p class="input_header">Transfered by:</p>
      <div class="input">
        <input type="text" name="transfered_by" value="" class="opt" />
      </div>
    </div>
    
    <div class="input_option textarea">
      <p class="input_header">Source:</p>
      <div class="input">
        <textarea name="source" rows="3" cols="45" value=""  class="opt"></textarea>
      </div>
    </div>
    
    <div class="input_option textarea">
      <p class="input_header">Mic Location:</p>
      <div class="input">
        <textarea name="mic_loc" rows="3" cols="45" value=""  class="opt"></textarea>
      </div>
    </div>
    
    <div class="input_option textarea">
      <p class="lin_heading input_header">Lineage:</p>
      <div class="lin_input input">
        <textarea name="lineage" rows="3" cols="45" value=""  class="opt"></textarea>
      </div>
    </div>
    
    <div class="input_option textarea">
      <p class="input_header">Show notes:</p>
      <div class="input">
        <textarea name="notes" rows="3" cols="45"  class="opt"></textarea>
      </div>
    </div>
    </div>

    <div id='show'>
    <div id='sets'>
      <div id='set1' class='set'>
        <h2>Set 1</h2>
        <div id='s01s01' class='song'>
          <div class="song_name_div">
            <h6>Song name:</h6>
            <input type="text" name="s01s01_name" class="song_name_input" size="25" />
          </div>
          <div class="song_loc_div">
            <h6>File location:</h6>
            <input type="file" name ="s01s01_loc" class="song_loc_input" />
          </div>
          <img src="images/ul_segue_hollow.png" alt="segue?" class="segue_indic" />
          <div class='addInfo'>
            <p>additional info:</p>
            <textarea name="s1s01_addInfo" class="add_info_input opt" rows="1" cols="40"></textarea>
          </div>
          <p class="remove_song">remove song -</p>
        </div>
        <p class="add_song">add song +</p>
        <p class="remove_set">remove set -</p>
      </div>
    <p class="add_set">add set +</p>
    <p class="add_encore">add encore +</p>
    </div>
    </div>
    
    <p id="submit"><button>upload!</button></p>
 
    <!--Submit Dialog, confirm/warning-->
    <div id="submit_dialog">
	    <p></p>
    </div>
    
    <!--Placeholder for upload form-->
    

    </body>
    </html>

