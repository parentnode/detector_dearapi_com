Util.Objects["header"] = new function() {
	this.init = function(e) {

		u.bug("fisk" + e.className)

		var list = u.ge("ul", e);
		list.e = e;

		// enable swipe
		u.e.swipe(list, new Array(e.offsetWidth - list.offsetWidth, 0, list.offsetWidth, list.offsetHeight));
		list.swipedLeft = function() {
			u.e.transition(list, "all 0.5s ease-out");
			u.e.transform(list, this.e.offsetWidth - this.offsetWidth, 0);
//			u.e.transform(list, -936, 0);
		}
		list.swipedRight = function() {
			u.e.transition(list, "all 0.5s ease-out");
			u.e.transform(list, 0, 0);
		}

		// enable links
		var buttons = u.ges("button", list);
		for(i = 0; button = buttons[i]; i++) {
			button.e = e;
			button.clicked = function() {
				this.e.navigation(u.getIJ(this, "id"));
			}
			u.e.click(button);
		}


		e.c = u.ge("content");
		e.navigation = function(page) {
			var i, slide;
			// identify page position
			var sections = page.split("_");

			// monitoring
//			u.bug(u.ge("h2", u.ge(page)).innerHTML);
			var page_title = u.ge("h2", u.ge(page)).innerHTML;
			page_title = page_title ? page_title : page;
//			u.bug(page_title);
			submitSlideEnter(page, page_title, page, document.title, false);

			u.saveCustomEvent();

			// set page id
			u.removeClass(document.body, "slide[0-9]+");
			u.addClass(document.body, sections[0]);

			// find button in header navigation
			var header_button = u.ge("id:"+page);

			// move to primary selection
			var target = u.ge(sections[0], this.c.list);
			u.e.transform(this.c.list, -(target.offsetLeft), 0);

			// check for front (slide1)
			if(target.className.match(/slide1[ $]/)) {
				u.addClass(document.body, "front");
			}
			else {
				u.removeClass(document.body, "front");
			}

			// sub sections
			if(sections[1]) {
				// move to sub section
				var ul = u.ge("ul", target);
				u.e.transform(ul, -(u.ge(sections[0]+"_"+sections[1], this.c.list).offsetLeft), 0);
			}

			// set header navigation position
			// don't shift navigation on close
			if(header_button && !header_button.className.match(/close/)) {

				list = u.ge("ul", this);
				// calculate from right, because main menu is on the right side
				if(list.offsetWidth - header_button.offsetLeft <= this.offsetWidth) {
					list.swipedLeft();
				}
				else {
					list.swipedRight();
				}
				
			}

			// set footer navigation position and indication
			var nav = u.ge("ul", u.ge("footer"));
			var nav_slides = u.ges("nav", nav);
			var nav_item = u.ge(page, nav);

			// fade out all elements
			for(i = 0; slide = nav_slides[i]; i++) {
				slide.style.opacity = 0.5;
			}
			// fade in selected slide
			nav_item.style.opacity = 1;

			// caluculate new centered position
			var display_width = this.offsetWidth;
			var item_width = nav_item.offsetWidth;
			var start_item = nav_item.offsetLeft;
			var new_pos =  (display_width-item_width)/2 - start_item;
			u.e.transform(nav, new_pos, 0);

//			u.bug("dw"+display_width);
//			u.bug("sd"+start_display);
//			u.bug(end_display);

//			u.bug("iw"+item_width);
//			u.bug("si"+start_item);
//			u.bug(start_item);
//			u.bug(end_item);

//			u.bug("np:"+new_pos);

			// visible?
//			if(start_item < start_display || end_item > end_display) {

//				u.bug("invisible")
//			}
//			nav_item.element_x > 0 &&  
			// is it visible
/*			if(Math.abs(nav.element_x - this.offsetWidth) < nav_item.offsetLeft || ) {
				u.bug(nav_item.offsetLeft)
				u.bug(nav.element_x)
				u.bug(this.offsetWidth)
			}
*/
//			u.bug("nl"+nav_slides)
//			u.bug(u.ge(sections[0], nav).className)
		}
	}
}
