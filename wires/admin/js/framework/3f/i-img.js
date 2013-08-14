// insert image
Util.Objects["img"] = new function() {
	this.init = function(e) {
		var img = document.createElement("img");
		img.src = Util.getIJ(e, "url");
		e.insertBefore(img, e.firstChild);
	}
}
