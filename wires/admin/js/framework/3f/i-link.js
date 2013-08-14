// extenders
Util.Objects["link"] = new function() {
	this.init = function(e) {
		var as = e.getElementsByTagName("a");
		if(as.length) {
			e.link = as[0].href
			Util.addClass(e, "link")
			e.onclick = function() {
				location.href = this.link;
			}
		}
	}
}
