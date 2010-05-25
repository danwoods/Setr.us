//use the following function as such
//thisMovie("music_player").nameofflashfunction(para);
//
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
  if(document.getElementById('active_playlist').hasChildNodes == 'false'){
    alert("add songs to the playlist!!");
    }
    
    //variables
    var searchIndex = index;
    var song_info = "";

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
    
    //loop through playlist
    var trackIsFound = 'false';
        
    var playlistElm = document.getElementById('active_playlist');
    //var playlistItems = playlistElm.getElementsByTagName('playlistItem');
    var currIndex = 0;
    
    while((currIndex < playlistElm.childNodes.length)&&(trackIsFound =='false')){
      //assign temporary/reuseable variables
      var currElm = playlistElm.childNodes[currIndex];
      //only check the nodes with ids
      if(currElm.hasAttributes('id')){
        var currElmId = currElm.getAttribute('id');
            
        if(searchIndex == currElmId.substring(0,4)){
          trackIsFound = 'true';
          var tempElmArr = currElm.ChildNodes;
                    
          song_info = getSongInfo(currElm);
                   
          //alert("song_info = " + song_info);
          }//end if
        }//end if
        currIndex++;
      }//end while

      if(trackIsFound == 'false'){
        alert("Next track not found :(");
      }
      
    //this will be used in flashPlrUpdateIndex()  
    currPositionInPL = currElmId.substring(0,4);    
    
    var trackLocation = currElmId.substring(4);
    
    //setup file location
    //get band abb
    var bandElm = currElm.getElementsByTagName("td");
    for( var i = 0; i < bandElm.length; ++i){
        if ( bandElm[i].getAttribute("class") === 'song_artist'){
          var band = bandElm[i].firstChild.data;
          }
        }
    
    var newAbb = $.ajax({
      url: "php/artistToAbb.php",
      type: "GET",
      async: false,
      data: ({artist : band}),
      dataType: "html"
    }).responseText;
    
    //get show date
    for( var i = 0; i < bandElm.length; ++i){
        if ( bandElm[i].getAttribute("class") === 'song_date'){
          var showDate = bandElm[i].firstChild.data;
          }
        }
    
    var trackDirectory = "music_directory" + "\/" + newAbb + "\/" + newAbb + showDate;
    //alert("track directory = " + trackDirectory);
    trackLocation = trackLocation + ".mp3";
    //alert("track Location = " + trackLocation);
    
    trackLocation = trackDirectory + "\/" + trackLocation;
    
    //setup index to send flash
    var newnewindex = currElmId.substring(0,4);
    newnewindex = newnewindex - 0;
      
    //send info to flash
    getFlashMovie("music_player").changeTrack(trackLocation, song_info, newnewindex, 'false');
    
    //alert ("new index being sent to flash = " + newnewindex);
    //resetFlashPlayerIndex(currElmId.substring(0,4));
  }

function abb(artistFullName){
  $.ajax({
      url: "php/artistToAbb.php",
      type: "GET",
      data: ({artist : artistFullName}),
      dataType: "html",
      success: function(html){
         alert("returned html = " + html);
      }
    }).responseText;
  }
