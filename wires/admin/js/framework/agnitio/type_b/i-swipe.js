Util.Objects["swipe"] = new function() {
	this.init = function(e) {
		var i, slide;
		u.addClass(e, "swipe");
		var list = u.ge("ul", e);
		var slides = u.ges("slide", list);

		// set list width
		// safari delays dom updating, so remember width
		var width = slides.length * slides[0].offsetWidth;
		list.style.width = width + "px";
		list.e = e;

		// need for swipe?
		if(e.offsetWidth < width) {
			// remember slide/swiper relataion
			for(i = 0; slide = slides[i]; i++) {
				slide.swiper = list;
			}

			u.e.swipe(list, new Array(e.offsetWidth - width, 0, width, e.offsetHeight));
			list.swipedLeft = function() {
				var next = Math.ceil(Math.abs(this.element_x/this.e.offsetWidth));
				u.e.transition(this, "all 0.5s ease-out");
				u.e.transform(this, -this.e.offsetWidth*next, 0);

				u.ge("header").navigation(u.ges("slide", this)[next].className.match(/slide[0-9_]+/)[0]);
			}
			list.swipedRight = function() {
				var prev = Math.floor(Math.abs(this.element_x/this.e.offsetWidth));
				u.e.transition(this, "all 0.5s ease-out");
				u.e.transform(this, -this.e.offsetWidth*prev, 0);

				u.ge("header").navigation(u.ges("slide", this)[prev].className.match(/slide[0-9_]+/)[0]);
			}
		}

		u.addClass(e, "ready");
		u.ge("content").ready();
	}
}
