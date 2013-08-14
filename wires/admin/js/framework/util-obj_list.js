Util.Objects["list"] = new function() {

	this.init = function(list) {
		var element, i;

		//this.menu = menu;
		list.toggle = function(event, element) {
			Util.nonClick(event);
			if(element.className.match(/open/g)) {
				Util.removeClass(element, "open");
			}
			else {
				Util.addClass(element, "open");
			}
		}
		Util.addClass(list, "list");

		list.elements = list ? list.getElementsByTagName("li") : false;
		for(element, i = 0; element = list.elements[i]; i++) {
			element.list = list;


			// if element has children
			if(element.getElementsByTagName("ul").length) {

				Util.addClass(element, "super");

				// open nesting to show selected element
				if(Util.getElementsByClass("selected", element).length){
					Util.addClass(element, "open");
				}

				// enable opening
				element.onclick = function(event) {
					this.list.toggle(event, this);
				}

			}
			// if no children, enable link in entire element
			else if(element.firstChild) {
				// add mouseovers and mouseouts
				element.onmouseover = function() {
					Util.over(this);
				}
				element.onmouseout = function() {
					Util.out(this);
				}

				if(element.firstChild.href) {
					element.onclick = function() {
						location.href = this.firstChild.href;
					}
				}
			}
		}
		Util.unSelectify(list);
	}

}
