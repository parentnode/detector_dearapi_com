Util.Animation = u.a = new function() {


	// get variant, to avoid setting more than the required type
	this.variant = function(e) {
		if(this.implementation == undefined) {
			if(document.body.style.webkitTransition != undefined) {
				this.implementation = "webkit";
			}
			else if(document.body.style.MozTransition != undefined) {
				this.implementation = "Moz";
//				this.implementation = "";
			}
			else if(document.body.style.oTransition != undefined) {
				this.implementation = "o";
			}
			else {
				this.implementation = "";
			}
		}
		return this.implementation;
	}
	/**
	*
	*/
	this.translate = function(e, x, y) {

//		u.bug("trans a")
		e.style[this.variant() + "Transform"] = "translate("+x+"px, "+y+"px)";

//		u.bug(x + ":" + y)

//		e.style.MozTransform = "translate("+x+"px, "+y+"px)";
//		e.style.webkitTransform = "translate3d("+x+"px, "+y+"px, 0)";
		e.element_x = x;
		e.element_y = y;
		e.transition_timestamp = new Date().getTime();
	}

	this.rotate = function(e, deg) {
//		u.bug("rotate a")
		e.style[this.variant() + "Transform"] = "rotate("+deg+"deg)";

//		e.style.MozTransform = "rotate("+deg+"deg)";
//		e.style.webkitTransform = "rotate("+deg+"deg)";
		e.rotation = deg;
		e.transition_timestamp = new Date().getTime();
	}

	this.scale = function(e, scale) {
//		u.bug("scale a")
		e.style[this.variant() + "Transform"] = "scale("+scale+")";
//		e.style.MozTransform = "scale("+scale+")";
//		e.style.webkitTransform = "scale("+scale+")";
		e.scale = scale;
		e.transition_timestamp = new Date().getTime();
	}


	this.rotateTranslate = function(e, deg, x, y) {

//		u.bug("trans a")
		e.style[this.variant() + "Transform"] = "rotate("+deg+"deg) translate("+x+"px, "+y+"px)";

//		u.bug(x + ":" + y)

//		e.style.MozTransform = "translate("+x+"px, "+y+"px)";
//		e.style.webkitTransform = "translate3d("+x+"px, "+y+"px, 0)";
		e.rotation = deg;
		e.element_x = x;
		e.element_y = y;
		e.transition_timestamp = new Date().getTime();
	}


	this.translateRotate = function(e, x, y, deg) {
		e.style[this.variant() + "Transform"] = "translate("+x+"px, "+y+"px) rotate("+deg+"deg)";
		e.element_x = x;
		e.element_y = y;
		e.rotation = deg;
		e.transition_timestamp = new Date().getTime();
	}


	/**
	*
	*/
	this.transition = function(e, transition) {
//		u.bug("trans")

		e.style[this.variant() + "Transition"] = transition;
//		e.style.webkitTransition = transition;

		u.e.addEvent(e, this.variant() + "TransitionEnd", this._transitioned);

		var duration = transition.match(/[0-9.]+[ms]/g);
		if(duration) {
			var d = duration[0];
//			u.bug(d);
			e.duration = d.match("ms") ? parseFloat(d) : (parseFloat(d) * 1000);
		}
		else {
			e.duration = false;
		}
	}

	// manual setting of transition end callback (when transitions are declared via CSS instead of JS)
	this.transitioned = function(e) {
//		u.bug("listen:" + e.className);
//		u.bug(this.variant()+"TransitionEnd")
		u.e.addEvent(e, this.variant()+"TransitionEnd", this._transitioned);
	}

	this._transitioned = function(event) {
		// maybe only callback when target == this?

//		u.bug("catch" + event.target.className + "::" + this.className)
		if(event.target == this && typeof(this.transitioned) == "function") {
			this.transitioned(event);
		}
	}

}
