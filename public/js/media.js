var playButtons = document.getElementsByClassName("controlBtn");
var stopButttons = document.getElementsByClassName("stopBtn");
var songs = document.getElementsByClassName("music");
var time = document.getElementsByClassName("time-display");

for(var i=0; i<songs.length; i++) {

	playButtons[i].addEventListener("click", function(e) {
		var num = this.id.substr(this.id.length - 1);
		 playAudio(num); 
		}, false);

	songs[i].addEventListener("timeupdate", function(e) {
		var num = this.id.substr(this.id.length - 1);
		timeUpdate(num); 
	}, false);

	songs[i].addEventListener("loadeddata", function(e) {
		var num = this.id.substr(this.id.length - 1);

		var secs = songs[num].duration;

		var hr  = Math.floor(secs / 3600);
	 	var min = Math.floor((secs - (hr * 3600))/60);
	  	var sec = Math.floor(secs - (hr * 3600) -  (min * 60));

		  if (min < 10){ 
		    min = "0" + min; 
		  }
		  if (sec < 10){ 
		    sec  = "0" + sec;
		  }

		time[num].innerHTML = min + ':' + sec;
	}, false);
	
}
// for(var i=0; i<stopButttons.length; i++) {
//    stopButttons[i].addEventListener("click", function() { stopAudio(i); }, false);
// }

function playAudio(num){

	console.log(num);
	var music = songs[num];
	var pButton = playButtons[num];

	if (music.paused) {
		console.log('play');
		music.play();
		pButton.className = "";
		pButton.className = "right controlBtn pauseBtn";
	} else { 
		console.log('paused');
		music.pause();
		pButton.className = "";
		pButton.className = "right controlBtn playBtn";
	}
}

function stopAudio(num) {
	var music = songs[num];
	var pButton = playButtons[num];
	pButton.className = "right playBtn";
	music.pause();
	music.currentTime = 0;
}

function timeUpdate(music_num) {
	console.log("update in ".concat(music_num));
	var secs = songs[music_num].currentTime;

	var hr  = Math.floor(secs / 3600);
 	var min = Math.floor((secs - (hr * 3600))/60);
  	var sec = Math.floor(secs - (hr * 3600) -  (min * 60));

	  if (min < 10){ 
	    min = "0" + min; 
	  }
	  if (sec < 10){ 
	    sec  = "0" + sec;
	  }

  	time[music_num].innerHTML =  min + ':' + sec;
}