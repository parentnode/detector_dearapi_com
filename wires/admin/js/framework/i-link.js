// extenders
Util.Objects["link"] = new function() {
	this.init = function(e) {
		e.a = u.ge("a", e);
		if(e.a) {
			e.link_id = e.a.getAttribute("href").replace("#", "");
			e.a.removeAttribute("href");
			Util.addClass("link", e)

			e.clicked = function() {
				u.e.transition(u.ge("presentation"), "all 1s ease-out");
				u.e.transform(u.ge("presentation"), -(u.ge(this.link_id).offsetLeft), 0);
			}
			u.e.click(e);
		}
	}
}
