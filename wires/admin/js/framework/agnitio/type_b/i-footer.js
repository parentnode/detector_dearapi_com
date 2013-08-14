Util.Objects["footer"] = new function() {
	this.init = function(e) {

		// show/hide
		e.clicked = function() {u.toggleClass(e, "show");}
		u.e.click(e);

		var home = u.ae(e, "div", "home");
		u.e.click(home);
		home.clicked = function(event) {
			u.e.kill(event);
			u.ge("header").navigation("slide1");
		}

		var ref = u.ae(e, "div", "ref");
		u.e.click(ref);
		ref.clicked = function(event) {
			u.e.kill(event);

			if(!u.ge("ref_layover")) {
				var ref_layover = u.ae(document.body, "div", {"id":"ref_layover"});
				ref_layover.innerHTML = u.ge("slide4").innerHTML;
				var bn = u.ae(ref_layover, "div", "close");
				u.e.click(bn);
				bn.clicked = function() {
					u.removeClass(document.body, "ref");
				}
			}
			this.ref_layover = u.ge("ref_layover");

			u.toggleClass(document.body, "ref");
			if(!this.ref_layover.initiated) {
				var ref = u.ge("ref", this.ref_layover);
				var ref_drag = u.ge("ref_drag", this.ref_layover);
				u.e.drag(ref, new Array(0, ref_drag.offsetHeight-ref.offsetHeight, ref.offsetWidth, ref.offsetHeight))
				this.ref_layover.initiated = true;
			}

//			u.ge("header").navigation("slide1");
		}

	}
}
