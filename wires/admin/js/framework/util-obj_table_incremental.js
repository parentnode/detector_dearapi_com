Util.Objects["table"].initIncremental = function(table) {

	var i, element;

	// add new row
	table.addRow = function(element) {
		var i, row, new_element, selects, select, input, inputs
		new_element = element.cloneNode(true);

		// reset select elements
		selects = new_element.getElementsByTagName("select");
		for(i = 0; select = selects[i]; i++) {
			select.selectedIndex = 0;
		}
		// reset input values
		old_inputs = element.getElementsByTagName("input");

		inputs = new_element.getElementsByTagName("input");
		for(i = 0; input = inputs[i]; i++) {
			input.value = "";
			input.onchange = old_inputs[i].onchange;
		}

		element.parentNode.appendChild(new_element);
		this.resetRowColor();
		this.activateElement(new_element);
		this.activateIncElement(new_element);
		this.updateRowFieldNames();

		// check if selects are interdependable (execute onchange action)
		for(i = 0; select = selects[i]; i++) {
			if(select.onchange) {
				select.onchange();
			}
		}
	}
	// remove row
	table.removeRow = function(element) {
		if(element.parentNode.childNodes.length > 2) {
			element.parentNode.removeChild(element);
			this.resetRowColor();
			this.updateRowFieldNames();
		}
		else {
			alert("You cannot delete this row!");
		}
	}
	// update input and select names based on class name:identifier
	table.updateRowFieldNames = function() {
		var i, element, select, selects, input, inputs, u;
		for(i = 0; element = this.elements[i]; i++) {
			// remove "remove"-link if only one item
			if(this.elements.length <= 1) {
				Util.removeClass(element.removeElement, "remove");
			}
			else {
				Util.addClass(element.removeElement, "remove");
			}
			selects = element.getElementsByTagName("select");
			for(u = 0; select = selects[u]; u++) {
				name = Util.getIJ("name", select);
				select.name = name + "[" + i + "]";
				select.id = select.name;
			}
			inputs = element.getElementsByTagName("input");
			for(u = 0; input = inputs[u]; u++) {
				name = Util.getIJ("name", input);
				input.name = name + "[" + i + "]";
				input.id = "";
			}
		}
	}
	// initiate element, add functions and references
	table.activateIncElement = function(element) {
		element.addElement = Util.getElementsByClass("incremental:add", element)[0];
		element.removeElement = Util.getElementsByClass("incremental:remove", element)[0];
		Util.unSelectify(element.addElement);
		Util.unSelectify(element.removeElement);
		element.table = this;
		element.addElement.element = element;
		element.removeElement.element = element;

		// initiate add/remove buttons
		Util.addClass(element.addElement, "add");
		if(this.elements.length > 1) {
			Util.addClass(element.removeElement, "remove");
		}
		element.addElement.onclick = function() {
			this.element.table.addRow(this.element);
		}
		element.removeElement.onclick = function() {
			this.element.table.removeRow(this.element);
		}
	}

	// initiate elements
	for(i = 0; element = table.elements[i]; i++) {
		table.activateIncElement(element);
	}
}
