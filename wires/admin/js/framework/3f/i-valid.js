Util.Objects["valid"] = new function() {
	this.init = function(e) {
		e.format = Util.getIJ(e, "format");
		e.onkeyup = function() {
			if(this.value.match(/[0-9]+/g)) {
				Util.addClass(e, "valid");
				Util.removeClass(e, "error");
			}
			else {
				Util.addClass(e, "error");
				Util.removeClass(e, "valid");
			}
		}
	}
}
