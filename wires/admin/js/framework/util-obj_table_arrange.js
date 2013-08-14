Util.Objects["table"].initArrange = function(table) {

	var element, intersection, i;

	// activate intersection row
	table.activateIntersection = function(intersection) {
		intersection.table = this;
		intersection.onmouseover = function() {
			if(this.table.dragging) {
				Util.addClass(this, "intover");
				this.table.dragged_on = this;
			}
		}
		intersection.onmouseout = function() {
			Util.removeClass(this, "intover");
			this.table.dragged_on = false;
		}
	}

	// index structure with relation (for sending to server)
	table.indexStructure = function() {
		var i, element, previous;
		this.indexed = new Array();
		// start element
		element = this.rows[0];
		// loop through elements
		for(i = 0; element = Util.nextRealSibling(element, "intersection"); i) {
			if(!element.className.match("disabled")) {
				this.indexed[i] = new Object();
				this.indexed[i].id = Util.getIJ("id", element);
				if(!element.indent) {
					this.indexed[i].relation = 0;
				}
				// element is indented, find parent (first element with less indent)
				else {
					previous = Util.previousRealSibling(element, "intersection");
					// loop backwards
					while(previous && previous.indent >= element.indent) {
						previous = Util.previousRealSibling(previous, "intersection");
					}
					this.indexed[i].relation = Util.getIJ("id", previous);
				}
				i++;
			}
		}
	}

	// initiate dragging of message
	table.pickElement = function(element, event) {
		var node, next;
		event = event ? event : (window.event) ? window.event : false;

		// calculate default offset
		this.pick_offset_x = event.clientX-Util.absoluteLeft(element)-20;
		this.pick_offset_y = event.clientY-Util.absoluteTop(element)-20;

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
		document.onmousemove = this.dragElement;
		document.onmouseup = this.dropElement;
		document.table = this;
		this.dragging = true;
	}
	// drag the element (is called from document)
	table.dragElement = function(event) {
		event = event ? event : (window.event) ? window.event : false;
		if(event) {
			this.table.drag_content.style.display = "inline";
			this.table.drag_content.style.left = event.clientX - this.table.pick_offset_x + 'px';
			this.table.drag_content.style.top = event.clientY - this.table.pick_offset_y + 'px';
		}
	}
	// release element, after drag (is called from document)
	table.dropElement = function(event) {
		this.onmousemove = null;
		this.onmouseup = null;
		this.table.reDraw();
	}

	// redraw the table
	table.reDraw = function() {
		var insert_at_level, insert_from_level, insert_before_element, i, u, previous, next;

		document.sort = null;
		this.dragging = false;

		this.drag_content.style.display = "none";

		// if element was dropped out of range or on itself
		if(!this.dragged_on || this.dragged_on.dragged || (this.dragged_on.className.match(/intersection/g) && Util.previousRealSibling(this.dragged_on).dragged)) {
			while(this.drag_content_body.firstChild) {
				this.drag_content_body.firstChild.source_element.dragged = false;
				this.drag_content_body.removeChild(this.drag_content_body.firstChild);
			}
			return;
		}
		// if the table is not indented, keep it simple
		else if(table.indent === false) {
			insert_from_level = 0;
			insert_at_level = 0;
			insert_before_element = this.dragged_on.className.match(/intersection/g) ? this.dragged_on : Util.nextRealSibling(this.dragged_on);
		}
		// or figure out where to insert elements
		else {
			insert_from_level = this.drag_content_body.firstChild.source_element.indent;

			// if element dragged onto intersection
			if(this.dragged_on.className.match(/intersection/g)) {

				// find previous element
				previous = Util.previousRealSibling(this.dragged_on);
				insert_at_level = previous.indent;
				insert_before_element = this.dragged_on;

				// dragged_on is not the last intersection
				while(Util.nextRealSibling(insert_before_element, "intersection")) {
					next = Util.nextRealSibling(insert_before_element, "intersection")
					// if next element is another child
					if(next.indent > insert_at_level) {
						insert_before_element = Util.nextRealSibling(next);
					}
					else {
						break;
					}
				}
			}
			// dragged onto item
			else {
				insert_at_level = this.dragged_on.indent + 1;
				// insert before following intersection
				insert_before_element = Util.nextRealSibling(this.dragged_on);
			}
		}

		// insert dragged elements
		while(this.drag_content_body.firstChild) {
			// set correct indention
			this.drag_content_body.firstChild.source_element.indent = this.drag_content_body.firstChild.source_element.indent - insert_from_level + insert_at_level;
			this.drag_content_body.firstChild.source_element.dragged = false; 

			// replace element
			insert_before_element.parentNode.insertBefore(insert_before_element.parentNode.removeChild(this.drag_content_body.firstChild.source_element), insert_before_element);

			// remove element from dragContent
			this.drag_content_body.removeChild(this.drag_content_body.firstChild);
		}

		// reset rows coloring and indenting
		for(i = 1, u = 0; element = this.rows[i]; i++) {

			//every other element has to be intersection
			if(i%2 == 1 && !element.className.match(/intersection/g)) {
				intersection = this.intersection.cloneNode(true);
				element.parentNode.insertBefore(intersection, element);
				this.activateIntersection(intersection);
			}
			else if(i%2 == 0) {
				// remove malplaced intersection
				if(element.className.match(/intersection/g)) {
					element.parentNode.removeChild(element);
					// compensate for missing row
					i--;
				}
				// or correct line color and indention
				else {
					element.className = element.className.replace(/tr\d/g, "tr"+u++%2);
					if(this.indent !== false) {
						this.setIndentClass(element);
					}
				}
			}
		}
		// show save button
		Util.enableButton(this.save_button);
		this.indexStructure();
	}
	// send index to server
	table.send = function() {
		var i;
		var parameters =  "";
		Util.disableButton(this.save_button);
		for(i = 0; i < this.indexed.length; i++) {
			parameters += "id[" + i + "]=" + this.indexed[i].id + "&";
			parameters += "relation[" + i + "]=" + this.indexed[i].relation + "&";
		}
		Util.Ajax.loadContainer(this.save_url, this.save_target, parameters);
	}

	Util.unSelectify(table);

	table.dragging = false;
	table.dragged_on = false;
	table.getIndentInfo();

	table.save_button = Util.getElementsByClass("arrange:save", table.parentNode)[0];
	table.save_url = Util.getIJ("save", table.save_button);
	table.save_target = Util.getIJ("target", table.save_button);

	table.save_button.table = table;
	table.save_button.onclick = function() {
		this.table.send();
	}
	table.save_button.shortcut = Util.getIJ("key", table.save_button);
	if(table.save_button.shortcut) {
		Util.Onkeydown.addShortcut(table.save_button.shortcut, table.save_button);
	}

	// create intersection element
	table.intersection = document.createElement('tr');
	table.intersection.className = "intersection";
	table.intersection_child = document.createElement('td');
	table.intersection_child.setAttribute('colspan', table.rowCellCount());
	table.intersection.appendChild(table.intersection_child);
	for(i = 0; element = table.elements[i]; i++) {

		// insert intersection
		intersection = table.intersection.cloneNode(true);
		element.parentNode.insertBefore(intersection, element);
		table.activateIntersection(intersection);

		if(!element.className.match("disabled")) {
			// init element
			Util.addClass(element, "dragable");

			// re-initiate row mouseover/out to also indicate draggedOn
			element.onmouseover = function() {
				this.table.dragged_on = this;
				Util.over(this);
			}
			element.onmouseout = function() {
				this.table.dragged_on = false;
				Util.out(this);
			}
			element.onmousedown = function(event) {
				this.table.pickElement(this, event);
			}
		}
	}
	// add final intersection
	intersection = table.intersection.cloneNode(true);

	// insert on element parentNode instead of table, because of variations on tbody existance
	table.elements[0].parentNode.appendChild(intersection);
	table.activateIntersection(intersection);

	// index the structure
	table.indexStructure();

	// create drag container
	table.drag_content = document.createElement('TABLE');
	table.drag_content_body = document.createElement('TBODY');
	table.drag_content.appendChild(table.drag_content_body);
	table.drag_content.className = "draggedElement";
	table.drag_content.style.display = "none";
	document.body.appendChild(table.drag_content);
}
