/********************
 * This file contains functions which pertain to the catalouge
 * of music
*********************/

//NOTE: NEED TO ESCAPE QUOTATIONS IN TOOL TIPS (should do that in php)
var xmlHttp = createXmlHttpRequestObject();

function createXmlHttpRequestObject(){
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

function process(group, artist, expandTo, username){
  //alert(expandTo);
  if(xmlHttp.readyState == 4 || xmlHttp.readyState == 0){
    try{
      if(expandTo == 'fav_artist' || expandTo == 'fav_years' || expandTo == 'fav_shows' || expandTo == 'fav_songs' || expandTo == 'playlist' || expandTo == 'pl_songs'
    ){
        var params = "group=" + group + "&artist=" + artist + "&expandTo=" + expandTo + "&username=" + username;
      }
      else{
       var params = "group=" + group + "&artist=" + artist + "&expandTo=" + expandTo;
      }
      //alert(params);
      xmlHttp.open("GET", "php/xml.php?" + params, true);
      //the weird function stuff is because you're assigning a function to a variable
      xmlHttp.onreadystatechange = function(){handleRequestStateChange(group, artist, expandTo, username);};
      xmlHttp.send(null);
    }//end try
    catch(e){
      alert("Can't connect to server: \n" + e.toString());
    }
  }
  
}

function handleRequestStateChange(group, artist, expandTo, username){
  
  if(xmlHttp.readyState == 4){
    //alert("from handleRequestStateChange ready state = " + xmlHttp.readyState);
    //alert("xmlHttp.status = " + xmlHttp.status);
   if(xmlHttp.status == 200){
     try{
       handleServerResponse(group, artist, expandTo, username);
     }//end try
     catch(e){
       alert("Error reading the response: " + e.toString());
     }//end catch
   }//end if
   else{
     alert("There was a problem retriving the data:\n" + xmlHttp.statusText);
   }//end if
 }//end if
}//end function

function handleServerResponse(group, artist, expandTo, username){
  //////CODE PROVIDE FROM PHP FREAKS//////
  
  if(expandTo == 'years'){
    group = artist;
  }
 
  var new_group = abb(artist);
 
  var xmlResponse = xmlHttp.responseXML;
 
  if(!xmlResponse || !xmlResponse.documentElement){
    throw("Invalid XML Structure:\n" + xmlHttp.responseText);
  }
 
  var rootNodeName = xmlResponse.documentElement.nodeName;
 
  //alert("xmlResponse.documentElement.nodeName = " + rootNodeName);
 

  if(rootNodeName == "parsererror"){
    throw("Invalid XML Strucutre");
  }
 
  xmlRoot = xmlResponse.documentElement;
 
   if(expandTo == 'songs' || expandTo == 'fav_songs'){
      var myDiv = document.getElementById(group);
      
      ///////////////////////////////////////////////////////////////////////////
      //I need to re-evaluate the way I orrganizes/gives class names to my html structure
      //This is where I'm currently working
      ///////////////////////////////////////////////////////////////////////////
      myDiv.setAttribute('class', 'show');

/*establish an array to hold all the sets (mainly for a reference to the number of elements tagged <set>, 
 * is there a better way to do this?)
 */
var setArray = xmlRoot.getElementsByTagName("set");

//create array of all the ids I'm creating
  var setIdArray = new Array();

//cycle through these instructions for each set
for(var i = 0; i < setArray.length; i++){
  //create new <ol> element
  var newOl = document.createElement('ol');
    //assign it's id to be the actual unique_song_id
    var setNum = i+1;
  newOl.setAttribute('id', group + 's' + setNum);
  newOl.setAttribute('class', 'set');
  //add id to proper id array
  setIdArray[i] = (group + 's' + (i+1));
  //create the text for the ol
  var newText = document.createElement('h4');
  //store the set label in the text
  newText.appendChild(document.createTextNode("Set " + (i+1)));
  //set up the hover function
  newText.setAttribute('onMouseOver', 'catMouseOver(this);');
  newText.setAttribute('onMouseOut', 'catMouseOut(this);');
  //add the new text to the <ol>
  newOl.appendChild(newText);
  //find the number  of elements tagged <song> in the current  set
  var songArray = xmlRoot.getElementsByTagName("set")[i].getElementsByTagName("song");

  //create array of all the ids I'm creating
  var songIdArray = new Array();
    
  //cycle through these instructions for each song
  for(var n = 0; n < songArray.length; n++){
    //create a new <li> element
    var newLi = document.createElement('li');
    //access the elements for the current song and store them in x
    x=xmlRoot.getElementsByTagName("set")[i].getElementsByTagName("song")[n];
    //access the currents song's <unique_song_id> tag and store it in y
    y=x.childNodes[0];
    //access the text within the song's <unique_song_id> tag and store it in txt
    idTxt=y.childNodes[0].nodeValue;
    //assign what's stored in txt as the new <li> element's id
    newLi.setAttribute('id', idTxt);
    newLi.setAttribute('class','song');
    songIdArray[n] = idTxt;
    //access the currents song's <song_name> tag and store it in z
    z=x.childNodes[1];
    //access the text within the song's <song_name> tag and store it in txt
    txt=z.childNodes[0].nodeValue;
    var newText = document.createElement('h2');
    newText.appendChild(document.createTextNode(txt));
    
    //set up the hover function for thee text
    newText.setAttribute('onMouseOver', 'catMouseOver(this);');
    newText.setAttribute('onMouseOut', 'catMouseOut(this);');
    
    //set class
    newText.setAttribute('class', 'song_txt');
  
    //append the text
    newLi.appendChild(newText);
    
    //check to see if song has any additional information
    if(xmlRoot.getElementsByTagName("set")[i].getElementsByTagName("song")[n].getElementsByTagName("song_info")[0].hasChildNodes()){
      //alert(xmlRoot.getElementsByTagName("set")[i].getElementsByTagName("song")[n].getElementsByTagName("song_info")[0].firstChild.nodeValue);
      //create info message image 
      var cat_song_info_btn = new Image();
      //assign it's source
      cat_song_info_btn.src = "images/16-message-info.png";
      //set pop-up info ballon function
      cat_song_info_btn.setAttribute('onMouseOver', 'tooltip.show("' + xmlRoot.getElementsByTagName("set")[i].getElementsByTagName("song")[n].getElementsByTagName("song_info")[0].firstChild.nodeValue + '");');
      cat_song_info_btn.setAttribute('onMouseOut', 'tooltip.hide()');
      //add to the <li>
      newLi.appendChild(cat_song_info_btn);
      }
    //alert(xmlRoot.getElementsByTagName("set")[i].getElementsByTagName("song")[n].getElementsByTagName("song_info")[0].firstChild.nodeValue);
    //if(add_info !== undefined){
      //
    //}
    //make the new <li> draggable
    //new Draggable(idTxt, { revert: true, snap: [40, 40] });
    newOl.appendChild(newLi);
  }//end for(var n
    myDiv.appendChild(newOl);
    
    //turn all the new <li>s into draggable elements
  for(var n = 0; n < songIdArray.length; n++){
    //var thisId = songIdArray[n];
    new Draggable(songIdArray[n], {superghosting: true, detached: true, onEnd: catClearHighlight});
  }
  }//end for(var i
  
  //turn all the new <li>s into draggable elements
  for(var n = 0; n < setIdArray.length; n++){
    new Draggable(setIdArray[n], {superghosting: true, detached: true, onEnd: catClearHighlight});
  }
  
  
  //For Encores
  var encoreArray = xmlRoot.getElementsByTagName("encore");

for(var i = 0; i < encoreArray.length; i++){
  var newOl = document.createElement('ol');
  newOl.setAttribute('id', group + 'e');
  newOl.setAttribute('class', 'set');
  var newText = document.createElement('h4');
  newText.appendChild(document.createTextNode("Encore "));
  
  //set up the hover function
  newText.setAttribute('onMouseOver', 'catMouseOver(this);');
  newText.setAttribute('onMouseOut', 'catMouseOut(this);');
  
  newOl.appendChild(newText);
  var songArray = xmlRoot.getElementsByTagName("encore")[i].getElementsByTagName("song");
  var encoreIdArray = new Array();
  for(var n = 0; n < songArray.length; n++){
    var newLi = document.createElement('li');
    x=xmlRoot.getElementsByTagName("encore")[i].getElementsByTagName("song")[n];
    y=x.childNodes[0];//get <unique_song_id>
    txt=y.childNodes[0].nodeValue;
    //alert("___" + txt + "___");
    newLi.setAttribute('id', txt);
    //alert("newLiId = " + newLi.getAttribute('id'));
    encoreIdArray[n] = txt;
    z=x.childNodes[1];//get <song_name>
    txt=z.childNodes[0].nodeValue;
    var newText = document.createElement('h2');
    newText.appendChild(document.createTextNode(txt));
    //set up the hover function
    newText.setAttribute('onMouseOver', 'catMouseOver(this);');
    newText.setAttribute('onMouseOut', 'catMouseOut(this);');
    
    //set class
    newText.setAttribute('class', 'song_txt');
  
    newLi.appendChild(newText);
    
    //check to see if song has any additional information
    if(xmlRoot.getElementsByTagName("encore")[i].getElementsByTagName("song")[n].getElementsByTagName("song_info")[0].hasChildNodes()){
      //alert(xmlRoot.getElementsByTagName("encore")[i].getElementsByTagName("song")[n].getElementsByTagName("song_info")[0].firstChild.nodeValue);
      //create info message image 
      var cat_song_info_btn = new Image();
      //assign it's source
      cat_song_info_btn.src = "images/16-message-info.png";
      //set pop-up info ballon function
      cat_song_info_btn.setAttribute('onMouseOver', 'tooltip.show("' + xmlRoot.getElementsByTagName("encore")[i].getElementsByTagName("song")[n].getElementsByTagName("song_info")[0].firstChild.nodeValue + '");');
      cat_song_info_btn.setAttribute('onMouseOut', 'tooltip.hide()');
      //add to the <li>
      newLi.appendChild(cat_song_info_btn);
      }
    
    newLi.setAttribute('class', 'song');
    newOl.appendChild(newLi);
    
    
  }//end for(var n
    myDiv.appendChild(newOl);
    //make all new encore songs draggable
    /////****************************************/////
    //I should go after this and climb back up making all the <ol>s
    //draggable, and also do this after every set
    /////****************************************/////
    for(var q = 0; q < encoreIdArray.length; q++){
      new Draggable(encoreIdArray[q], {superghosting: true, detached: true, onEnd: catClearHighlight});
      }//end for q
      
  }//end for(var i
  var olArray = document.getElementsByTagName('ol');
  //alert(olArray[(olArray.length-1)].getAttribute('id'));
      for(var count = (olArray.length - 1); count > 0; count--){
      new Draggable(olArray[count].getAttribute('id'), {superghosting: true, detached: true, onEnd: catClearHighlight});
    }
}//end if(....

else if(expandTo == 'pl_songs'){
      var myDiv = document.getElementById(group);
      
      
  //create new <ol> element
  var newOl = document.createElement('ol');
    
  newOl.setAttribute('id', group + '_songs');
  newOl.setAttribute('class', 'playlist');
  
  var songArray = xmlRoot.getElementsByTagName("song");

  //create array of all the ids I'm creating
  var songIdArray = new Array();
    
  //cycle through these instructions for each song
  for(var n = 0; n < songArray.length; n++){
    //create a new <li> element
    var newLi = document.createElement('li');
    //access the elements for the current song and store them in x
    x=xmlRoot.getElementsByTagName("song")[n];
    //access the currents song's <unique_song_id> tag and store it in y
    y=x.childNodes[0];
    //access the text within the song's <unique_song_id> tag and store it in txt
    idTxt=y.childNodes[0].nodeValue;
    //assign what's stored in txt as the new <li> element's id
    newLi.setAttribute('id', idTxt);
    newLi.setAttribute('class','song');
    songIdArray[n] = idTxt;
    //access the currents song's <song_name> tag and store it in z
    z=x.childNodes[1];
    //access the text within the song's <song_name> tag and store it in txt
    txt=z.childNodes[0].nodeValue;
    var newText = document.createElement('h2');
    newText.appendChild(document.createTextNode(txt));
    
    //set up the hover function
    newText.setAttribute('onMouseOver', 'catMouseOver(this);');
    newText.setAttribute('onMouseOut', 'catMouseOut(this);');
    
    //give it a class
    newText.setAttribute('class', 'song_txt');
  
    newLi.appendChild(newText);
    
    newOl.appendChild(newLi);
  }//end for(var n
    myDiv.appendChild(newOl);
    
    //turn all the new <li>s into draggable elements
  for(var n = 0; n < songIdArray.length; n++){
    new Draggable(songIdArray[n], {superghosting: true, detached: true, onEnd: catClearHighlight});
  }
  
}//end else if playlist songs
  
  //else not individual songs
  else{
  //store all elements in <artist> tags in artistArray
  artistArray = xmlRoot.getElementsByTagName("artist");
  
  //NOT SURE IF I NEED THIS
  var html = "";
  
  //need to switch of what expandTo means and what it becomes
  var myDiv = document.getElementById(group);
  var newOl = document.createElement('ol');
  
  //set newOl id attribute
  if(expandTo == 'artist'){
    //newOl.setAttribute('id', new_group + 'years');
    newOl.setAttribute('class', 'artists');
  }
  
  else if(expandTo == 'fav_artist'){
    //newOl.setAttribute('id', 'fav_' + new_group + 'years');
    newOl.setAttribute('class', 'fav_artists');
  }
  
  else if(expandTo == 'years'){
    newOl.setAttribute('id', new_group + 'years');
    newOl.setAttribute('class', 'artist');
  }
  else if(expandTo == 'fav_years'){
    newOl.setAttribute('id', new_group + '_fav_years');
    newOl.setAttribute('class', 'fav_year');
  }
  else if(expandTo == 'shows'){//need to add in the year here////
    newOl.setAttribute('id', new_group + 'shows');
    newOl.setAttribute('class', 'year');
  }
  else if(expandTo == 'fav_shows'){//need to add in the year here////
    newOl.setAttribute('id', new_group + 'fav_shows');
    newOl.setAttribute('class', 'fav_shows');
  }
  else if(expandTo == 'songs'){
    newOl.setAttribute('id', group + 'songs');
    newOl.setAttribute('class', 'show');
  }
  else if(expandTo == 'fav_songs'){
    newOl.setAttribute('id', group + 'fav_songs');
    newOl.setAttribute('class', 'fav_show');
  }
  else if(expandTo == 'playlist'){
    newOl.setAttribute('id', 'playlist');
    newOl.setAttribute('class', 'playlist');
  }
  else{
  //newOl.setAttribute('id', artist);
  }
  
  //create array of all the ids I'm creating
  var idArray = new Array();
  
  //create the list items
  for(var i = 0; i < artistArray.length; i++){
    var newLi = document.createElement('li');
    
    //newLi.setAttribute('class', 'artist');
    
    if(expandTo != 'songs'){//no images for songs
    //create image element and store it in newImage
      var imageElement = document.getElementById('expAllArtistImg');
      var newImage = imageElement.cloneNode(false);
      newImage.setAttribute('src', 'images/16-arrow-right.png');
    }
    //var imageElement = document.getElementById('expImg');
    //var newImage = imageElement.cloneNode(false);
    //newImage.setAttribute('src', '16-arrow-right.png');
    
    //set the call to expand
    if(expandTo == 'artist'){
      newImage.setAttribute('onclick', "expand('" + artistArray.item(i).firstChild.data + "', '" + artistArray.item(i).firstChild.data + "', 'years')");
    }
    else if(expandTo == 'fav_artist'){
      newImage.setAttribute('onclick', "expand('" + 'fav_' + artistArray.item(i).firstChild.data + "', '" + artistArray.item(i).firstChild.data + "', 'fav_years', '" + username + "')");
      newImage.removeAttribute('id');
    }
    else if(expandTo == 'years'){
      //alert(new_group);
      newImage.setAttribute('onclick', "expand('" + new_group + artistArray.item(i).firstChild.data + "', '" + artist + "', 'shows')");
    }
    else if(expandTo == 'fav_years'){
      newImage.setAttribute('onclick', "expand('" + 'fav_' + new_group + artistArray.item(i).firstChild.data + "', '" + artist + "', 'fav_shows', '" + username + "')");
      newImage.removeAttribute('id');
    }
    else if(expandTo == 'shows'){
      newImage.setAttribute('onclick', "expand('" + new_group + artistArray.item(i).firstChild.data + "', '" + artist + "', 'songs')");
    }
    else if(expandTo == 'fav_shows'){
      newImage.setAttribute('onclick', "expand('" + 'fav_' + new_group + artistArray.item(i).firstChild.data + "', '" + artist + "', 'fav_songs', '" + username + "')");
    }
    else if(expandTo == 'playlist'){
      newImage.setAttribute('onclick', "expand('" + 'pl_' + artistArray.item(i).firstChild.data + "', '" + artist + "', 'pl_songs', '" + username + "')");
    }
    else if(expandTo == 'songs'){
    }
    else{//error reporting
      alert("Error creating document structure.\n expandTo " + expandTo + " notrecognized");
    }
    var newText = document.createElement('h2');
    newText.appendChild(document.createTextNode(artistArray.item(i).firstChild.data));
    
    //set up the hover function
  newText.setAttribute('onMouseOver', 'catMouseOver(this);');
  newText.setAttribute('onMouseOut', 'catMouseOut(this);');
    
    //set li id attributes
    if(expandTo == 'artist'){
      var thisId = abb(artistArray.item(i).firstChild.data);
      //alert(thisId);
      newLi.setAttribute('id', artistArray.item(i).firstChild.data);
      newLi.setAttribute('class', 'artist');
      var Id = artistArray.item(i).firstChild.data;
      newText.setAttribute('class', 'artist_txt');
    }
    else if(expandTo == 'fav_artist'){
      newLi.setAttribute('id', 'fav_' + artistArray.item(i).firstChild.data);
      newLi.setAttribute('class', 'artist');
      var Id = 'fav_' + artistArray.item(i).firstChild.data;
      newText.setAttribute('class', 'artist_txt');
    }
    else if(expandTo == 'fav_years'){
      newLi.setAttribute('id', 'fav_' + new_group + artistArray.item(i).firstChild.data);
      newLi.setAttribute('class', 'year');
      var Id = 'fav_' + new_group + artistArray.item(i).firstChild.data;
      newText.setAttribute('class', 'year_txt');
    }
    else if(expandTo == 'fav_shows'){
      newLi.setAttribute('id', 'fav_' + new_group + artistArray.item(i).firstChild.data);
      newLi.setAttribute('class', 'show');
      var Id = 'fav_' + new_group + artistArray.item(i).firstChild.data;
      newText.setAttribute('class', 'show_txt');
    }
    else if(expandTo == 'fav_songs'){
      newLi.setAttribute('id', 'fav_' + new_group + artistArray.item(i).firstChild.data);
      var Id = 'fav_' + new_group + artistArray.item(i).firstChild.data;
      newText.setAttribute('class', 'song_txt');
    }
    else if(expandTo == 'playlist'){
      newLi.setAttribute('id', 'pl_' + artistArray.item(i).firstChild.data);
      var Id = 'pl_'  + artistArray.item(i).firstChild.data;
      newLi.setAttribute('class', 'playlist');
    }
    //else if(expandTo == 'shows'){
      //newLi.setAttribute('class', 'show');
    //}
    else{
      newLi.setAttribute('id', new_group + artistArray.item(i).firstChild.data);
      var Id = new_group + artistArray.item(i).firstChild.data;
      var newClass = expandTo.substring(0, (expandTo.length-1));
      newLi.setAttribute('class', newClass);
      //alert("newclass = " + newClass);
      
    }
    
    //add the new id to the id array
    idArray[i] = Id;
    // append image if not a song
    if(expandTo != 'songs'){//no images for songs
      newLi.appendChild(newImage);
    }
    
    newLi.appendChild(newText);
    newOl.appendChild(newLi);
    //alert(Id);

    }//end for
    
    //place newOl into document
  myDiv.appendChild(newOl);
  
  //turn all the new <li>s into draggable elements
  for(var n = 0; n < idArray.length; n++){
    new Draggable(idArray[n], {superghosting: true, detached: false, scroll: 'active_playlist', onEnd: catClearHighlight});
  }
}//end else

}//end function

function expand(group, artist, expandTo, username){
  //alert("group = " + group);
  
  var calling_element = document.getElementById(group);
    
  var calling_element_img = calling_element.getElementsByTagName('img');
  var calling_img_src = calling_element_img[0].getAttribute('src');
  
  if(calling_element_img[0].getAttribute('src') == 'images/16-arrow-right.png'){    
    calling_element_img[0].setAttribute('src', 'images/16-arrow-down.png');
    process(group, artist, expandTo, username);
    }
  else if(calling_element_img[0].getAttribute('src') == 'images/16-arrow-down.png'){
    calling_element_img[0].setAttribute('src', 'images/16-arrow-right.png');
    removeChildren(group);
    }
}
//still need to work on collapsing them all
function removeChildren(group){
  //debug
  //alert("remove children called");
  
  //find calling element
  var calling_element = document.getElementById(group);
  //assign removable element
  var removable_element = calling_element.getElementsByTagName('ol');
  //alert(removable_element.length);
  //remove element
  if(calling_element.getAttribute('class') == 'show'){
  for(var i = (removable_element.length-1); i >= 0; i--){
    //alert("remove element id  = " + removable_element[i].getAttribute('id'));
  calling_element.removeChild(removable_element[i]);
}
}
//if(calling_element.getAttribute('class') == 'set'){
  //    removeChildren(i.getAttribute('id'));
    //}
    else{
  for(var i = (calling_element.lastChild); i !== null; i = i.previousSibling){
    calling_element.removeChild(i);
  }
}
}

function fix_spaces(phrase){
  phrase = phrase.replace(/ /g, '_');
  alert("gap detected in GET variable, new phrase is " + phrase);
  return phrase;
}

function abb(band){
    /*this function takes in the user inputed band name and show date.
    It then produces an abbreviation from the band name.
    It then returns this abbreviation combined with the show date
    to create the show id*/

    //variable declarations
    var abbrev;//store the abbreviated band name
    //convert the band name to lower case for easier comparison
    band = band.toLowerCase();
    //set abbreviation based on band name
    switch(band) {
        case "widespread panic":
            abbrev = "wsp";
            break;
        case "grateful dead":
            abbrev = "gd";
            break;
        case "phish":
            abbrev =  "ph";
            break;
        case "ryan adams":
            abbrev =  "ra";
            break;
        case "disco biscuits":
            abbrev = "tdb";
            break;
        case "moe.":
        	abbrev = "moe";
        	break;
        case "warren zevon":
          abbrev = "wz";
          break;
        case "drive-by truckers" :
          abbrev = "dbt";
          break;
        case "john mayer" :
          abbrev = "jm";
          break;
        case "guster" :
          abbrev = "gust";
          break;
        default:
        	abbrev = "unknown";
        	break;
    }//end switch
    //return a combination of the band name abbreviation and the show date
    return abbrev;
}//end showIdCreator()  


function catMouseOver(elm){
  //alert("Hey!!");
  var childElms = elm.parentNode.childNodes;
  //alert(childElms.length);
  for(var i = 0; i < childElms.length; i++){
    var currClass = childElms[i].getAttribute('class');
    currClass += ' highlight';
    //alert(currClass);
    childElms[i].setAttribute('class', currClass);
  }
}

function catMouseOut(elm){
  //alert("Hey!!");
  var childElms = elm.parentNode.childNodes;
  //alert(childElms.length);
  for(var i = 0; i < childElms.length; i++){
    var currClass = childElms[i].getAttribute('class');
    currClass = currClass.replace(/ highlight/, "");
    //alert(childElms[i].getAttribute('id'));
    childElms[i].setAttribute('class', currClass);
  }
}

function catClearHighlight(){
  //alert("Hey!!");
  //var highlightElms = document.getElementsByTagName('highlight');
  var highlightElms = getElementsByClassName('highlight');
  //alert(highlightElms.length);
  for(var i = 0; i < highlightElms.length; i++){
    var currClass = highlightElms[i].getAttribute('class');
    currClass = currClass.replace(/ highlight/, "");
    //alert(childElms[i].getAttribute('id'));
    highlightElms[i].setAttribute('class', currClass);
  }
}

/*The following function was not written by me, but taken from
 * the following source:
 * 
	Developed by Robert Nyman, http://www.robertnyman.com
	Code/licensing: http://code.google.com/p/getelementsbyclassname/
*/
var getElementsByClassName = function (className, tag, elm){
	if (document.getElementsByClassName) {
		getElementsByClassName = function (className, tag, elm) {
			elm = elm || document;
			var elements = elm.getElementsByClassName(className),
				nodeName = (tag)? new RegExp("\\b" + tag + "\\b", "i") : null,
				returnElements = [],
				current;
			for(var i=0, il=elements.length; i<il; i+=1){
				current = elements[i];
				if(!nodeName || nodeName.test(current.nodeName)) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	else if (document.evaluate) {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = "",
				xhtmlNamespace = "http://www.w3.org/1999/xhtml",
				namespaceResolver = (document.documentElement.namespaceURI === xhtmlNamespace)? xhtmlNamespace : null,
				returnElements = [],
				elements,
				node;
			for(var j=0, jl=classes.length; j<jl; j+=1){
				classesToCheck += "[contains(concat(' ', @class, ' '), ' " + classes[j] + " ')]";
			}
			try	{
				elements = document.evaluate(".//" + tag + classesToCheck, elm, namespaceResolver, 0, null);
			}
			catch (e) {
				elements = document.evaluate(".//" + tag + classesToCheck, elm, null, 0, null);
			}
			while ((node = elements.iterateNext())) {
				returnElements.push(node);
			}
			return returnElements;
		};
	}
	else {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = [],
				elements = (tag === "*" && elm.all)? elm.all : elm.getElementsByTagName(tag),
				current,
				returnElements = [],
				match;
			for(var k=0, kl=classes.length; k<kl; k+=1){
				classesToCheck.push(new RegExp("(^|\\s)" + classes[k] + "(\\s|$)"));
			}
			for(var l=0, ll=elements.length; l<ll; l+=1){
				current = elements[l];
				match = false;
				for(var m=0, ml=classesToCheck.length; m<ml; m+=1){
					match = classesToCheck[m].test(current.className);
					if (!match) {
						break;
					}
				}
				if (match) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	return getElementsByClassName(className, tag, elm);
};

function createBalloon(txt){
  alert(txt);
}

var tooltip=function(){
 var id = 'tt';
 var top = 3;
 var left = 3;
 var maxw = 300;
 var speed = 10;
 var timer = 20;
 var endalpha = 95;
 var alpha = 0;
 var tt,t,c,b,h;
 var ie = document.all ? true : false;
 return{
  show:function(v,w){
   if(tt == null){
    tt = document.createElement('div');
    tt.setAttribute('id',id);
    t = document.createElement('div');
    t.setAttribute('id',id + 'top');
    c = document.createElement('div');
    c.setAttribute('id',id + 'cont');
    b = document.createElement('div');
    b.setAttribute('id',id + 'bot');
    tt.appendChild(t);
    tt.appendChild(c);
    tt.appendChild(b);
    document.body.appendChild(tt);
    tt.style.opacity = 0;
    tt.style.filter = 'alpha(opacity=0)';
    document.onmousemove = this.pos;
   }
   tt.style.display = 'block';
   c.innerHTML = v;
   tt.style.width = w ? w + 'px' : 'auto';
   if(!w && ie){
    t.style.display = 'none';
    b.style.display = 'none';
    tt.style.width = tt.offsetWidth;
    t.style.display = 'block';
    b.style.display = 'block';
   }
  if(tt.offsetWidth > maxw){tt.style.width = maxw + 'px'}
  h = parseInt(tt.offsetHeight) + top;
  clearInterval(tt.timer);
  tt.timer = setInterval(function(){tooltip.fade(1)},timer);
  },
  pos:function(e){
   var u = ie ? event.clientY + document.documentElement.scrollTop : e.pageY;
   var l = ie ? event.clientX + document.documentElement.scrollLeft : e.pageX;
   tt.style.top = (u - h) + 'px';
   tt.style.left = (l + left) + 'px';
  },
  fade:function(d){
   var a = alpha;
   if((a != endalpha && d == 1) || (a != 0 && d == -1)){
    var i = speed;
   if(endalpha - a < speed && d == 1){
    i = endalpha - a;
   }else if(alpha < speed && d == -1){
     i = a;
   }
   alpha = a + (i * d);
   tt.style.opacity = alpha * .01;
   tt.style.filter = 'alpha(opacity=' + alpha + ')';
  }else{
    clearInterval(tt.timer);
     if(d == -1){tt.style.display = 'none'}
  }
 },
 hide:function(){
  clearInterval(tt.timer);
   tt.timer = setInterval(function(){tooltip.fade(-1)},timer);
  }
 };
}();
