Util.Objects["flash"] = new function() {

	this.init = function(e) {
		var url = Util.getIJ("url", e);
		var w = Util.getIJ("w", e);
		var h = Util.getIJ("h", e);

		e.innerHTML = Util.flash(url, w, h);
	}
}
