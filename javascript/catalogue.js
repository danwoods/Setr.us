/********************
 * This file contains functions which pertain to accordian-style
 * the catalouge of music
 * 
 * Author: Dan Woodson
 * Last Evolution: 12/23/09
*********************/

//main function
function expand(callingImg, artist, request, username){
  
  //trace calling arrow image back to it's parent
  var origElm = $(callingImg).parent();
  
  //check if the element is already expanded
  if($(origElm).hasClass('expanded')){
    //if so, remove all it's decendant ordered list
    $(origElm).children('ol').remove();
    //reset arrow image and class tag
    $(callingImg).attr("src","images/16-arrow-right.png");
    $(origElm).removeClass('expanded');
  }
  
    
  else{
    /**ajax triage**/  
    //set placeholder variables
    var retrivalFile; //holds the location of the retrival file
    var data; //holds the data to be passed to php
  
    //get location of php retrival file based on the request
    //set parameters based on calling object			
	  if(request == "artists"){
	    retrivalFile = "php/getArtists.php";
	    params = "";
    }
    else if(request == "years"){
	    retrivalFile = "php/getYears.php";
	    params = "artist="+artist;
	  }
    else if(request == "shows"){
	    retrivalFile = "php/getShows.php";
	    params = "artistYear="+artist;
	  }
    else if(request == "songs"){
	    retrivalFile = "php/getSongs.php";
	    params = "artistShow="+artist;
	  }	  
			 
    //begin actual ajax
    $.ajax({   
			
	    //set parameters
	    url: retrivalFile,
	    type: "GET",
	    data: params,
	    cache: false,
	    async: false,
	    //dataType: "xml",
			
	    //on success
	    success: function (data) {
	    
	      //change arrow image
			  $(callingImg).attr("src","images/16-arrow-down.png");
			
			  //call function to handle xml traversal and placement
			  if(request == "artists")
	        addArtists(artist, request, origElm, data);
        else if(request == "years")
	        addYears(artist, request, origElm, data);
        else if(request == "shows")
	        addShows(artist, request, origElm, data);
        else if(request == "songs")
	        addSongs(artist, request, origElm, data);
        else
          alert("Sorry there was an error retrieving that data, we're looking into it");
      },
      //on error
      error:function (XMLHttpRequest, textStatus, errorThrown){
        alert("Sorry there was an error retrieving that data, we're looking into it");
        //alert("ERROR!");
        //alert('textStatus = ' + textStatus + '\n' + 
          //  'errorThrown = ' + errorThrown);
      }       
   
  });
}

}
  
function addArtists(artist, request, origElm, xml){
  
  //create ordered list
  //set new <ol>s class
  var olClass = "artists"; //holds the class of new <ol>
      
  //create id for new <ol>
  var olId = "artists"; //holds the id of new <ol>
	    
  //create actual <ol> element
	var ol = $('<ol></ol>').attr('id',olId)
                          .addClass(olClass)
                          .appendTo(origElm);
		  	
	//create the <li> elements from the returned xml
	//create class for new <li>s, (just the request minus the 's')
  var liClass = request.substring(0, request.length-1);
  //traverse xml
	$('element', xml).each(function(){

		//create id for new <li> based on artist abbreviation
		var artist = $(this).text();		
		
		$.ajax({
        url: "php/artistToAbb.php", 
		    data: {artist: artist}, 
		    dataType: "html", 
		    async: false,
		    success: function(html) {
        var artistAbb = html;
		
		    //create li			  
        var li = $('<li></li>').attr('id', artistAbb)
                               .addClass(liClass)
                               .appendTo(ol);	
                           
        //create arrow icon/button for li
        var img = $('<img />').attr('id', artistAbb + 'ArrowImg')
                              .attr("src","images/16-arrow-right.png")
                              //.attr('onclick', "expand(this, '" + artistAbb + "', 'years', '" + username + "')")
                              .addClass("expImg")
                              .appendTo(li)
                              .bind('click', function() {
                                expand(this, artistAbb, 'years', username);
                              });	
                                     
        var artistTxt = $('<h2>' + artist + '</h2>')
                           .addClass("artist_txt")
                           .attr('onMouseOver', 'catMouseOver(this)')
                           .attr('onMouseOut', 'catMouseOut(this)')
                           .appendTo(li);
                           
        //tag the ol element's class
        $($(origElm)[0]).addClass('expanded');
        
        //make new element draggable
        $(li).draggable({ helper: 'clone', appendTo: 'body' });
				
      }//end on success     
     
    });//end ajax call
					  
  });//end each(function()
  
}

function addYears(artist, request, origElm, xml){
  
  //create ordered list
  //set new <ol>s class
  var olClass = "years"; //holds the class of new <ol>
      
  //create id for new <ol>
  var olId = artist + "years"; //holds the id of new <ol>
	  
  //create actual <ol> element
	var ol = $('<ol></ol>').attr('id',olId)
                         .addClass(olClass)
                         .appendTo(origElm);

		  
	//create the <li> elements from the returned xml
	//create class for new <li>s, (just the request minus the 's')
  var liClass = "year";
  //traverse xml
	$('element', xml).each(function(){
			  
		//create id for new <li> based on artist abbreviation
		var year = $(this).text();
		
		//create li			  
    var li = $('<li></li>').attr('id', artist + year)
                           .addClass(liClass)
                           .appendTo(ol);	
                           
    //create arrow icon/button for li
    var img = $('<img />').attr('id', artist + year + 'ArrowImg')
                          .attr("src","images/16-arrow-right.png")
                          .addClass("expImg")
                          //.attr('onclick', "expand(this, '" + artist + year + "', 'shows', '" + username + "')")
                          .appendTo(li)
                          .bind('click', function() {
                            expand(this, artist + year, 'shows', username);
                          });
                                     
    var artistTxt = $('<h2>' + year + '</h2>')
                           .addClass("year_txt")
                           .attr('onMouseOver', 'catMouseOver(this)')
                           .attr('onMouseOut', 'catMouseOut(this)')
                           .appendTo(li);	
                           
    //make new element draggable
    $(li).draggable({ helper: 'clone', appendTo: 'body' });
				
  });
  
  //tag the ol element's class
  $($(origElm)[0]).addClass('expanded');
				  
  }

function addShows(artist, request, origElm, xml){
  
  //create artist abbreviation from 'artist'
  var artistAbb = artist.slice(0, -4);
  
  //create ordered list
  //set new <ol>s class
  var olClass = "shows"; //holds the class of new <ol>
      
  //create id for new <ol>
  var olId = artist + "shows"; //holds the id of new <ol>
	  
  //create actual <ol> element
	var ol = $('<ol></ol>').attr('id',olId)
                          .addClass(olClass)
                          .appendTo(origElm);

		  
	//create the <li> elements from the returned xml
	//create class for new <li>s, (just the request minus the 's')
  var liClass = "show";
  //traverse xml
	$('element', xml).each(function(){
			  
		//create id for new <li> based on artist abbreviation
		var show = $(this).text();
		
		//create li			  
    var li = $('<li></li>').attr('id', artistAbb + show)
                           .addClass(liClass)
                           .appendTo(ol);	
                           
    //create arrow icon/button for li
    var img = $('<img />').attr('id', artistAbb + show + 'ArrowImg')
                          .attr("src","images/16-arrow-right.png")
                          .addClass("expImg")
                          //.attr('onclick', "expand(this, '" + artistAbb + show + "', 'songs', '" + username + "')")
                          .appendTo(li)
                          .bind('click', function() {
                            expand(this, artistAbb + show, 'songs', username);
                          });	
                                     
    var artistTxt = $('<h2>' + show + '</h2>')
                           .addClass("show_txt")
                           .attr('onMouseOver', 'catMouseOver(this)')
                           .attr('onMouseOut', 'catMouseOut(this)')
                           .appendTo(li);

    var show_info_cat_icon = $('<img />').attr('id', artistAbb + show + 'info_icon')
                          .attr("src","images/16-message-info.png")
                          .addClass("show_info_cat_icon")
                          .appendTo(li)
                          .bind('mouseover', function() {
                            //on mouseover, use ajax to request the show's xml
                            $.ajax({
                              url: 'php/showXmlToHtml.php',
                              data: { show: artistAbb + show },
                              datatype: 'xml',
                              success: function (xml) {
                                //on success, parse the xml into a string of html
                                var showDiv = '<div id="show_tooltip">';
                                var songAddInfoDiv = '<div id="show_tooltip_notes">';
                                var songInfoCounter = 0;
                                $(xml).find('show').each(function(){
                                  var setCounter = 0; //counter for number of sets
                                  var encoreCounter = 0; //counter for encores
                                  showDiv += '<span id="show_tooltip_artist">' + $(this).find('artist').text() + '</span>';
                                  
                                  //add and format the show's date
                                  showDiv += '<span id="show_tooltip_date">';
                                  if($(this).find('month').text()[0] == '0')
                                    showDiv += $(this).find('month').text()[1];
                                  else
                                    showDiv += $(this).find('month').text();
                                  showDiv += '-';
                                  if($(this).find('day').text()[0] == '0')
                                    showDiv += $(this).find('day').text()[1];
                                  else
                                    showDiv += $(this).find('day').text();
                                  showDiv += '-';
                                  showDiv += $(this).find('year').text() + '</span><hr />';
                                  
                                  //add set and encore information
                                  $(this).find('set').each(function(){
                                    setCounter++;
                                    var curSet = $(this);
                                    showDiv += '<div><span class="show_tooltip_setHeading">Set ' + setCounter + '</span><br />';
                                    showDiv += '<span class="show_tooltip_songs">';
                                    $(this).find('song').each(function(){
                                      showDiv += $(this).find('song_name').text();
                                      
                                      //check if any additional information
                                      if($(this).find('song_addInfo').text() !== '') {
                                        songInfoCounter++;
                                        for(i = 0; i < songInfoCounter; i++) {  
                                          //add astericks to setlist                                         
                                          showDiv += '*';
                                          //add astericks to song notes
                                          songAddInfoDiv += '*';
                                        }
                                        //write it to the song info div
                                        songAddInfoDiv += $(this).find('song_addInfo').text() + '<br />';
                                      }
                                        
                                      //check if last element
                                      if($(this).attr('id') == $('song:last', curSet).attr('id')){
                                      }
                                      else if($(this).attr('partOfASeuge') == 1)
                                        showDiv += " > ";
                                      else
                                        showDiv += ', ';
                                    });
                                    showDiv += '</span></div><br />';
                                  });
                                  $(this).find('encore').each(function(){
                                    encoreCounter++;
                                    var curEncore = $(this);
                                    if(encoreCounter > 1)                                    
                                      showDiv += '<br /><div><span class="show_tooltip_setHeading">Encore ' + encoreCounter + '</span><br />';
                                    else
                                      showDiv += '<div><span class="show_tooltip_setHeading">Encore</span><br />';
                                      showDiv += '<span class="show_tooltip_songs">';
                                    $(this).find('song').each(function(){
                                      showDiv += $(this).find('song_name').text();
                                      
                                      //check if any additional information
                                      if($(this).find('song_addInfo').text() !== '') {
                                        songInfoCounter++;
                                        for(i = 0; i < songInfoCounter; i++) {  
                                          //add astericks to setlist                                         
                                          showDiv += '*';
                                          //add astericks to song notes
                                          songAddInfoDiv += '*';
                                        }
                                        //write it to the song info div
                                        songAddInfoDiv += $(this).find('song_addInfo').text() + '<br />';
                                      }
                                        
                                      //check if last element
                                      if($(this).attr('id') == $('song:last', curEncore).attr('id')){
                                      }
                                      else if($(this).attr('partOfASeuge') == 1)
                                        showDiv += " > ";
                                      else
                                        showDiv += ', ';
                                    });
                                    showDiv += '</span></div><br />';
                                  });
                                  songAddInfoDiv += '</div>';
                                  showDiv += (songAddInfoDiv + '</div>');
                                });
                              
                                 //and create a tool tip with the string of html as it's content
                                $(show_info_cat_icon).qtip({
                                  content: {
                                    text: showDiv
                                  },
                                  position: {
                                    corner: {
                                      target: 'rightMiddle',
                                      tooltip: 'leftMiddle'
                                    }
                                  },
                                  style: { 
                                    tip: 'leftMiddle',
                                    name: 'red',
                                          'font-size': 9,
                                          'font-family': 'verdana',
                                    width: 420
                                  },   
                                  show: { ready: true },
                                  hide: {
                                    fixed: true,
                                    when: { event: 'mouseleave' }
                                  }
                                });
                              }
                          });
                        });
         
                           
    //make new element draggable
    $(li).draggable({ helper: 'clone', appendTo: 'body' });
				
  });
  
  //tag the ol element's class
  $($(origElm)[0]).addClass('expanded');
				  
  }

function addSongs(artist, request, origElm, xml){
  
  //create ordered list
  //set new <ol>s class
  var olClass = "sets"; //holds the class of new <ol>
      
  //create id for new <ol>
  var olId = artist + 'sets';
	  
  //create actual <ol> element
	var ol = $('<ol></ol>').attr('id',olId)
                          .addClass(olClass)
                          .appendTo(origElm);
  var setNum = 0;
  
  //create ordered list based on sets and encores
  //cycle through sets
  $('set', xml).each(function(){    
    //increment setNum
    setNum++;
    addSet(xml, 'set', setNum, artist, ol);    
  });
  
  //reset setNum
  var encoreNum = 0;
  
  //cycle through encores
  $('encore', xml).each(function(){
    encoreNum++;
    addSet(xml, 'encore', encoreNum, artist, ol);
  });
  
  //tag the ol element's class
  $($(origElm)[0]).addClass('expanded');

}

function addSet(xml, type, setNum, artistAbb, origElm){
  
  //create ordered list
  //set new <ol>s class
  var liClass = type; //holds the class of new <ol>
      
  //create id for new <ol>
  if(type == 'set'){    
    if(setNum < 10)
      var liId = artistAbb + "s0" + setNum; //holds the id of new <li>
    else
      var liId = artistAbb + "s" + setNum; //holds the id of new <li>
    }
  else{
    if(setNum < 10)
      var liId = artistAbb + "e0" + setNum; //holds the id of new <li>
    else
      var liId = artistAbb + "e" + setNum; //holds the id of new <li>
    }
    
    
	  
  //create actual <ol> element
	var li = $('<li></li>').attr('id',liId)
                         .addClass(liClass)
                         .appendTo(origElm);
                           
  //give it text
  if(type == 'set')
    var artistTxt = $('<h4>' + "Set " + setNum + '</h4>')
                               .addClass("set_txt")
                               .attr('onMouseOver', 'catMouseOver(this)')
                               .attr('onMouseOut', 'catMouseOut(this)')
                               .appendTo(li);	
                               
  else
    var artistTxt = $('<h4>' + "Encore" + '</h4>')
                               .addClass("set_txt")
                               .attr('onMouseOver', 'catMouseOver(this)')
                               .attr('onMouseOut', 'catMouseOut(this)')
                               .appendTo(li);
                               
    //make new element draggable
    $(li).draggable({ helper: 'clone', appendTo: 'body', start: function(e, ui) {
                                                                  $(ui.helper).addClass("drag_set"); }
                    });
    
    //create <ol> to hold songs
    var olId = liId + 'songs';
    var olClass = 'songs';
    
    var ol = $('<ol></ol>').attr('id',olId)
                           .addClass(olClass)
                           .appendTo(li);
                           
    //cycle through current set's songs creating <li>s for each
    $('song', $(type, xml)[setNum-1]).each(function(){
      
      //create <li> variables
      var liClass = "song"
      var songName = $(this).find("song_name").text();
      var songId = $(this).find("song_id").text();
      var songAddInfo = $(this).find("song_info").text();	
      var segue = $(this).find("segue").text();	
		
		  //create li			  
      var li = $('<li></li>').attr('id', songId)
                             .addClass(liClass)
                             .appendTo(ol);	
                           
      var artistTxt = $('<h2>' + songName + '</h2>')
                               .addClass("song_txt")
                               .attr('onMouseOver', 'catMouseOver(this)')
                               .attr('onMouseOut', 'catMouseOut(this)')
                               .appendTo(li);	
                               
      //if there is extra information associated with a song, display an indicator and create a tooltip
      if(songAddInfo != ""){
        
        var addInfoSpan = $('<span>*</span>').attr('id', songId + 'addInfo')
                              .css('cursor', 'pointer')
                              .appendTo(li)
                              .bind('mouseenter', function(){
                                //create tool tip
                                var selector = '#' + songId + 'addInfo';

                                $(selector).qtip({
                                  content: songAddInfo,
                                  position: {
                                    corner: {
                                      target: 'topMiddle',
                                      tooltip: 'bottomLeft'
                                    }
                                  },
                                  style: { 
                                    tip: 'bottomLeft',
                                    name: 'blue',
                                    'font-size': 10 
                                  },   
                                  show: { ready: true },
                                  hide: {
                                    fixed: true,
                                    when: { event: 'mouseleave' }
                                  }
                                }); //end creating tool tip
                              });//end binding mounseenter
      } //end if (addInfo)      
      
      //setup suege indicator
      if(segue != "0")        
        var segue_indicator = $('<span class="segue_indicator"> ></span>').appendTo(li);	
                               
      //make new element draggable
      $(li).draggable({ helper: 'clone', appendTo: 'body' });
        
		}); //end for each song
}

function catMouseOver(elm){
  //get an array of child nodes
  var childElms = elm.parentNode.childNodes;

  //cycle through the element's child nodes
  for(var i = 0; i < childElms.length; i++){
    //get the element's class
    var currClass = childElms[i].getAttribute('class');
    //append 'highlight' to the class
    currClass += ' highlight';
    //reassign the class
    childElms[i].setAttribute('class', currClass);
  }
}

function catMouseOut(elm){
  
  var childElms = elm.parentNode.childNodes;
  
  for(var i = 0; i < childElms.length; i++){
    var currClass = childElms[i].getAttribute('class');
    currClass = currClass.replace(/ highlight/, "");
    
    childElms[i].setAttribute('class', currClass);
  }
}

function catClearHighlight(){
  var highlightElms = getElementsByClassName('highlight');

  for(var i = 0; i < highlightElms.length; i++){
    var currClass = highlightElms[i].getAttribute('class');
    currClass = currClass.replace(/ highlight/, "");
    highlightElms[i].setAttribute('class', currClass);
  }
}
