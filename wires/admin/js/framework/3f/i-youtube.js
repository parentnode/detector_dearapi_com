// insert youtube image
Util.Objects["youtube"] = new function() {
	this.init = function(e) {
		var youtube_id = Util.getIJ(e, "id"); //.firstChild.href.match(/http:\/\/www.youtube.com\/v\/([A-Za-z0-9_-]+)&/i);
		if(youtube_id) {
			Util.addClass(e, "youtube")
			var img = document.createElement("img");
			var span = document.createElement("span");
			Util.addClass(span, "play");
			img.src = "http://i1.ytimg.com/vi/"+youtube_id+"/default.jpg";
			e.insertBefore(img, e.firstChild);
			e.insertBefore(span, e.firstChild);
		}
	}
}
