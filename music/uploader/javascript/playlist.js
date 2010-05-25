
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
var highlight;                              //used to pass the setInterval object between functions
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
  //check if element has class to make it identifiable
  if(newSong.attr('class')){
    //check if member of playlist
    if(newSong.attr('class') === 'row-even'||newSong.attr('class') === 'row-odd'){

      //account for clones, this should always be true of playlist elements
      if(elementId.match("clone_")){
        elementId = elementId.substr(6);
        }
        //get rid of 'fav_' tag
      if(elementId.match("fav_")){
        elementId = elementId.substr(4);        
        }
      
      //remove element from playlist
      var duplicateTr = document.getElementById(elementId);
      document.getElementById('active_playlist').removeChild(document.getElementById(elementId));
      
      //alert flash to the change in playlist
      updateFlashPlrIndex(elementId, 'dec');
      //insert song into it's new position
      
      document.getElementById('active_playlist').insertBefore(duplicateTr, document.getElementById(droppableId));
      reOrder();
      reColor();

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
          //alert(file_loc + params);
        }
        else if(isPlaylist == 'true'){
          var params = ("elementId=" + elementId + "&username=" + username);
          var file_loc = "php/p_plSongInfoRetrevial.php?";
          //alert(file_loc + params);
        }
        else{
          var params = ("elementId=" + elementId);
          var file_loc = "php/p_songInfoRetrevial.php?";
        }
        
        //alert(file_loc + params);
        
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
}//end if has class
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
       alert("Error reading the response: " +e.toString());
     }//end catch
   }//end if
   else{
     alert("There was a problem retriving the data:\n" + xmlHttp.statusText);
   }//end else
 }//end if
}//end function

function p_handleServerResponse_test(droppableId, elementId){
  //alert("entered handleServerResponse_test, parameters are droppableId = " + droppableId);// + " and elementId = " + elementId);
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
    //alert("song location = " + song_location);

    //create a new table row based on this information and assign the new song_id the the new 
    trBuilder_test(song_id, song_name, song_artist, song_date, song_location, droppableId);
    
    //droppableId = song_id;

    
  }//end for
reOrder();                                                    
reColor();

//reset playlist variable
playlist_loaded = 'false';
}//end function

function trBuilder_test(song_id, song_name, song_artist, song_date, song_location, droppableId){                              
/******begin building the new <tr>*******/

//alert(song_id);
//set DOM reference
var newParent = document.getElementById('active_playlist');
//create new <tr>
var newTr = document.createElement('tr');

//create song name <td>
var newTd = document.createElement('td');
newTd.setAttribute('class', 'song_name');
//var newText = document.createElement('h2');
newTd.appendChild(document.createTextNode(song_name));
//newTd.appendChild(newText);
//and add it to the new <tr>
newTr.appendChild(newTd);

//create artist <td>
var newTd = document.createElement('td');
newTd.setAttribute('class', 'song_artist');
//var newText = document.createElement('h2');
newTd.appendChild(document.createTextNode(song_artist));
//newTd.appendChild(newText);
//and add it to the new <tr>
newTr.appendChild(newTd);

//create date <td>
var newTd = document.createElement('td');
newTd.setAttribute('class', 'song_date');
//var newText = document.createElement('h2');
newTd.appendChild(document.createTextNode(song_date));
//newTd.appendChild(newText);
//and add it to the new <tr>
newTr.appendChild(newTd);

//create location <td>
var newTd = document.createElement('td');
newTd.setAttribute('class', 'song_location');
//var newText = document.createElement('h2');
newTd.appendChild(document.createTextNode(song_location));
//newTd.appendChild(newText);
//and add it to the new <tr>
newTr.appendChild(newTd);

//create extras <td>
var newTd = document.createElement('td');
newTd.setAttribute('class', 'song_extras');

//set up delete button
//Note: I want a visual change when this is clicked (in the brief second it's held down)
var delImage = new Image();
delImage.setAttribute('class', 'del_btn');
delImage.src = "images/12-em-cross.png";
newTd.appendChild(delImage);


//set up favorite button
var favImage = new Image();

//add class
favImage.setAttribute('class', 'fav_btn');

//check to see if this is one of the users favorite songs

var isFavSong = 'false';
for(var i = 0; i < favSongArray.length; i++){
  //alert(favSongArray[i]);
  if(song_id == favSongArray[i]){
    isFavSong = 'true';
  }//end if    
}//endfor

//set image source
if(isFavSong == 'false'){
favImage.src = "images/16-star-cold.png";
}
else{favImage.src = "images/16-star-hot.png";}

//place image in song extras element
newTd.appendChild(favImage);

 
//and add it to the new <tr>
newTr.appendChild(newTd);

//set the new <tr>s id
//get num of rows
var rowArray = document.getElementById('active_playlist').getElementsByTagName('tr');
//alert("active playlist has " + rowArray.length + "nodes");

//create new id
var newId;
//set the prefix of the id NOTE: rowArray already 1 node at start(this is a 1-based index
if(rowArray.length > 10)
  newId = "00" + (rowArray.length-1);
  else{newId = "000" + (rowArray.length-1);}
if(rowArray.length > 100)
    newId = "0" + (rowArray.length-1);
if(rowArray.length > 1000)
    newId = rowArray.length;

//add the son's actual unique song id to the prefix
newId += song_id;

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
    

//end for

}//end function

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

function reOrder(){
  //need to update remove also
  //alert("entered reOrder");
  var mainElm = document.getElementById('active_playlist');
  var songElm = mainElm.childNodes;
  var newId = "";
  var count = 0;
  
  for(var currElm = mainElm.firstChild; currElm !== null; currElm = currElm.nextSibling){
    if(currElm.nodeType === 1){

    var elementId = currElm.getAttribute("id");
    //if this is a playlist item
    if(elementId.match(/\b\d{4}/)){
      //remove it from droppables
      //Droppables.remove(elementId);
      //and get to the root of it's id by removing the leading playlist position
      elementId = elementId.substr(4);
      
      if(count < 10)
        newId = "000" + (count);
      else if (count < 100)
        newId = "00" + count;
      else if(count < 1000)
        newId = "0" + (count);
      if(count >= 1000)
        newId = count;

      newId += elementId;
 
      currElm.setAttribute('id', newId);
      currElm.setAttribute("onDblClick", "getTrack(\'" + newId.substring(0,4) + "\')");
      //alert(currElm.getAttribute("onDblClick"));
      
      //reset the element as a droppable
      var acceptClasses = new Array("song", "row-even", "row-odd", "artist", "year", "show", "set", "fav_artist", "playlist");
      $(currElm).droppable({
			    drop: function(event, ui) {
				      p_process_test(ui, $(currElm));
			    }
		  });
      //Droppables.add(newId, {
        //      accept: acceptClasses, onDrop : p_process_test });
      
      //reset remove button
      var delButton = currElm.getElementsByTagName('img')[0];
      delButton.setAttribute('id', newId + 'del');
      delButton.setAttribute('onclick', "removeSong(\"" + newId + "del\")");
      count++;
    }//end if
  }//end if
    
  }//end for
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


function removeSong(elmId){
  /*function takes in id of delImage and deletes the whole <tr>*/
  oldNode = document.getElementById(elmId);
  oldNode.parentNode.parentNode.parentNode.removeChild(oldNode.parentNode.parentNode);
  reOrder();
  reColor();
  //i should make sure this doesn't affect the playing order
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
  //alert("unset index = " + index);
  if(index < 10)
    index = "000" + (index);
    else if (index < 100)
      index = "00" + (index);
      else if(index < 1000)
        index = "0" + (index);
        if(index >= 1000)
          index = index;
        
    var mainElm = document.getElementById('active_playlist');
    var elmIndex = "";
  
    for(var currElm = mainElm.firstChild; currElm !== null; currElm = currElm.nextSibling){
      if(currElm.nodeType === 1){

      var elementId = currElm.getAttribute("id");

      if(elementId.match(/\b\d{4}/)){
        
        elmIndex = elementId.substr(0,4);
    //    alert("elmIndex = " + elmIndex + "index = " + index);
        
                
        if(elmIndex === index){
          var that = currElm;
          clearInterval(highlight);
  //alert("cleared Interval");
  if(that.hasAttribute("style")){
    //using .previousSibling. because the lement we're looking for is the one before the current one
  that.removeAttribute("style");
          //alert("style removed");
        }
          }
        }
      }
    }
    //attempt at getting song length via javascript
getSongLength('stop');
//reColor();
//alert(songLength);
}

function clear_playlist(){
  //get a list of all trs in the playlist;
  var songList = document.getElementById('active_playlist').getElementsByTagName('tr');
  //while 'empty' is not found, cycle through trs, removing them
  for(var s = 0; songList[s].getAttribute('id') !== 'empty'; s++){
    if(songList[s].getAttribute('id') !== 'empty'){
      document.getElementById('active_playlist').removeChild(songList[s]);
    }//end if
    //as songList is updated dynamically, keep the var s where it is
    s--;
  }//end for
  
  //update flash player index
  //resetFlashPlayerIndex(0);
  
  //update playlist variable
  playlist_loaded = 'false';
}

function favSong(elmId){
  songId = elmId.substr(4);
  songId = songId.replace(/fav/g, '');
  if(document.getElementById(elmId).getAttribute('src') === "images/16-star-cold.png"){
    setUserFavSongs(username, songId, 'inc');
    document.getElementById(elmId).setAttribute('src', 'images/16-star-hot.png');
  }
  else{
    setUserFavSongs(username, songId, 'dec');
    document.getElementById(elmId).setAttribute('src', 'images/16-star-cold.png');
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
