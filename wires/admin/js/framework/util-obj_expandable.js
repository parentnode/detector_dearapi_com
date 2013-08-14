Util.Objects["expandable"] = new function() {

	this.init = function(e) {

		Util.removeClass(e, "init:expandable");
		Util.addClass(e, "expandable");

		e.mem_id = Util.getIJ("id", e);

		if(Util.Mem.get("e:"+e.mem_id)) {
			Util.addClass(e, "open");
		}

		var toggler = e.getElementsByTagName("h2")[0];
		toggler.e = e;
		toggler.onclick = function() {
			Util.toggleClass(this.e, "open");
			Util.Mem.set("e:"+this.e.mem_id, this.e.className.match("open"));
		}
	}
}
