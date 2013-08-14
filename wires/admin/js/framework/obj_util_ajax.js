Util.Ajax = new Object();

// container to hold the requests
Util.Ajax.requests = new Array();

// Send request to url, calls the specified notify function on object on response
Util.Ajax.send = function(url, notify, object, parameters, async, type) {
	Util.setLoadStatus("Loading", "load");

	// set request id
	var id = this.requests.length;

	this.requests[id] = new Object();
	// save request parameters
	this.requests[id].url = url;
	this.requests[id].notifier = notify;
	this.requests[id].object = (typeof(object) != "undefined" ? object : window);
	this.requests[id].parameters = (typeof(parameters) != "undefined" ? parameters : "");
	this.requests[id].async = (typeof(async) != "undefined" ? async : true);
	this.requests[id].type = (typeof(type) == "string" ? type : "POST");

	// get request object, and verify it
	this.requests[id].xmlHttp = this.createRequestObject();
	if(!this.requests[id].xmlHttp || typeof(this.requests[id].xmlHttp.send) == 'undefined') {
		this.responder(id, false);
		return;
	}

	this.requests[id].xmlHttp.open(this.requests[id].type, this.requests[id].url, this.requests[id].async);
	this.requests[id].xmlHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

	try {
		this.requests[id].xmlHttp.send(parameters);
	}
	catch(e) {
		this.responder(id, false);
		return;
	}

	// If async initiate onreadystatechange
	if(this.requests[id].async) {
		this.requests[id].xmlHttp.onreadystatechange = function() {
			if(Util.Ajax.requests[id].xmlHttp.readyState == 4) {
				Util.Ajax.responder(id, true);
			}
		}
	}
	else {
		Util.Ajax.responder(id, true);
	}
	return;
}

// Create xmlhttprequest object 
Util.Ajax.createRequestObject = function() {
	var request_object = false;
	if(Util.explorer() && typeof(window.ActiveXObject) == "function") {
		try {
			request_object = new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch(e) {
			return false;
		}
	}
	else if(window.XMLHttpRequest) {
		try {
			request_object = new XMLHttpRequest();
		}
		catch(e) {
			return false;
		}
	}
	return request_object;
}

// XML load responder, calls notifier function specified in notify
Util.Ajax.responder = function(id, state) {
	var response_object, response;
	// get respond-to object and free the ressource
	response_object = this.requests[id].object;
	response_object.exe = this.requests[id].notifier;
	this.requests[id].object = null;
	this.requests[id].notifier = null;

	// if request could not be executed
	if(!state) {
		response_object.exe(false);
	}
	else {
		try {
			// xmlHttp.status will throw an exception under certain conditions, this was the only way I found to catch it
			this.requests[id].xmlHttp.status;

			if(this.requests[id].xmlHttp.status == 200) {
				this.requests[id].status = this.requests[id].xmlHttp.status;
				this.requests[id].statusText = this.requests[id].xmlHttp.statusText;
				this.requests[id].result = this.requests[id].xmlHttp.responseXML;

				Util.debug("responseText:"+this.requests[id].xmlHttp.responseText);
				Util.debug("###");

//				this.requests[id].resultText = this.requests[id].xmlHttp.responseText.trim(); ??? safari 4 breakdown
				this.requests[id].resultText = this.requests[id].xmlHttp.responseText;
				this.requests[id].xmlHttp = null;
				// relocate request to response
				response = this.requests[id];
//				Util.debug("res" + response);
				response_object.exe(response);
			}
			else {
				response_object.exe(false);
			}
		}
		catch(e) {
			Util.debug("faila:" + e)
			if(this.requests[id]) {
				response_object.exe(false);
			}
		}
	}
	// reset request
	Util.Ajax.requests[id] = null;
}

Util.Ajax.loader = function(container) {
//	var container = document.getElementById(container_id);
	var height = container.offsetHeight;
	var width = container.offsetWidth;
	var loader = document.createElement("div");
	loader.className = "loader";
	container.style.position = "relative";
	container.appendChild(loader);
	loader.style.height = height + "px";
	loader.style.width = width + "px";
}

Util.Ajax.deleteConfirm = function(container) {
	container.style.position = "relative";
	var confirm = Util.getElementsByClass("deleteConfirm", container)[0];
	confirm.style.display = "block";
}
Util.Ajax.deleteCancel = function(container) {
	var confirm = Util.getElementsByClass("deleteConfirm", container)[0];
	confirm.style.display = "none";
	
}


// Load new container into predefined target
Util.Ajax.loadContainer = function(url, target_id, parameters) {
	var target = document.getElementById(target_id);
	parameters = (typeof(parameters) != "undefined" ? parameters + "&" : "") + "response_column=" + this.getResponseColumn(target);
	this.send(url, this.replaceElement, target, parameters);
}
// Submit container content
Util.Ajax.submitContainer = function(container_id) {

	var elements, proporties, element, target, i;
	var form = document.getElementById(container_id);
	var parameters = "";

	if(form) {
		proporties = this.getFormProporties(form);
		elements = this.getAllFormElements(form);
		parameters = "response_column=" + (proporties.classname ? proporties.classname : "");


		if(proporties.action) {
			// show load layer
			if(proporties.target) {
				Util.Ajax.loader(proporties.target);
			}

			for(i = 0; element = elements[i]; i++) {
				parameters += "&"+element.name+"="+encodeURIComponent(element.value);
			}
//			Util.debug("Send:" + parameters);
			this.send(proporties.action, this.replaceElement, proporties.target, parameters, true, proporties.method);
			return true;
		}
		// we don't have sufficient information to submit?
		Util.debug("No form action!!!")
		return false;
	}
	Util.debug("Something is all wrong with the form identification!!!");
	return false;
}

// Submit container content
Util.Ajax.submitElement = function(e) {

	var proporties = this.getFormProporties(e.submit_form);

//	Util.debug();

	Util.debug("submit:" + e +"&&"+ proporties.action +"&&"+ e.html_input);
	if(e && e.html_input && e.page_status && e.item_id) {
		var params = "page_status=" + e.page_status;
		params += "&id=" + e.item_id;
		params += "&atr=" + e.html_input.name;
		params += "&" + e.html_input.name + "=" + e.html_input.value;
		Util.debug(params);
		this.send(proporties.action, this.replaceElement, e.submit_form, params);
		return true;
	}
	Util.debug("Something is all wrong with the form identification!!!");
	return false;
}


Util.Ajax.setUrlMarker = function(marker) {
	location.href = "#page_status=page,"+marker;
}
Util.Ajax.resetUrlMarker = function(marker) {
	location.href = "#";
}

// Replace element child with request response
// (function can be attached to child.parentNode, used with this.replaceElement)
Util.Ajax.replaceElementChild = function(response, child) {
	var component;
//	Util.debug("resc" + response);

	if(response) {
		component = Util.Ajax.validateResult(response.resultText);

		// did we get a response container?
		if(typeof(component) == "object") {
			this.replaceChild(component, child);

			// initialize component
			Util.initElements(component);
			return true;
		}
		// otherwise return default error (not implemented yet)
		else {
			Util.debug("Something is all wrong with the response!!!");
			Util.debug("Response:"+response.resultText);
			Util.debug("###")
			return false;
		}
	}
	else {
		Util.debug("No response!!!");
		return false;
	}
}
// Replace element with request response element (function is attached to element)
// commonly used as response handler
Util.Ajax.replaceElement = function(response) {
//	Util.debug("resb" + response);

	Util.setLoadStatus("Initiating", "init");
	this.parentNode.replaceElementChild = Util.Ajax.replaceElementChild;
	this.parentNode.replaceElementChild(response, this);
}

// Reset container - by simply removing content, and adding target class to element to hide if from IE
Util.Ajax.resetContainer = function(target_id) {
	document.getElementById(target_id).innerHTML = "";
	document.getElementById(target_id).className += " targetContainer";
}

// Update select with new values
Util.Ajax.updateSelect = function(e, url, target) {
	// looks for id[] in target (of incremental list)
	var adjust_target_name = e.id.indexOf("[");
	if(adjust_target_name != -1) {
		target = target + e.id.substring(adjust_target_name);
	}
	target.length = 0;
	this.send(url, Util.Ajax.insertNewSelectValues, document.getElementById(target), 'id=' + e.options[e.selectedIndex].value);
}
// Insert new values in select
Util.Ajax.insertNewSelectValues = function(response) {
	var elements, i, e, values;
	this.length = 0;
	elements = response.resultText.split("#");
	this.length = elements.length;
	for(i = 0; e = elements[i]; i++) {
		values = e.split(",");
		this.options[i].value = values[0];
		this.options[i].text = values[1];
	}
}

// Simple validation of response
// automatically executes script elements
// returns content element
Util.Ajax.validateResult = function(result){
	var valid, content_element, script_elements, child, i;

	this.validateElement = typeof(this.validateElement) == "object" ? this.validateElement : document.createElement("div");
	// IE ignores script-tags in the begining of result, unless an extra &nbsp; is added
	if(Util.explorer()) {
		this.validateElement.innerHTML = "&nbsp;"+result;
	}
	else {
		this.validateElement.innerHTML = result;
	}
	content_element = this.validateElement.getElementsByTagName("div").length ? this.validateElement.getElementsByTagName("div")[0] : false;

	script_elements = new Array();
	// only use root level script tags
	for(i = 0; child = this.validateElement.childNodes[i]; i++) {
		if(child.nodeName.toLowerCase() == "script") {
			script_elements.unshift(child);
		}
	}

	// execute script elements
	this.executeScript(script_elements);
	// return content element
	return content_element;
}
// Execute script response
Util.Ajax.executeScript = function(script_elements) {
	var i;

	if(script_elements.length) {
		for(i = script_elements.length-1; i >= 0; i--) {
			// W3C
			if(script_elements[i].firstChild && script_elements[i].firstChild.nodeValue) {
				eval(script_elements[i].firstChild.nodeValue);
			}
			// IE
			else if(Util.explorer() && script_elements[i].text) {
				eval(script_elements[i].text);
			}
		}
	}
}

// Get all form element names and values
Util.Ajax.getAllFormElements = function(container) {
	var input, inputs, select, selects, textarea, textareas, i;
	var elements = new Array();
	// Inputs
	inputs = container.getElementsByTagName("input");
	for(i = 0; input = inputs[i]; i++) {
		// only get enabled inputs
		if(!input.disabled) {
			if(input.type == "text" || input.type == "password" || input.type == "hidden" && input.name != "list:search") {
				elements[elements.length] = {name:input.name, value:input.value};
			}
			else if((input.type == "checkbox" || input.type == "radio") && input.checked && input.name != "list:selectall") {
				elements[elements.length] = {name:input.name, value:input.value};
			}
		}
	}
	// Selects
	selects = container.getElementsByTagName("select");
	for(i = 0; select = selects[i]; i++) {
		if(!select.disabled && select.options.length) {
			elements[elements.length] = {name:select.name, value:select.options[select.selectedIndex].value};
		}
	}
	// Textareas
	textareas = container.getElementsByTagName("textarea");
	for(i = 0; textarea = textareas[i]; i++) {
		if(!textarea.disabled) {
			elements[elements.length] = {name:textarea.name, value:textarea.value};
		}
	}
	return elements;
}
// Get form proporties
// - the form action
// - the form method
// - the form target
// - class of the container (column based names)
Util.Ajax.getFormProporties = function(container) {
	var regexp;
	var proporties = new Object();

	// get action (Fx 1.0.7 has some problems with this regexp when it is given as a string)
	regexp = new RegExp("form:action:[?=\\w/\\#~:.?+=?&%@!\\-]*");
	if(container.className.match(regexp)) {
		proporties.action = container.className.match(regexp)[0].replace(/form:action:/g, "");
	}

	// get method (value)
	regexp = new RegExp(/form:method:[?=\w]*/);
	if(container.className.match(regexp)) {
		proporties.method = container.className.match(regexp)[0].replace(/form:method:/g, "");
	}
	else {
		proporties.method = "POST";
	}

	// get target (element)
	regexp = new RegExp(/form:target:[?=\w\_:-\\]*/);
	if(container.className.match(regexp)) {
		proporties.target = container.className.match(regexp)[0].replace(/form:target:/g, "");
		proporties.target = document.getElementById(proporties.target);
	}
	else {
		proporties.target = container;
	}

	// get columntype classname
	proporties.classname = this.getResponseColumn(proporties.target);
	return proporties;
}
// Get response column type
Util.Ajax.getResponseColumn = function(target) {
	var regexp, classname;
	regexp = new RegExp(/c[?=\w]*/);
	if(target.className.match(regexp)) {
		
		// TODO (new column system)
		//classname = target.className.match(regexp)[0].replace(/column/g, "");
		classname = target.className.match(regexp)[0].replace(/c/g, "");
	}
	return classname;
}

// Check the returned value (for debugging purposes)
Util.Ajax.check = function(response) {
	if(response) {
		Util.debug(response.status+","+response.resultText+","+response.result);
	}
	else {
		alert("failed");
	}
}
