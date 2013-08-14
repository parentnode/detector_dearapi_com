Util.Objects["navigation"] = new function() {
	this.init = function(e) {

		// home (slide 1) button
		var nav_home = u.ge("home", e);
		if(nav_home) {
			nav_home.e = e;
			nav_home.clicked = function() {u.e.transform(this.e, 0, 0);}
			u.e.click(nav_home);
		}

		// regular H2 based navigation
		e.nav_index = u.ge("index", e);
		if(e.nav_index) {
			e.nav_index.e = e;
			e.nav_index.clicked = function() {u.toggleClass(this.e, "index");}
			u.e.click(e.nav_index);
			e.index = e.nav_appendChild(document.createElement("ul"));
			u.addClass(e.index, "index");

		}
		// sitemap library
		e.nav_sitemap = u.ge("sitemap", e);
		if(e.nav_sitemap) {
			e.nav_sitemap.e = e;
			e.nav_sitemap.clicked = function() {u.toggleClass(this.e, "sitemap");}
			u.e.click(e.nav_sitemap);
			e.sitemap = e.nav.appendChild(document.createElement("ul"));
			u.addClass(e.sitemap, "sitemap");
		}

		// initialize slides
		for(var i = 0; slide = e.slides[i]; i++) {

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
					u.toggleClass(this.e, "sitemap");
				}
				u.e.click(li);
			}
		}

		u.addClass(e, "ready");

	}
}
