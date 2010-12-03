/*ActionScript for Setr.us music player*/

//import external interface
import flash.external.ExternalInterface;

//set variables
var index:int = -1;
var music:Sound = new Sound(new URLRequest("moe2008-05-24d02t02_vbr.mp3"));
var sc:SoundChannel;
var isPlaying:Boolean = false;

//begin creating functions and binding them to buttoms
stop_btn.addEventListener(MouseEvent.CLICK, stopMusic);

function stopMusic(e:Event):void{
	sc.stop();
  //need to stop track from pulsing, maybe do several things in a function over in javascript
	//sc:close();
	isPlaying = false;
}

play_btn.addEventListener(MouseEvent.CLICK, playMusic);

function playMusic(e:Event):void{
	if(!isPlaying){
		ExternalInterface.call("getNextTrack", index);
		//sc = music.play();
		//isPlaying = true;
		
		//attempt ExternalInterface call
		//ExternalInterface.call("alert", "Attempmting to call Javascript");
		
	}
}

prev_btn.addEventListener(MouseEvent.CLICK, loadPrevTrack);

function loadPrevTrack(e:Event):void{
	sc.stop();
	ExternalInterface.call("alert", "Use Javascript to send location of prev track!");
	}

next_btn.addEventListener(MouseEvent.CLICK, loadNextTrack);

function loadNextTrack(e:Event):void{
	sc.stop();
	ExternalInterface.call("alert", "Use Javascript to send location of next track!");
	}

//messing with externalinterface a bit more...
ExternalInterface.addCallback("jsAlert", jsAlert);

function jsAlert(mess){
	ExternalInterface.call("alert", mess);
	}

ExternalInterface.addCallback("changeTrack", changeTrack);

function changeTrack(newTrack){
  //everything should be handled in 'play()'. 'changeTrack()' should call 'play()' after it handles the other details
	ExternalInterface.call("alert", "From Flash: changeTrack called");
	if(isPlaying){
		jsAlert("From Flash: curTrack is playing");
		sc.stop();
	    //sc:close();
	    isPlaying = false;
	    }
	var music:Sound = new Sound();
	jsAlert("From Flash: new sound is created");
	music.load(new URLRequest(newTrack));
	jsAlert("From Flash: music loading");
	sc = music.play();
	sc.addEventListener(Event.SOUND_COMPLETE, trackDone);
	isPlaying = true;
	index++;
}

function trackDone($evt:Event):void
{
    ExternalInterface.call("alert", "From Flash: Song done.");
}
	
