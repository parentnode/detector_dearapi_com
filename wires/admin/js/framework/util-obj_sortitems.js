Util.Objects["lisort"] = new function() {

	this.init = function(e) {

		Util.Objects["listoptions"].init(e);
		
		var i, li;

		Util.removeClass(e, "init:sortitems");
		Util.addClass(e, "sort");
		Util.unSelectify(e);

		// initiate dragging of message
		e.pickElement = function(li, event) {
			var node, next;
			event = event ? event : (window.event) ? window.event : false;

			// calculate default offset
			this.li = li;
			this.pick_offset_x = event.clientX-Util.absoluteLeft(li);
			this.pick_offset_y = event.clientY-Util.absoluteTop(li);
			this.li.style.zIndex = this.zindex++;
 			this.li.style.opacity = 0.8;

			//Util.debug("x:" + this.pick_offset_x);
			//Util.debug("y:" + this.pick_offset_y);

//			element.position
			/*
			// add node
			node = this.drag_content_body.appendChild(element.cloneNode(true));

			// remember reference
			node.source_element = element;

			// remember that this element is dragged
			element.dragged = true;

			// add children
			next = Util.nextRealSibling(element, "intersection");
			while(next && next.indent > element.indent) {
				node = this.drag_content_body.appendChild(next.cloneNode(true));
				node.source_element = next;
				next.dragged = true;
				next = Util.nextRealSibling(next, "intersection");
			}
			// prepare for dragging
			this.drag_content.style.left = event.clientX - this.pick_offset_x + 'px';
			this.drag_content.style.top = event.clientY - this.pick_offset_y + 'px';
			document.table = this;
			this.dragging = true;
			*/
			//document.e = this;
			//Util.debug(this.className + "::" + li.className);
			this.onmousemove = this.dragElement;
			this.onmouseup = this.dropElement;
//			this.onmouseout = this.dropElement;
		}

		// drag the element (is called from document)
		e.dragElement = function(event) {
			event = event ? event : (window.event) ? window.event : false;
			if(event) {
				/*
				Util.debug("x:" + this.pick_offset_x);
				Util.debug("y:" + this.pick_offset_y);

				Util.debug("mx:" + event.clientX);
				Util.debug("my:" + event.clientY);

				Util.debug("px:" + this.page_offset_y);
				Util.debug("py:" + this.page_offset_y);
				
				Util.debug("by:" + ((Util.explorer()) ? document.documentElement.scrollTop : window.pageYOffset));
				Util.debug(event.clientY-this.page_offset_y - this.pick_offset_y + ((Util.explorer()) ? document.documentElement.scrollTop : window.pageYOffset));
				*/
				Util.addClass(this.li, "dragstart");
				this.li.style.position = "absolute";
				this.li.style.left = event.clientX-this.page_offset_x - this.pick_offset_x + 'px';
				this.li.style.top = event.clientY-this.page_offset_y - this.pick_offset_y + 'px';

				this.insertion_at = false; 

				var page_scroll = ((Util.explorer()) ? document.documentElement.scrollTop : window.pageYOffset);
				for(var i = 0; li = this.childNodes[i]; i++) {
					if(this.li != li && !li.className.match("disabled|options")) {
						Util.removeClass(li, "leftover");
						Util.removeClass(li, "rightover");
//						li.style.border = "1px solid #ffffff";
//						li.style.borderLeft = "1px solid #ff0000";
//						li.style.borderRight = "1px solid #ff0000";
//						li.style.borderBottom = "1px solid #ff0000";

//						li.style.border = "none";
//						Util.debug(Util.absoluteTop(li) + " < "+ event.clientY + " + " + page_scroll)
						if(Util.absoluteLeft(li) < event.clientX && Util.absoluteLeft(li) + (li.offsetWidth/2) > event.clientX && Util.absoluteTop(li) < (event.clientY + page_scroll) && Util.absoluteTop(li) + li.offsetHeight > (event.clientY + page_scroll)) {
							Util.addClass(li, "leftover");
							Util.removeClass(li, "rightover");
//							li.style.borderTop = "1px solid #ff0000";
//							li.style.borderLeft = "1px solid #ff0000";
							this.insertion_type = "before";
							this.insertion_at = li;
						}
						else if(Util.absoluteLeft(li) + (li.offsetWidth/2) < event.clientX && Util.absoluteLeft(li) + li.offsetWidth > event.clientX && Util.absoluteTop(li) < (event.clientY + page_scroll) && Util.absoluteTop(li) + li.offsetHeight > (event.clientY + page_scroll)) {
							Util.addClass(li, "rightover");
							Util.removeClass(li, "leftover");
//							li.style.borderRight = "1px solid #ff0000";
//7							li.style.borderBottom = "1px solid #ff0000";
							this.insertion_type = "after";
							this.insertion_at = li;
						}
					}
				}

			}
		}
		// release element, after drag (is called from document)
		e.dropElement = function(event) {
			//Util.debug(this.li + "ib:" + this.insertion_at.className);
//			Util.debug(this.insertion_at + ":::" + this.insertion_at.className + "::" + this.insertion_type);
			if(this.insertion_at) {
				if(this.insertion_type == "before") {
					this.insertBefore(this.li, this.insertion_at);
				}
				else {
					var node = Util.nextRealSibling(this.insertion_at);
					if(node == this.insertion_at) {
						this.appendChild(this.li);
					}
					else {
						this.insertBefore(this.li, node);
					}
				}

			}

 			this.li.style.opacity = 1;
			this.li.style.position = "static";
			Util.addClass(this.li, "dragstop");
			this.onmousemove = null;
			this.onmouseup = null;
			this.onmouseout = null;

			this.reDraw();
		}

		e.reDraw = function() {
			var form = document.getElementById("container:item");
			var action = Util.getIJ("form:action", form);
			var parameters =  "page_status=" + form.status_input.value;
			parameters +=  "&id=" + form.id_input.value;

			for(var i = 0; li = this.childNodes[i]; i++) {
				li.style.border = "none";
				if(!li.className.match("disabled|options")) {
					parameters += "&items[" + i + "]=" + Util.getIJ("id", li);
				}
			}

//			Util.debug(form.status_input.value);
//			Util.debug(form.id_input.value);
			Util.Ajax.loadContainer(action, "container:item", parameters);

			Util.debug(parameters);

			/*
			// send index to server
			table.send = function() {
				var i;
				Util.disableButton(this.save_button);
				for(i = 0; i < this.indexed.length; i++) {
					parameters += "id[" + i + "]=" + this.indexed[i].id + "&";
					parameters += "relation[" + i + "]=" + this.indexed[i].relation + "&";
				}
				Util.Ajax.loadContainer(this.save_url, this.save_target, parameters);
			}
			*/
		}

		e.page_offset_x = Util.absoluteLeft(document.getElementById("page"));
		e.page_offset_y = Util.absoluteTop(document.getElementById("page"));
		e.zindex = 100;
		e.elements = new Array();

		for(i = 0; li = e.childNodes[i]; i++) {

			if(!li.className.match("disabled|options")) {

				li.e = e;

				// init element
				Util.addClass(li, "dragable");

				// re-initiate row mouseover/out to also indicate draggedOn
				li.onmouseover = function() {
					//Util.debug("over:" + this.className);
//					this.ul.dragged_on = this;
//					Util.over(this);
				}
				li.onmouseout = function() {
//					this.table.dragged_on = false;
//					Util.out(this);
				}
				li.onmousedown = function(event) {
					this.e.pickElement(this, event);
				}
			}
		}

	}
}
