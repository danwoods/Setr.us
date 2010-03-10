/*This script cycles through the all the songs in a particular show
 * with a length value of NULL and plays them, setting their length
 * once the song has stopped */

//variable declarations to be used throughout the session
var untimedSongArray = [];
var index = 0;

function beginProcess(){

new Ajax.Request('php/getUntimed.php', {
  method: 'get',
  onSuccess: function(transport){
      var response = transport.responseText || "no response text";
      untimedSongArray = response.split("+");  
      alert(response);
      getFlashMovie("trackTimer").timeThisTrack(untimedSongArray[0]); 
      //alert("Success! \n\n" + response);
      //var html = response;
      },
    onFailure: function(){ alert('Something went wrong...') }

  });
 }

function getFlashMovie(movieName) {
  var isIE = navigator.appName.indexOf("Microsoft") != -1;
  return (isIE) ? window[movieName] : document[movieName];  }

function setSongTime(track, time){
  //alert("track " + track + " has a time of " + time);
  //$.get("php/setSongTime.php", { trackname: track, tracktime: time } );
  var pars = 'trackname=' + track + '&tracktime=' + time;

  new Ajax.Request('php/setSongTime.php', {
    method: 'get',
    parameters: pars,
    onSuccess: function(transport){
      var response = transport.responseText || "no response text";
      //alert(response);
      if((index - 1) < untimedSongArray.length){
        index++;
        getFlashMovie("trackTimer").timeThisTrack(untimedSongArray[index]);
        }
      else{
        alert("all done!");
        }
      },
    onFailure: function(transport){
      var response = transport.responseText || "no response text";
      document.write(response);
      document.write('</br>');
      }
    });

  }
