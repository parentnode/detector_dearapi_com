// news list image 
Util.Objects["audio"] = new function() {
	this.init = function(li) {

		u.ce(li);
		li.clicked = function() {

			if(!this.audioplayer) {
				this.audioplayer = u.audioPlayer();
			}

			if(!u.hc(this, "playing")) {
				this.audioplayer.loadAndPlay(this.url);
				u.ac(this, "playing");
			}
			else {
				this.audioplayer.stop();
				u.rc(this, "playing");
			}

		}

	}
}

// news list image 
Util.Objects["video"] = new function() {
	this.init = function(li) {

		u.ce(li);
		li.clicked = function() {

			if(!this.videoplayer) {
				this.videoplayer = u.videoPlayer();
				u.ae(this, this.videoplayer);
			}

			if(!u.hc(this, "playing")) {
				this.videoplayer.loadAndPlay(this.url);
				u.ac(this, "playing");
			}
			else {
				this.videoplayer.stop();
				u.rc(this, "playing");
			}

		}

	}
}