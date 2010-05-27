//use the following function as such
//thisMovie("music_player").nameofflashfunction(para);
//

var highlight;                         //used to pass the setInterval object between functions
var currently_playing = $('#empty');   //used to hold the currently playing song, useful in stopping "flash" animation

function getFlashMovie(movieName) {
  var isIE = navigator.appName.indexOf("Microsoft") != -1;
  return (isIE) ? window[movieName] : document[movieName];  }
  
function getSongInfo(elm){
  /*THIS MAY BREAK IN WINDOWS*/
  var strings = [];
  getStrings(elm, strings);
  return strings.join("");
}
  
function getStrings(elm, strings){
  var count = 0;
  if(elm.nodeType == 3){
    //alert(count + " = " + elm.data);
    strings.push(elm.data + ' ');
    //alert("song_info = " + strings);
  }
  else if(elm.nodeType == 1){
    for(var i = elm.firstChild; i !== null; i = i.nextSibling){
      getStrings(i, strings);
    }
  }
}

function getTrack(index){
  /*This function is called from the flash player 
   * as well as when individual tracks in the playlist 
   * are double clicked, and returns the next track*/
  
  //check to see if there are any songs in the playlist
    if( $('#playlist > *').length < 2 ){
      alert("add songs to the playlist!!");
    }
    
    //variables
    var searchIndex = index;
    var song_info = "";
/*
  //this if sequence accumulates leading zeros
  if(searchIndex.length < 2){
    searchIndex = "0" + searchIndex;
    }
  if(searchIndex.length < 3){
    searchIndex = "0" + searchIndex;
    }
  if(searchIndex.length < 4){
    searchIndex = "0" + searchIndex;
    }
  */  
    //loop through playlist
    var trackIsFound = 'false';
        
    var playlistElm = $('#playlist');
    //var playlistItems = playlistElm.getElementsByTagName('playlistItem');
    var currIndex = 0;
    
    while((currIndex < $(playlistElm).children().length)&&(trackIsFound =='false')){
      //assign temporary/reuseable variables
      var currElm = $('#playlist > .playlist_item')[currIndex];
      //only check the nodes with ids
      
        var currElmId = $(currElm).attr('id');
        //alert('currElmId.substring(4, 8) = ' + currElmId.substring(4, 8) + ' searchIndex = ' + searchIndex);
            
        if(searchIndex == currElmId.substring(4, 8)){
          trackIsFound = 'true';
          var tempElmArr = currElm.ChildNodes;
                    
          //song_info = getSongInfo(currElm);
                   
          //alert("song_info = " + song_info);
          }//end if

        currIndex++;
      }//end while

      if(trackIsFound == 'false'){
        alert("Next track not found :(");
      }
      
    //this will be used in flashPlrUpdateIndex()  
    currPositionInPL = currElmId.substring(4,8);    
    
    var trackLocation = currElmId.substring(9);
    //alert('track location = ' + trackLocation);
    
    //setup file location
    //get band abb
    /*var bandElm = currElm.getElementsByTagName("td");
    for( var i = 0; i < bandElm.length; ++i){
        if ( bandElm[i].getAttribute("class") === 'song_artist'){
          var band = bandElm[i].firstChild.data;
          }
        }
    */
    //turn currElm into jquery object
    //currElm = $('currElm');
    var songName = $("#" + currElmId + ' .playlist_item_name').text();
    var artist = $("#" + currElmId + ' .playlist_item_artist').text();
    var location = $("#" + currElmId + ' .playlist_item_location').text();
    var newAbb = $.ajax({
      url: "php/artistToAbb.php",
      type: "GET",
      async: false,
      data: ({artist : artist}),
      dataType: "html"
    }).responseText;
    
    //get show date
    var showDate = $('#' + currElmId + ' .playlist_item_date').text();
    
    //setup track info
    song_info = songName + ' ' + artist + ' ' + showDate + ' ' + location;
    
    /*setup track location*/
    var trackDirectory = "music_directory" + "\/" + newAbb + "\/" + newAbb + showDate;
    
    trackLocation = trackLocation + ".mp3";
    
    trackLocation = trackDirectory + "\/" + trackLocation;
    
    //setup index to send flash
    var newnewindex = currElmId.substring(4,8);
    
    //convert to Number
    newnewindex = newnewindex - 0;
    
    /*clear old highlight*/
    //stop currently playing song from flashing/pulsing
    currently_playing.stop(true, false);
    clearInterval(highlight);
    //and reset colors
    $("#playlist .playlist_item:odd").css("background", "#d6d6d6");
    $("#playlist .playlist_item:even").css("background", "#b3b3b3");
    
    //set highlight
    highlight = setInterval(function(){$('#' + currElmId).animate({backgroundColor: "#49a0e1"}, 2000).animate({backgroundColor: "#185D92"}, 2000);}, 4000);
    
    //but, since the interval takes 4000 ms before it starts, start the animation immediately, here
    $('#' + currElmId).animate({backgroundColor: "#49a0e1"}, 2000).animate({backgroundColor: "#185D92"}, 2000);
    
    //set new currently playing
    currently_playing = $('#' + currElmId);
    
    //send info to flash
    getFlashMovie("music_player").changeTrack(trackLocation, song_info, newnewindex, 'false');
  }

function abb(artistFullName){
  $.ajax({
      url: "php/artistToAbb.php",
      type: "GET",
      data: ({artist : artistFullName}),
      dataType: "html",
      success: function(html){
         //alert("returned html = " + html);
      }
    }).responseText;
  }
