// Add mouseover and -out event handling
Util.activate = function(e) {
	e.onmouseover = function() {
		Util.over(this);
	}
	e.onmouseout = function() {
		Util.out(this);
	}
}
// Add over class to element
Util.over = function(e) {
	this.addClass(e, "over");
}
// Remove over class from element
Util.out = function(e) {
	this.removeClass(e, "over");
}

// Make element unselectable
Util.unSelectify = function(e) {
	if(Util.explorer()) {
		e.onselectstart = function() {return false;}
	}
	else {
		e.onmousedown = function() {return false;}
	}
}
// Make element selectable
Util.selectify = function(e) {
	if(Util.explorer()) {
//		element.onselectstart = 
	}
	else {
		e.onmousedown = function() {return true;}
	}
}
// Put focus on first input, select or textarea if existing
Util.focusOnFirstInput = function(container) {
	var e, elements, i;
	elements = container.getElementsByTagName("*");
	for(i = 0; e = elements[i]; i++) {
		if(e.nodeName.match(/INPUT|SELECT|TEXTAREA/g) && !e.disabled && e.type != "hidden") {
			e.focus();
			return;
		}
	}
}

// Add new message to message
Util.addMessageBoard = function(message, classname) {
	if(document.getElementById("message")) {
		var message_board, new_message, undefined;
//		type = typeof(type) != "undefined" ? type : false;
//		type = type != undefined ? type : false;
		message_board = document.getElementById("message");
		new_message = document.createElement("p");
		new_message.innerHTML = message;
		Util.addClass(new_message, classname);
//		new_message.className = type ? type : '';
		message_board.appendChild(new_message);
	}
}
// Clear message
Util.clearMessageBoard = function() {
	if(document.getElementById("message")) {
		document.getElementById("message").innerHTML = '';
	}
}
// Updates the page load status
Util.setLoadStatus = function(message, classname) {
	if(document.getElementById("progress")) {
		Util.removeClass(document.getElementById("progress"), "init|done|load");
		Util.addClass(document.getElementById("progress"), classname);
		document.getElementById("progress").innerHTML = message;
	}
}

Util.IEsucks = function(e) {
	
	// Encapsulate problematic elements within element in div
//	var input, inputs, select, selects, textarea, textareas, i, div, border_elements, border_element;
//	inputs = element.getElementsByTagName("input");
//	for(i = 0; input = inputs[i]; i++) {
//		if(input.type == "text" && input.parentNode.nodeName != "TH") {
//			div = document.createElement("div");
//			div.appendChild(input.parentNode.replaceChild(div, input));
//		}
//	}
//	selects = element.getElementsByTagName("select");
//	for(i = 0; select = selects[i]; i++) {
//		if(select.parentNode.nodeName != "TD") {
//			div = document.createElement("div");
//			div.appendChild(select.parentNode.replaceChild(div, select));
//		}
//	}
//	textareas = element.getElementsByTagName("textarea");
//	for(i = 0; textarea = textareas[i]; i++) {
//		div = document.createElement("div");
//		div.appendChild(textarea.parentNode.replaceChild(div, textarea));
//	}

	// correct border class element (by moving class to new element)
	border_elements = Util.getElementsByClass("border", e);
	for(i = 0; border_element = border_elements[i]; i++) {
		border_element.className = Util.removeClass(border_element, "border");

		div = document.createElement("div");
		div.className = "border";
		while(border_element.childNodes.length) {
			div.appendChild(border_element.childNodes[0]);
		}
		border_element.appendChild(div);
	}
}

// ajax submits form on key "Enter" in input field
Util.submitOnEnter = function(event, form) {
	event = event ? event : window.event;
	if(event.keyCode == 13) {
		Util.nonClick(event);
		Util.Ajax.submitContainer(form);
	}
}

// set default input value
Util.defaultInputValue = function(e) {
	e.default_value = e.value;
	e.onfocus = function() {
		if(this.value == this.default_value) {
			this.value = "";
		}
	}
	e.onblur = function() {
		if(this.value == "") {
			this.value = this.default_value;
		}
	}
}

// Enable/disable select
Util.selectEnabling = function(e, state) {
	e.disabled = state ? false : true;
//	if(state) {
//		e.disabled = false;
//	}
//	else {
//		e.disabled = true;
//	}
}
// Enable disabled button
Util.enableButton = function(e) {
	Util.removeClass(e, "disabled");
	e.disabled = false;
	Util.Objects["button"].init(e);
}
// Disable button
Util.disableButton = function(e) {
	Util.removeClass(e, "over");
	Util.addClass(e, "disabled");
	e.disabled = "disabled";
}
// Count remaining characters until maxlength (used with HTML->textarea)
Util.textCounter = function(max_length, e) {
	if(e.value.length >= max_length) {
		e.value = e.value.substring(0, max_length);
	}
	document.getElementById("counter:" + e.name).innerHTML = "(" + (max_length - e.value.length) + ")";
}
// Javascript confirm action before relocating
Util.confirmAction = function(s, action) {
	var confirmation = confirm(s);
	if(confirmation) {
		location.href = action;
	}
}
// Set value of element with element_id to value
Util.setValue = function(e_id, v) {
	document.getElementById(e_id).value = v;
}


Util.selectValue = function(element_id, value) {
	var element = document.getElementById(element_id);
	for(var i = 0; option = element.options[i]; i++) {
		if(option.text == value) {
			element.selectedIndex = i;
		}
	}
}

