
/******************************************
 * Filename: playlist.js
 * Description: javascript functions pertaining to the playlist
 * To-Do: reColor()
 * Last Updated: 6/6/09
 * Notes: Need to streamline the usage of reOrder(), I 
 *        feel like it's not being used effeciently
 ******************************************/

var xmlHttp = createXmlHttpRequestObject();
var currPositionInPL = 0;
var playlistTitle = "Untitled";
var playlist_loaded = 'false';
var songLength;

//this is defined in music_player.js do I need it here?
function getFlashMovie(movieName) {
  var isIE = navigator.appName.indexOf("Microsoft") != -1;
  return (isIE) ? window[movieName] : document[movieName];  }

function p_createXmlHttpRequestObject(){
  var xmlHttp;
  
  try{
    xmlHttp = new XMLHttpRequest();
  }
  catch(e){
    var XmlHttpVersions = new Array("MSXML2.XMLHTTP.6.0",
                                    "MSXML2.XMLHTTP.5.0",
                                    "MSXML2.XMLHTTP.4.0",
                                    "MSXML2.XMLHTTP.3.0",
                                    "MSXML2.XMLHTTP",
                                    "Microsoft.XMLHTTP");
                                    
    for(var i = 0; i < XmlHttpVersions.length && !xmlHttp; i++){
      try{
        xmlHttp = new ActiveXObject(XmlHttpVersions[i]);
      }
      catch(e){}
    }
  }
  
  if(!xmlHttp){
    alert("Error creating the XMLHttpRequest object.");
  }
  else{
    return xmlHttp;
  }
}

function p_process_test(ui, droppable){
  var isFavorite = 'false';
  var isPlaylist = 'false';
  
  var elementId = $(ui.draggable).attr("id");
  var droppableId = droppable.attr('id');
  var newSong = $(ui.draggable);
  
  //alert($(newSong).attr('class') + ', ' + $(newSong).attr('id'));

  
  //check if element has class to make it identifiable
    //check if member of playlist
    if(newSong.hasClass('.playlist_item')){

      //account for clones, this should always be true of playlist elements
      if(elementId.match("clone_")){
        elementId = elementId.substr(6);
      }
        //get rid of 'fav_' tag
      if(elementId.match("fav_")){
        elementId = elementId.substr(4);        
      }
      if(elementId.match("pli_")){
        elementId = elementId.substr(9);
      }

      //alert flash to the change in playlist
      updateFlashPlrIndex(elementId, 'dec');
      
      if($('#playlist .playlist_item_temp').length < 1)
        $(ui.draggable).insertBefore('#' + droppableId);
      else  
        $('.playlist_item_temp').replaceWith(ui.draggable);

      
      reOrder();
      //reColor();

    }//end if is a playlist item
    
    else if(newSong.hasClass('song')||newSong.hasClass('artist')||
            newSong.hasClass('year')||newSong.hasClass('show')||
            newSong.hasClass('set')||newSong.hasClass('playlist')){
              
    //account for clones
    if(elementId.match("clone_")){
      elementId = elementId.substr(6);
    }
    
    if(elementId.match("fav_")){
      elementId = elementId.substr(4);
      isFavorite = 'true';      
    }
        
    if(elementId.match("pl_")){
      elementId = elementId.substr(3);
      isPlaylist = 'true';              
    }    
    
    //check if xmlHttp is available        
    if(xmlHttp){
      //if yes try
      try{
        if(isFavorite == 'true'){
          var params = ("elementId=" + elementId + "&username=" + username);
          var file_loc = "php/p_favSongInfoRetrevial.php?";
        }
        else if(isPlaylist == 'true'){
          var params = ("elementId=" + elementId + "&username=" + username);
          var file_loc = "php/p_plSongInfoRetrevial.php?";
        }
        else{
          var params = ("elementId=" + elementId);
          var file_loc = "php/p_songInfoRetrevial.php?";
        }
        //alert('made it here');
        //open xmlHttp
        xmlHttp.open("GET", file_loc + params, true);
        //check ready state
        xmlHttp.onreadystatechange = function(){p_handleRequestStateChange_test(droppableId, elementId);};
        //alert("attempted to call p_handleRequestStateChange_test");
        xmlHttp.send(null);
      }//end try
      catch(e){
        alert("Can't connect to server: \n" + e.toString());
      }//end catch
    }//end if xmlHHttp
  }//end else if from catalouge
//end if has class
}//end function

function p_handleRequestStateChange_test(droppableId, elementId){
  //alert("entered handleRequestStateChange droppable = " + droppableId);
  if(xmlHttp.readyState == 4){
    //alert("from handleRequestStateChange ready state = " + xmlHttp.readyState);
   if(xmlHttp.status == 200){
     try{
       p_handleServerResponse_test(droppableId, elementId);
     }//end try
     catch(e){
       //alert("Error reading the response: " +e.toString());
     }//end catch
   }//end if
   else{
     alert("There was a problem retriving the data:\n" + xmlHttp.statusText);
   }//end else
 }//end if
}//end function

function p_handleServerResponse_test(droppableId, elementId){
  //pull xml from xml response
  var xmlResponse = xmlHttp.responseXML;
 
  //check to see if xml was pulled
  if(!xmlResponse || !xmlResponse.documentElement){
    throw("Invalid XML Structure:\n" + xmlHttp.responseText);
  }
 
  //access xml root
  var rootNodeName = xmlResponse.documentElement.nodeName;
 
  //check for errors
  if(rootNodeName == "parsererror"){
    throw("Invalid XML Strucutre");
  }
  
  //ugh... get the root again?
  xmlRoot = xmlResponse.documentElement;

  //declare placeholder variables
  var song_id = "song id not found";
  var song_name = 'song name not found';
  var song_artist = 'artist not found';
  var song_date = 'date not found';
  var song_location = 'location not found';
  
  var songArray = xmlRoot.getElementsByTagName("song");

  for(var i = 0; i < songArray.length; i++){

    //set reference in xml
    var song_info = xmlRoot.getElementsByTagName("song")[i];
    //alert("stored tag 'song' in song_info");

    //begin extracting values from xml
    var song_id_node = song_info.childNodes[0];
    song_id = song_id_node.childNodes[0].nodeValue;
    //alert("song id = " + song_id);

    var song_name_node = song_info.childNodes[1];
    song_name = song_name_node.childNodes[0].nodeValue;
    //alert("song name = " + song_name);

    var song_artist_node = song_info.childNodes[2];
    song_artist = song_artist_node.childNodes[0].nodeValue;
    //alert("song artist = " + song_artist);

    var song_date_node = song_info.childNodes[3];
    song_date = song_date_node.childNodes[0].nodeValue;
    //alert("song date = " + song_date);

    var song_location_node = song_info.childNodes[4];
    song_location = song_location_node.childNodes[0].nodeValue;
    if(song_location === "unknown, unknown"){
      song_location = 'unknown';
    }
    
    var song_segue_node = song_info.childNodes[5];
    song_segue = song_segue_node.childNodes[0].nodeValue;
    if(song_segue == 1)
      song_name += ' >';
    //alert("song location = " + song_location);

    //create a new table row based on this information and assign the new song_id the the new 
    trBuilder_test(song_id, song_name, song_artist, song_date, song_location, droppableId);
    
    //droppableId = song_id;

    
  }//end for
reOrder();              
//I don't know why this doesn't throw an error. I can't find reColor() anywhere                                  
reColor();

//reset playlist variable
playlist_loaded = 'false';
}//end function

function trBuilder_test(song_id, song_name, song_artist, song_date, song_location, droppableId){                              
/******begin building the new <tr>*******/

//var rowArray = document.getElementById('playlist').getElementsByTagName('div');
var numOfPli = $('#playlist').children().length;
//alert("active playlist has " + rowArray.length + "nodes");

//create new id
var newId;
//set the prefix of the id NOTE: rowArray already 1 node at start(this is a 1-based index
/*
if(rowArray.length > 10)
  newId = "00" + (rowArray.length-1);
  else{newId = "000" + (rowArray.length-1);}
if(rowArray.length > 100)
    newId = "0" + (rowArray.length-1);
if(rowArray.length > 1000)
    newId = rowArray.length;
    */

if(numOfPli < 10)
  newId = "000" + numOfPli;
else if(numOfPli < 100)
  newId = "00" + numOfPli;
else if(numOfPli < 1000)
  newId = "0" + numOfPli;
else
  newId = numOfPli;

//add the son's actual unique song id to the prefix
song_id = newId + '_' + song_id;
/*
//set the new id and class attribute for <tr> and <img>
newTr.setAttribute('id', newId);
newTr.setAttribute('class', 'song');
newTr.setAttribute("onDblClick", "getTrack(" + newId.substring(0,4) + ")");

//set the new id attribute for delImage and attach to removeSong function
delImage.setAttribute('id', newId + 'del');
delImage.setAttribute('onclick', "removeSong(\"" + newId + "del\")");

//set the new id attribute for favImage and attach to favSong function
favImage.setAttribute('id', newId + 'fav');
favImage.setAttribute('onclick', "favSong(\"" + newId + "fav\")");

//positioning
newParent.insertBefore(newTr, document.getElementById(droppableId));

//Here is where to snycronize player and playlist (increment)
updateFlashPlrIndex(newId, 'inc');

//set it as a draggable
//new Draggable(newId, {superghosting: true, detached: false, scroll: 'active_playlist'});
$(newTr).draggable({ helper: 'clone', appendTo: 'body' });

//set it as droppable
var acceptClasses = new Array("song", "row-even", "row-odd", "artist", "year", "show", "set");

$(newTr).droppable({
			drop: function(event, ui) {
				p_process_test(ui, $(newTr));
			}
		});
//Droppables.add(newId, {
    //accept: acceptClasses, onDrop : function(DRAGGABLE){  p_process_test(newId, DRAGGABLE.id); }});
    
*/
//end for
//alert('song id = ' + song_id);
//alert($('#' + droppableId).attr('class'));
//alert($('#playlist .playlist_item_temp').length);

if($('#playlist .playlist_item_temp').length < 1)
  $('<div id="pli_' + song_id + '"></div>').insertBefore('#' + droppableId);
else  
  $('.playlist_item_temp').replaceWith('<div id="pli_' + song_id + '"></div>');

$('#pli_' + song_id).attr('class','playlist_item')
                    .draggable({ helper: 'clone', appendTo: 'body',
                                  start: function(e, ui) {
                                    $(ui.helper).addClass("ui-draggable-helper");
                                  }
                               })
                    .droppable({
                      drop: function(event, ui) {
                        p_process_test(ui, $(this));
                      }
                    })
                    .bind('dropover', function(event, ui) {
                      $('<div class="playlist_item_temp"></div>').insertBefore(this);
                      $('.playlist_item_temp').slideDown("slow");
                    })
                    .bind('dropout', function(event, ui) {
                      $('.playlist_item_temp').slideUp("slow");
                      $('.playlist_item_temp').remove();
                    })
                    .bind('dblclick', function() {
                      getTrack(song_id.substring(0,4));
                    });
                    
$('<div>' + song_name + '</div>').appendTo('#pli_' + song_id).attr('class','playlist_item_name').disableSelection();
$('<div>' + song_artist + '</div>').appendTo('#pli_' + song_id).attr('class','playlist_item_artist').disableSelection();
$('<div>' + song_date + '</div>').appendTo('#pli_' + song_id).attr('class','playlist_item_date').disableSelection();
$('<div>' + song_location + '</div>').appendTo('#pli_' + song_id).attr('class','playlist_item_location').disableSelection();
$('<div></div>').appendTo('#pli_' + song_id).attr('class','playlist_item_extras').disableSelection();

//delete button/favorite button
//check if song is one of user's favorites
var isFavSong = 'false';
for(var i = 0; i < favSongArray.length; i++){
  //alert(favSongArray[i]);
  if(song_id.substr(5) == favSongArray[i]){
    isFavSong = 'true';
  }//end if    
}//endf
if(isFavSong == 'true')
  var src = 'images/16-star-hot.png';
else
  var src = 'images/16-star-cold.png';
  

$('<img></img>').attr('class', 'fav_btn')
                .attr('src', src)
                .appendTo('#pli_' + song_id + ' .playlist_item_extras')
                .bind('click', function() {
                  favSong(this);                 
                });
                
$('<img></img>').attr('class', 'del_btn')
                .attr('src', 'images/12-em-cross.png')
                .appendTo('#pli_' + song_id + ' .playlist_item_extras')
                .bind('click', function() {
                  $(this).parents('.playlist_item').remove('.playlist_item');                 
                });

}//end function

/*

function reColor(){
  var playlist = document.getElementById('active_playlist');
  var trs = playlist.getElementsByTagName('tr');
  for(var i = 0; i < trs.length; i++){
    trs[i].removeAttribute('class');
    //alert("i = " + i);
    //alert( i + " mod 2 = " + (i % 2));
    if( (i % 2) === 0){      
      if(trs[i].getAttribute('id') !== 'empty'){
        trs[i].setAttribute('class', 'row-even');
      }
    }
    else{trs[i].setAttribute('class', 'row-odd');}
    //alert("updated class");
  }
}
*/
function reOrder(){
  //need to update remove also
  //alert("entered reOrder");
  //var mainElm = document.getElementById('playlist');
  //var songElm = mainElm.childNodes;
  var newId = "";
  //var count = 0;
  
  $('#playlist .playlist_item').each(function(index) {
    
      //elementId = $(this).attr('id').substr(8);
      
      if((index+1) < 10)
        newId = "000" + (index+1);
      else if ((index+1) < 100)
        newId = "00" + (index+1);
      else if((index+1) < 1000)
        newId = "0" + (index+1);
      else
        newId = (index+1);

      newId = "pli_" + newId + '_' + $(this).attr('id').substr(9);
      
      //alert('visiting pli with new id of '  + newId);
      
      $(this).attr('id', newId);
      //currElm.setAttribute('id', newId);
      //currElm.setAttribute("onDblClick", "getTrack(\'" + newId.substring(0,4) + "\')");
      $(this).bind('dblclick', function(){
        getTrack(index+1);
      });
      //alert(currElm.getAttribute("onDblClick"));
      
      //reset the element as a droppable
      //var acceptClasses = new Array("song", "row-even", "row-odd", "artist", "year", "show", "set", "fav_artist", "playlist");
      //$(currElm).droppable({
			    //drop: function(event, ui) {
				    //  p_process_test(ui, $(currElm));
			    //}
		  //});
      //Droppables.add(newId, {
        //      accept: acceptClasses, onDrop : p_process_test });
      
      //reset remove button
      //var delButton = currElm.getElementsByTagName('img')[0];
      //delButton.setAttribute('id', newId + 'del');
      //delButton.setAttribute('onclick', "removeSong(\"" + newId + "del\")");
      //count++;
    //}//end if
  //}//end if
    
  });//end for
}

function updateFlashPlrIndex(elmPos, dir){
  //get index
  var changedPos = elmPos.substring(0,4);
  
  //check to see if current playing index is affected
  if(changedPos < currPositionInPL){
    //alert("attempting call to flash");
    getFlashMovie("music_player").updateIndex(dir);
  }

}

function resetFlashPlayerIndex(newIndex){
  getFlashMovie("music_player").resetIndex(newIndex);
}

function setHighlight(elmId, index, songLength){
  //alert("called set highlight, params are " + elmId + " " + index + " " + songLength);
  var colors = ["#FEFEFF", "#F1F8DC", "#CFE787", "#ACD531", "#CFE787","#F1F8DC"];
  var nextColor = 0;
  
  //var highlight;
  if(index < 10)
        index = "000" + (index);
      else if (index < 100)
        index = "00" + (index);
      else if(index < 1000)
        index = "0" + (index);
      if(index >= 1000)
        index = index;
  //alert("called set highlight, params are " + elmId + " " + index + " " + songLength);
  
  //this will be useful for finding the element but pulsate will not work, need to research animations in javascript
  
    var mainElm = document.getElementById('active_playlist');
    var elmIndex = "";
  
    for(var currElm = mainElm.firstChild; currElm !== null; currElm = currElm.nextSibling){
      if(currElm.nodeType === 1){

      var elementId = currElm.getAttribute("id");

      if(elementId.match(/\b\d{4}/)){
        
        elmIndex = elementId.substr(0,4);
        
                
        if(elmIndex == index){
          var that = currElm;
          highlight = setInterval(function(){
            that.style.background = colors[(nextColor++)%(colors.length)];
          }, 175);
        }
      }
    }
  }//end for
getSongLength('start');
}

function unSetHighlight(index){
  alert("called unset highlight");
  //clearInterval(highlight);
}

function clear_playlist(){
  var emptyClone = $('#empty').clone(true);
  $('#playlist').empty();  
  $('#playlist').append(emptyClone);
  $("#empty").droppable({
			drop: function(event, ui) {
				p_process_test(ui, $('#empty'));
			}
		});
  
  //update playlist variable
  playlist_loaded = 'false';
}

function favSong(elm){
  //songId = elmId.substr(4);
  //songId = songId.replace(/fav/g, '');
  var songId = $(elm).parents('.playlist_item').attr('id');
  songId = songId.substr(9);
  alert('song id = ' + songId);
  if($(elm).attr('src') === "images/16-star-cold.png"){
    alert('calling setUserFavSongs(' + username + ', ' + songId + ', inc)');
    setUserFavSongs(username, songId, 'inc');
    $(elm).attr('src', 'images/16-star-hot.png');
  }
  else{
    setUserFavSongs(username, songId, 'dec');
    $(elm).attr('src', 'images/16-star-cold.png');
  }
  getUserInfo('danwoods');
}//end function

function save_playlist(form){
  alert("entered save");
  var titleElm = document.getElementById('playlist_title');
  var newPlaylistTitle = document.playlistTitleForm.playlistTitleInput.value;
  var playlist = "";
  
  //need to cycle through playlist song ids here
  var songList = document.getElementById('active_playlist').getElementsByTagName('tr');
  for(var s = 0; songList[s].getAttribute('id') !== 'empty'; s++){
    if(songList[s].getAttribute('id') !== 'empty'){
      var currId = songList[s].getAttribute('id');
      currId = currId.substring(4);
      playlist += currId + ' ';
    }//end if
    
  }
  //alert(playlist);
  
  if(xmlHttp){
      //alert("sever is available");
      //if yes try
      try{
        var params = ("username=" + username + "&oldPlaylistTitle=" + playlistTitle + "&newPlaylistTitle=" + newPlaylistTitle + "&playlist=" + playlist);
        //alert("parameters being sent to the server are " + params);
        alert('playlist_loaded = ' + playlist_loaded);
        //if(playlist_loaded === 'false'){
          xmlHttp.open("GET", "php/savePlaylist.php?" + params, true);
        //}
        //else if(playlist_loaded === 'true'){
          //xmlHttp.open("GET", "php/updatePlaylist.php?" + params, true);
        //}
        xmlHttp.send(null);
      }//end try
      catch(e){
        alert("Can't connect to server: \n" + e.toString());
      }//end catch
    }//end if xmlHHttp
  
  //set new playlist title
  playlistTitle = newPlaylistTitle;
  
  titleElm.removeChild(titleElm.firstChild);
  titleElm.appendChild(document.createTextNode(playlistTitle));
  titleElm.setAttribute('onClick', 'editPlaylistTitile();');
  
  //set playlist variable
  playlist_loaded = 'true';
  
  //alert(playlistTitle);
  }

function editPlaylistTitile(){
  var titleElm = document.getElementById('playlist_title');
  titleElm.removeChild(titleElm.firstChild);
  titleElm.setAttribute('onClick', '');
  //alert(titleElm.getAttribute('onclick'));
  var newForm = document.createElement('form');
  newForm.setAttribute('onSubmit', 'renamePlaylist(this.form);')
  newForm.setAttribute('name', 'playlistTitleForm');
  var newInput = document.createElement('input');
  newInput.setAttribute('id', 'playlistTitleInput');
  newInput.setAttribute('name', 'playlistTitleInput');
  newInput.setAttribute('class', 'text');
  newInput.setAttribute('type', 'text');
  newInput.setAttribute('value', playlistTitle);
  //newInput.setAttribute('action', 'php/savePlaylist.php');
  newInput.setAttribute('method', 'GET');
  
  //<input id="username" name="username" class="text" type="text" />
  newForm.appendChild(newInput);
  titleElm.appendChild(newForm);
  //alert('called edit playlist title');
  }

function renamePlaylist(){
  alert("entered rename");
  var titleElm = document.getElementById('playlist_title');
  var newPlaylistTitle = document.playlistTitleForm.playlistTitleInput.value;
  
  //set new playlist title
  playlistTitle = newPlaylistTitle;
  
  titleElm.removeChild(titleElm.firstChild);
  titleElm.appendChild(document.createTextNode(playlistTitle));
  titleElm.setAttribute('onClick', 'editPlaylistTitile();');
  
  //set playlist variable
  playlist_loaded = 'true';
  
}

function getSongLength(start_stop){
  if(start_stop == 'start'){
    songLength = 0;
    var thisSongLength = setInterval(function(){songLength++;}, 1000);
    //need to adapt for songs > an hour
    //songLength = songLength/60;
    }
  else if(start_stop == 'stop'){
    clearInterval(thisSongLength);
    var min = songLength/60;
    var sec = songLength%60;
    songLength = min + ':' + sec;
    }
  else{
    alert("impropper arguements sent to getSongLength");
    }
  }
    
function abb(artistFullName){
  $.ajax({
      url: "php/artistToAbb.php",
      global: false,
      type: "GET",
      data: ({artist : artistFullName}),
      dataType: "html",
      success: function(html){
         alert("returned html = " + html);
      }
    });
  }
  
$.fn.extend({ 
        disableSelection : function() { 
                this.each(function() { 
                        this.onselectstart = function() { return false; }; 
                        this.unselectable = "on"; 
                        jQuery(this).css('-moz-user-select', 'none'); 
                }); 
        } 
}); 
