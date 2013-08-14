var Util = new function() {
	this.Objects = new Array();
}
String.prototype.cutString = function(l) {
	var length_compensation, matches, i;
	length_compensation = 0;
	matches = this.match(/(\&)([\w\d]+)(\;)/g);
	for (i = 0; match = matches[i]; i++){
		if(this.indexOf(match) < l){
			l += match.length-1;
		}
	}
	return this.substring(0, l) + (this.length > l ? "..." : "");
}
String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g, "");
}
Util.explorer = function(version, scope) {
	if(document.all) {
		var undefined;
		var current_version = navigator.userAgent.match(/(MSIE )(\d+.\d)/i)[2];
		if(scope && !eval(current_version + scope + version)){
			return false;
		}
		else if(version && current_version != version) {
			return false;
		}
		else {
			return current_version;
		}
	}
	else {
		return false;
	}
}
Util.safari = function(version, scope) {
	if(navigator.userAgent.indexOf("Safari") >= 0) {
		var undefined;
		var current_version = navigator.userAgent.match(/(Safari\/)(\d+)(.\d)/i)[2];
		if(scope && !eval(current_version + scope + version)){
			return false;
		}
		else if(scope && version && current_version != version) {
			return false;
		}
		else {
			return current_version;
		}
	}
	else {
		return false;
	}
}
Util.firefox = function(version, scope) {
	if(navigator.userAgent.indexOf("Firefox") >= 0) {
		var undefined;
		var current_version = navigator.userAgent.match(/(Firefox\/)(\d+\.\d+)(\.\d+)/i)[2];
		if(scope && !eval(current_version + scope + version)){
			return false;
		}
		else if(version && current_version != version) {
			return false;
		}
		else {
			return current_version;
		}
	}
	else {
		return false;
	}
}
Util.opera = function() {
	return (navigator.userAgent.indexOf("Opera") >= 0) ? true : false;
}
Util.windows = function() {
	return (navigator.userAgent.indexOf("Windows") >= 0) ? true : false;
}
Util.osx = function() {
	return (navigator.userAgent.indexOf("OS X") >= 0) ? true : false;
}
Util.otliam = function(name, dom){
	document.write('<a onclick="Util.otliamNoise(\''+name+'\', \''+dom+'\')">'+name+'<span>@</span>'+dom+'</a>');
}
Util.otliamNoise = function(name, dom){
	location.href = "ma"+"ilto:"+name+"@"+dom;
}
Util.getCookie = function(name){
	var cookie_id, cookie_position, cookie_value, cookie_value_start, cookie_value_end;
	cookie_id = name + "=";
	cookie_position = document.cookie.indexOf(cookie_id);
	if(cookie_position != -1) {
		cookie_value_start = cookie_position + cookie_id.length;
		cookie_value_end = document.cookie.indexOf(';', cookie_value_start);
		cookie_value_end = cookie_value_end ? cookie_value_end : document.cookie.length;
		cookie_value = document.cookie.substring(cookie_value_start, cookie_value_end);
		return unescape(cookie_value);
	}
	return false;
}
Util.delCookie = function(name) {
	document.cookie = name + "=;expires=Thu, 01-Jan-70 00:00:01 GMT";
}
Util.popUp = function(url, name, w, h, extra) {
	var p;
	name = name ? name : "POPUP_" + new Date().getHours() + "_" + new Date().getMinutes() + "_" + new Date().getMilliseconds();
	w = w ? w : 330;
	h = h ? h : 150;
	p = "width=" + w + ",height=" + h;
	p += ",left=" + (screen.width-w)/2;
	p += ",top=" + ((screen.height-h)-20)/2;
	p += extra ? "," + extra : ",scrollbars";
	document[name] = window.open(url, name, p);
}
Util.getVar = function(s) {
	var p = location.search;
	var start_index = (p.indexOf("&" + s + "=") > -1) ? p.indexOf("&" + s + "=") + s.length + 2 : ((p.indexOf("?" + s + "=") > -1) ? p.indexOf("?" + s + "=") + s.length + 2 : false);
	var end_index = (p.substring(start_index).indexOf("&") > -1) ? p.substring(start_index).indexOf("&") + start_index : false;
	var return_string = start_index ? p.substring(start_index,(end_index ? end_index : p.length)): "";
	return return_string;
}
Util.flash = function(url, w, h, background, name, id, print) {
	var s;
	background = background ? background : "#FFFFFF";
	name = name ? name : "flash_" + new Date().getHours() + "_" + new Date().getMinutes() + "_" + new Date().getMilliseconds();
	id = id ? id : name;
	s = '<object id="'+name+'" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="'+w+'" height="'+h+'" name="'+name+'" align="middle">';
	s += '<param name="allowScriptAccess" value="always" />';
	s += '<param name="movie" value="'+url+'" />';
	s += '<param name="quality" value="high" />';
	s += '<param name="bgcolor" value="'+background+'" />';
	s += '<param name="wmode" value="transparent" />';
	s += '<param name="menu" value="false" />';
	s += '<param name="scale" value="noscale" />';
	s += '<embed id="'+name+'" src="'+url+'" menu="false" scale="noscale" quality="high" bgcolor="'+background+'" wmode="transparent" width="'+w+'" height="'+h+'" name="'+name+'" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />';
	s += '</object>';
	if(print) {
		document.write(s);
		return "";
	}
	return s;
}
Util.activate = function(e) {
	e.onmouseover = function() {
		Util.over(this);
	}
	e.onmouseout = function() {
		Util.out(this);
	}
}
Util.over = function(e) {
	this.addClass(e, "over");
}
Util.out = function(e) {
	this.removeClass(e, "over");
}
Util.unSelectify = function(e) {
	if(Util.explorer()) {
		e.onselectstart = function() {return false;}
	}
	else {
		e.onmousedown = function() {return false;}
	}
}
Util.selectify = function(e) {
	if(Util.explorer()) {
	}
	else {
		e.onmousedown = function() {return true;}
	}
}
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
Util.addMessageBoard = function(message, classname) {
	if(document.getElementById("message")) {
		var message_board, new_message, undefined;
		message_board = document.getElementById("message");
		new_message = document.createElement("p");
		new_message.innerHTML = message;
		Util.addClass(new_message, classname);
		message_board.appendChild(new_message);
	}
}
Util.clearMessageBoard = function() {
	if(document.getElementById("message")) {
		document.getElementById("message").innerHTML = '';
	}
}
Util.setLoadStatus = function(message) {
	if(document.getElementById("progress")) {
		document.getElementById("progress").innerHTML = message;
	}
}
Util.IEsucks = function(e) {
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
Util.submitOnEnter = function(event, form) {
	event = event ? event : window.event;
	if(event.keyCode == 13) {
		Util.nonClick(event);
		Util.Ajax.submitContainer(form);
	}
}
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
Util.selectEnabling = function(e, state) {
	e.disabled = state ? false : true;
}
Util.enableButton = function(e) {
	Util.removeClass(e, "disabled");
	e.disabled = false;
	Util.Objects["button"].init(e);
}
Util.disableButton = function(e) {
	Util.removeClass(e, "over");
	Util.addClass(e, "disabled");
	e.disabled = "disabled";
}
Util.textCounter = function(max_length, e) {
	if(e.value.length >= max_length) {
		e.value = e.value.substring(0, max_length);
	}
	document.getElementById("counter:" + e.name).innerHTML = "(" + (max_length - e.value.length) + ")";
}
Util.confirmAction = function(s, action) {
	var confirmation = confirm(s);
	if(confirmation) {
		location.href = action;
	}
}
Util.setValue = function(e_id, v) {
	document.getElementById(e_id).value = v;
}
Util.nonClick = function(event) {
	event = event ? event : window.event;
	if(event.preventDefault) {event.preventDefault();}
	if(event.stopPropagation) {event.stopPropagation();}
	event.returnValue = false;
	event.cancelBubble = true;
}
Util.addEventHandler = function(e, type, action) {
	if(Util.explorer()) {
		e.attachEvent("on" + type, action);
	}
	else {
		e.addEventListener(type, action, false);
	}
}
Util.removeEventHandler = function(e, type, action) {
	if(Util.explorer()) {
		e.detachEvent("on" + type, action);
	}
	else {
		e.removeEventListener(type, action, false);
	}
}
Util.Onload = new function() {
	this.actions = new Array();
	this.onloadCatcher = function(event) {
		Util.Onload.execute(event);
	}
	this.addAction = function(action) {
		if(!this.actions.length) {
			Util.addEventHandler(window, "load", this.onloadCatcher);
		}
		this.actions[this.actions.length] = action;
	}
	this.execute = function() {
		var i, action;
		Util.initElements(document.getElementById('page'));
		for(i = 0; action = this.actions[i]; i++) {
			if(typeof(action) == "function") {
				action();
			}
			else {
				eval(action);
			}
		}
	}
}
Util.addEventHandler(window, "load", Util.Onload.onloadCatcher);
Util.Ontimeout = new function() {
	this.actions = new Array();
	this.objects = new Array();
	this.timers = new Array();
	this.setTimer = function(object, action, timeout) {
		var id = this.actions.length;
		this.actions[id] = action;
		this.objects[id] = object;
		this.timers[id] = setTimeout("Util.Ontimeout.execute("+id+")", timeout);
		return id;
	}
	this.resetTimer = function(id) {
		clearTimeout(this.timers[id]);
	}
	this.execute = function(id) {
		this.objects[id].exe = this.actions[id];
		this.objects[id].exe();
		this.objects[id].exe = null;
		this.actions[id] = null;
		this.objects[id] = null;
		this.timers[id] = null;
	}
}
Util.Onkeydown = new function() {
	this.shortcuts = new Array();
	this.onkeydownCatcher = function(event) {
		Util.Onkeydown.catchKey(event);
	}
	this.addShortcut = function(key, action) {
		if(!this.shortcuts.length) {
			Util.addEventHandler(document, "keydown", this.onkeydownCatcher);
		}
		if(this.shortcuts[key.toString().toUpperCase()] == undefined) {
			this.shortcuts[key.toString().toUpperCase()] = action;
		}
		else{
			alert("Shortcut for: " + key + "\nconflicts with shortcut\naction: " + this.shortcuts[key].action);
		}
	}
	this.catchKey = function(event) {
		event = event ? event : window.event;
		var pressed_key = String.fromCharCode(event.keyCode);
		if(this.shortcuts[pressed_key] && (event.ctrlKey || event.metaKey)) {
			Util.nonClick(event);
			eval(this.shortcuts[pressed_key]);
		}
	}
}
Util.getParentTag = function(tag, e) {
	if(element.nodeName != tag && e.nodeName != "BODY") {
		e = Util.getParentTag(tag, e.parentNode);
	} 
	return element;
}
Util.getElementsByClass = function(classname, content) {
	var e, i, elements, regexp, return_array;
	return_array = new Array();
	elements = content ? (typeof(content) == "string" ? document.getElementById(content).getElementsByTagName("*") : content.getElementsByTagName("*")) : document.getElementById("content").getElementsByTagName("*");
	elements = elements.length ? elements : (Util.explorer() ? document.all : elements);
	regexp = new RegExp("(^|\\s)" + classname + "(\\s|$|\:)");
	for(i = 0; e = elements[i]; i++) {
		if(regexp.test(e.className)) {
			return_array[return_array.length] = e;
		}
	}
	return return_array;
}
Util.previousRealSibling = function(e, exclude) {
	var regexp, previous, undefined;
	exclude = exclude ? exclude : false;
	regexp = new RegExp("(^|\\s)" + exclude + "(\\s|$)");
	previous = e.previousSibling;
	while(previous && previous.nodeType == 3 && (!exclude || previous.className.match(regexp) || previous.nodeName == exclude)) {
		previous = previous.previousSibling;
	}
	return previous;
}
Util.nextRealSibling = function(e, exclude) {
	var regexp, next, undefined;
	exclude = exclude ? exclude : false;
	regexp = new RegExp("(^|\\s)" + exclude + "(\\s|$)");
	next = e.nextSibling;
	while(next && next.nodeType == 3 && (!exclude || next.className.match(regexp) || next.nodeName == exclude)) {
		next = next.nextSibling;
	}
	return next;
}
Util.getIJ = function(id, e) {
	var regexp = new RegExp(id + ":[?=\\w/\\#~:.?+=?&%@!\\-]*");
	if(e.className.match(regexp)) {
		return e.className.match(regexp)[0].replace(id + ":", "");
	}
	return false;
}
Util.addClass = function(e, classname) {
	if(classname) {
		var regexp = new RegExp("(^|\\s)" + classname + "(\\s|$|\:)");
		if(!regexp.test(e.className)) {
			e.className += e.className ? " " + classname : classname;
		}
	}
}
Util.removeClass = function(e, classname) {
	if(classname) {
		var regexp = new RegExp(classname + " | " + classname + "|" + classname);
		e.className = e.className.replace(regexp, "");
	}
}
Util.absoluteLeft = function(e) {
	if(e.offsetParent) {
		return e.offsetLeft + Util.absoluteLeft(e.offsetParent);
	}
	return e.offsetLeft;
} 
Util.absoluteTop = function(e) {
	if(e.offsetParent) {
		return e.offsetTop + Util.absoluteTop(e.offsetParent);
	}
	return e.offsetTop;
}
Util.docWidth = function() {
	var w;
	if(self.innerHeight) {
		w = self.innerWidth;
	}
	else if(document.documentElement && document.documentElement.clientHeight) {
		w = document.documentElement.clientWidth;
	}
	else if(document.body) {
		w = document.body.clientWidth;
	}
	return w;
}
Util.docHeight = function() {
	var h;
	if(self.innerHeight) {
		h = self.innerHeight;
	}
	else if(document.documentElement && document.documentElement.clientHeight) {
		h = document.documentElement.clientHeight;
	}
	else if(document.body) {
		h = document.body.clientHeight;
	}
	return h;
}
Util.debugWindow = false;
Util.openDebugger = function() {
	Util.debugWindow = window.open("", "debugWindow", "width=600, height=400, scrollbars=yes, resizable=yes");
}
Util.debug = function(output) {
	var element, br;
	if(location.href.indexOf("mkn") != -1) { //|| location.href.indexOf("test") != -1) {
		if(Util.debugWindow) {
			element = Util.debugWindow.document.createTextNode(output);
			br = Util.debugWindow.document.createElement('br');
			Util.debugWindow.document.body.appendChild(element);
			Util.debugWindow.document.body.appendChild(br);
			Util.debugWindow.scrollBy(0,1000);
		}
		else {
			Util.openDebugger();
			if(!Util.debugWindow) {
				alert("Disable popup blocker!");
			}
			else {
				Util.debug(output);
			}
		}
	}
}
Util.Objects["defaultInputValue"] = new function() {
	this.init = function(input) {
		input.defaultValue = input.value;
		input.onfocus = function() {
			if(this.value == this.defaultValue) {
				this.value = "";
			}
		}
		input.onblur = function() {
			if(this.value == "") {
				this.value = this.defaultValue;
			}
		}
	}
}
Util.Objects["list"] = new function() {
	this.init = function(list) {
		var element, i;
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
			if(element.getElementsByTagName("ul").length) {
				Util.addClass(element, "super");
				if(Util.getElementsByClass("selected", element).length){
					Util.addClass(element, "open");
				}
				element.onclick = function(event) {
					this.list.toggle(event, this);
				}
			}
			else if(element.firstChild) {
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
Util.Objects["table"] = new function() {
	this.init = function(table) {
		var rows, row, i, cell, cells, u, input, select, s;
		table.correctCharOrder = function(value) {
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
		table.check = function(element, state) {
			if(typeof(state) != "undefined") {
				element.checkbox.checked = state;
				element.checked = state;
			}
			else if(element.checked) {
				element.checkbox.checked = false;
				element.checked = false;
			}
			else {
				element.checkbox.checked = true;
				element.checked = true;
			}
			if(!element.checked && this.select_all) {
				this.select_all.checked = false;
			}
			if(element.selects) {
				for(i = 0; select = element.selects[i]; i++) {
					Util.selectEnabling(select, element.checked);
				}
			}
		}
		table.selectAll = function() {
			var element, i;
			for(i = 0; element = this.elements[i]; i++) {
				if(this.select_all.checked && element.style.display != "none") {
					this.check(element, this.select_all.checked);
				}
				else {
					this.check(element, this.select_all.checked);
				}
			}
		}
		table.checkIndented = function(element) {
			var table_element, i;
			var start_checking = false;
			this.check(element);
			if(this.type == "indented") {
				this.getIndentInfo();
				for(i = 0; table_element = this.elements[i]; i++) {
					if(table_element == element) {
						start_checking = true;
					}
					else if(start_checking && element.indent < table_element.indent) {
						this.check(table_element, element.checked);
					}
					else if(start_checking && (!table_element.indent || element.indent >= table_element.indent)) {
						return;
					}
				}
				return;
			}
		}
		table.getIndentInfo = function() {
			var i, u, element, cell;
			var regexp = new RegExp(/indent_[?=\d]*/);
			for(i = 0; element = this.elements[i]; i++) {
				if(this.indent) {
					if(element.cells[this.indent].className.match(regexp)) {
						element.indent = parseInt(element.cells[this.indent].className.match(regexp)[0].replace(/indent_/g, ""));
					}
				}
				else {
					for(u = 0; cell = element.cells[u]; u++) {
						if(cell.className.match(regexp)) {
							this.indent = u;
							element.indent = parseInt(cell.className.match(regexp)[0].replace(/indent_/g, ""));
						}
					}
				}
				element.indent = element.indent ? element.indent : 0;
			}
		}
		table.setIndentClass = function(element) {
			var regexp = new RegExp(/indent_[?=\d]*/);
			Util.removeClass(element.cells[this.indent], "indent_[?=\d]*");
			Util.addClass(element.cells[this.indent], element.indent ? "indent_"+element.indent : "");
		}
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
		table.sortBy = function(column_header) {
			var time1 = new Date().getTime();
			var header, direction, ascii, sorting, i, u, o, element;
			if(!this.indexed) {
				for(i = 0; header = this.headers[i]; i++) {
					if(header.type == "sortable"){
						if(header.sort == "numeric") {
							header.ascii = false;
						}
						else if(header.sort == "ascii") {
							header.ascii = true;
						}
						else {
							if(Util.getIJ("sortby", this.elements[0].cells[header.column])) {
								header.ascii = isNaN(Util.getIJ("sortby", this.elements[0].cells[header.column]));
							}
							else {
								header.ascii = isNaN(this.findTextNode(this.elements[0].cells[header.column]));
							}
						}
						for(u = 0, o = 0; element = this.elements[u]; u++, o++) {
							header.sortInfo[o] = new Object();
							header.sortInfo[o].row = element;
							if(Util.getIJ("sortby", element.cells[i])) {
								if(this.findTextNode(element.cells[i]).match(/(\d\d)-(\d\d)-(\d\d\d\d)/g)) {
									matches = this.findTextNode(element.cells[i]);
									Util.removeClass(element.cells[i], "sortby:"+matches);
									Util.addClass(element.cells[i], "sortby:"+ new Date(matches.substring(6,10), matches.substring(3,5), matches.substring(0,2)).getTime());
								}
								else if(this.findTextNode(element.cells[i]).match(/[?=\d].(\d\d\d),(\d\d)/g)) {
									matches = this.findTextNode(element.cells[i]);
									Util.removeClass(element.cells[i], "sortby:"+matches);
									Util.addClass(element.cells[i], "sortby:"+ matches.replace(".", ""));
								}
								header.sortInfo[o].value = Util.getIJ("sortby", element.cells[i]).toLowerCase();
							}
							else {
								header.sortInfo[o].value = this.findTextNode(element.cells[i]).toLowerCase();
							}
							header.sortInfo[o].value = header.ascii ? this.correctCharOrder(header.sortInfo[o].value) : parseFloat(header.sortInfo[o].value);
						}
					}
				}
				this.indexed = true;
			}
			direction = column_header.className ? (column_header.className.match(/sortup/g) ? "sortdown" : "sortup") : "sortup";
			for(i = 0; header = this.headers[i]; i++) {
				header.className = header.className.replace(/ sortup|sortup | sortdown|sortdown |sortup|sortdown/g, "");
			}
			column_header.className += column_header.className ? " "+direction : direction;
			this.headers[column_header.column].sortInfo.sort(this.compare);
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
		table.activateElement = function(element) {
			element.table = this;
			element.selects = element.getElementsByTagName("select").length ? element.getElementsByTagName("select") : false;
			element.inputs = element.getElementsByTagName("input").length ? element.getElementsByTagName("input") : false;
			if(!element.selects) {
				Util.activate(element);
			}
			if(element.inputs) {
				for(u = 0; input = element.inputs[u]; u++) {
					if(input.type == "checkbox") {
						element.checkbox = input;
						input.element = element;
						element.checked = input.checked;
						Util.addClass(element, "clickable");
						if(this.type == "indented") {
							element.onclick = function() {
								this.table.checkIndented(this);
							}
						}
						else if(Util.safari()) {
							element.onclick = function() {
								this.table.check(this);
							}
						}
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
						if(element.selects){
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
						break;
					}
				}
			}
		}
		table.rowCellCount = function() {
			var cell, i;
			var cell_count = 0;
			for(i = 0; cell = this.elements[0].cells[i]; i++) {
				cell_count += cell.getAttribute("colspan") ? parseInt(cell.getAttribute("colspan")) : 1;
			}
			return cell_count;
		}
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
		table.body = table.getElementsByTagName("tbody")[0];
		table.header = false;
		table.elements = new Array();
		table.search = false;
		table.select_all = false;
		table.indent = false;
		this.ondragover = false;
		table.type = Util.getIJ("table", table);
		rows = table.getElementsByTagName("tr");
		for(i = 0; row = rows[i]; i++) {
			if(row.getElementsByTagName("td").length) {
				table.elements[table.elements.length] = row;
			}
			else if(row.getElementsByTagName("th").length) {
				table.header = row;
				table.headers = row.getElementsByTagName("th");
				for(u = 0; cell = table.headers[u]; u++) {
					if(cell.className.match(/search/g)) {
						if(cell.getElementsByTagName("input").length) {
							table.search = cell.getElementsByTagName("input")[0];
						}
					}
					else if(cell.className.match(/selectall/g)) {
						if(cell.getElementsByTagName("input").length) {
							table.select_all = cell.getElementsByTagName("input")[0];
							table.select_all.table = table;
							table.select_all.onclick = function() {
								this.table.selectAll();
							}
						}
					}
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
						Util.unSelectify(cell);
					}
				}
			}
		}
		for(i = 0; element = table.elements[i]; i++) {
			table.activateElement(element);
		}
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
Util.Objects["table"].initSearch = function(table) {
	table.indexContent = function() {
		var cells, element, i, cell, u;
		for(i = 0; element = this.elements[i]; i++) {
			cells = element.getElementsByTagName("td");
			this.search.info[i] = new Array();
			this.search.info[i][0] = "";
			this.search.info[i][1] = true;
			for(u = 0; cell = cells[u]; u++) {
				if(cell.firstChild && cell.firstChild.nodeType == 3) {
					this.search.info[i][0] += cell.firstChild.nodeValue.toLowerCase() + " ";
				}
			}
		}
	}
	table.search.update = function() {
		var info, i, element;
		this.query = (this.value ? (this.value != "" ? this.value.toLowerCase() : false) : false);
		if(this.info.length) {
			if(this.query) {
				for(i = 0; info = this.info[i]; i++) {
					if(info[0].match(this.query)) {
						if(!info[1]) {
						 	this.table.elements[i].style.display = "";
						 	info[1] = true;
						}
					}
					else if(info[1]) {
						this.table.elements[i].style.display = "none";
						info[1] = false;
					}
				}
			}
			else {
				for(i = 0; element = this.table.elements[i]; i++) {
					element.style.display = "";
					this.info[i][1] = true;
				}
			}
		}
		this.table.resetRowColor();
	}
	Util.defaultInputValue(table.search);
	table.search.table = table;
	table.search.query = false;
	table.search.info = new Array();
	table.indexContent();
	table.search.onkeyup = function() {
		this.update();
	}
}
Util.Objects["table"].initIncremental = function(table) {
	var i, element;
	table.addRow = function(element) {
		var i, row, new_element, selects, select, input, inputs
		new_element = element.cloneNode(true);
		selects = new_element.getElementsByTagName("select");
		for(i = 0; select = selects[i]; i++) {
			select.selectedIndex = 0;
		}
		inputs = new_element.getElementsByTagName("input");
		for(i = 0; input = inputs[i]; i++) {
			input.value = "";
		}
		element.parentNode.appendChild(new_element);
		this.resetRowColor();
		this.activateElement(new_element);
		this.activateIncElement(new_element);
		this.updateRowFieldNames();
		for(i = 0; select = selects[i]; i++) {
			if(select.onchange) {
				select.onchange();
			}
		}
	}
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
	table.updateRowFieldNames = function() {
		var i, element, select, selects, input, inputs, u;
		for(i = 0; element = this.elements[i]; i++) {
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
	table.activateIncElement = function(element) {
		element.addElement = Util.getElementsByClass("incremental:add", element)[0];
		element.removeElement = Util.getElementsByClass("incremental:remove", element)[0];
		Util.unSelectify(element.addElement);
		Util.unSelectify(element.removeElement);
		element.table = this;
		element.addElement.element = element;
		element.removeElement.element = element;
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
	for(i = 0; element = table.elements[i]; i++) {
		table.activateIncElement(element);
	}
}
Util.Objects["table"].initArrange = function(table) {
	var element, intersection;
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
	table.indexStructure = function() {
		var i, element, previous;
		this.indexed = new Array();
		element = this.rows[0];
		for(i = 0; element = Util.nextRealSibling(element, "intersection"); i++) {
			this.indexed[i] = new Object();
			this.indexed[i].id = Util.getIJ("id", element);
			if(!element.indent) {
				this.indexed[i].relation = 0;
			}
			else {
				previous = Util.previousRealSibling(element, "intersection");
				while(previous && previous.indent >= element.indent) {
					previous = Util.previousRealSibling(previous, "intersection");
				}
				this.indexed[i].relation = Util.getIJ("id", previous);
			}
		}
	}
	table.pickElement = function(element, event) {
		var node, next;
		event = event ? event : (window.event) ? window.event : false;
		this.pick_offset_x = event.clientX-Util.absoluteLeft(element)-20;
		this.pick_offset_y = event.clientY-Util.absoluteTop(element)-20;
		node = this.drag_content_body.appendChild(element.cloneNode(true));
		node.source_element = element;
		element.dragged = true;
		next = Util.nextRealSibling(element, "intersection");
		while(next && next.indent > element.indent) {
			node = this.drag_content_body.appendChild(next.cloneNode(true));
			node.source_element = next;
			next.dragged = true;
			next = Util.nextRealSibling(next, "intersection");
		}
		this.drag_content.style.left = event.clientX - this.pick_offset_x + 'px';
		this.drag_content.style.top = event.clientY - this.pick_offset_y + 'px';
		document.onmousemove = this.dragElement;
		document.onmouseup = this.dropElement;
		document.table = this;
		this.dragging = true;
	}
	table.dragElement = function(event) {
		event = event ? event : (window.event) ? window.event : false;
		if(event) {
			this.table.drag_content.style.display = "inline";
			this.table.drag_content.style.left = event.clientX - this.table.pick_offset_x + 'px';
			this.table.drag_content.style.top = event.clientY - this.table.pick_offset_y + 'px';
		}
	}
	table.dropElement = function(event) {
		this.onmousemove = null;
		this.onmouseup = null;
		this.table.reDraw();
	}
	table.reDraw = function() {
		var insert_at_level, insert_from_level, insert_before_element, i, u, previous, next;
		document.sort = null;
		this.dragging = false;
		this.drag_content.style.display = "none";
		if(!this.dragged_on || this.dragged_on.dragged || (this.dragged_on.className.match(/intersection/g) && Util.previousRealSibling(this.dragged_on).dragged)) {
			while(this.drag_content_body.firstChild) {
				this.drag_content_body.firstChild.source_element.dragged = false;
				this.drag_content_body.removeChild(this.drag_content_body.firstChild);
			}
			return;
		}
		else if(table.indent === false) {
			insert_from_level = 0;
			insert_at_level = 0;
			insert_before_element = this.dragged_on.className.match(/intersection/g) ? this.dragged_on : Util.nextRealSibling(this.dragged_on);
		}
		else {
			insert_from_level = this.drag_content_body.firstChild.source_element.indent;
			if(this.dragged_on.className.match(/intersection/g)) {
				previous = Util.previousRealSibling(this.dragged_on);
				insert_at_level = previous.indent;
				insert_before_element = this.dragged_on;
				while(Util.nextRealSibling(insert_before_element, "intersection")) {
					next = Util.nextRealSibling(insert_before_element, "intersection")
					if(next.indent > insert_at_level) {
						insert_before_element = Util.nextRealSibling(next);
					}
					else {
						break;
					}
				}
			}
			else {
				insert_at_level = this.dragged_on.indent + 1;
				insert_before_element = Util.nextRealSibling(this.dragged_on);
			}
		}
		while(this.drag_content_body.firstChild) {
			this.drag_content_body.firstChild.source_element.indent = this.drag_content_body.firstChild.source_element.indent - insert_from_level + insert_at_level;
			this.drag_content_body.firstChild.source_element.dragged = false; 
			insert_before_element.parentNode.insertBefore(insert_before_element.parentNode.removeChild(this.drag_content_body.firstChild.source_element), insert_before_element);
			this.drag_content_body.removeChild(this.drag_content_body.firstChild);
		}
		for(i = 1, u = 0; element = this.rows[i]; i++) {
			if(i%2 == 1 && !element.className.match(/intersection/g)) {
				intersection = this.intersection.cloneNode(true);
				element.parentNode.insertBefore(intersection, element);
				this.activateIntersection(intersection);
			}
			else if(i%2 == 0) {
				if(element.className.match(/intersection/g)) {
					element.parentNode.removeChild(element);
					i--;
				}
				else {
					element.className = element.className.replace(/tr\d/g, "tr"+u++%2);
					if(this.indent !== false) {
						this.setIndentClass(element);
					}
				}
			}
		}
		Util.enableButton(this.save_button);
		this.indexStructure();
	}
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
	table.intersection = document.createElement('tr');
	table.intersection.className = "intersection";
	table.intersection_child = document.createElement('td');
	table.intersection_child.setAttribute('colspan', table.rowCellCount());
	table.intersection.appendChild(table.intersection_child);
	for(i = 0; element = table.elements[i]; i++) {
		intersection = table.intersection.cloneNode(true);
		element.parentNode.insertBefore(intersection, element);
		table.activateIntersection(intersection);
		Util.addClass(element, "dragable");
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
	intersection = table.intersection.cloneNode(true);
	table.elements[0].parentNode.appendChild(intersection);
	table.activateIntersection(intersection);
	table.indexStructure();
	table.drag_content = document.createElement('TABLE');
	table.drag_content_body = document.createElement('TBODY');
	table.drag_content.appendChild(table.drag_content_body);
	table.drag_content.className = "draggedElement";
	table.drag_content.style.display = "none";
	document.body.appendChild(table.drag_content);
}
Util.Objects["input"] = new function() {
	this.init = function(input) {
		input.onfocus = function() {
			Util.over(this);
		}
		input.onblur = function() {
			Util.out(this);
		}
	}
}
Util.Objects["button"] = new function() {
	this.init = function(button) {
		Util.activate(button);
	}
}
Util.Objects["form"] = new function() {
	this.init = function(form) {
		var i, button, buttons, input, inputs;
		if(Util.getIJ("form", form)) {
			inputs = form.getElementsByTagName("input");
			for(i = 0; input = inputs[i]; i++) {
				if(input.type == "hidden" && input.name == "page_status") {
					form.status_input = input;
				}
				else if(input.type == "hidden" && input.name == "id") {
					form.id_input = input;
				}
				else if(input.type == "text") {
					input.submit_form = form;
					if(input.onkeyup) {
						input.orgonkeyup = function(event) {
							Util.submitOnEnter(event, this.submit_form.id);
						}
					}
					else {
						input.onkeyup = function(event) {
							Util.submitOnEnter(event, this.submit_form.id);
						}
					}
				}
			}
			buttons = Util.getElementsByClass("status([:a-z_0-9])+", form);
			for(i = 0; button = buttons[i]; i++) {
				button.status_value = Util.getIJ("status", button);
				button.status_input = form.status_input ? form.status_input : false;
				button.id_value = Util.getIJ("id", button);
				button.id_input = form.id_input ? form.id_input : false;
				button.submit_form = form;
				Util.addClass(button, "clickable");
				if(button.status_value == "delete_confirm") {
					button.onclick = function() {
						Util.Ajax.deleteConfirm(this.submit_form);
					}
				}
				else {
					button.onclick = function() {
						if(this.status_input && this.status_value) {
							this.status_input.value = this.status_value;
						}
						if(this.id_input && this.id_value) {
							this.id_input.value = this.id_value;
						}
						Util.Ajax.submitContainer(this.submit_form.id);
					}
				}
			}
		}
		else if(form.nodeName == "FORM") {
			inputs = form.getElementsByTagName("input");
			for(i = 0; input = inputs[i]; i++) {
				if(input.type == "hidden" && input.name == "page_status") {
					form.status_input = input;
				}
				else if(input.type == "hidden" && input.name == "id") {
					form.id_input = input;
				}
			}
			buttons = Util.getElementsByClass("status([:a-z_0-9])+", form);
			for(i = 0; button = buttons[i]; i++) {
				button.status_value = Util.getIJ("status", button);
				button.status_input = form.status_input ? form.status_input : false;
				button.id_value = Util.getIJ("id", button);
				button.id_input = form.id_input ? form.id_input : false;
				button.submit_form = form;
				Util.addClass(button, "clickable");
				button.onclick = function() {
					if(this.status_input && this.status_value) {
						this.status_input.value = this.status_value;
					}
					if(this.id_input && this.id_value) {
						this.id_input.value = this.id_value;
					}
					this.submit_form.submit();
				}
			}
		}
	}
}
Util.Objects["autocomplete"] = new function() {
	this.init = function(element) {
		var div, autocomplete;
		element.enableAutoComplete = function(response) {
			var i, options, option_set, option_array;
			options = response.resultText.trim().split("#");
			this.allOptions = new Array();
			this.updateTargets = new Array();
			this.updateValues = new Array();
			for(i = 0; option_set = options[i]; i++) {
				option_array = option_set.split("->");
				this.allOptions[i] = option_array[0];
				this.updateTargets[i] = new Array();
				this.updateValues[i] = new Array();
				for(u = 1; u+1 < option_array.length; u++) {
					this.updateTargets[i][this.updateTargets[i].length] = option_array[u++].trim();
					this.updateValues[i][this.updateValues[i].length] = option_array[u].trim();
				}
			}
		}
		element.orgonkeyup = element.onkeyup;
		element.onkeyup = function(event) {
			var i, u, option, regexp, option_div, e, target, allOptionsIndex;
			event = event ? event : window.event;
			Util.nonClick(event);
			if(!this.allOptions.length) {
				this.orgonkeyup(event);
				return;
			}
			else if(event.keyCode == 27) {
				this.autocompleteElement.style.display = "none";
				this.selectedIndex = -1;
			}
			else if(event.keyCode == 13) {
				if(typeof(this.selectedIndex) != "undefined" && this.selectedIndex != -1) {
					allOptionsIndex = this.options[this.selectedIndex].allOptionsIndex;
					this.value = this.allOptions[allOptionsIndex];
					if(this.updateTargets[allOptionsIndex].length) {
						for(i = 0; i < this.updateTargets[allOptionsIndex].length; i++) {
							target = document.getElementById(this.updateTargets[allOptionsIndex][i]);
							if(target.nodeName.toLowerCase() == "select") {
								for(u = 0; u < target.options.length; u++) {
									if(target.options[u].value == this.updateValues[allOptionsIndex][i]){
										target.options[u].selected = true;
									}
								}
							}
							else if(target.nodeName.toLowerCase() == "input" && target.type == "text") {
								target.value = this.updateValues[allOptionsIndex][i];
							}
							if(target.onchange){
								target.onchange();
							}
						}
					}
					this.autocompleteElement.style.display = "none";
					this.selectedIndex = -1;
				}
				else {
					this.autocompleteElement.style.display = "none";
					this.orgonkeyup(event);
				}
			}
			else if(event.keyCode == 40 && this.options.length) {
				if(this.selectedIndex != -1) {
					this.options[this.selectedIndex].className = this.options[this.selectedIndex].className.replace(/ selected|selected |selected/g, "");
				}
				if(this.options.length > (this.selectedIndex+1)) {
					this.selectedIndex++;
				}
				else {
					this.selectedIndex = 0;
				}
				this.options[this.selectedIndex].className = "selected";
				Util.nonClick(event);
			}
			else if(event.keyCode == 38 && this.options.length) {
				if(this.selectedIndex != -1) {
					this.options[this.selectedIndex].className = this.options[this.selectedIndex].className.replace(/ selected|selected |selected/g, "");
				}
				if(this.selectedIndex > 0) {
					this.selectedIndex--;
				}
				else {
					this.selectedIndex = this.options.length-1;
				}
				this.options[this.selectedIndex].className = "selected";
				Util.nonClick(event);
			}
			else if(this.value.length > 1) {
				this.autocompleteElement.style.display = "none";
				this.autocompleteElement.innerHTML = "";
				this.options = new Array();
				for(i = 0; i < this.allOptions.length; i++) {
					option = this.allOptions[i];
					if(option && option.toLowerCase().indexOf(this.value.toLowerCase()) == 0) {
						option_div = document.createElement("div");
						option_div.innerHTML = option;
						option_div.allOptionsIndex = i;
						this.selectedIndex = -1;
						this.autocompleteElement.appendChild(option_div);
						this.autocompleteElement.style.display = "block";
						this.options[this.options.length] = option_div;
					}
				}
			}
			else {
				this.autocompleteElement.style.display = "none";
			}
			element.onblur = function(event) {
				event = event ? event : window.event;
				Util.nonClick(event);
				this.autocompleteElement.style.display = "none";
			}
		}
		autocomplete = Util.getIJ("autocomplete", element).split(":");
		if(autocomplete.length) {
			Util.Ajax.send(autocomplete[1], element.enableAutoComplete, element, "page_status="+autocomplete[0]);
			div = document.createElement("div");
			document.body.appendChild(div);
			div.id = "autocompleteOptions";
			div.className = "autocompleteOptions";
			div.style.width = element.offsetWidth-2+"px";
			div.style.top = Util.absoluteTop(element)+18+"px";
			div.style.left = Util.absoluteLeft(element)+"px";
			element.autocompleteElement = div;
		}
	}
}
Util.Ajax = new Object();
Util.Ajax.requests = new Array();
Util.Ajax.send = function(url, notify, object, parameters, async, type) {
	Util.setLoadStatus("Loading");
	var id = this.requests.length;
	this.requests[id] = new Object();
	this.requests[id].url = url;
	this.requests[id].notifier = notify;
	this.requests[id].object = (typeof(object) != "undefined" ? object : window);
	this.requests[id].parameters = (typeof(parameters) != "undefined" ? parameters : "");
	this.requests[id].async = (typeof(async) != "undefined" ? async : true);
	this.requests[id].type = (typeof(type) == "string" ? type : "POST");
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
Util.Ajax.createRequestObject = function() {
	var request_object = false;
	if(typeof(window.ActiveXObject) == "function") {
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
Util.Ajax.responder = function(id, state) {
	var response_object, response;
	response_object = this.requests[id].object;
	response_object.exe = this.requests[id].notifier;
	this.requests[id].object = null;
	this.requests[id].notifier = null;
	if(!state) {
		response_object.exe(false);
	}
	else {
		try {
			this.requests[id].xmlHttp.status;
			if(this.requests[id].xmlHttp.status == 200) {
				this.requests[id].status = this.requests[id].xmlHttp.status;
				this.requests[id].statusText = this.requests[id].xmlHttp.statusText;
				this.requests[id].result = this.requests[id].xmlHttp.responseXML;
				Util.debug("responseText:"+this.requests[id].xmlHttp.responseText.trim());
				Util.debug("###");
				this.requests[id].resultText = this.requests[id].xmlHttp.responseText.trim();
				this.requests[id].xmlHttp = null;
				response = this.requests[id];
				response_object.exe(response);
			}
			else {
				response_object.exe(false);
			}
		}
		catch(e) {
			if(this.requests[id]) {
				response_object.exe(false);
			}
		}
	}
	Util.Ajax.requests[id] = null;
}
Util.Ajax.loader = function(container) {
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
Util.Ajax.loadContainer = function(url, target_id, parameters) {
	var target = document.getElementById(target_id);
	parameters = (typeof(parameters) != "undefined" ? parameters + "&" : "") + "response_column=" + this.getResponseColumn(target);
	this.send(url, this.replaceElement, target, parameters);
}
Util.Ajax.submitContainer = function(container_id) {
	var elements, proporties, element, target, i;
	var form = document.getElementById(container_id);
	var parameters = "";
	if(form) {
		proporties = this.getFormProporties(form);
		elements = this.getAllFormElements(form);
		parameters = "response_column=" + (proporties.classname ? proporties.classname : "");
		if(proporties.action) {
			if(proporties.target) {
				Util.Ajax.loader(proporties.target);
			}
			for(i = 0; element = elements[i]; i++) {
				parameters += "&"+element.name+"="+encodeURIComponent(element.value);
			}
			this.send(proporties.action, this.replaceElement, proporties.target, parameters, true, proporties.method);
			return true;
		}
		Util.debug("No form action!!!")
		return false;
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
Util.Ajax.replaceElementChild = function(response, child) {
	var component;
	if(response) {
		component = Util.Ajax.validateResult(response.resultText);
		if(typeof(component) == "object") {
			this.replaceChild(component, child);
			Util.initElements(component);
			return true;
		}
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
Util.Ajax.replaceElement = function(response) {
	Util.setLoadStatus("Initiating");
	this.parentNode.replaceElementChild = Util.Ajax.replaceElementChild;
	this.parentNode.replaceElementChild(response, this);
}
Util.Ajax.resetContainer = function(target_id) {
	document.getElementById(target_id).innerHTML = "";
	document.getElementById(target_id).className += " targetContainer";
}
Util.Ajax.updateSelect = function(e, url, target) {
	var adjust_target_name = e.id.indexOf("[");
	if(adjust_target_name != -1) {
		target = target + e.id.substring(adjust_target_name);
	}
	target.length = 0;
	this.send(url, Util.Ajax.insertNewSelectValues, document.getElementById(target), 'id=' + e.options[e.selectedIndex].value);
}
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
Util.Ajax.validateResult = function(result){
	var valid, content_element, script_elements, child, i;
	this.validateElement = typeof(this.validateElement) == "object" ? this.validateElement : document.createElement("div");
	if(Util.explorer()) {
		this.validateElement.innerHTML = "&nbsp;"+result;
	}
	else {
		this.validateElement.innerHTML = result;
	}
	content_element = this.validateElement.getElementsByTagName("div").length ? this.validateElement.getElementsByTagName("div")[0] : false;
	script_elements = new Array();
	for(i = 0; child = this.validateElement.childNodes[i]; i++) {
		if(child.nodeName.toLowerCase() == "script") {
			script_elements.unshift(child);
		}
	}
	this.executeScript(script_elements);
	return content_element;
}
Util.Ajax.executeScript = function(script_elements) {
	var script, script_i;
	if(script_elements.length) {
		for(i = script_elements.length-1; i >= 0; i--) {
			if(script_elements[i].firstChild && script_elements[i].firstChild.nodeValue) {
				eval(script_elements[i].firstChild.nodeValue);
			}
			else if(Util.explorer() && script_elements[i].text) {
				eval(script_elements[i].text);
			}
		}
	}
}
Util.Ajax.getAllFormElements = function(container) {
	var input, inputs, select, selects, textarea, textareas, i;
	var elements = new Array();
	inputs = container.getElementsByTagName("input");
	for(i = 0; input = inputs[i]; i++) {
		if(!input.disabled) {
			if(input.type == "text" || input.type == "password" || input.type == "hidden" && input.name != "list:search") {
				elements[elements.length] = {name:input.name, value:input.value};
			}
			else if((input.type == "checkbox" || input.type == "radio") && input.checked && input.name != "list:selectall") {
				elements[elements.length] = {name:input.name, value:input.value};
			}
		}
	}
	selects = container.getElementsByTagName("select");
	for(i = 0; select = selects[i]; i++) {
		if(!select.disabled && select.options.length) {
			elements[elements.length] = {name:select.name, value:select.options[select.selectedIndex].value};
		}
	}
	textareas = container.getElementsByTagName("textarea");
	for(i = 0; textarea = textareas[i]; i++) {
		if(!textarea.disabled) {
			elements[elements.length] = {name:textarea.name, value:textarea.value};
		}
	}
	return elements;
}
Util.Ajax.getFormProporties = function(container) {
	var regexp;
	var proporties = new Object();
	regexp = new RegExp("form:action:[?=\\w/\\#~:.?+=?&%@!\\-]*");
	if(container.className.match(regexp)) {
		proporties.action = container.className.match(regexp)[0].replace(/form:action:/g, "");
	}
	regexp = new RegExp(/form:method:[?=\w]*/);
	if(container.className.match(regexp)) {
		proporties.method = container.className.match(regexp)[0].replace(/form:method:/g, "");
	}
	else {
		proporties.method = "POST";
	}
	regexp = new RegExp(/form:target:[?=\w\_:-\\]*/);
	if(container.className.match(regexp)) {
		proporties.target = container.className.match(regexp)[0].replace(/form:target:/g, "");
		proporties.target = document.getElementById(proporties.target);
	}
	else {
		proporties.target = container;
	}
	proporties.classname = this.getResponseColumn(proporties.target);
	return proporties;
}
Util.Ajax.getResponseColumn = function(target) {
	var regexp, classname;
	regexp = new RegExp(/c[?=\w]*/);
	if(target.className.match(regexp)) {
		classname = target.className.match(regexp)[0].replace(/c/g, "");
	}
	return classname;
}
Util.Ajax.check = function(response) {
	if(response) {
		Util.debug(response.status+","+response.resultText+","+response.result);
	}
	else {
		alert("failed");
	}
}
Util.initElements = function(container) {
	var i, e, elements, ij_value;
	this.setLoadStatus("Initiating");
	if(Util.explorer(6, "<=")) {
		this.IEsucks(container);
	}
	scripts = container.getElementsByTagName("script");
	if(container.id != "page" && scripts.length) {
		if(scripts[0].firstChild && scripts[0].firstChild.nodeValue) {
			script = eval(scripts[0].firstChild.nodeValue);
		}
		else if(Util.explorer() && scripts[0].text) {
			script = eval(scripts[0].text);
		}
		scripts[0].parentNode.innerHTML = script;
	}
	elements = this.getElementsByClass("init([:a-z])+", container);
	if(this.getIJ("init", container)){
		elements[elements.length] = container;
	}
	for(i = 0; e = elements[i]; i++) {
		ij_value = this.getIJ("init", e);
		if(ij_value && typeof(this.Objects[ij_value]) == "object") {
			this.Objects[ij_value].init(e);
		}
	}
	this.focusOnFirstInput(container);
	this.setLoadStatus("Done");
}
