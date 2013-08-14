// pull extra content
Util.Objects["pull"] = new function() {
	this.init = function(e) {
		var url = Util.getIJ(e, "url");
		Util.request(url, e);
	}
}
