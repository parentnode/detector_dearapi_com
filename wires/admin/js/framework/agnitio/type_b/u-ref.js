Util.ref = function(e, classname) {

	return;

	if(!u.ge("ref_layover")) {
		var ref_layover = u.ae(document.body, "div", {"id":"ref_layover"});
		ref_layover.innerHTML = u.ge("slide4").innerHTML;
		var bn = u.ae(ref_layover, "div", "close");
		u.e.click(bn);
		bn.clicked = function() {
			u.removeClass(document.body, "ref");
		}
	}

	classname = classname ? " "+classname : "";
	var ref = u.ae(e, "div", "ref" + classname);
	ref.ref_layover = u.ge("ref_layover");

	u.e.click(ref);
	ref.clicked = function() {
		u.addClass(document.body, "ref");
		if(!this.ref_layover.initiated) {
			var ref = u.ge("ref", this.ref_layover);
			var ref_drag = u.ge("ref_drag", this.ref_layover);
			u.e.drag(ref, new Array(0, ref_drag.offsetHeight-ref.offsetHeight, ref.offsetWidth, ref.offsetHeight))
			this.ref_layover.initiated = true;
		}

//		u.ge("header").navigation(this.ref_slide);
	}
}
