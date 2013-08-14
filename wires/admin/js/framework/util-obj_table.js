Util.Objects["table"] = new function() {

	this.init = function(table) {
		var rows, row, i, cell, cells, u, input, select, s, element;

		// Some specialchars do not have an alphabetically correct charCode
		table.correctCharOrder = function(value) {

			// In array below you can define a new charCode for misplaced chars (used only for sorting)
			var misplacedChars = new Array();
			misplacedChars["å"] = 250;

			var regexp = "";
			for(x in misplacedChars) {
				if(typeof(misplacedChars[x]) == "number") {
					regexp += (regexp ? "|" : "") + x;
				}
			}
			regexp = new RegExp(regexp, "g");
			value = value.replace(regexp, function(spechar){return String.fromCharCode(misplacedChars[spechar])});
			return value;
		}
		// compare function for sort
		table.compare = function(a, b) {
			if (a.value == b.value) {
				return -1;
			}
			else if (a.value < b.value) {
				return -1;
			}
			else {
				return 1;
			}
		}

		// check element, state optional
		table.check = function(element, state) {
			// explicit state
			if(typeof(state) != "undefined") {
				element.checkbox.checked = state;
				element.checked = state;
			}
			// uncheck - element is checked
			else if(element.checked) {
				element.checkbox.checked = false;
				element.checked = false;
			}
			// check - element is not checked
			else {
				element.checkbox.checked = true;
				element.checked = true;
			}
			// uncheck select all if one element is unchecked
			if(!element.checked && this.select_all) {
				this.select_all.checked = false;
			}
			// If row has selects, set state
			if(element.selects) {
				for(i = 0; select = element.selects[i]; i++) {
					Util.selectEnabling(select, element.checked);
				}
			}
		}
		// check all elements based on select_all value
		table.selectAll = function() {
			var element, i;
			for(i = 0; element = this.elements[i]; i++) {
				// only check visible elements
				if(this.select_all.checked && element.style.display != "none") {
					this.check(element, this.select_all.checked);
				}
				// but uncheck all
				else {
					this.check(element, this.select_all.checked);
				}
			}
		}

		// check element and indented children
		table.checkIndented = function(element) {
			var table_element, i;
			var start_checking = false;
			this.check(element);
			if(this.type == "indented") {
				this.getIndentInfo();
				for(i = 0; table_element = this.elements[i]; i++) {
					// find this element in flow and startChecking
					if(table_element == element) {
						start_checking = true;
					}
					// if element is child
					else if(start_checking && element.indent < table_element.indent) {
						this.check(table_element, element.checked);
					}
					// else element is sibling or parent
					else if(start_checking && (!table_element.indent || element.indent >= table_element.indent)) {
						return;
					}
				}
				return;
			}
		}
		// get indent info from element (not knowing on which cell)
		table.getIndentInfo = function() {
			var i, u, element, cell;
			var regexp = new RegExp(/indent_[?=\d]*/);
			for(i = 0; element = this.elements[i]; i++) {
				// if we know which cell to look in
				if(this.indent) {
					if(element.cells[this.indent].className.match(regexp)) {
						element.indent = parseInt(element.cells[this.indent].className.match(regexp)[0].replace(/indent_/g, ""));
					}
				}
				// look for cell with indent className, and remember for next element
				else {
					for(u = 0; cell = element.cells[u]; u++) {
						// found it
						if(cell.className.match(regexp)) {
							this.indent = u;
							element.indent = parseInt(cell.className.match(regexp)[0].replace(/indent_/g, ""));
						}
					}
				}
				// set indent=0 if we didn't find indent cell
				element.indent = element.indent ? element.indent : 0;
			}
		}
		// set element indent classname based on element.indent
		table.setIndentClass = function(element) {
			var regexp = new RegExp(/indent_[?=\d]*/);
			// insertion has to be done separately, because row may not already have indent class
			Util.removeClass(element.cells[this.indent], "indent_[?=\d]*");
			// only insert if indent > 0
			Util.addClass(element.cells[this.indent], element.indent ? "indent_"+element.indent : "");
		}

		// find child text node with content (besides space)
		table.findTextNode = function(element) {
			var i, node;
			for(i = 0; node = element.childNodes[i]; i++) {
				if(node.nodeType == 3 && node.nodeValue && node.nodeValue.trim()) {
					return node.nodeValue;
				}
				else if(node.childNodes.length) {
					return this.findTextNode(node);
				}
			}
			return "";
		}

		// sort by column
		table.sortBy = function(column_header) {
			var time1 = new Date().getTime();

			var header, direction, ascii, sorting, i, u, o, element;

			if(!this.indexed) {
				for(i = 0; header = this.headers[i]; i++) {
					if(header.type == "sortable"){
						// is sorttype defined?
						if(header.sort == "numeric") {
							header.ascii = false;
						}
						else if(header.sort == "ascii") {
							header.ascii = true;
						}
						// else guess ...
						else {
							if(Util.getIJ("sortby", this.elements[0].cells[header.column])) {
								header.ascii = isNaN(Util.getIJ("sortby", this.elements[0].cells[header.column]));
							}
							else {
								header.ascii = isNaN(this.findTextNode(this.elements[0].cells[header.column]));
							}
						}

						// index
						for(u = 0, o = 0; element = this.elements[u]; u++, o++) {

							header.sortInfo[o] = new Object();
							header.sortInfo[o].row = element;

							// do we have sortby definition
							if(Util.getIJ("sortby", element.cells[i])) {
								// check for date
								if(this.findTextNode(element.cells[i]).match(/(\d\d)-(\d\d)-(\d\d\d\d)/g)) {
									matches = this.findTextNode(element.cells[i]);
									Util.removeClass(element.cells[i], "sortby:"+matches);
									Util.addClass(element.cells[i], "sortby:"+ new Date(matches.substring(6,10), matches.substring(3,5), matches.substring(0,2)).getTime());
								}
								// amounts
								else if(this.findTextNode(element.cells[i]).match(/[?=\d].(\d\d\d),(\d\d)/g)) {
									matches = this.findTextNode(element.cells[i]);
									Util.removeClass(element.cells[i], "sortby:"+matches);
									Util.addClass(element.cells[i], "sortby:"+ matches.replace(".", ""));
								}
								header.sortInfo[o].value = Util.getIJ("sortby", element.cells[i]).toLowerCase();
							}
							// otherwise find textnode
							else {
								header.sortInfo[o].value = this.findTextNode(element.cells[i]).toLowerCase();
							}
							header.sortInfo[o].value = header.ascii ? this.correctCharOrder(header.sortInfo[o].value) : parseFloat(header.sortInfo[o].value);
						}
					}
				}
				this.indexed = true;
			}

			// check for direction
			direction = column_header.className ? (column_header.className.match(/sortup/g) ? "sortdown" : "sortup") : "sortup";
			// reset direction indicators
			for(i = 0; header = this.headers[i]; i++) {
				header.className = header.className.replace(/ sortup|sortup | sortdown|sortdown |sortup|sortdown/g, "");
			}
			column_header.className += column_header.className ? " "+direction : direction;

			// sort
			this.headers[column_header.column].sortInfo.sort(this.compare);

			// reappend
			if(direction == "sortdown") {
				for(i = this.headers[column_header.column].sortInfo.length-1; element = this.headers[column_header.column].sortInfo[i]; i--) {
					this.body.appendChild(element.row);
				}
			}
			else {
				for(i = 0; element = this.headers[column_header.column].sortInfo[i]; i++) {
					this.body.appendChild(element.row);
				}
			}

			this.resetRowColor();
		}

		// activate element
		table.activateElement = function(element) {
			element.table = this;

			element.selects = element.getElementsByTagName("select").length ? element.getElementsByTagName("select") : false;
			element.inputs = element.getElementsByTagName("input").length ? element.getElementsByTagName("input") : false;

			// only enable mouseover if no selects to avoid flicker when selecting option
			if(!element.selects) {
				Util.activate(element);
			}

			// if the row contains an input
			if(element.inputs) {
				// look for checkbox
				for(u = 0; input = element.inputs[u]; u++) {
					if(input.type == "checkbox") {
						element.checkbox = input;
						input.element = element;
						element.checked = input.checked;
						Util.addClass(element, "clickable");

						// if table is indented change select method
						if(this.type == "indented") {
							element.onclick = function() {
								this.table.checkIndented(this);
							}
						}
						// else basic variation for safari
						else if(Util.safari()) {
							element.onclick = function() {
								this.table.check(this);
							}
						}
						// or enable multible checking ondragover (buggy in safari)
						else {
							element.onclick = function(event) {
								Util.nonClick(event)
							}
							element.onmousedown = function() {
								this.table.check(this);
							}
							input.onmousedown = function() {
								Util.Objects["table"].ondragover = this.checked ? "off" : "on";
								document.onmouseup = function() {
									Util.Objects["table"].ondragover = false;
									document.onmouseup = null;
								}
							}
							input.onmouseover = function() {
								if(Util.Objects["table"].ondragover) {
									this.element.table.check(this.element, (Util.Objects["table"].ondragover == "on" ? true : false));
								}
							}
						}
						// enable checkbox-select dependability
						if(element.selects){
							// disable onclick forwarding on selects
							for(s = 0; select = element.selects[s]; s++) {
								if(Util.firefox()) {
									select.onmousedown = function(event) {
										Util.nonClick(event);
									}
								}
								select.onclick = function(event) {
									Util.nonClick(event);
								}
							}
						}
						// checkbox found -> break
						break;
					}
				}
			}
		}
		// get real row cell count (including colspans correctly)
		table.rowCellCount = function() {
			var cell, i;
			var cell_count = 0;
			for(i = 0; cell = this.elements[0].cells[i]; i++) {
				cell_count += cell.getAttribute("colspan") ? parseInt(cell.getAttribute("colspan")) : 1;
			}
			return cell_count;
		}
		// reset rows coloring
		table.resetRowColor = function() {
			var i, element;
			this.elements = new Array();
			for(i = 0, u = 0; element = this.rows[i]; i++) {
				if(element.getElementsByTagName("td").length) {
					this.elements[this.elements.length] = element;
				}
			}
			for(i = 0, u = 0; element = this.elements[i]; i++) {
				if(element.style.display != "none") {
					element.className = element.className.replace(/tr\d/g, "tr"+u++%2);
				}
			}
		}

		// get body
		table.body = table.getElementsByTagName("tbody")[0];
		table.header = false;
		table.elements = new Array();
		table.search = false;
		table.select_all = false;
		table.indent = false;

		this.ondragover = false;

		// get element type, if defined
		table.type = Util.getIJ("table", table);
		
		// index table content
		rows = table.getElementsByTagName("tr");
		for(i = 0; row = rows[i]; i++) {

			// element - row contains TD
			if(row.getElementsByTagName("td").length) {
				table.elements[table.elements.length] = row;
			}
			// header - row contains TH
			else if(row.getElementsByTagName("th").length) {
				table.header = row;

				// look for special cases in header
				table.headers = row.getElementsByTagName("th");
				for(u = 0; cell = table.headers[u]; u++) {
					// look for search field
					if(cell.className.match(/search/g)) {
						if(cell.getElementsByTagName("input").length) {
							table.search = cell.getElementsByTagName("input")[0];
						}
					}
					// look for selectall
					else if(cell.className.match(/selectall/g)) {
						if(cell.getElementsByTagName("input").length) {
							table.select_all = cell.getElementsByTagName("input")[0];
							table.select_all.table = table;
							table.select_all.onclick = function() {
								this.table.selectAll();
							}
						}
					}
					// look for sortby
					else if(cell.className.match(/sortby/g)) {
						cell.type = "sortable";
						cell.sort = Util.getIJ("sort", cell);
						cell.table = table;
						cell.column = u;
						cell.sortInfo = new Array();
						cell.onclick = function() {
							this.table.sortBy(this);
						}
						Util.activate(cell);
						Util.addClass(cell, "clickable");
					}
					else {
						// un-select text on datagrid buttons
						Util.unSelectify(cell);
					}
				}
			}
		}
		// element details
		for(i = 0; element = table.elements[i]; i++) {
			table.activateElement(element);
		}
		// if search input identified, add search object
		if(table.search) {
			this.initSearch(table);
		}
		if(table.type == "arrange") {
			this.initArrange(table);
		}
		if(table.type == "incremental") {
			this.initIncremental(table);
		}
	}
}
