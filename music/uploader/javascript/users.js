/*This file handles all the user-based computations*/

//variable declarations to be used throughout the session
var xmlHttp = u_createXmlHttpRequestObject();
var favSongArray = [];

function u_createXmlHttpRequestObject(){
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

function getUserInfo(username){
  //alert("entered getUserInfo, username = " + username);
  getUserFavSongs(username);
}

function getUserFavSongs(username){
  
  if(xmlHttp){
      //alert("sever is available");
      //if yes try
      try{
        var params = ("username=" + username);
        //alert("parameters being sent to the server are " + params);
        xmlHttp.open("GET", "php/getUserFavSongs.php?" + params, true);
        //the weird function stuff is because you're assigning a function to a variable
        //alert("droppableId = " + droppableId + " elementId = " + elementId);
        xmlHttp.onreadystatechange = function(){u_handleRequestStateChange();};
        //alert("attempted to call p_handleRequestStateChange_test");
        xmlHttp.send(null);
      }//end try
      catch(e){
        alert("Can't connect to server: \n" + e.toString());
      }//end catch
    }//end if xmlHHttp
  
}//end function

function u_handleRequestStateChange(){
  //alert("entered handleRequestStateChange droppable = " + droppableId);
  if(xmlHttp.readyState == 4){
    //alert("from handleRequestStateChange ready state = " + xmlHttp.readyState);
   if(xmlHttp.status == 200){
     try{
       u_handleServerResponse();
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

function u_handleServerResponse(){
  //need to clear array each time
  var response = xmlHttp.responseText;
  
  favSongArray = response.split(".");

  /***XML VERSION***
  //pull xml from xml response
  var xmlResponse = xmlHttp.responseXML;
 //alert(xmlResponse);
  //check to see if xml was pulled
  if(!xmlResponse || !xmlResponse.documentElement){
    throw("Invalid XML Structure:\n" + xmlHttp.responseText);
  }
 
  //this is for catching errors with firefox
  var rootNodeName = xmlResponse.documentElement.nodeName;
 
  //check for errors
  if(rootNodeName == "parsererror"){
    throw("Invalid XML Strucutre");
  }
  
  //get the root
  xmlRoot = xmlResponse.documentElement;

  var songArray = xmlRoot.getElementsByTagName("song");

  for(var i = 0; i < songArray.length; i++){

    //set reference in xml
    favSongArray[i] = songArray[i].firstChild.data;
  }//end for
}//end function
  */
}
function setUserFavSongs(username, songId, incORdec){
  
  if(xmlHttp){
      //alert("sever is available");
      //if yes try
      try{
        var params = ("username=" + username + "&songId=" + songId + "&incORdec=" + incORdec);
        //alert("parameters being sent to the server are " + params);
        xmlHttp.open("GET", "php/setUserFavSongs.php?" + params, true);
        xmlHttp.send(null);
      }//end try
      catch(e){
        alert("Can't connect to server: \n" + e.toString());
      }//end catch
    }//end if xmlHHttp
  
}//end function
