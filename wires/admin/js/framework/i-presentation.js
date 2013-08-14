Util.Objects["presentation"] = new function() {
	this.init = function(e) {
		// get presentation elements
		e.display = u.ge("display");
		e.slides = u.ges("slide");
		e.nav = u.ge("navigation")

		// calculate presentation sizes
		e.slide_width = e.slides[0].offsetWidth;
		e.display_width = e.display.offsetWidth;

		e.w = (e.slides.length * e.display_width);
		e.style.width = e.w + "px";

		e.h = e.display.offsetHeight;

		// initialization of presentation navigation

		// home (slide 1) button
		e.nav.home = u.ge("home", e.nav);
		if(e.nav.home) {
			e.nav.home.e = e;
			e.nav.home.clicked = function() {u.e.transform(this.e, 0, 0);}
			u.e.click(e.nav.home);
		}
		// regular H2 based navigation
		e.nav.index = u.ge("index", e.nav);
		if(e.nav.index) {
			e.nav.index.e = e;
			e.nav.index.clicked = function() {u.toggleClass(this.e.nav, "index");}
			u.e.click(e.nav.index);
			e.index = e.nav.appendChild(document.createElement("ul"));
			u.addClass(e.index, "index");
		}
		// sitemap library
		e.nav.sitemap = u.ge("sitemap", e.nav);
		if(e.nav.sitemap) {
			e.nav.sitemap.e = e;
			e.nav.sitemap.clicked = function() {u.toggleClass(this.e.nav, "sitemap");}
			u.e.click(e.nav.sitemap);
			e.sitemap = e.nav.appendChild(document.createElement("ul"));
			u.addClass(e.sitemap, "sitemap");
		}

		// initialize slides
		for(var i = 0; slide = e.slides[i]; i++) {

			slide.style.width = e.slide_width+"px";
			u.addClass(slide, "ready");

			if(e.index) {
				li = e.index.appendChild(document.createElement("li"));
				li.innerHTML = u.ge("h2", slide).innerHTML;
				li.e = e;
				li.slide = slide;

				li.clicked = function() {
					u.e.transition(this.e, "all 1s ease-out");
					u.e.transform(this.e, -this.slide.offsetLeft, 0);
					u.toggleClass(this.e.nav, "index");
				}
				u.e.click(li);
			}

			if(e.index.offsetHeight > e.h) {
				li = e.index.removeChild(li, true);
				e.index = e.nav.appendChild(document.createElement("ul"));
				e.index.appendChild(li);
			}

			if(e.sitemap) {
				li = e.sitemap.appendChild(document.createElement("li"));
				li.appendChild(document.createElement("h5")).innerHTML = u.ge("h1", slide).innerHTML;
				li.e = e;
				li.slide = slide;

				li.clicked = function() {
					u.e.transition(this.e, "all 1s ease-out");
					u.e.transform(this.e, -this.slide.offsetLeft, 0);
					u.toggleClass(this.e.nav, "sitemap");
				}
				u.e.click(li);
			}
		}

		u.addClass(e.nav, "ready");

		e.swipedLeft = function() {
			current_slide = Math.floor(this.element_x/this.display_width);
			eta = Math.abs(Math.round(((this.element_x - (current_slide * this.display_width)) / this.current_xps) * 10) / 10);
			eta = eta > 0.5 ? 0.5 : eta < 0.2 ? 0.2 : eta;
			u.e.transition(this, "all "+eta+"s ease-out");
			u.e.transform(this, current_slide*this.display_width, 0);
		}
		e.swipedRight = function() {
			current_slide = Math.floor(this.element_x/this.display_width);
			eta = Math.abs(Math.round((((current_slide * this.display_width) - this.element_x) / this.current_xps) * 10) / 10);
			eta = eta > 0.5 ? 0.5 : eta < 0.2 ? 0.2 : eta;
			u.e.transition(this, "all "+eta+"s ease-out");
			u.e.transform(this, (current_slide+1)*this.display_width, 0);
		}

		u.e.swipe(e, new Array(e.display.offsetWidth - e.w, 0, e.w, e.h));

	}
}
