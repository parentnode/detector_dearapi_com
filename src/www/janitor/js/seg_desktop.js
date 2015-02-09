
/*seg_desktop_include.js*/

/*seg_desktop_include.js*/

/*seg_desktop.js*/
if(!u || !Util) {
	var u, Util = u = new function() {};
	u.version = 0.8;
	u.bug = function() {};
	u.nodeId = function() {};
	u.stats = new function() {this.pageView = function(){};this.event = function(){};this.customVar = function(){};}
}
Util.debugURL = function(url) {
	if(u.bug_force) {
		return true;
	}
	return document.domain.match(/.local$/);
}
Util.nodeId = function(node, include_path) {
		if(!include_path) {
			return node.id ? node.nodeName+"#"+node.id : (node.className ? node.nodeName+"."+node.className : (node.name ? node.nodeName + "["+node.name+"]" : node.nodeName));
		}
		else {
			if(node.parentNode && node.parentNode.nodeName != "HTML") {
				return u.nodeId(node.parentNode, include_path) + "->" + u.nodeId(node);
			}
			else {
				return u.nodeId(node);
			}
		}
	return "Unindentifiable node!";
}
Util.bug = function(message, corner, color) {
	if(u.debugURL()) {
		if(!u.bug_console_only) {
			var option, options = new Array([0, "auto", "auto", 0], [0, 0, "auto", "auto"], ["auto", 0, 0, "auto"], ["auto", "auto", 0, 0]);
			if(isNaN(corner)) {
				color = corner;
				corner = 0;
			}
			if(typeof(color) != "string") {
				color = "black";
			}
			option = options[corner];
			if(!document.getElementById("debug_id_"+corner)) {
				var d_target = u.ae(document.body, "div", {"class":"debug_"+corner, "id":"debug_id_"+corner});
				d_target.style.position = u.bug_position ? u.bug_position : "absolute";
				d_target.style.zIndex = 16000;
				d_target.style.top = option[0];
				d_target.style.right = option[1];
				d_target.style.bottom = option[2];
				d_target.style.left = option[3];
				d_target.style.backgroundColor = u.bug_bg ? u.bug_bg : "#ffffff";
				d_target.style.color = "#000000";
				d_target.style.textAlign = "left";
				if(d_target.style.maxWidth) {
					d_target.style.maxWidth = u.bug_max_width ? u.bug_max_width+"px" : "auto";
				}
				d_target.style.padding = "3px";
			}
			if(typeof(message) != "string") {
				message = message.toString();
			}
			var debug_div = document.getElementById("debug_id_"+corner);
			message = message ? message.replace(/\>/g, "&gt;").replace(/\</g, "&lt;").replace(/&lt;br&gt;/g, "<br>") : "Util.bug with no message?";
			u.ae(debug_div, "div", {"style":"color: " + color, "html": message});
		}
		if(typeof(console) == "object") {
			console.log(message);
		}
	}
}
Util.xInObject = function(object) {
	if(u.debugURL()) {
		var x, s = "--- start object ---<br>";
		for(x in object) {
			if(object[x] && typeof(object[x]) == "object" && typeof(object[x].nodeName) == "string") {
				s += x + "=" + object[x]+" -> " + u.nodeId(object[x], 1) + "<br>";
			}
			else if(object[x] && typeof(object[x]) == "function") {
				s += x + "=function<br>";
			}
			else {
				s += x + "=" + object[x]+"<br>";
			}
		}
		s += "--- end object ---"
		u.bug(s);
	}
}
Util.Animation = u.a = new function() {
	this.support3d = function() {
		if(this._support3d === undefined) {
			var node = document.createElement("div");
			try {
				var test = "translate3d(10px, 10px, 10px)";
				node.style[this.variant() + "Transform"] = test;
				if(node.style[this.variant() + "Transform"] == test) {
					this._support3d = true;
				}
				else {
					this._support3d = false;
				}
			}
			catch(exception) {
				this._support3d = false;
			}
		}
		return this._support3d;
	}
	this.variant = function() {
		if(this._variant === undefined) {
			if(document.body.style.webkitTransform != undefined) {
				this._variant = "webkit";
			}
			else if(document.body.style.MozTransform != undefined) {
				this._variant = "Moz";
			}
			else if(document.body.style.oTransform != undefined) {
				this._variant = "o";
			}
			else if(document.body.style.msTransform != undefined) {
				this._variant = "ms";
			}
			else {
				this._variant = "";
			}
		}
		return this._variant;
	}
	this.transition = function(node, transition) {
		try {		
			node.style[this.variant() + "Transition"] = transition;
			if(this.variant() == "Moz") {
				u.e.addEvent(node, "transitionend", this._transitioned);
			}
			else {
				u.e.addEvent(node, this.variant() + "TransitionEnd", this._transitioned);
			}
			var duration = transition.match(/[0-9.]+[ms]+/g);
			if(duration) {
				node.duration = duration[0].match("ms") ? parseFloat(duration[0]) : (parseFloat(duration[0]) * 1000);
			}
			else {
				node.duration = false;
				if(transition.match(/none/i)) {
					node.transitioned = null;
				}
			}
		}
		catch(exception) {
			u.bug("Exception ("+exception+") in u.a.transition(" + node + "), called from: "+arguments.callee.caller);
		}
	}
	this._transitioned = function(event) {
		if(event.target == this && typeof(this.transitioned) == "function") {
			this.transitioned(event);
		}
	}
	this.removeTransform = function(node) {
		node.style[this.variant() + "Transform"] = "none";
	}
	this.translate = function(node, x, y) {
		if(this.support3d()) {
			node.style[this.variant() + "Transform"] = "translate3d("+x+"px, "+y+"px, 0)";
		}
		else {
			node.style[this.variant() + "Transform"] = "translate("+x+"px, "+y+"px)";
		}
		node._x = x;
		node._y = y;
		node.offsetHeight;
	}
	this.rotate = function(node, deg) {
		node.style[this.variant() + "Transform"] = "rotate("+deg+"deg)";
		node._rotation = deg;
		node.offsetHeight;
	}
	this.scale = function(node, scale) {
		node.style[this.variant() + "Transform"] = "scale("+scale+")";
		node._scale = scale;
		node.offsetHeight;
	}
	this.setOpacity = function(node, opacity) {
		node.style.opacity = opacity;
		node._opacity = opacity;
		node.offsetHeight;
	}
	this.setWidth = function(node, width) {
		width = width.toString().match(/\%|auto|px/) ? width : (width + "px");
		node.style.width = width;
		node._width = width;
		node.offsetHeight;
	}
	this.setHeight = function(node, height) {
		height = height.toString().match(/\%|auto|px/) ? height : (height + "px");
		node.style.height = height;
		node._height = height;
		node.offsetHeight;
	}
	this.setBgPos = function(node, x, y) {
		x = x.toString().match(/\%|auto|px|center|top|left|bottom|right/) ? x : (x + "px");
		y = y.toString().match(/\%|auto|px|center|top|left|bottom|right/) ? y : (y + "px");
		node.style.backgroundPosition = x + " " + y;
		node._bg_x = x;
		node._bg_y = y;
		node.offsetHeight;
	}
	this.setBgColor = function(node, color) {
		node.style.backgroundColor = color;
		node._bg_color = color;
		node.offsetHeight;
	}
	this.rotateScale = function(node, deg, scale) {
		node.style[this.variant() + "Transform"] = "rotate("+deg+"deg) scale("+scale+")";
		node._rotation = deg;
		node._scale = scale;
		node.offsetHeight;
	}
	this.scaleRotateTranslate = function(node, scale, deg, x, y) {
		if(this.support3d()) {
			node.style[this.variant() + "Transform"] = "scale("+scale+") rotate("+deg+"deg) translate3d("+x+"px, "+y+"px, 0)";
		}
		else {
			node.style[this.variant() + "Transform"] = "scale("+scale+") rotate("+deg+"deg) translate("+x+"px, "+y+"px)";
		}
		node._rotation = deg;
		node._scale = scale;
		node._x = x;
		node._y = y;
		node.offsetHeight;
	}
}
Util.saveCookie = function(name, value, options) {
	expiry = false;
	path = false;
	if(typeof(options) == "object") {
		var argument;
		for(argument in options) {
			switch(argument) {
				case "expiry"	: expiry	= (typeof(options[argument]) == "string" ? options[argument] : "Mon, 04-Apr-2020 05:00:00 GMT"); break;
				case "path"		: path		= options[argument]; break;
			}
		}
	}
	document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) +";" + (path ? "path="+path+";" : "") + (expiry ? "expires="+expiry+";" : "")
}
Util.getCookie = function(name) {
	var matches;
	return (matches = document.cookie.match(encodeURIComponent(name) + "=([^;]+)")) ? decodeURIComponent(matches[1]) : false;
}
Util.deleteCookie = function(name, options) {
	path = false;
	if(typeof(options) == "object") {
		var argument;
		for(argument in options) {
			switch(argument) {
				case "path"	: path	= options[argument]; break;
			}
		}
	}
	document.cookie = encodeURIComponent(name) + "=;" + (path ? "path="+path+";" : "") + "expires=Thu, 01-Jan-70 00:00:01 GMT";
}
Util.saveNodeCookie = function(node, name, value) {
	var ref = u.cookieReference(node);
	var mem = JSON.parse(u.getCookie("man_mem"));
	if(!mem) {
		mem = {};
	}
	if(!mem[ref]) {
		mem[ref] = {};
	}
	mem[ref][name] = (value !== false && value !== undefined) ? value : "";
	u.saveCookie("man_mem", JSON.stringify(mem), {"path":"/"});
}
Util.getNodeCookie = function(node, name) {
	var ref = u.cookieReference(node);
	var mem = JSON.parse(u.getCookie("man_mem"));
	if(mem && mem[ref]) {
		if(name) {
			return mem[ref][name] ? mem[ref][name] : "";
		}
		else {
			return mem[ref];
		}
	}
	return false;
}
Util.deleteNodeCookie = function(node, name) {
	var ref = u.cookieReference(node);
	var mem = JSON.parse(u.getCookie("man_mem"));
	if(mem && mem[ref]) {
		if(name) {
			delete mem[ref][name];
		}
		else {
			delete mem[ref];
		}
	}
	u.saveCookie("man_mem", JSON.stringify(mem), {"path":"/"});
}
Util.cookieReference = function(node) {
	var ref;
	if(node.id) {
		ref = node.nodeName + "#" + node.id;
	}
	else {
		var id_node = node;
		while(!id_node.id) {
			id_node = id_node.parentNode;
		}
		if(id_node.id) {
			ref = id_node.nodeName + "#"+id_node.id + " " + (node.name ? (node.nodeName + "["+node.name+"]") : (node.className ? (node.nodeName+"."+node.className) : node.nodeName));
		}
	}
	return ref;
}
Util.date = function(format, timestamp, months) {
	var date = timestamp ? new Date(timestamp) : new Date();
	if(isNaN(date.getTime())) {
		if(!timestamp.match(/[A-Z]{3}\+[0-9]{4}/)) {
			if(timestamp.match(/ \+[0-9]{4}/)) {
				date = new Date(timestamp.replace(/ (\+[0-9]{4})/, " GMT$1"));
			}
		}
		if(isNaN(date.getTime())) {
			date = new Date();
		}
	}
	var tokens = /d|j|m|n|F|Y|G|H|i|s/g;
	var chars = new Object();
	chars.j = date.getDate();
	chars.d = (chars.j > 9 ? "" : "0") + chars.j;
	chars.n = date.getMonth()+1;
	chars.m = (chars.n > 9 ? "" : "0") + chars.n;
	chars.F = months ? months[date.getMonth()] : "";
	chars.Y = date.getFullYear();
	chars.G = date.getHours();
	chars.H = (chars.G > 9 ? "" : "0") + chars.G;
	var i = date.getMinutes();
	chars.i = (i > 9 ? "" : "0") + i;
	var s = date.getSeconds();
	chars.s = (s > 9 ? "" : "0") + s;
	return format.replace(tokens, function (_) {
		return _ in chars ? chars[_] : _.slice(1, _.length - 1);
	});
};
Util.querySelector = u.qs = function(query, scope) {
	scope = scope ? scope : document;
	return scope.querySelector(query);
}
Util.querySelectorAll = u.qsa = function(query, scope) {
	scope = scope ? scope : document;
	return scope.querySelectorAll(query);
}
Util.getElement = u.ge = function(identifier, scope) {
	var node, i, regexp;
	if(document.getElementById(identifier)) {
		return document.getElementById(identifier);
	}
	scope = scope ? scope : document;
	regexp = new RegExp("(^|\\s)" + identifier + "(\\s|$|\:)");
	for(i = 0; node = scope.getElementsByTagName("*")[i]; i++) {
		if(regexp.test(node.className)) {
			return node;
		}
	}
	return scope.getElementsByTagName(identifier).length ? scope.getElementsByTagName(identifier)[0] : false;
}
Util.getElements = u.ges = function(identifier, scope) {
	var node, i, regexp;
	var nodes = new Array();
	scope = scope ? scope : document;
	regexp = new RegExp("(^|\\s)" + identifier + "(\\s|$|\:)");
	for(i = 0; node = scope.getElementsByTagName("*")[i]; i++) {
		if(regexp.test(node.className)) {
			nodes.push(node);
		}
	}
	return nodes.length ? nodes : scope.getElementsByTagName(identifier);
}
Util.parentNode = u.pn = function(node, node_type) {
	if(node_type) {
		if(node.parentNode) {
			var parent = node.parentNode;
		}
		while(parent.nodeName.toLowerCase() != node_type.toLowerCase()) {
			if(parent.parentNode) {
				parent = parent.parentNode;
			}
			else {
				return false;
			}
		}
		return parent;
	}
	else {
		return node.parentNode;
	}
}
Util.previousSibling = u.ps = function(node, exclude) {
	node = node.previousSibling;
	while(node && (node.nodeType == 3 || node.nodeType == 8 || exclude && (u.hc(node, exclude) || node.nodeName.toLowerCase().match(exclude)))) {
		node = node.previousSibling;
	}
	return node;
}
Util.nextSibling = u.ns = function(node, exclude) {
	node = node.nextSibling;
	while(node && (node.nodeType == 3 || node.nodeType == 8 || exclude && (u.hc(node, exclude) || node.nodeName.toLowerCase().match(exclude)))) {
		node = node.nextSibling;
	}
	return node;
}
Util.childNodes = u.cn = function(node, exclude) {
	var i, child;
	var children = new Array();
	for(i = 0; child = node.childNodes[i]; i++) {
		if(child && child.nodeType != 3 && child.nodeType != 8 && (!exclude || (!u.hc(child, exclude) && !child.nodeName.toLowerCase().match(exclude) ))) {
			children.push(child);
		}
	}
	return children;
}
Util.appendElement = u.ae = function(parent, node_type, attributes) {
	try {
		var node = (typeof(node_type) == "object") ? node_type : document.createElement(node_type);
		node = parent.appendChild(node);
		if(attributes) {
			var attribute;
			for(attribute in attributes) {
				if(attribute == "html") {
					node.innerHTML = attributes[attribute];
				}
				else {
					node.setAttribute(attribute, attributes[attribute]);
				}
			}
		}
		return node;
	}
	catch(exception) {
		u.bug("Exception ("+exception+") in u.ae, called from: "+arguments.callee.caller.name);
		u.bug("node:" + u.nodeId(parent, 1));
		u.xInObject(attributes);
	}
	return false;
}
Util.insertElement = u.ie = function(parent, node_type, attributes) {
	try {
		var node = (typeof(node_type) == "object") ? node_type : document.createElement(node_type);
		node = parent.insertBefore(node, parent.firstChild);
		if(attributes) {
			var attribute;
			for(attribute in attributes) {
				if(attribute == "html") {
					node.innerHTML = attributes[attribute];
				}
				else {
					node.setAttribute(attribute, attributes[attribute]);
				}
			}
		}
		return node;
	}
	catch(exception) {
		u.bug("Exception ("+exception+") in u.ie, called from: "+arguments.callee.caller);
		u.bug("node:" + u.nodeId(parent, 1));
		u.xInObject(attributes);
	}
	return false;
}
Util.wrapElement = u.we = function(node, node_type, attributes) {
	try {
		var wrapper_node = node.parentNode.insertBefore(document.createElement(node_type), node);
		if(attributes) {
			var attribute;
			for(attribute in attributes) {
				wrapper_node.setAttribute(attribute, attributes[attribute]);
			}
		}	
		wrapper_node.appendChild(node);
		return wrapper_node;
	}
	catch(exception) {
		u.bug("Exception ("+exception+") in u.we, called from: "+arguments.callee.caller);
		u.bug("node:" + u.nodeId(node, 1));
		u.xInObject(attributes);
	}
	return false;
}
Util.wrapContent = u.wc = function(node, node_type, attributes) {
	try {
		var wrapper_node = document.createElement(node_type);
		if(attributes) {
			var attribute;
			for(attribute in attributes) {
				wrapper_node.setAttribute(attribute, attributes[attribute]);
			}
		}	
		while(node.childNodes.length) {
			wrapper_node.appendChild(node.childNodes[0]);
		}
		node.appendChild(wrapper_node);
		return wrapper_node;
	}
	catch(exception) {
		u.bug("Exception ("+exception+") in u.wc, called from: "+arguments.callee.caller);
		u.bug("node:" + u.nodeId(node, 1));
		u.xInObject(attributes);
	}
	return false;
}
Util.textContent = u.text = function(node) {
	return node.textContent;
}
Util.clickableElement = u.ce = function(node, options) {
	var a = (node.nodeName.toLowerCase() == "a" ? node : u.qs("a", node));
	if(a) {
		u.ac(node, "link");
		if(a.getAttribute("href") !== null) {
			node.url = a.href;
			a.removeAttribute("href");
		}
	}
	else {
		u.ac(node, "clickable");
	}
	if(typeof(u.e.click) == "function") {
		u.e.click(node);
		if(typeof(options) == "object") {
			var argument;
			for(argument in options) {
				switch(argument) {
					case "type"			: node._click_type		= options[argument]; break;
					case "method"		: node._click_method	= options[argument]; break;
				}
			}
			if(node._click_type == "link") {
				node.clicked = function(event) {
					if(event.metaKey || event.ctrlKey) {
						window.open(this.url);
					}
					else {
						if(typeof(page.navigate) == "function") {
							page.navigate(this.url);
						}
						else {
							location.href = this.url;
						}
					}
				}
			}
		}
	}
	return node;
}
Util.classVar = u.cv = function(node, var_name) {
	try {
		var regexp = new RegExp(var_name + ":[?=\\w/\\#~:.?+=?&%@!\\-]*");
		if(node.className.match(regexp)) {
			return node.className.match(regexp)[0].replace(var_name + ":", "");
		}
	}
	catch(exception) {
		u.bug("Exception ("+exception+") in u.cv, called from: "+arguments.callee.caller);
	}
	return false;
}
u.getIJ = u.cv;
Util.setClass = u.sc = function(node, classname) {
	try {
		var old_class = node.className;
		node.className = classname;
		node.offsetTop;
		return old_class;
	}
	catch(exception) {
		u.bug("Exception ("+exception+") in u.setClass, called from: "+arguments.callee.caller);
	}
	return false;
}
Util.hasClass = u.hc = function(node, classname) {
	try {
		if(classname) {
			var regexp = new RegExp("(^|\\s)(" + classname + ")(\\s|$)");
			if(regexp.test(node.className)) {
				return true;
			}
		}
	}
	catch(exception) {
		u.bug("Exception ("+exception+") in u.hasClass("+u.nodeId(node)+"), called from: "+arguments.callee.caller);
	}
	return false;
}
Util.addClass = u.ac = function(node, classname, dom_update) {
	try {
		if(classname) {
			var regexp = new RegExp("(^|\\s)" + classname + "(\\s|$)");
			if(!regexp.test(node.className)) {
				node.className += node.className ? " " + classname : classname;
				dom_update === false ? false : node.offsetTop;
			}
			return node.className;
		}
	}
	catch(exception) {
		u.bug("Exception ("+exception+") in u.addClass, called from: "+arguments.callee.caller);
	}
	return false;
}
Util.removeClass = u.rc = function(node, classname, dom_update) {
	try {
		if(classname) {
			var regexp = new RegExp("(\\b)" + classname + "(\\s|$)", "g");
			node.className = node.className.replace(regexp, " ").trim().replace(/[\s]{2}/g, " ");
			dom_update === false ? false : node.offsetTop;
			return node.className;
		}
	}
	catch(exception) {
		u.bug("Exception ("+exception+") in u.removeClass, called from: "+arguments.callee.caller);
	}
	return false;
}
Util.toggleClass = u.tc = function(node, classname, _classname, dom_update) {
	try {
		var regexp = new RegExp("(^|\\s)" + classname + "(\\s|$|\:)");
		if(regexp.test(node.className)) {
			u.rc(node, classname, false);
			if(_classname) {
				u.ac(node, _classname, false);
			}
		}
		else {
			u.ac(node, classname, false);
			if(_classname) {
				u.rc(node, _classname, false);
			}
		}
		dom_update === false ? false : node.offsetTop;
		return node.className;
	}
	catch(exception) {
		u.bug("Exception ("+exception+") in u.toggleClass, called from: "+arguments.callee.caller);
	}
	return false;
}
Util.applyStyle = u.as = function(node, property, value, dom_update) {
	node.style[property] = value;
	dom_update === false ? false : node.offsetTop;
}
Util.applyStyles = u.ass = function(node, styles, dom_update) {
	if(styles) {
		var style;
		for(style in styles) {
			node.style[style] = styles[style];
		}
	}
	dom_update === false ? false : node.offsetTop;
}
Util.getComputedStyle = u.gcs = function(node, property) {
	node.offsetHeight;
	if(document.defaultView && document.defaultView.getComputedStyle) {
		return document.defaultView.getComputedStyle(node, null).getPropertyValue(property);
	}
	return false;
}
Util.hasFixedParent = u.hfp = function(node) {
	while(node.nodeName.toLowerCase() != "body") {
		if(u.gcs(node.parentNode, "position").match("fixed")) {
			return true;
		}
		node = node.parentNode;
	}
	return false;
}
Util.Events = u.e = new function() {
	this.event_pref = typeof(document.ontouchmove) == "undefined" || navigator.maxTouchPoints > 1 ? "mouse" : "touch";
	this.kill = function(event) {
		if(event) {
			event.preventDefault();
			event.stopPropagation();
		}
	}
	this.addEvent = function(node, type, action) {
		try {
			node.addEventListener(type, action, false);
		}
		catch(exception) {
			alert("exception in addEvent:" + node + "," + type + ":" + exception);
		}
	}
	this.removeEvent = function(node, type, action) {
		try {
			node.removeEventListener(type, action, false);
		}
		catch(exception) {
			u.bug("exception in removeEvent:" + node + "," + type + ":" + exception);
		}
	}
	this.addStartEvent = this.addDownEvent = function(node, action) {
		u.e.addEvent(node, (this.event_pref == "touch" ? "touchstart" : "mousedown"), action);
	}
	this.removeStartEvent = this.removeDownEvent = function(node, action) {
		u.e.removeEvent(node, (this.event_pref == "touch" ? "touchstart" : "mousedown"), action);
	}
	this.addMoveEvent = function(node, action) {
		u.e.addEvent(node, (this.event_pref == "touch" ? "touchmove" : "mousemove"), action);
	}
	this.removeMoveEvent = function(node, action) {
		u.e.removeEvent(node, (this.event_pref == "touch" ? "touchmove" : "mousemove"), action);
	}
	this.addEndEvent = this.addUpEvent = function(node, action) {
		u.e.addEvent(node, (this.event_pref == "touch" ? "touchend" : "mouseup"), action);
		if(node.snapback && u.e.event_pref == "mouse") {
			u.e.addEvent(node, "mouseout", this._snapback);
		}
	}
	this.removeEndEvent = this.removeUpEvent = function(node, action) {
		u.e.removeEvent(node, (this.event_pref == "touch" ? "touchend" : "mouseup"), action);
		if(node.snapback && u.e.event_pref == "mouse") {
			u.e.removeEvent(node, "mouseout", this._snapback);
		}
	}
	this.resetClickEvents = function(node) {
		u.t.resetTimer(node.t_held);
		u.t.resetTimer(node.t_clicked);
		this.removeEvent(node, "mouseup", this._dblclicked);
		this.removeEvent(node, "touchend", this._dblclicked);
		this.removeEvent(node, "mousemove", this._cancelClick);
		this.removeEvent(node, "touchmove", this._cancelClick);
		this.removeEvent(node, "mouseout", this._cancelClick);
		this.removeEvent(node, "mousemove", this._move);
		this.removeEvent(node, "touchmove", this._move);
	}
	this.resetEvents = function(node) {
		this.resetClickEvents(node);
		if(typeof(this.resetDragEvents) == "function") {
			this.resetDragEvents(node);
		}
	}
	this.resetNestedEvents = function(node) {
		while(node && node.nodeName != "HTML") {
			this.resetEvents(node);
			node = node.parentNode;
		}
	}
	this._inputStart = function(event) {
		this.event_var = event;
		this.input_timestamp = event.timeStamp;
		this.start_event_x = u.eventX(event);
		this.start_event_y = u.eventY(event);
		this.current_xps = 0;
		this.current_yps = 0;
		this.swiped = false;
		if(this.e_click || this.e_dblclick || this.e_hold) {
			var node = this;
			while(node) {
				if(node.e_drag || node.e_swipe) {
					u.e.addMoveEvent(this, u.e._cancelClick);
					break;
				}
				else {
					node = node.parentNode;
				}
			}
			u.e.addMoveEvent(this, u.e._move);
			if(u.e.event_pref == "touch") {
				u.e.addMoveEvent(this, u.e._cancelClick);
			}
			u.e.addEndEvent(this, u.e._dblclicked);
			if(u.e.event_pref == "mouse") {
				u.e.addEvent(this, "mouseout", u.e._cancelClick);
			}
		}
		if(this.e_hold) {
			this.t_held = u.t.setTimer(this, u.e._held, 750);
		}
		if(this.e_drag || this.e_swipe) {
			u.e.addMoveEvent(this, u.e._pick);
			u.e.addEndEvent(this, u.e._drop);
		}
		if(this.e_scroll) {
			u.e.addMoveEvent(this, u.e._scrollStart);
			u.e.addEndEvent(this, u.e._scrollEnd);
		}
		if(typeof(this.inputStarted) == "function") {
			this.inputStarted(event);
		}
	}
	this._cancelClick = function(event) {
		u.e.resetClickEvents(this);
		if(typeof(this.clickCancelled) == "function") {
			this.clickCancelled(event);
		}
	}
	this._move = function(event) {
		if(typeof(this.moved) == "function") {
			this.moved(event);
		}
	}
	this.hold = function(node) {
		node.e_hold = true;
		u.e.addStartEvent(node, this._inputStart);
	}
	this._held = function(event) {
		u.stats.event(this, "held");
		u.e.resetNestedEvents(this);
		if(typeof(this.held) == "function") {
			this.held(event);
		}
	}
	this.click = this.tap = function(node) {
		node.e_click = true;
		u.e.addStartEvent(node, this._inputStart);
	}
	this._clicked = function(event) {
		u.stats.event(this, "clicked");
		u.e.resetNestedEvents(this);
		if(typeof(this.clicked) == "function") {
			this.clicked(event);
		}
	}
	this.dblclick = this.doubletap = function(node) {
		node.e_dblclick = true;
		u.e.addStartEvent(node, this._inputStart);
	}
	this._dblclicked = function(event) {
		if(u.t.valid(this.t_clicked) && event) {
			u.stats.event(this, "dblclicked");
			u.e.resetNestedEvents(this);
			if(typeof(this.dblclicked) == "function") {
				this.dblclicked(event);
			}
			return;
		}
		else if(!this.e_dblclick) {
			this._clicked = u.e._clicked;
			this._clicked(event);
		}
		else if(!event) {
			this._clicked = u.e._clicked;
			this._clicked(this.event_var);
		}
		else {
			u.e.resetNestedEvents(this);
			this.t_clicked = u.t.setTimer(this, u.e._dblclicked, 400);
		}
	}
}
u.e.addDOMReadyEvent = function(action) {
	if(document.readyState && document.addEventListener) {
		if((document.readyState == "interactive" && !u.browser("ie")) || document.readyState == "complete" || document.readyState == "loaded") {
			action();
		}
		else {
			var id = u.randomString();
			window["DOMReady_" + id] = action;
			eval('window["_DOMReady_' + id + '"] = function() {window["DOMReady_'+id+'"](); u.e.removeEvent(document, "DOMContentLoaded", window["_DOMReady_' + id + '"])}');
			u.e.addEvent(document, "DOMContentLoaded", window["_DOMReady_" + id]);
		}
	}
	else {
		u.e.addOnloadEvent(action);
	}
}
u.e.addOnloadEvent = function(action) {
	if(document.readyState && (document.readyState == "complete" || document.readyState == "loaded")) {
		action();
	}
	else {
		var id = u.randomString();
		window["Onload_" + id] = action;
		eval('window["_Onload_' + id + '"] = function() {window["Onload_'+id+'"](); u.e.removeEvent(window, "load", window["_Onload_' + id + '"])}');
		u.e.addEvent(window, "load", window["_Onload_" + id]);
	}
}
u.e.addResizeEvent = function(node, action) {
}
u.e.removeResizeEvent = function(node, action) {
}
u.e.addScrollEvent = function(node, action) {
}
u.e.removeScrollEvent = function(node, action) {
}
u.e.resetDragEvents = function(node) {
	this.removeEvent(node, "mousemove", this._pick);
	this.removeEvent(node, "touchmove", this._pick);
	this.removeEvent(node, "mousemove", this._drag);
	this.removeEvent(node, "touchmove", this._drag);
	this.removeEvent(node, "mouseup", this._drop);
	this.removeEvent(node, "touchend", this._drop);
	this.removeEvent(node, "mouseout", this._drop_mouse);
	this.removeEvent(node, "mousemove", this._scrollStart);
	this.removeEvent(node, "touchmove", this._scrollStart);
	this.removeEvent(node, "mousemove", this._scrolling);
	this.removeEvent(node, "touchmove", this._scrolling);
	this.removeEvent(node, "mouseup", this._scrollEnd);
	this.removeEvent(node, "touchend", this._scrollEnd);
}
u.e.overlap = function(node, boundaries, strict) {
	if(boundaries.constructor.toString().match("Array")) {
		var boundaries_start_x = Number(boundaries[0]);
		var boundaries_start_y = Number(boundaries[1]);
		var boundaries_end_x = Number(boundaries[2]);
		var boundaries_end_y = Number(boundaries[3]);
	}
	else if(boundaries.constructor.toString().match("HTML")) {
		var boundaries_start_x = u.absX(boundaries) - u.absX(node);
		var boundaries_start_y =  u.absY(boundaries) - u.absY(node);
		var boundaries_end_x = Number(boundaries_start_x + boundaries.offsetWidth);
		var boundaries_end_y = Number(boundaries_start_y + boundaries.offsetHeight);
	}
	var node_start_x = Number(node._x);
	var node_start_y = Number(node._y);
	var node_end_x = Number(node_start_x + node.offsetWidth);
	var node_end_y = Number(node_start_y + node.offsetHeight);
	if(strict) {
		if(node_start_x >= boundaries_start_x && node_start_y >= boundaries_start_y && node_end_x <= boundaries_end_x && node_end_y <= boundaries_end_y) {
			return true;
		}
		else {
			return false;
		}
	} 
	else if(node_end_x < boundaries_start_x || node_start_x > boundaries_end_x || node_end_y < boundaries_start_y || node_start_y > boundaries_end_y) {
		return false;
	}
	return true;
}
u.e.drag = function(node, boundaries, settings) {
	node.e_drag = true;
	if(node.childNodes.length < 2 && node.innerHTML.trim() == "") {
		node.innerHTML = "&nbsp;";
	}
	node.drag_strict = true;
	node.drag_elastica = 0;
	node.drag_dropout = true;
	node.show_bounds = false;
	node.callback_picked = "picked";
	node.callback_moved = "moved";
	node.callback_dropped = "dropped";
	if(typeof(settings) == "object") {
		var argument;
		for(argument in settings) {
			switch(argument) {
				case "strict"			: node.drag_strict			= settings[argument]; break;
				case "elastica"			: node.drag_elastica		= Number(settings[argument]); break;
				case "dropout"			: node.drag_dropout			= settings[argument]; break;
				case "show_bounds"		: node.show_bounds			= settings[argument]; break; 
				case "vertical_lock"	: node.vertical_lock		= settings[argument]; break;
				case "horizontal_lock"	: node.horizontal_lock		= settings[argument]; break;
				case "callback_picked"	: node.callback_picked		= settings[argument]; break;
				case "callback_moved"	: node.callback_moved		= settings[argument]; break;
				case "callback_dropped"	: node.callback_dropped		= settings[argument]; break;
			}
		}
	}
	if((boundaries.constructor && boundaries.constructor.toString().match("Array")) || (boundaries.scopeName && boundaries.scopeName != "HTML")) {
		node.start_drag_x = Number(boundaries[0]);
		node.start_drag_y = Number(boundaries[1]);
		node.end_drag_x = Number(boundaries[2]);
		node.end_drag_y = Number(boundaries[3]);
	}
	else if((boundaries.constructor && boundaries.constructor.toString().match("HTML")) || (boundaries.scopeName && boundaries.scopeName == "HTML")) {
		node.start_drag_x = u.absX(boundaries) - u.absX(node);
		node.start_drag_y = u.absY(boundaries) - u.absY(node);
		node.end_drag_x = node.start_drag_x + boundaries.offsetWidth;
		node.end_drag_y = node.start_drag_y + boundaries.offsetHeight;
	}
	if(node.show_bounds) {
		var debug_bounds = u.ae(document.body, "div", {"class":"debug_bounds"})
		debug_bounds.style.position = "absolute";
		debug_bounds.style.background = "red"
		debug_bounds.style.left = (u.absX(node) + node.start_drag_x - 1) + "px";
		debug_bounds.style.top = (u.absY(node) + node.start_drag_y - 1) + "px";
		debug_bounds.style.width = (node.end_drag_x - node.start_drag_x) + "px";
		debug_bounds.style.height = (node.end_drag_y - node.start_drag_y) + "px";
		debug_bounds.style.border = "1px solid white";
		debug_bounds.style.zIndex = 9999;
		debug_bounds.style.opacity = .5;
		if(document.readyState && document.readyState == "interactive") {
			debug_bounds.innerHTML = "WARNING - injected on DOMLoaded"; 
		}
		u.bug("node: "+u.nodeId(node)+" in (" + u.absX(node) + "," + u.absY(node) + "), (" + (u.absX(node)+node.offsetWidth) + "," + (u.absY(node)+node.offsetHeight) +")");
		u.bug("boundaries: (" + node.start_drag_x + "," + node.start_drag_y + "), (" + node.end_drag_x + ", " + node.end_drag_y + ")");
	}
	node._x = node._x ? node._x : 0;
	node._y = node._y ? node._y : 0;
	node.locked = ((node.end_drag_x - node.start_drag_x == node.offsetWidth) && (node.end_drag_y - node.start_drag_y == node.offsetHeight));
	node.only_vertical = (node.vertical_lock || (!node.locked && node.end_drag_x - node.start_drag_x == node.offsetWidth));
	node.only_horizontal = (node.horizontal_lock || (!node.locked && node.end_drag_y - node.start_drag_y == node.offsetHeight));
	u.e.addStartEvent(node, this._inputStart);
}
u.e._pick = function(event) {
	var init_speed_x = Math.abs(this.start_event_x - u.eventX(event));
	var init_speed_y = Math.abs(this.start_event_y - u.eventY(event));
	if((init_speed_x > init_speed_y && this.only_horizontal) || 
	   (init_speed_x < init_speed_y && this.only_vertical) ||
	   (!this.only_vertical && !this.only_horizontal)) {
		u.e.resetNestedEvents(this);
	    u.e.kill(event);
		this.move_timestamp = event.timeStamp;
		this.move_last_x = this._x;
		this.move_last_y = this._y;
		if(u.hasFixedParent(this)) {
			this.start_input_x = u.eventX(event) - this._x - u.scrollX(); 
			this.start_input_y = u.eventY(event) - this._y - u.scrollY();
		}
		else {
			this.start_input_x = u.eventX(event) - this._x; 
			this.start_input_y = u.eventY(event) - this._y;
		}
		this.current_xps = 0;
		this.current_yps = 0;
		u.a.transition(this, "none");
		u.e.addMoveEvent(this, u.e._drag);
		u.e.addEndEvent(this, u.e._drop);
		if(typeof(this[this.callback_picked]) == "function") {
			this[this.callback_picked](event);
		}
	}
	if(this.drag_dropout && u.e.event_pref == "mouse") {
		u.e.addEvent(this, "mouseout", u.e._drop_mouse);
	}
}
u.e._drag = function(event) {
	if(u.hasFixedParent(this)) {
		this.current_x = u.eventX(event) - this.start_input_x - u.scrollX();
		this.current_y = u.eventY(event) - this.start_input_y - u.scrollY();
	}
	else {
		this.current_x = u.eventX(event) - this.start_input_x;
		this.current_y = u.eventY(event) - this.start_input_y;
	}
	this.current_xps = Math.round(((this.current_x - this.move_last_x) / (event.timeStamp - this.move_timestamp)) * 1000);
	this.current_yps = Math.round(((this.current_y - this.move_last_y) / (event.timeStamp - this.move_timestamp)) * 1000);
	this.move_timestamp = event.timeStamp;
	this.move_last_x = this.current_x;
	this.move_last_y = this.current_y;
	if(!this.locked && this.only_vertical) {
		this._y = this.current_y;
	}
	else if(!this.locked && this.only_horizontal) {
		this._x = this.current_x;
	}
	else if(!this.locked) {
		this._x = this.current_x;
		this._y = this.current_y;
	}
	if(this.e_swipe) {
		if(this.current_xps && (Math.abs(this.current_xps) > Math.abs(this.current_yps) || this.only_horizontal)) {
			if(this.current_xps < 0) {
				this.swiped = "left";
			}
			else {
				this.swiped = "right";
			}
		}
		else if(this.current_yps && (Math.abs(this.current_xps) < Math.abs(this.current_yps) || this.only_vertical)) {
			if(this.current_yps < 0) {
				this.swiped = "up";
			}
			else {
				this.swiped = "down";
			}
		}
	}
	if(!this.locked) {
		if(u.e.overlap(this, [this.start_drag_x, this.start_drag_y, this.end_drag_x, this.end_drag_y], true)) {
			u.a.translate(this, this._x, this._y);
		}
		else if(this.drag_elastica) {
			this.swiped = false;
			this.current_xps = 0;
			this.current_yps = 0;
			var offset = false;
			if(!this.only_vertical && this._x < this.start_drag_x) {
				offset = this._x < this.start_drag_x - this.drag_elastica ? - this.drag_elastica : this._x - this.start_drag_x;
				this._x = this.start_drag_x;
				this.current_x = this._x + offset + (Math.round(Math.pow(offset, 2)/this.drag_elastica));
			}
			else if(!this.only_vertical && this._x + this.offsetWidth > this.end_drag_x) {
				offset = this._x + this.offsetWidth > this.end_drag_x + this.drag_elastica ? this.drag_elastica : this._x + this.offsetWidth - this.end_drag_x;
				this._x = this.end_drag_x - this.offsetWidth;
				this.current_x = this._x + offset - (Math.round(Math.pow(offset, 2)/this.drag_elastica));
			}
			else {
				this.current_x = this._x;
			}
			if(!this.only_horizontal && this._y < this.start_drag_y) {
				offset = this._y < this.start_drag_y - this.drag_elastica ? - this.drag_elastica : this._y - this.start_drag_y;
				this._y = this.start_drag_y;
				this.current_y = this._y + offset + (Math.round(Math.pow(offset, 2)/this.drag_elastica));
			}
			else if(!this.horizontal && this._y + this.offsetHeight > this.end_drag_y) {
				offset = (this._y + this.offsetHeight > this.end_drag_y + this.drag_elastica) ? this.drag_elastica : (this._y + this.offsetHeight - this.end_drag_y);
				this._y = this.end_drag_y - this.offsetHeight;
				this.current_y = this._y + offset - (Math.round(Math.pow(offset, 2)/this.drag_elastica));
			}
			else {
				this.current_y = this._y;
			}
			if(offset) {
				u.a.translate(this, this.current_x, this.current_y);
			}
		}
		else {
			this.swiped = false;
			this.current_xps = 0;
			this.current_yps = 0;
			if(this._x < this.start_drag_x) {
				this._x = this.start_drag_x;
			}
			else if(this._x + this.offsetWidth > this.end_drag_x) {
				this._x = this.end_drag_x - this.offsetWidth;
			}
			if(this._y < this.start_drag_y) {
				this._y = this.start_drag_y;
			}
			else if(this._y + this.offsetHeight > this.end_drag_y) { 
				this._y = this.end_drag_y - this.offsetHeight;
			}
			u.a.translate(this, this._x, this._y);
		}
	}
	if(typeof(this[this.callback_moved]) == "function") {
		this[this.callback_moved](event);
	}
}
u.e._drop = function(event) {
	u.e.resetEvents(this);
	if(this.e_swipe && this.swiped) {
		if(this.swiped == "left" && typeof(this.swipedLeft) == "function") {
			this.swipedLeft(event);
		}
		else if(this.swiped == "right" && typeof(this.swipedRight) == "function") {
			this.swipedRight(event);
		}
		else if(this.swiped == "down" && typeof(this.swipedDown) == "function") {
			this.swipedDown(event);
		}
		else if(this.swiped == "up" && typeof(this.swipedUp) == "function") {
			this.swipedUp(event);
		}
	}
	else if(!this.drag_strict && !this.locked) {
		this.current_x = Math.round(this._x + (this.current_xps/2));
		this.current_y = Math.round(this._y + (this.current_yps/2));
		if(this.only_vertical || this.current_x < this.start_drag_x) {
			this.current_x = this.start_drag_x;
		}
		else if(this.current_x + this.offsetWidth > this.end_drag_x) {
			this.current_x = this.end_drag_x - this.offsetWidth;
		}
		if(this.only_horizontal || this.current_y < this.start_drag_y) {
			this.current_y = this.start_drag_y;
		}
		else if(this.current_y + this.offsetHeight > this.end_drag_y) {
			this.current_y = this.end_drag_y - this.offsetHeight;
		}
		this.transitioned = function() {
			this.transitioned = null;
			u.a.transition(this, "none");
			if(typeof(this.projected) == "function") {
				this.projected(event);
			}
		}
		if(this.current_xps || this.current_yps) {
			u.a.transition(this, "all 1s cubic-bezier(0,0,0.25,1)");
		}
		else {
			u.a.transition(this, "all 0.2s cubic-bezier(0,0,0.25,1)");
		}
		u.a.translate(this, this.current_x, this.current_y);
	}
	if(typeof(this[this.callback_dropped]) == "function") {
		this[this.callback_dropped](event);
	}
}
u.e._drop_mouse = function(event) {
	if(event.target == this) {
		this._drop = u.e._drop;
		this._drop(event);
	}
}
u.e.swipe = function(node, boundaries, settings) {
	node.e_swipe = true;
	u.e.drag(node, boundaries, settings);
}
Util.flashDetection = function(version) {
	var flash_version = false;
	var flash = false;
	if(navigator.plugins && navigator.plugins["Shockwave Flash"] && navigator.plugins["Shockwave Flash"].description && navigator.mimeTypes && navigator.mimeTypes["application/x-shockwave-flash"]) {
		flash = true;
		var Pversion = navigator.plugins["Shockwave Flash"].description.match(/\b([\d]+)\b/);
		if(Pversion.length > 1 && !isNaN(Pversion[1])) {
			flash_version = Pversion[1];
		}
	}
	else if(window.ActiveXObject) {
		try {
			var AXflash, AXversion;
			AXflash = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
			if(AXflash) {
				flash = true;
				AXversion = AXflash.GetVariable("$version").match(/\b([\d]+)\b/);
				if(AXversion.length > 1 && !isNaN(AXversion[1])) {
					flash_version = AXversion[1];
				}
			}
		}
		catch(exception) {}
	}
	if(flash_version || (flash && !version)) {
		if(!version) {
			return true;
		}
		else {
			if(!isNaN(version)) {
				return flash_version == version;
			}
			else {
				return eval(flash_version + version);
			}
		}
	}
	else {
		return false;
	}
}
Util.flash = function(node, url, settings) {
	var width = "100%";
	var height = "100%";
	var background = "transparent";
	var id = "flash_" + new Date().getHours() + "_" + new Date().getMinutes() + "_" + new Date().getMilliseconds();
	var allowScriptAccess = "always";
	var menu = "false";
	var scale = "showall";
	var wmode = "transparent";
	if(typeof(settings) == "object") {
		var argument;
		for(argument in settings) {
			switch(argument) {
				case "id"					: id				= settings[argument]; break;
				case "width"				: width				= Number(settings[argument]); break;
				case "height"				: height			= Number(settings[argument]); break;
				case "background"			: background		= settings[argument]; break;
				case "allowScriptAccess"	: allowScriptAccess = settings[argument]; break;
				case "menu"					: menu				= settings[argument]; break;
				case "scale"				: scale				= settings[argument]; break;
				case "wmode"				: wmode				= settings[argument]; break;
			}
		}
	}
	html = '<object';
	html += ' id="'+id+'"';
	html += ' width="'+width+'"';
	html += ' height="'+height+'"';
	if(u.browser("explorer")) {
		html += ' classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"';
	}
	else {
		html += ' type="application/x-shockwave-flash"';
		html += ' data="'+url+'"';
	}
	html += '>';
	html += '<param name="allowScriptAccess" value="'+allowScriptAccess+'" />';
	html += '<param name="movie" value="'+url+'" />';
	html += '<param name="quality" value="high" />';
	html += '<param name="bgcolor" value="'+background+'" />';
	html += '<param name="play" value="true" />';
	html += '<param name="wmode" value="'+wmode+'" />';
	html += '<param name="menu" value="'+menu+'" />';
	html += '<param name="scale" value="'+scale+'" />';
	html += '</object>';
	var temp_node = document.createElement("div");
	temp_node.innerHTML = html;
	node.insertBefore(temp_node.firstChild, node.firstChild);
	var flash_object = u.qs("#"+id, node);
	return flash_object;
}
Util.Form = u.f = new function() {
	this.customInit = {};
	this.customValidate = {};
	this.customSend = {};
	this.init = function(form, settings) {
		var i, j, field, action, input;
		form.form_send = "params";
		form.ignore_inputs = "ignoreinput";
		if(typeof(settings) == "object") {
			var argument;
			for(argument in settings) {
				switch(argument) {
					case "ignore_inputs"	: form.ignore_inputs	= settings[argument]; break;
					case "form_send"		: form.form_send		= settings[argument]; break;
				}
			}
		}
		form.onsubmit = function(event) {return false;}
		form.setAttribute("novalidate", "novalidate");
		form._submit = this._submit;
		form.fields = {};
		form.tab_order = [];
		form.actions = {};
		var fields = u.qsa(".field", form);
		for(i = 0; field = fields[i]; i++) {
			var abbr = u.qs("abbr", field);
			if(abbr) {
				abbr.parentNode.removeChild(abbr);
			}
			var error_message = field.getAttribute("data-error");
			if(error_message) {
				u.ae(field, "div", {"class":"error", "html":error_message})
			}
			field._indicator = u.ae(field, "div", {"class":"indicator"});
			field._help = u.qs(".help", field);
			field._hint = u.qs(".hint", field);
			field._error = u.qs(".error", field);
			var not_initialized = true;
			var custom_init;
			for(custom_init in this.customInit) {
				if(field.className.match(custom_init)) {
					this.customInit[custom_init](field);
					not_initialized = false;
				}
			}
			if(not_initialized) {
				if(u.hc(field, "string|email|tel|number|integer|password")) {
					field._input = u.qs("input", field);
					field._input.field = field;
					field._input._label = u.qs("label[for="+field._input.id+"]", field);
					this.formIndex(form, field._input);
				}
				else if(u.hc(field, "text")) {
					field._input = u.qs("textarea", field);
					field._input.field = field;
					field._input._label = u.qs("label[for="+field._input.id+"]", field);
					this.formIndex(form, field._input);
					if(u.hc(field, "autoexpand")) {
						this.autoExpand(field._input)
					}
				}
				else if(u.hc(field, "select")) {
					field._input = u.qs("select", field);
					field._input.field = field;
					field._input._label = u.qs("label[for="+field._input.id+"]", field);
					this.formIndex(form, field._input);
				}
				else if(u.hc(field, "checkbox|boolean")) {
					field._input = u.qs("input[type=checkbox]", field);
					field._input.field = field;
					field._input._label = u.qs("label[for="+field._input.id+"]", field);
					this.formIndex(form, field._input);
				}
				else if(u.hc(field, "radio|radio_buttons")) {
					field._input = u.qsa("input", field);
					for(j = 0; input = field._input[j]; j++) {
						input.field = field;
						input._label = u.qs("label[for="+input.id+"]", field);
						this.formIndex(form, input);
					}
				}
				else if(u.hc(field, "date|datetime")) {
					field._input = u.qsa("select,input", field);
					for(j = 0; input = field._input[j]; j++) {
						input.field = field;
						input._label = u.qs("label[for="+input.id+"]", field);
						this.formIndex(form, input);
					}
				}
				else if(u.hc(field, "tags")) {
					field._input = u.qs("input", field);
					field._input.field = field;
					field._input._label = u.qs("label\[for\="+field._input.id+"\]", field);
					this.formIndex(form, field._input);
				}
				else if(u.hc(field, "prices")) {
					field._input = u.qs("input", field);
					field._input.field = field;
					field._input._label = u.qs("label[for="+field._input.id+"]", field);
					this.formIndex(form, field._input);
				}
				else if(u.hc(field, "files")) {
					field._input = u.qs("input", field);
					field._input.field = field;
					field._input._label = u.qs("label[for="+field._input.id+"]", field);
					this.formIndex(form, field._input);
				}
				else if(u.hc(field, "location")) {
					field._input = u.qsa("input", field);
					for(j = 0; input = field._input[j]; j++) {
						input.field = field;
						input._label = u.qs("label[for="+input.id+"]", field);
						this.formIndex(form, input);
					}
					if(navigator.geolocation) {
						this.geoLocation(field);
					}
				}
			}
		}
		var hidden_fields = u.qsa("input[type=hidden]", form);
		for(i = 0; hidden_field = hidden_fields[i]; i++) {
			if(!form.fields[hidden_field.name]) {
				form.fields[hidden_field.name] = hidden_field;
				hidden_field.val = this._value;
			}
		}
		var actions = u.qsa(".actions li, .actions", form);
		for(i = 0; action = actions[i]; i++) {
			action._input = u.qs("input,a", action);
			if(action._input.type && action._input.type == "submit") {
				action._input.onclick = function(event) {
					u.e.kill(event ? event : window.event);
				}
			}
			u.ce(action._input);
			action._input.clicked = function(event) {
				u.e.kill(event);
				if(!u.hc(this, "disabled")) {
					if(this.type && this.type.match(/submit/i)) {
						this.form._submit_button = this;
						this.form._submit_input = false;
						this.form._submit(event, this);
					}
				}
			}
			this.buttonOnEnter(action._input);
			this.activateButton(action._input);
			var action_name = action._input.name ? action._input.name : action.className;
				form.actions[action_name] = action._input;
			if(typeof(u.k) == "object" && u.hc(action._input, "key:[a-z0-9]+")) {
				u.k.addKey(u.cv(action._input, "key"), action._input);
			}
		}
		if(!actions.length) {
			var p_ul = u.pn(form, "ul");
			if(u.hc(p_ul, "actions")) {
				u.bug("valid pure button form found")
				var input = u.qs("input,a", form);
				if(input.type && input.type == "submit") {
					input.onclick = function(event) {
						u.e.kill(event ? event : window.event);
					}
				}
				u.ce(input);
				input.clicked = function(event) {
					u.e.kill(event);
					if(!u.hc(this, "disabled")) {
						if(this.type && this.type.match(/submit/i)) {
							this.form._submit_button = this;
							this.form._submit_input = false;
							this.form._submit(event, this);
						}
					}
				}
				this.buttonOnEnter(input);
				this.activateButton(input);
				if(input.name) {
					form.actions[input.name] = input;
				}
				if(typeof(u.k) == "object" && u.hc(input, "key:[a-z0-9]+")) {
					u.k.addKey(u.cv(input, "key"), input);
				}
			}
		}
	}
	this._value = function(value) {
		if(value !== undefined) {
			this.value = value;
			u.f.validate(this);
		}
		return this.value;
	}
	this._value_radio = function(value) {
		if(value) {
			for(i = 0; option = this.form[this.name][i]; i++) {
				if(option.value == value) {
					option.checked = true;
					u.f.validate(this);
				}
			}
		}
		else {
			var i, option;
			for(i = 0; option = this.form[this.name][i]; i++) {
				if(option.checked) {
					return option.value;
				}
			}
		}
		return false;
	}
	this._value_checkbox = function(value) {
		if(value) {
			this.checked = true
			u.f.validate(this);
		}
		else {
			if(this.checked) {
				return this.value;
			}
		}
		return false;
	}
	this._value_select = function(value) {
		if(value !== undefined) {
			var i, option;
			for(i = 0; option = this.options[i]; i++) {
				if(option.value == value) {
					this.selectedIndex = i;
					u.f.validate(this);
					return i;
				}
			}
			return false;
		}
		else {
			return this.options[this.selectedIndex].value;
		}
	}
	this.inputOnEnter = function(node) {
		node.keyPressed = function(event) {
			if(this.nodeName.match(/input/i) && (event.keyCode == 40 || event.keyCode == 38)) {
				this._submit_disabled = true;
			}
			else if(this.nodeName.match(/input/i) && this._submit_disabled && (
				event.keyCode == 46 || 
				(event.keyCode == 39 && u.browser("firefox")) || 
				(event.keyCode == 37 && u.browser("firefox")) || 
				event.keyCode == 27 || 
				event.keyCode == 13 || 
				event.keyCode == 9 ||
				event.keyCode == 8
			)) {
				this._submit_disabled = false;
			}
			else if(event.keyCode == 13 && !this._submit_disabled) {
				u.e.kill(event);
				this.blur();
				this.form.submitInput = this;
				this.form.submitButton = false;
				this.form._submit(event, this);
			}
		}
		u.e.addEvent(node, "keydown", node.keyPressed);
	}
	this.buttonOnEnter = function(node) {
		node.keyPressed = function(event) {
			if(event.keyCode == 13 && !u.hc(this, "disabled")) {
				u.e.kill(event);
				this.form.submit_input = false;
				this.form.submit_button = this;
				this.form._submit(event);
			}
		}
		u.e.addEvent(node, "keydown", node.keyPressed);
	}
	this.formIndex = function(form, iN) {
		iN.tab_index = form.tab_order.length;
		form.tab_order[iN.tab_index] = iN;
		if(iN.field && iN.name) {
			form.fields[iN.name] = iN;
			if(iN.nodeName.match(/input/i) && iN.type && iN.type.match(/text|email|tel|number|password|datetime|date/)) {
				iN.val = this._value;
				u.e.addEvent(iN, "keyup", this._updated);
				u.e.addEvent(iN, "change", this._changed);
				this.inputOnEnter(iN);
			}
			else if(iN.nodeName.match(/textarea/i)) {
				iN.val = this._value;
				u.e.addEvent(iN, "keyup", this._updated);
				u.e.addEvent(iN, "change", this._changed);
			}
			else if(iN.nodeName.match(/select/i)) {
				iN.val = this._value_select;
				u.e.addEvent(iN, "change", this._updated);
				u.e.addEvent(iN, "keyup", this._updated);
				u.e.addEvent(iN, "change", this._changed);
			}
			else if(iN.type && iN.type.match(/checkbox/)) {
				iN.val = this._value_checkbox;
				if(u.browser("explorer", "<=8")) {
					iN.pre_state = iN.checked;
					iN._changed = u.f._changed;
					iN._updated = u.f._updated;
					iN._clicked = function(event) {
						if(this.checked != this.pre_state) {
							this._changed(window.event);
							this._updated(window.event);
						}
						this.pre_state = this.checked;
					}
					u.e.addEvent(iN, "click", iN._clicked);
				}
				else {
					u.e.addEvent(iN, "change", this._updated);
					u.e.addEvent(iN, "change", this._changed);
				}
				this.inputOnEnter(iN);
			}
			else if(iN.type && iN.type.match(/radio/)) {
				iN.val = this._value_radio;
				if(u.browser("explorer", "<=8")) {
					iN.pre_state = iN.checked;
					iN._changed = u.f._changed;
					iN._updated = u.f._updated;
					iN._clicked = function(event) {
						var i, input;
						if(this.checked != this.pre_state) {
							this._changed(window.event);
							this._updated(window.event);
						}
						for(i = 0; input = this.field._input[i]; i++) {
							input.pre_state = input.checked;
						}
					}
					u.e.addEvent(iN, "click", iN._clicked);
				}
				else {
					u.e.addEvent(iN, "change", this._updated);
					u.e.addEvent(iN, "change", this._changed);
				}
				this.inputOnEnter(iN);
			}
			else if(iN.type && iN.type.match(/file/)) {
				iN.val = function(value) {
					if(value !== undefined) {
						alert('adding values manually to input type="file" is not supported')
					}
					else {
						var i, file, files = [];
						for(i = 0; file = this.files[i]; i++) {
							files.push(file);
						}
						return files.join(",");
					}
				}
				u.e.addEvent(iN, "keyup", this._updated);
				u.e.addEvent(iN, "change", this._changed);
			}
			this.activateField(iN);
			this.validate(iN);
		}
	}
	this._changed = function(event) {
		this.used = true;
		if(typeof(this.changed) == "function") {
			this.changed(this);
		}
		if(typeof(this.form.changed) == "function") {
			this.form.changed(this);
		}
	}
	this._updated = function(event) {
		if(event.keyCode != 9 && event.keyCode != 13 && event.keyCode != 16 && event.keyCode != 17 && event.keyCode != 18) {
			if(this.used || u.hc(this.field, "error")) {
				u.f.validate(this);
			}
			if(typeof(this.updated) == "function") {
				this.updated(this);
			}
			if(typeof(this.form.updated) == "function") {
				this.form.updated(this);
			}
		}
	}
	this._validate = function() {
		u.f.validate(this);
	}
	this._submit = function(event, iN) {
		for(name in this.fields) {
			if(this.fields[name].field) {
				this.fields[name].used = true;
				u.f.validate(this.fields[name]);
			}
		}
		if(u.qs(".field.error", this)) {
			if(typeof(this.validationFailed) == "function") {
				this.validationFailed();
			}
		}
		else {
			if(typeof(this.submitted) == "function") {
				this.submitted(iN);
			}
			else {
				this.submit();
			}
		}
	}
	this._focus = function(event) {
		this.field.focused = true;
		u.ac(this.field, "focus");
		u.ac(this, "focus");
		u.as(this.field, "zIndex", 99);
		if(this.field._help) {
			var f_h =  this.field.offsetHeight;
			var f_p_t = parseInt(u.gcs(this.field, "padding-top"));
			var f_p_b = parseInt(u.gcs(this.field, "padding-bottom"));
			var f_h_h = this.field._help.offsetHeight;
			u.as(this.field._help, "top", (((f_h - (f_p_t + f_p_b)) / 2) + 2) - (f_h_h / 2) + "px");
		}
		if(typeof(this.focused) == "function") {
			this.focused();
		}
		if(typeof(this.form.focused) == "function") {
			this.form.focused(this);
		}
	}
	this._blur = function(event) {
		this.field.focused = false;
		u.rc(this.field, "focus");
		u.rc(this, "focus");
		u.as(this.field, "zIndex", 90);
		if(this.field._help) {
			u.as(this.field._help, "top", ((this.offsetTop + this.offsetHeight/2 + 2) - (this.field._help.offsetHeight/2)) + "px")
		}
		this.used = true;
		if(typeof(this.blurred) == "function") {
			this.blurred();
		}
		if(typeof(this.form.blurred) == "function") {
			this.form.blurred(this);
		}
	}
	this._button_focus = function(event) {
		u.ac(this, "focus");
		if(typeof(this.focused) == "function") {
			this.focused();
		}
		if(typeof(this.form.focused) == "function") {
			this.form.focused(this);
		}
	}
	this._button_blur = function(event) {
		u.rc(this, "focus");
		if(typeof(this.blurred) == "function") {
			this.blurred();
		}
		if(typeof(this.form.blurred) == "function") {
			this.form.blurred(this);
		}
	}
	this._default_value_focus = function() {
		u.rc(this, "default");
		if(this.val() == this.default_value) {
			this.val("");
		}
	}
	this._default_value_blur = function() {
		if(this.val() == "") {
			u.ac(this, "default");
			this.val(this.default_value);
		}
	}
	this.activateField = function(iN) {
		u.e.addEvent(iN, "focus", this._focus);
		u.e.addEvent(iN, "blur", this._blur);
		u.e.addEvent(iN, "blur", this._validate);
		if(iN.form.labelstyle || u.hc(iN.form, "labelstyle:[a-z]+")) {
			iN.form.labelstyle = iN.form.labelstyle ? iN.form.labelstyle : u.cv(iN.form, "labelstyle");
			if(iN.form.labelstyle == "inject" && (!iN.type || !iN.type.match(/file|radio|checkbox/))) {
				iN.default_value = iN._label.innerHTML;
				u.e.addEvent(iN, "focus", this._default_value_focus);
				u.e.addEvent(iN, "blur", this._default_value_blur);
				if(iN.val() == "") {
					iN.val(iN.default_value);
					u.ac(iN, "default");
				}
			}
		}
	}
	this.activateButton = function(button) {
		u.e.addEvent(button, "focus", this._button_focus);
		u.e.addEvent(button, "blur", this._button_blur);
	}
 	this.isDefault = function(iN) {
		if(iN.default_value && iN.val() == iN.default_value) {
			return true;
		}
		return false;
	}
	this.fieldError = function(iN) {
		u.rc(iN, "correct");
		u.rc(iN.field, "correct");
		if(iN.used || !this.isDefault(iN) && iN.val()) {
			u.ac(iN, "error");
			u.ac(iN.field, "error");
			if(iN.field._help) {
				u.as(iN.field._help, "top", ((iN.offsetTop + iN.offsetHeight/2 + 2) - (iN.field._help.offsetHeight/2)) + "px")
			}
			if(typeof(iN.validationFailed) == "function") {
				iN.validationFailed();
			}
		}
	}
	this.fieldCorrect = function(iN) {
		if(!this.isDefault(iN) && iN.val()) {
			u.ac(iN, "correct");
			u.ac(iN.field, "correct");
			u.rc(iN, "error");
			u.rc(iN.field, "error");
		}
		else {
			u.rc(iN, "correct");
			u.rc(iN.field, "correct");
			u.rc(iN, "error");
			u.rc(iN.field, "error");
		}
	}
	this.autoExpand = function(iN) {
		var current_height = parseInt(u.gcs(iN, "height"));
		var current_value = iN.val();
		iN.val("");
		u.as(iN, "overflow", "hidden");
		iN.autoexpand_offset = 0;
		if(parseInt(u.gcs(iN, "height")) != iN.scrollHeight) {
			iN.autoexpand_offset = iN.scrollHeight - parseInt(u.gcs(iN, "height"));
		}
		iN.val(current_value);
		iN.setHeight = function() {
			u.bug("iN.setHeight:" + u.nodeId(this));
			var textarea_height = parseInt(u.gcs(this, "height"));
			if(this.val()) {
				if(u.browser("webkit") || u.browser("firefox", ">=29")) {
					if(this.scrollHeight - this.autoexpand_offset > textarea_height) {
						u.a.setHeight(this, this.scrollHeight);
					}
				}
				else if(u.browser("opera") || u.browser("explorer")) {
					if(this.scrollHeight > textarea_height) {
						u.a.setHeight(this, this.scrollHeight);
					}
				}
				else {
					u.a.setHeight(this, this.scrollHeight);
				}
			}
		}
		u.e.addEvent(iN, "keyup", iN.setHeight);
		iN.setHeight();
	}
	this.geoLocation = function(field) {
		u.ac(field, "geolocation");
		var bn_geolocation = u.ae(field, "div", {"class":"geolocation"});
		bn_geolocation.field = field;
		u.ce(bn_geolocation);
		bn_geolocation.clicked = function() {
			window._geoLocationField = this.field;
			window._foundLocation = function(position) {
				var lat = position.coords.latitude;
				var lon = position.coords.longitude;
				var lat_input = u.qs("div.latitude input", window._geolocationField);
				var lon_input = u.qs("div.longitude input", window._geolocationField);
				lat_input.val(lat);
				lat_input.focus();
				lon_input.val(lon);
				lon_input.focus();
			}
			window._noLocation = function() {
				alert('Could not find location');
			}
			navigator.geolocation.getCurrentPosition(window._foundLocation, window._noLocation);
		}
	}
	this.validate = function(iN) {
		var min, max, pattern;
		var not_validated = true;
		if(!u.hc(iN.field, "required") && (iN.val() == "" || this.isDefault(iN))) {
			this.fieldCorrect(iN);
			return true;
		}
		else if(u.hc(iN.field, "required") && (iN.val() == "" || this.isDefault(iN))) {
			this.fieldError(iN);
			return false;
		}
		var custom_validate;
		for(custom_validate in u.f.customValidate) {
			if(u.hc(iN.field, custom_validate)) {
				u.f.customValidate[custom_validate](iN);
				not_validated = false;
			}
		}
		if(not_validated) {
			if(u.hc(iN.field, "password")) {
				min = Number(u.cv(iN.field, "min"));
				max = Number(u.cv(iN.field, "max"));
				min = min ? min : 8;
				max = max ? max : 20;
				pattern = iN.getAttribute("pattern");
				if(
					iN.val().length >= min && 
					iN.val().length <= max && 
					(!pattern || iN.val().match("^"+pattern+"$"))
				) {
					this.fieldCorrect(iN);
				}
				else {
					this.fieldError(iN);
				}
			}
			else if(u.hc(iN.field, "number")) {
				min = Number(u.cv(iN.field, "min"));
				max = Number(u.cv(iN.field, "max"));
				min = min ? min : 0;
				max = max ? max : 99999999999999999999999999999;
				pattern = iN.getAttribute("pattern");
				if(
					!isNaN(iN.val()) && 
					iN.val() >= min && 
					iN.val() <= max && 
					(!pattern || iN.val().match("^"+pattern+"$"))
				) {
					this.fieldCorrect(iN);
				}
				else {
					this.fieldError(iN);
				}
			}
			else if(u.hc(iN.field, "integer")) {
				min = Number(u.cv(iN.field, "min"));
				max = Number(u.cv(iN.field, "max"));
				min = min ? min : 0;
				max = max ? max : 99999999999999999999999999999;
				pattern = iN.getAttribute("pattern");
				if(
					!isNaN(iN.val()) && 
					Math.round(iN.val()) == iN.val() && 
					iN.val() >= min && 
					iN.val() <= max && 
					(!pattern || iN.val().match("^"+pattern+"$"))
				) {
					this.fieldCorrect(iN);
				}
				else {
					this.fieldError(iN);
				}
			}
			else if(u.hc(iN.field, "tel")) {
				pattern = iN.getAttribute("pattern");
				if(
					!pattern && iN.val().match(/^([\+0-9\-\.\s\(\)]){5,18}$/) ||
					(pattern && iN.val().match("^"+pattern+"$"))
				) {
					this.fieldCorrect(iN);
				}
				else {
					this.fieldError(iN);
				}
			}
			else if(u.hc(iN.field, "email")) {
				if(
					!pattern && iN.val().match(/^([^<>\\\/%$])+\@([^<>\\\/%$])+\.([^<>\\\/%$]{2,20})$/) ||
					(pattern && iN.val().match("^"+pattern+"$"))
				) {
					this.fieldCorrect(iN);
				}
				else {
					this.fieldError(iN);
				}
			}
			else if(u.hc(iN.field, "text")) {
				min = Number(u.cv(iN.field, "min"));
				max = Number(u.cv(iN.field, "max"));
				min = min ? min : 1;
				max = max ? max : 10000000;
				pattern = iN.getAttribute("pattern");
				if(
					iN.val().length >= min && 
					iN.val().length <= max && 
					(!pattern || iN.val().match("^"+pattern+"$"))
				) {
					this.fieldCorrect(iN);
				}
				else {
					this.fieldError(iN);
				}
			}
			else if(u.hc(iN.field, "select")) {
				if(iN.val()) {
					this.fieldCorrect(iN);
				}
				else {
					this.fieldError(iN);
				}
			}
			else if(u.hc(iN.field, "checkbox|boolean|radio|radio_buttons")) {
				if(iN.val()) {
					this.fieldCorrect(iN);
				}
				else {
					this.fieldError(iN);
				}
			}
			else if(u.hc(iN.field, "string")) {
				min = Number(u.cv(iN.field, "min"));
				max = Number(u.cv(iN.field, "max"));
				min = min ? min : 1;
				max = max ? max : 255;
				pattern = iN.getAttribute("pattern");
				if(
					iN.val().length >= min &&
					iN.val().length <= max && 
					(!pattern || iN.val().match("^"+pattern+"$"))
				) {
					this.fieldCorrect(iN);
				}
				else {
					this.fieldError(iN);
				}
			}
			else if(u.hc(iN.field, "date")) {
				pattern = iN.getAttribute("pattern");
				if(
					!pattern && iN.val().match(/^([\d]{4}[\-\/\ ]{1}[\d]{2}[\-\/\ ][\d]{2})$/) ||
					(pattern && iN.val().match("^"+pattern+"$"))
				) {
					this.fieldCorrect(iN);
				}
				else {
					this.fieldError(iN);
				}
			}
			else if(u.hc(iN.field, "datetime")) {
				pattern = iN.getAttribute("pattern");
				if(
					!pattern && iN.val().match(/^([\d]{4}[\-\/\ ]{1}[\d]{2}[\-\/\ ][\d]{2} [\d]{2}[\-\/\ \:]{1}[\d]{2}[\-\/\ \:]{0,1}[\d]{0,2})$/) ||
					(pattern && iN.val().match(pattern))
				) {
					this.fieldCorrect(iN);
				}
				else {
					this.fieldError(iN);
				}
			}
			else if(u.hc(iN.field, "tags")) {
				if(
					!pattern && iN.val().match(/\:/) ||
					(pattern && iN.val().match("^"+pattern+"$"))
				) {
					this.fieldCorrect(iN);
				}
				else {
					this.fieldError(iN);
				}
			}
			else if(u.hc(iN.field, "prices")) {
				if(
					!isNaN(iN.val())
				) {
					this.fieldCorrect(iN);
				}
				else {
					this.fieldError(iN);
				}
			}
			else if(u.hc(iN.field, "location")) {
				if(u.hc(iN, "location")) {
					min = min ? min : 1;
					max = max ? max : 255;
					if(
						iN.val().length >= min &&
						iN.val().length <= max
					) {
						this.fieldCorrect(iN);
					}
					else {
						this.fieldError(iN);
					}
				}
				if(u.hc(iN, "latitude")) {
					min = min ? min : -90;
					max = max ? max : 90;
					if(
						!isNaN(iN.val()) && 
						iN.val() >= min && 
						iN.val() <= max
					) {
						this.fieldCorrect(iN);
					}
					else {
						this.fieldError(iN);
					}
				}
				if(u.hc(iN, "longitude")) {
					min = min ? min : -180;
					max = max ? max : 180;
					if(
						!isNaN(iN.val()) && 
						iN.val() >= min && 
						iN.val() <= max
					) {
						this.fieldCorrect(iN);
					}
					else {
						this.fieldError(iN);
					}
				}
				if(u.qsa(".correct", iN.field).length != 3) {
					u.rc(iN.field, "correct");
					u.ac(iN.field, "error");
				}
			}
			else if(u.hc(iN.field, "files")) {
				u.bug("files:" + iN.files.length);
				if(
					1
				) {
					this.fieldCorrect(iN);
				}
				else {
					this.fieldError(iN);
				}
			}
		}
		if(u.hc(iN.field, "error")) {
			return false;
		}
		else {
			return true;
		}
	}
	this.getParams = function(form, settings) {
		var send_as = "params";
		var ignore_inputs = "ignoreinput";
		if(typeof(settings) == "object") {
			var argument;
			for(argument in settings) {
				switch(argument) {
					case "ignore_inputs"	: ignore_inputs		= settings[argument]; break;
					case "send_as"			: send_as			= settings[argument]; break;
				}
			}
		}
		var i, input, select, textarea, param;
			var params = new Object();
		if(form._submit_button && form._submit_button.name) {
			params[form._submit_button.name] = form._submit_button.value;
		}
		var inputs = u.qsa("input", form);
		var selects = u.qsa("select", form)
		var textareas = u.qsa("textarea", form)
		for(i = 0; input = inputs[i]; i++) {
			if(!u.hc(input, ignore_inputs)) {
				if((input.type == "checkbox" || input.type == "radio") && input.checked) {
					if(!this.isDefault(input)) {
						params[input.name] = input.value;
					}
				}
				else if(input.type == "file") {
					if(!this.isDefault(input)) {
						params[input.name] = input.value;
					}
				}
				else if(!input.type.match(/button|submit|reset|file|checkbox|radio/i)) {
					if(!this.isDefault(input)) {
						params[input.name] = input.value;
					}
					else {
						params[input.name] = "";
					}
				}
			}
		}
		for(i = 0; select = selects[i]; i++) {
			if(!u.hc(select, ignore_inputs)) {
				if(!this.isDefault(select)) {
					params[select.name] = select.options[select.selectedIndex].value;
				}
			}
		}
		for(i = 0; textarea = textareas[i]; i++) {
			if(!u.hc(textarea, ignore_inputs)) {
				if(!this.isDefault(textarea)) {
					params[textarea.name] = textarea.value;
				}
				else {
					params[textarea.name] = "";
				}
			}
		}
		if(send_as && typeof(this.customSend[send_as]) == "function") {
			return this.customSend[send_as](params, form);
		}
		else if(send_as == "json") {
			return u.f.convertNamesToJsonObject(params);
		}
		else if(send_as == "object") {
			return params;
		}
		else {
			var string = "";
			for(param in params) {
					string += (string ? "&" : "") + param + "=" + encodeURIComponent(params[param]);
			}
			return string;
		}
	}
}
u.f.convertNamesToJsonObject = function(params) {
 	var indexes, root, indexes_exsists, param;
	var object = new Object();
	for(param in params) {
	 	indexes_exsists = param.match(/\[/);
		if(indexes_exsists) {
			root = param.split("[")[0];
			indexes = param.replace(root, "");
			if(typeof(object[root]) == "undefined") {
				object[root] = new Object();
			}
			object[root] = this.recurseName(object[root], indexes, params[param]);
		}
		else {
			object[param] = params[param];
		}
	}
	return object;
}
u.f.recurseName = function(object, indexes, value) {
	var index = indexes.match(/\[([a-zA-Z0-9\-\_]+)\]/);
	var current_index = index[1];
	indexes = indexes.replace(index[0], "");
 	if(indexes.match(/\[/)) {
		if(object.length !== undefined) {
			var i;
			var added = false;
			for(i = 0; i < object.length; i++) {
				for(exsiting_index in object[i]) {
					if(exsiting_index == current_index) {
						object[i][exsiting_index] = this.recurseName(object[i][exsiting_index], indexes, value);
						added = true;
					}
				}
			}
			if(!added) {
				temp = new Object();
				temp[current_index] = new Object();
				temp[current_index] = this.recurseName(temp[current_index], indexes, value);
				object.push(temp);
			}
		}
		else if(typeof(object[current_index]) != "undefined") {
			object[current_index] = this.recurseName(object[current_index], indexes, value);
		}
		else {
			object[current_index] = new Object();
			object[current_index] = this.recurseName(object[current_index], indexes, value);
		}
	}
	else {
		object[current_index] = value;
	}
	return object;
}
u.f.addForm = function(node, settings) {
	var form_name = "js_form";
	var form_action = "#";
	var form_method = "post";
	var form_class = "";
	if(typeof(settings) == "object") {
		var argument;
		for(argument in settings) {
			switch(argument) {
				case "name"			: form_name				= settings[argument]; break;
				case "action"		: form_action			= settings[argument]; break;
				case "method"		: form_method			= settings[argument]; break;
				case "class"		: form_class			= settings[argument]; break;
			}
		}
	}
	var form = u.ae(node, "form", {"class":form_class, "name": form_name, "action":form_action, "method":form_method});
	return form;
}
u.f.addFieldset = function(node) {
	return u.ae(node, "fieldset");
}
u.f.addField = function(node, settings) {
	var field_type = "string";
	var field_label = "Value";
	var field_name = "js_name";
	var field_value = "";
	var field_class = "";
	if(typeof(settings) == "object") {
		var argument;
		for(argument in settings) {
			switch(argument) {
				case "type"			: field_type			= settings[argument]; break;
				case "label"		: field_label			= settings[argument]; break;
				case "name"			: field_name			= settings[argument]; break;
				case "value"		: field_value			= settings[argument]; break;
				case "class"		: field_class			= settings[argument]; break;
			}
		}
	}
	var input_id = "input_"+field_type+"_"+field_name;
	var field = u.ae(node, "div", {"class":"field "+field_type+" "+field_class});
	if(field_type == "string") {
		var label = u.ae(field, "label", {"for":input_id, "html":field_label});
		var input = u.ae(field, "input", {"id":input_id, "value":field_value, "name":field_name, "type":"text"});
	}
	else if(field_type == "email" || field_type == "number" || field_type == "tel") {
		var label = u.ae(field, "label", {"for":input_id, "html":field_label});
		var input = u.ae(field, "input", {"id":input_id, "value":field_value, "name":field_name, "type":field_type});
	}
	else if(field_type == "select") {
		u.bug("Select not implemented yet")
	}
	else {
		u.bug("input type not implemented yet")
	}
	return field;
}
u.f.addAction = function(node, settings) {
	var action_type = "submit";
	var action_name = "js_name";
	var action_value = "";
	var action_class = "";
	if(typeof(settings) == "object") {
		var argument;
		for(argument in settings) {
			switch(argument) {
				case "type"			: action_type			= settings[argument]; break;
				case "name"			: action_name			= settings[argument]; break;
				case "value"		: action_value			= settings[argument]; break;
				case "class"		: action_class			= settings[argument]; break;
			}
		}
	}
	var p_ul = node.nodeName.toLowerCase() == "ul" ? node : u.pn(node, "ul");
	if(!u.hc(p_ul, "actions")) {
		p_ul = u.ae(node, "ul", {"class":"actions"});
	}
	var p_li = node.nodeName.toLowerCase() == "li" ? node : u.pn(node, "li");
	if(p_ul != p_li.parentNode) {
		p_li = u.ae(p_ul, "li", {"class":action_name});
	}
	else {
		p_li = node;
	}
	var action = u.ae(p_li, "input", {"type":action_type, "class":action_class, "value":action_value, "name":action_name})
	return action;
}
Util.absoluteX = u.absX = function(node) {
	if(node.offsetParent) {
		u.bug("node.offsetParent, node.offsetLeft + u.absX(node.offsetParent):" + node.offsetLeft + ", " + u.nodeId(node.offsetParent))
		return node.offsetLeft + u.absX(node.offsetParent);
	}
	u.bug("node.offsetLeft:" + node.offsetLeft)
	return node.offsetLeft;
}
Util.absoluteY = u.absY = function(node) {
	if(node.offsetParent) {
		return node.offsetTop + u.absY(node.offsetParent);
	}
	return node.offsetTop;
}
Util.relativeX = u.relX = function(node) {
	if(u.gcs(node, "position").match(/absolute/) == null && node.offsetParent && u.gcs(node.offsetParent, "position").match(/relative|absolute|fixed/) == null) {
		return node.offsetLeft + u.relX(node.offsetParent);
	}
	return node.offsetLeft;
}
Util.relativeY = u.relY = function(node) {
	if(u.gcs(node, "position").match(/absolute/) == null && node.offsetParent && u.gcs(node.offsetParent, "position").match(/relative|absolute|fixed/) == null) {
		return node.offsetTop + u.relY(node.offsetParent);
	}
	return node.offsetTop;
}
Util.actualWidth = u.actualW = function(node) {
	return parseInt(u.gcs(node, "width"));
}
Util.actualHeight = u.actualH = function(node) {
	return parseInt(u.gcs(node, "height"));
}
Util.eventX = function(event){
	return (event.targetTouches ? event.targetTouches[0].pageX : event.pageX);
}
Util.eventY = function(event){
	return (event.targetTouches ? event.targetTouches[0].pageY : event.pageY);
}
Util.browserWidth = u.browserW = function() {
	return document.documentElement.clientWidth;
}
Util.browserHeight = u.browserH = function() {
	return document.documentElement.clientHeight;
}
Util.htmlWidth = u.htmlW = function() {
	return document.body.offsetWidth + parseInt(u.gcs(document.body, "margin-left")) + parseInt(u.gcs(document.body, "margin-right"));
}
Util.htmlHeight = u.htmlH = function() {
	return document.body.offsetHeight + parseInt(u.gcs(document.body, "margin-top")) + parseInt(u.gcs(document.body, "margin-bottom"));
}
Util.pageScrollX = u.scrollX = function() {
	return window.pageXOffset;
}
Util.pageScrollY = u.scrollY = function() {
	return window.pageYOffset;
}
Util.History = u.h = new function() {
	this.popstate = ("onpopstate" in window);
	this.catchEvent = function(node, callback) {
		this.node = node;
		this.node.callback = callback;
		var hashChanged = function(event) {
			if(!location.hash || !location.hash.match(/^#\//)) {
				location.hash = "#/"
				return;
			}
			var url = u.h.getCleanHash(location.hash);
			u.h.node.callback(url);
		}
		var urlChanged = function(event) {
			var url = u.h.getCleanUrl(location.href);
			u.h.node.callback(url);
		}
		if(this.popstate) {
			window.onpopstate = urlChanged;
		}
		else if("onhashchange" in window && !u.browser("explorer", "<=7")) {
			window.onhashchange = hashChanged;
		}
		else {
			u.current_hash = window.location.hash;
			window.onhashchange = hashChanged;
			setInterval(
				function() {
					if(window.location.hash !== u.current_hash) {
						u.current_hash = window.location.hash;
						window.onhashchange();
					}
				}, 200
			);
		}
	}
	this.getCleanUrl = function(string, levels) {
		string = string.replace(location.protocol+"//"+document.domain, "").match(/[^#$]+/)[0];
		if(!levels) {
			return string;
		}
		else {
			var i, return_string = "";
			var path = string.split("/");
			levels = levels > path.length-1 ? path.length-1 : levels;
			for(i = 1; i <= levels; i++) {
				return_string += "/" + path[i];
			}
			return return_string;
		}
	}
	this.getCleanHash = function(string, levels) {
		string = string.replace("#", "");
		if(!levels) {
			return string;
		}
		else {
			var i, return_string = "";
			var hash = string.split("/");
			levels = levels > hash.length-1 ? hash.length-1 : levels;
			for(i = 1; i <= levels; i++) {
				return_string += "/" + hash[i];
			}
			return return_string;
		}
	}
}
Util.Objects = u.o = new Object();
Util.init = function(scope) {
	var i, node, nodes, object;
	scope = scope && scope.nodeName ? scope : document;
	nodes = u.ges("i\:([_a-zA-Z0-9])+");
	for(i = 0; node = nodes[i]; i++) {
		while((object = u.cv(node, "i"))) {
			u.rc(node, "i:"+object);
			if(object && typeof(u.o[object]) == "object") {
				u.o[object].init(node);
			}
		}
	}
}
Util.random = function(min, max) {
	return Math.round((Math.random() * (max - min)) + min);
}
Util.numToHex = function(num) {
	return num.toString(16);
}
Util.hexToNum = function(hex) {
	return parseInt(hex,16);
}
Util.round = function(number, decimals) {
	var round_number = number*Math.pow(10, decimals);
	return Math.round(round_number)/Math.pow(10, decimals);
}
u.navigation = function(options) {
	page._nav_path = page._nav_path ? page._nav_path : u.h.getCleanUrl(location.href);
	page._nav_history = page._nav_history ? page._nav_history : [];
	page._navigate = function(url) {
		url = u.h.getCleanUrl(url);
		page._nav_history.unshift(url);
		u.stats.pageView(url);
		if(!this._nav_path || ((this._nav_path != u.h.getCleanHash(location.hash, 1) && !u.h.popstate) || (this._nav_path != u.h.getCleanUrl(location.href, 1) && u.h.popstate))) {
			if(this.cN && typeof(this.cN.navigate) == "function") {
				this.cN.navigate(url);
			}
		}
		else {
			if(this.cN.scene && this.cN.scene.parentNode && typeof(this.cN.scene.navigate) == "function") {
				this.cN.scene.navigate(url);
			}
			else if(this.cN && typeof(this.cN.navigate) == "function") {
				this.cN.navigate(url);
			}
		}
		if(!u.h.popstate) {
			this._nav_path = u.h.getCleanHash(location.hash, 1);
		}
		else {
			this._nav_path = u.h.getCleanUrl(location.href, 1);
		}
	}
	page.navigate = function(url, node) {
		this.history_node = node ? node : false;
		if(u.h.popstate) {
			history.pushState({}, url, url);
			page._navigate(url);
		}
		else {
			location.hash = u.h.getCleanUrl(url);
		}
	}
	if(location.hash.length && location.hash.match(/^#!/)) {
		location.hash = location.hash.replace(/!/, "");
	}
	if(!u.h.popstate) {
		if(location.hash.length < 2) {
			page.navigate(location.href, page);
			page._nav_path = u.h.getCleanUrl(location.href);
			u.init(page.cN);
		}
		else if(u.h.getCleanHash(location.hash) != u.h.getCleanUrl(location.href) && location.hash.match(/^#\//)) {
			page._nav_path = u.h.getCleanUrl(location.href);
			page._navigate();
		}
		else {
			u.init(page.cN);
		}
	}
	else {
		if(u.h.getCleanHash(location.hash) != u.h.getCleanUrl(location.href) && location.hash.match(/^#\//)) {
			page._nav_path = u.h.getCleanHash(location.hash);
			page.navigate(u.h.getCleanHash(location.hash), page);
		}
		else {
			u.init(page.cN);
		}
	}
	page._initHistory = function() {
		u.h.catchEvent(page, page._navigate);
	}
	u.t.setTimer(page, page._initHistory, 100);
	page.historyBack = function() {
		if(this._nav_history.length > 1) {
			this._nav_history.shift();
			return this._nav_history.shift();
		}
		else {
			return "/";
		}
	}
}
Util.period = function(format, time) {
	var seconds = 0;
	if(typeof(time) == "object") {
		var argument;
		for(argument in time) {
			switch(argument) {
				case "seconds"		: seconds = time[argument]; break;
				case "milliseconds" : seconds = Number(time[argument])/1000; break;
				case "minutes"		: seconds = Number(time[argument])*60; break;
				case "hours"		: seconds = Number(time[argument])*60*60 ; break;
				case "days"			: seconds = Number(time[argument])*60*60*24; break;
				case "months"		: seconds = Number(time[argument])*60*60*24*(365/12); break;
				case "years"		: seconds = Number(time[argument])*60*60*24*365; break;
			}
		}
	}
	var tokens = /y|n|o|O|w|W|c|d|e|D|g|h|H|l|m|M|r|s|S|t|T|u|U/g;
	var chars = new Object();
	chars.y = 0; 
	chars.n = 0; 
	chars.o = (chars.n > 9 ? "" : "0") + chars.n; 
	chars.O = 0; 
	chars.w = 0; 
	chars.W = 0; 
	chars.c = 0; 
	chars.d = 0; 
	chars.e = 0; 
	chars.D = Math.floor(((seconds/60)/60)/24);
	chars.g = Math.floor((seconds/60)/60)%24;
	chars.h = (chars.g > 9 ? "" : "0") + chars.g;
	chars.H = Math.floor((seconds/60)/60);
	chars.l = Math.floor(seconds/60)%60;
	chars.m = (chars.l > 9 ? "" : "0") + chars.l;
	chars.M = Math.floor(seconds/60);
	chars.r = Math.floor(seconds)%60;
	chars.s = (chars.r > 9 ? "" : "0") + chars.r;
	chars.S = Math.floor(seconds);
	chars.t = Math.round((seconds%1)*10);
	chars.T = Math.round((seconds%1)*100);
	chars.T = (chars.T > 9 ? "": "0") + Math.round(chars.T);
	chars.u = Math.round((seconds%1)*1000);
	chars.u = (chars.u > 9 ? chars.u > 99 ? "" : "0" : "00") + Math.round(chars.u);
	chars.U = Math.round(seconds*1000);
	return format.replace(tokens, function (_) {
		return _ in chars ? chars[_] : _.slice(1, _.length - 1);
	});
};
Util.popup = function(url, settings) {
	var width = "330";
	var height = "150";
	var name = "popup" + new Date().getHours() + "_" + new Date().getMinutes() + "_" + new Date().getMilliseconds();
	var extra = "";
	if(typeof(settings) == "object") {
		var argument;
		for(argument in settings) {
			switch(argument) {
				case "name"		: name		= settings[argument]; break;
				case "width"	: width		= Number(settings[argument]); break;
				case "height"	: height	= Number(settings[argument]); break;
				case "extra"	: extra		= settings[argument]; break;
			}
		}
	}
	var p;
	p = "width=" + width + ",height=" + height;
	p += ",left=" + (screen.width-width)/2;
	p += ",top=" + ((screen.height-height)-20)/2;
	p += extra ? "," + extra : ",scrollbars";
	document[name] = window.open(url, name, p);
	return document[name];
}
u.preloader = function(node, files, options) {
	var callback, callback_min_delay
	if(typeof(options) == "object") {
		var argument;
		for(argument in options) {
			switch(argument) {
				case "callback"				: callback				= options[argument]; break;
				case "callback_min_delay"	: callback_min_delay	= options[argument]; break;
			}
		}
	}
	if(!u._preloader_queue) {
		u._preloader_queue = document.createElement("div");
		u._preloader_processes = 0;
		if(u.e && u.e.event_pref == "touch") {
			u._preloader_max_processes = 1;
		}
		else {
			u._preloader_max_processes = 1;
		}
	}
	if(node && files) {
		var entry, file;
		var new_queue = u.ae(u._preloader_queue, "ul");
		new_queue._callback = callback;
		new_queue._node = node;
		new_queue._files = files;
		new_queue.nodes = new Array();
		new_queue._start_time = new Date().getTime();
		for(i = 0; file = files[i]; i++) {
			entry = u.ae(new_queue, "li", {"class":"waiting"});
			entry.i = i;
			entry._queue = new_queue
			entry._file = file;
		}
		u.ac(node, "waiting");
		if(typeof(node.waiting) == "function") {
			node.waiting();
		}
	}
	u._queueLoader();
	return u._preloader_queue;
}
u._queueLoader = function() {
	if(u.qs("li.waiting", u._preloader_queue)) {
		while(u._preloader_processes < u._preloader_max_processes) {
			var next = u.qs("li.waiting", u._preloader_queue);
			if(next) {
				if(u.hc(next._queue._node, "waiting")) {
					u.rc(next._queue._node, "waiting");
					u.ac(next._queue._node, "loading");
					if(typeof(next._queue._node.loading) == "function") {
						next._node._queue.loading();
					}
				}
				u._preloader_processes++;
				u.rc(next, "waiting");
				u.ac(next, "loading");
				next.loaded = function(event) {
					this.image = event.target;
					this._image = this.image;
					this._queue.nodes[this.i] = this;
					u.rc(this, "loading");
					u.ac(this, "loaded");
					u._preloader_processes--;
					if(!u.qs("li.waiting,li.loading", this._queue)) {
						u.rc(this._queue._node, "loading");
						if(typeof(this._queue._callback) == "function") {
							this._queue._node._callback = this._queue._callback;
							this._queue._node._callback(this._queue.nodes);
						}
						else if(typeof(this._queue._node.loaded) == "function") {
							this._queue._node.loaded(this._queue.nodes);
						}
					}
					u._queueLoader();
				}
				u.loadImage(next, next._file);
			}
			else {
				break
			}
		}
	}
}
u.loadImage = function(node, src) {
	var image = new Image();
	image.node = node;
	u.ac(node, "loading");
    u.e.addEvent(image, 'load', u._imageLoaded);
	u.e.addEvent(image, 'error', u._imageLoadError);
	image.src = src;
}
u._imageLoaded = function(event) {
	u.rc(this.node, "loading");
	if(typeof(this.node.loaded) == "function") {
		this.node.loaded(event);
	}
}
u._imageLoadError = function(event) {
	u.rc(this.node, "loading");
	u.ac(this.node, "error");
	if(typeof(this.node.loaded) == "function" && typeof(this.node.failed) != "function") {
		this.node.loaded(event);
	}
	else if(typeof(this.node.failed) == "function") {
		this.node.failed(event);
	}
}
u._imageLoadProgress = function(event) {
	u.bug("progress")
	if(typeof(this.node.progress) == "function") {
		this.node.progress(event);
	}
}
u._imageLoadDebug = function(event) {
	u.bug("event:" + event.type);
	u.xInObject(event);
}
Util.createRequestObject = u.createRequestObject = function() {
	return new XMLHttpRequest();
}
Util.request = u.request = function(node, url, settings) {
	var request_id = u.randomString(6);
	node[request_id] = {};
	node[request_id].request_url = url;
	node[request_id].request_method = "GET";
	node[request_id].request_async = true;
	node[request_id].request_params = "";
	node[request_id].request_headers = false;
	node[request_id].response_callback = "response";
	if(typeof(settings) == "object") {
		var argument;
		for(argument in settings) {
			switch(argument) {
				case "method"		: node[request_id].request_method		= settings[argument]; break;
				case "params"		: node[request_id].request_params		= settings[argument]; break;
				case "async"		: node[request_id].request_async		= settings[argument]; break;
				case "headers"		: node[request_id].request_headers		= settings[argument]; break;
				case "callback"		: node[request_id].response_callback	= settings[argument]; break;
			}
		}
	}
	if(node[request_id].request_method.match(/GET|POST|PUT|PATCH/i)) {
		node[request_id].HTTPRequest = this.createRequestObject();
		node[request_id].HTTPRequest.node = node;
		node[request_id].HTTPRequest.request_id = request_id;
		if(node[request_id].request_async) {
			node[request_id].HTTPRequest.onreadystatechange = function() {
				if(this.readyState == 4) {
					u.validateResponse(this);
				}
			}
		}
		try {
			if(node[request_id].request_method.match(/GET/i)) {
				var params = u.JSONtoParams(node[request_id].request_params);
				node[request_id].request_url += params ? ((!node[request_id].request_url.match(/\?/g) ? "?" : "&") + params) : "";
				node[request_id].HTTPRequest.open(node[request_id].request_method, node[request_id].request_url, node[request_id].request_async);
				node[request_id].HTTPRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				var csfr_field = u.qs('meta[name="csrf-token"]');
				if(csfr_field && csfr_field.content) {
					node[request_id].HTTPRequest.setRequestHeader("X-CSRF-Token", csfr_field.content);
				}
				if(typeof(node[request_id].request_headers) == "object") {
					var header;
					for(header in node[request_id].request_headers) {
						node[request_id].HTTPRequest.setRequestHeader(header, node[request_id].request_headers[header]);
					}
				}
				node[request_id].HTTPRequest.send("");
			}
			else if(node[request_id].request_method.match(/POST|PUT|PATCH/i)) {
				var params;
				if(typeof(node[request_id].request_params) == "object" && !node[request_id].request_params.constructor.toString().match(/FormData/i)) {
					params = JSON.stringify(node[request_id].request_params);
				}
				else {
					params = node[request_id].request_params;
				}
				node[request_id].HTTPRequest.open(node[request_id].request_method, node[request_id].request_url, node[request_id].request_async);
				node[request_id].HTTPRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				var csfr_field = u.qs('meta[name="csrf-token"]');
				if(csfr_field && csfr_field.content) {
					node[request_id].HTTPRequest.setRequestHeader("X-CSRF-Token", csfr_field.content);
				}
				if(typeof(node[request_id].request_headers) == "object") {
					var header;
					for(header in node[request_id].request_headers) {
						node[request_id].HTTPRequest.setRequestHeader(header, node[request_id].request_headers[header]);
					}
				}
				node[request_id].HTTPRequest.send(params);
			}
		}
		catch(exception) {
			node[request_id].HTTPRequest.exception = exception;
			u.validateResponse(node[request_id].HTTPRequest);
			return;
		}
		if(!node[request_id].request_async) {
			u.validateResponse(node[request_id].HTTPRequest);
		}
	}
	else if(node[request_id].request_method.match(/SCRIPT/i)) {
		var key = u.randomString();
		document[key] = new Object();
		document[key].node = node;
		document[key].request_id = request_id;
		document[key].responder = function(response) {
			var response_object = new Object();
			response_object.node = this.node;
			response_object.request_id = this.request_id;
			response_object.responseText = response;
			u.validateResponse(response_object);
		}
		var params = u.JSONtoParams(node[request_id].request_params);
		node[request_id].request_url += params ? ((!node[request_id].request_url.match(/\?/g) ? "?" : "&") + params) : "";
		node[request_id].request_url += (!node[request_id].request_url.match(/\?/g) ? "?" : "&") + "callback=document."+key+".responder";
		u.ae(u.qs("head"), "script", ({"type":"text/javascript", "src":node[request_id].request_url}));
	}
	return request_id;
}
Util.JSONtoParams = function(json) {
	if(typeof(json) == "object") {
		var params = "", param;
		for(param in json) {
			params += (params ? "&" : "") + param + "=" + json[param];
		}
		return params
	}
	var object = u.isStringJSON(json);
	if(object) {
		return u.JSONtoParams(object);
	}
	return json;
}
Util.isStringJSON = function(string) {
	if(string.trim().substr(0, 1).match(/[\{\[]/i) && string.trim().substr(-1, 1).match(/[\}\]]/i)) {
		try {
			var test = JSON.parse(string);
			if(typeof(test) == "object") {
				test.isJSON = true;
				return test;
			}
		}
		catch(exception) {}
	}
	return false;
}
Util.isStringHTML = function(string) {
	if(string.trim().substr(0, 1).match(/[\<]/i) && string.trim().substr(-1, 1).match(/[\>]/i)) {
		try {
			var test = document.createElement("div");
			test.innerHTML = string;
			if(test.childNodes.length) {
				var body_class = string.match(/<body class="([a-z0-9A-Z_: ]+)"/);
				test.body_class = body_class ? body_class[1] : "";
				var head_title = string.match(/<title>([^$]+)<\/title>/);
				test.head_title = head_title ? head_title[1] : "";
				test.isHTML = true;
				return test;
			}
		}
		catch(exception) {}
	}
	return false;
}
Util.evaluateResponseText = function(responseText) {
	var object;
	if(typeof(responseText) == "object") {
		responseText.isJSON = true;
		return responseText;
	}
	else {
		var response_string;
		if(responseText.trim().substr(0, 1).match(/[\"\']/i) && responseText.trim().substr(-1, 1).match(/[\"\']/i)) {
			response_string = responseText.trim().substr(1, responseText.trim().length-2);
		}
		else {
			response_string = responseText;
		}
		var json = u.isStringJSON(response_string);
		if(json) {
			return json;
		}
		var html = u.isStringHTML(response_string);
		if(html) {
			return html;
		}
		return responseText;
	}
}
Util.validateResponse = function(response){
	var object = false;
	if(response) {
		try {
			if(response.status && !response.status.toString().match(/403|404|500/)) {
				object = u.evaluateResponseText(response.responseText);
			}
			else if(response.responseText) {
				object = u.evaluateResponseText(response.responseText);
			}
		}
		catch(exception) {
			response.exception = exception;
		}
	}
	if(object) {
		if(typeof(response.node[response.node[response.request_id].response_callback]) == "function") {
			response.node[response.node[response.request_id].response_callback](object, response.request_id);
		}
	}
	else {
		if(typeof(response.node.ResponseError) == "function") {
			response.node.ResponseError(response);
		}
		if(typeof(response.node.responseError) == "function") {
			response.node.responseError(response);
		}
	}
}
Util.cutString = function(string, length) {
	var matches, match, i;
	if(string.length <= length) {
		return string;
	}
	else {
		length = length-3;
	}
	matches = string.match(/\&[\w\d]+\;/g);
	if(matches) {
		for(i = 0; match = matches[i]; i++){
			if(string.indexOf(match) < length){
				length += match.length-1;
			}
		}
	}
	return string.substring(0, length) + (string.length > length ? "..." : "");
}
Util.prefix = function(string, length, prefix) {
	string = string.toString();
	prefix = prefix ? prefix : "0";
	while(string.length < length) {
		string = prefix + string;
	}
	return string;
}
Util.randomString = function(length) {
	var key = "", i;
	length = length ? length : 8;
	var pattern = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ".split('');
	for(i = 0; i < length; i++) {
		key += pattern[u.random(0,35)];
	}
	return key;
}
Util.uuid = function() {
	var chars = '0123456789abcdef'.split('');
	var uuid = [], rnd = Math.random, r, i;
	uuid[8] = uuid[13] = uuid[18] = uuid[23] = '-';
	uuid[14] = '4';
	for(i = 0; i < 36; i++) {
		if(!uuid[i]) {
			r = 0 | rnd()*16;
			uuid[i] = chars[(i == 19) ? (r & 0x3) | 0x8 : r & 0xf];
		}
 	}
	return uuid.join('');
}
Util.stringOr = u.eitherOr = function(value, replacement) {
	if(value !== undefined && value !== null) {
		return value;
	}
	else {
		return replacement ? replacement : "";
	}	
}
Util.browser = function(model, version) {
	var current_version = false;
	if(model.match(/\bexplorer\b|\bie\b/i)) {
		if(window.ActiveXObject && navigator.userAgent.match(/(MSIE )(\d+.\d)/i)) {
			current_version = navigator.userAgent.match(/(MSIE )(\d+.\d)/i)[2];
		}
		else if(navigator.userAgent.match(/Trident\/[\d+]\.\d[^$]+rv:(\d+.\d)/i)) {
			current_version = navigator.userAgent.match(/Trident\/[\d+]\.\d[^$]+rv:(\d+.\d)/i)[1];
		}
	}
	else if(model.match(/\bfirefox\b|\bgecko\b/i)) {
		if(window.navigator.mozIsLocallyAvailable) {
			current_version = navigator.userAgent.match(/(Firefox\/)(\d+\.\d+)/i)[2];
		}
	}
	else if(model.match(/\bwebkit\b/i)) {
		if(document.body.style.webkitTransform != undefined) {
			current_version = navigator.userAgent.match(/(AppleWebKit\/)(\d+.\d)/i)[2];
		}
	}
	else if(model.match(/\bchrome\b/i)) {
		if(window.chrome && document.body.style.webkitTransform != undefined) {
			current_version = navigator.userAgent.match(/(Chrome\/)(\d+)(.\d)/i)[2];
		}
	}
	else if(model.match(/\bsafari\b/i)) {
		if(!window.chrome && document.body.style.webkitTransform != undefined) {
			current_version = navigator.userAgent.match(/(Version\/)(\d+)(.\d)/i)[2];
		}
	}
	else if(model.match(/\bopera\b/i)) {
		if(window.opera) {
			if(navigator.userAgent.match(/Version\//)) {
				current_version = navigator.userAgent.match(/(Version\/)(\d+)(.\d)/i)[2];
			}
			else {
				current_version = navigator.userAgent.match(/(Opera[\/ ]{1})(\d+)(.\d)/i)[2];
			}
		}
	}
	if(current_version) {
		if(!version) {
			return current_version;
		}
		else {
			if(!isNaN(version)) {
				return current_version == version;
			}
			else {
				return eval(current_version + version);
			}
		}
	}
	else {
		return false;
	}
}
Util.segment = function(segment) {
	if(!u.current_segment) {
		var scripts = document.getElementsByTagName("script");
		var script, i, src;
		for(i = 0; script = scripts[i]; i++) {
			seg_src = script.src.match(/\/seg_([a-z_]+)/);
			if(seg_src) {
				u.current_segment = seg_src[1];
			}
		}
	}
	if(segment) {
		return segment == u.current_segment;
	}
	return u.current_segment;
}
Util.system = function(os, version) {
}
Util.support = function(property) {
	if(document.documentElement) {
		property = property.replace(/(-\w)/g, function(word){return word.replace(/-/, "").toUpperCase()});
		return property in document.documentElement.style;
	}
	return false;
}
Util.windows = function() {
	return (navigator.userAgent.indexOf("Windows") >= 0) ? true : false;
}
Util.osx = function() {
	return (navigator.userAgent.indexOf("OS X") >= 0) ? true : false;
}
Util.Timer = u.t = new function() {
	this._timers = new Array();
	this.setTimer = function(node, action, timeout) {
		var id = this._timers.length;
		this._timers[id] = {"_a":action, "_n":node, "_t":setTimeout("u.t._executeTimer("+id+")", timeout)};
		return id;
	}
	this.resetTimer = function(id) {
		if(this._timers[id]) {
			clearTimeout(this._timers[id]._t);
			this._timers[id] = false;
		}
	}
	this._executeTimer = function(id) {
		var node = this._timers[id]._n;
		node._timer_action = this._timers[id]._a;
		node._timer_action();
		node._timer_action = null;
		this._timers[id] = false;
	}
	this.setInterval = function(node, action, interval) {
		var id = this._timers.length;
		this._timers[id] = {"_a":action, "_n":node, "_i":setInterval("u.t._executeInterval("+id+")", interval)};
		return id;
	}
	this.resetInterval = function(id) {
		if(this._timers[id]) {
			clearInterval(this._timers[id]._i);
			this._timers[id] = false;
		}
	}
	this._executeInterval = function(id) {
		var node = this._timers[id]._n;
		node._interval_action = this._timers[id]._a;
		node._interval_action();
		node._timer_action = null;
	}
	this.valid = function(id) {
		return this._timers[id] ? true : false;
	}
	this.resetAllTimers = function() {
		var i, t;
		for(i = 0; i < this._timers.length; i++) {
			if(this._timers[i] && this._timers[i]._t) {
				this.resetTimer(i);
			}
		}
	}
	this.resetAllIntervals = function() {
		var i, t;
		for(i = 0; i < this._timers.length; i++) {
			if(this._timers[i] && this._timers[i]._i) {
				this.resetInterval(i);
			}
		}
	}
}
Util.getVar = function(param, url) {
	var string = url ? url.split("#")[0] : location.search;
	var regexp = new RegExp("[\&\?\b]{1}"+param+"\=([^\&\b]+)");
	var match = string.match(regexp);
	if(match && match.length > 1) {
		return match[1];
	}
	else {
		return "";
	}
}


/*beta-u-sortable.js*/
Util.Sort = u.s = new function() {
	this.sortable = function(list) {
		var i, j, node;
		var target_class = u.cv(list, "targets");
		if(!target_class) {
			list.sortable_nodes = u.qsa("li", list);
		}
		else {
			list.sortable_nodes = u.qsa("."+target_class, list);
		}
		if(list.sortable_nodes.length) {
			list.list_type = list.offsetWidth < list.sortable_nodes[0].offsetWidth*2 ? "vertical" : "horizontal";
		}
		for(i = 0; node = list.sortable_nodes[i]; i++) {
			node.list = list;
			node.dragme = true;
			node.rel_ox = u.absX(node) - u.relX(node);
			node.rel_oy = u.absY(node) - u.relY(node);
			node.drag = u.qs(".drag", node);
			if(!node.drag) {
				node.drag = node;
			}
			node.drag.node = node;
			var drag_children = u.qsa("*", node.drag);
			if(drag_children) {
				for(j = 0; child = drag_children[j]; j++) {
					child.node = node;
				}
			}
			u.e.addStartEvent(node.drag , this._pick);
		}
	}
	this._pick = function(event) {
		if(!this._sorting_disabled) {
			u.e.kill(event);
			if(!this.node.list.dragged) {
				var node = this.node.list.dragged = this.node;
				node.start_opacity = u.gcs(node, "opacity");
				node.start_position = u.gcs(node, "position");
				node.start_width = u.gcs(node, "opacity");
				node.start_height = u.gcs(node, "position");
				if(!node.list.tN) {
					node.list.tN = document.createElement(node.nodeName);
				}
				u.sc(node.list.tN, "target " + node.className);
				u.as(node.list.tN, "height", u.actualHeight(node)+"px");
				u.as(node.list.tN, "width", u.actualWidth(node)+"px");
				u.as(node.list.tN, "opacity", node.start_opacity - 0.5);
				node.list.tN.innerHTML = node.innerHTML;
				u.as(node, "width", u.actualWidth(node) + "px");
				u.as(node, "opacity", node.start_opacity - 0.3);
				u.as(node.list, "width", u.actualWidth(node.list) + "px");
				u.as(node.list, "height", u.actualHeight(node.list) + "px");
				node.mouse_ox = u.eventX(event) - u.absX(node);
				node.mouse_oy = u.eventY(event) - u.absY(node);
				u.as(node, "position", "absolute");
				u.e.addMoveEvent(document.body , u.s._drag);
				u.e.addEndEvent(document.body , u.s._drop);
				document.body.list = node.list;
				u.as(node, "left", (u.eventX(event) - node.rel_ox) - node.mouse_ox+"px");
				u.as(node, "top", (u.eventY(event) - node.rel_oy) - node.mouse_oy+"px");
				u.ac(node, "dragged");
				node.list.insertBefore(node.list.tN, node);
				if(typeof(node.list.picked) == "function") {
					node.list.picked(event);
				}
			}
		}
	}
	this._drag = function(event) {
		var i, node;
		u.e.kill(event);
		if(this.list.dragged) {
			var d_left = u.eventX(event) - this.list.dragged.mouse_ox;
			var d_top = u.eventY(event) - this.list.dragged.mouse_oy;
			if(u.scrollY() >= d_top && 0) {
				if(u.browserH() < u.htmlH()) {
					u.as(this.list.dragged, "position", "fixed");
					u.as(this.list.dragged, "left", d_left - this.list.dragged.rel_ox+"px");
					u.as(this.list.dragged, "top", 0);
					u.as(this.list.dragged, "bottom", "auto");
					this.list.scroll_speed = Math.round((d_top - u.scrollY()));
					this.list._scrollWindowY();
				}
			}
			else if(u.browserH() + u.scrollY() < d_top + this.list.dragged.offsetHeight && 0) {
				if(u.browserH() < u.htmlH()) {
					u.as(this.list.dragged, "position", "fixed");
					u.as(this.list.dragged, "left", d_left - this.list.dragged.rel_ox+"px");
					u.as(this.list.dragged, "top", "auto");
					u.as(this.list.dragged, "bottom", 0);
					this.list.scroll_speed = -(Math.round((u.browserH() + u.scrollY() - d_top - this.list.dragged.offsetHeight)));
					this.list._scrollWindowY();
				}
			}
			else {
				var d_center_x = d_left + (this.list.dragged.offsetWidth/2);
				var d_center_y = d_top + (this.list.dragged.offsetHeight/2);
				u.as(this.list.dragged, "position", "absolute");
				u.as(this.list.dragged, "left", d_left - this.list.dragged.rel_ox+"px");
				u.as(this.list.dragged, "top", d_top - this.list.dragged.rel_oy+"px");
				u.as(this.list.dragged, "bottom", "auto");
				for(i = 0; node = this.list.sortable_nodes[i]; i++) {
					if(node != this.list.dragged && node != this.list.tN) {
						if(this.list.list_type == "vertical") {
							var o_top = u.absY(node);
							var o_height = node.offsetHeight; 
						 	if(o_top < d_center_y && (o_top + o_height) > d_center_y) {
								if(o_top < d_center_y && o_top + (o_height/2) > d_center_y) {
									this.list.insertBefore(this.list.tN, node);
								}
								else {
									var next = u.ns(node);
									if(next) {
										this.list.insertBefore(this.list.tN, next);
									}
									else {
										this.list.appendChild(this.list.tN);
									}
								}
								break;
							}
						}
						else {
							var o_left = u.absX(node);
							var o_top = u.absY(node);
							var o_width = node.offsetWidth;
							var o_height = node.offsetHeight;
						 	if(o_left < d_center_x && (o_left + o_width) > d_center_x && o_top < d_center_y && (o_top + o_height) > d_center_y) {
								if(o_left < d_center_x && o_left + (o_width/2) > d_center_x) {
									this.list.insertBefore(this.list.tN, node);
								}
								else {
									var next = u.ns(node);
									if(next) {
										this.list.insertBefore(this.list.tN, next);
									}
									else {
										this.list.appendChild(this.list.tN);
									}
								}
								break;
							}
						}
					}
				}
			}
		}
		if(typeof(this.list.dragged) == "function") {
			this.list.dragged(event);
		}
	}
	this._drop = function(event) {
		u.e.kill(event);
		u.e.removeMoveEvent(document.body , u.s._drag);
		u.e.removeEndEvent(document.body , u.s._drop);
		this.list.tN = this.list.replaceChild(this.list.dragged, this.list.tN);
		u.as(this.list.dragged, "position", this.list.dragged.start_position);
		u.as(this.list.dragged, "opacity", this.list.dragged.start_opacity);
		u.as(this.list.dragged, "left", "");
		u.as(this.list.dragged, "top", "");
		u.as(this.list.dragged, "bottom", "");
		u.as(this.list.dragged, "width", "");
		u.as(this.list, "width", "");
		u.as(this.list, "height", "");
		u.rc(this.list.dragged, "dragged");
		this.list.dragged = false;
		var target_class = u.getIJ(this.list, "targets");
		if(!target_class) {
			this.list.sortable_nodes = u.qsa("li", this.list);
		}
		else {
			this.list.sortable_nodes = u.qsa("."+target_class, this.list);
		}
		if(typeof(this.list.dropped) == "function") {
			this.list.dropped(event);
		}
	}
}
u.sortable = function(node, options) {
	var callback;
	var draggables;	
	var targets;	
	var sources;
	if(typeof(options) == "object") {
		var argument;
		for(argument in options) {
			switch(argument) {
				case "callback"				: callback				= options[argument]; break;
				case "draggables"			: draggables			= options[argument]; break;
				case "sources"				: sources				= options[argument]; break;
			}
		}
	}
	node._sortablepick = function(event) {
		u.bug("pick:" + u.nodeId(this) + "; "+ u.nodeId(this.d_node) + ";" + u.nodeId(this.d_node.node));
		if(!this.d_node.node._sorting_disabled) {
			u.e.kill(event);
			if(!this.d_node.node._dragged) {
				var d_node = this.d_node.node._dragged = this.d_node;
				u.bug("now dragging:" + u.nodeId(d_node));
				d_node.start_opacity = u.gcs(d_node, "opacity");
				d_node.start_position = u.gcs(d_node, "position");
				d_node.start_width = u.gcs(d_node, "width");
				d_node.start_height = u.gcs(d_node, "height");
				if(!d_node.node.tN) {
					d_node.node.tN = document.createElement(d_node.nodeName);
				}
				u.sc(d_node.node.tN, "target " + d_node.className);
				u.as(d_node.node.tN, "height", u.actualHeight(d_node)+"px");
				u.as(d_node.node.tN, "width", u.actualWidth(d_node)+"px");
				u.as(d_node.node.tN, "opacity", d_node.start_opacity - 0.5);
				d_node.node.tN.innerHTML = d_node.innerHTML;
				u.bug("now dragging:" + u.nodeId(d_node));
				u.as(d_node, "width", u.actualWidth(d_node) + "px");
				u.as(d_node, "opacity", d_node.start_opacity - 0.3);
	// 			
	// 			
	// 
	// 
	// 			
	// 			
	// 
	// 
	// 			
	// 			
	// 
	// 			
				if(typeof(d_node.node.picked) == "function") {
					d_node.node.picked(event);
				}
			}
		}
	}
	node._sortabledrag = function(event) {
		var i, node;
		u.e.kill(event);
		if(this.list.dragged) {
			var d_left = u.eventX(event) - this.list.dragged.mouse_ox;
			var d_top = u.eventY(event) - this.list.dragged.mouse_oy;
			if(u.scrollY() >= d_top && 0) {
				if(u.browserH() < u.htmlH()) {
					u.as(this.list.dragged, "position", "fixed");
					u.as(this.list.dragged, "left", d_left - this.list.dragged.rel_ox+"px");
					u.as(this.list.dragged, "top", 0);
					u.as(this.list.dragged, "bottom", "auto");
					this.list.scroll_speed = Math.round((d_top - u.scrollY()));
					this.list._scrollWindowY();
				}
			}
			else if(u.browserH() + u.scrollY() < d_top + this.list.dragged.offsetHeight && 0) {
				if(u.browserH() < u.htmlH()) {
					u.as(this.list.dragged, "position", "fixed");
					u.as(this.list.dragged, "left", d_left - this.list.dragged.rel_ox+"px");
					u.as(this.list.dragged, "top", "auto");
					u.as(this.list.dragged, "bottom", 0);
					this.list.scroll_speed = -(Math.round((u.browserH() + u.scrollY() - d_top - this.list.dragged.offsetHeight)));
					this.list._scrollWindowY();
				}
			}
			else {
				var d_center_x = d_left + (this.list.dragged.offsetWidth/2);
				var d_center_y = d_top + (this.list.dragged.offsetHeight/2);
				u.as(this.list.dragged, "position", "absolute");
				u.as(this.list.dragged, "left", d_left - this.list.dragged.rel_ox+"px");
				u.as(this.list.dragged, "top", d_top - this.list.dragged.rel_oy+"px");
				u.as(this.list.dragged, "bottom", "auto");
				for(i = 0; node = this.list.sortable_nodes[i]; i++) {
					if(node != this.list.dragged && node != this.list.tN) {
						if(this.list.list_type == "vertical") {
							var o_top = u.absY(node);
							var o_height = node.offsetHeight; 
						 	if(o_top < d_center_y && (o_top + o_height) > d_center_y) {
								if(o_top < d_center_y && o_top + (o_height/2) > d_center_y) {
									this.list.insertBefore(this.list.tN, node);
								}
								else {
									var next = u.ns(node);
									if(next) {
										this.list.insertBefore(this.list.tN, next);
									}
									else {
										this.list.appendChild(this.list.tN);
									}
								}
								break;
							}
						}
						else {
							var o_left = u.absX(node);
							var o_top = u.absY(node);
							var o_width = node.offsetWidth;
							var o_height = node.offsetHeight;
						 	if(o_left < d_center_x && (o_left + o_width) > d_center_x && o_top < d_center_y && (o_top + o_height) > d_center_y) {
								if(o_left < d_center_x && o_left + (o_width/2) > d_center_x) {
									this.list.insertBefore(this.list.tN, node);
								}
								else {
									var next = u.ns(node);
									if(next) {
										this.list.insertBefore(this.list.tN, next);
									}
									else {
										this.list.appendChild(this.list.tN);
									}
								}
								break;
							}
						}
					}
				}
			}
		}
		if(typeof(this.list.dragged) == "function") {
			this.list.dragged(event);
		}
	}
	node._sortabledrop = function(event) {
		u.e.kill(event);
		u.e.removeMoveEvent(document.body , this._sortabledrag);
		u.e.removeEndEvent(document.body , this._sortabledrop);
		this.list.tN = this.list.replaceChild(this.list.dragged, this.list.tN);
		u.as(this.list.dragged, "position", this.list.dragged.start_position);
		u.as(this.list.dragged, "opacity", this.list.dragged.start_opacity);
		u.as(this.list.dragged, "left", "");
		u.as(this.list.dragged, "top", "");
		u.as(this.list.dragged, "bottom", "");
		u.as(this.list.dragged, "width", "");
		u.as(this.list, "width", "");
		u.as(this.list, "height", "");
		u.rc(this.list.dragged, "dragged");
		this.list.dragged = false;
		var target_class = u.getIJ(this.list, "targets");
		if(!target_class) {
			this.list.sortable_nodes = u.qsa("li", this.list);
		}
		else {
			this.list.sortable_nodes = u.qsa("."+target_class, this.list);
		}
		if(typeof(this.list.dropped) == "function") {
			this.list.dropped(event);
		}
	}
	var i, j, d_node;
	if(!draggables) {
		node.draggable_nodes = u.qsa("li", node);
	}
	else {
		node.draggable_nodes = u.qsa(draggables, node);
	}
	if(!targets) {
		node.target_nodes = u.qsa("ul", node);
	}
	else {
		node.target_nodes = u.qsa(targets, node);
	}
	if(node.draggable_nodes.length) {
		node.list_type = node.offsetWidth < node.draggable_nodes[0].offsetWidth*2 ? "vertical" : "horizontal";
	}
	for(i = 0; d_node = node.draggable_nodes[i]; i++) {
		d_node.node = node;
		d_node.dragme = true;
		d_node.rel_ox = u.absX(d_node) - u.relX(d_node);
		d_node.rel_oy = u.absY(d_node) - u.relY(d_node);
		d_node.drag = u.qs(".drag", d_node);
		if(!d_node.drag) {
			d_node.drag = d_node;
		}
		d_node.drag.d_node = d_node;
		var drag_children = u.qsa("*", d_node.drag);
		if(drag_children) {
			for(j = 0; child = drag_children[j]; j++) {
				child.d_node = d_node;
			}
		}
		u.e.addStartEvent(d_node.drag , node._sortablepick);
	}
}


/*beta-u-audio.js*/
Util.audioPlayer = function(node) {
	var player;
	if(node) {
		player = u.ae(node, "div", {"class":"audioplayer"});
	}
	else {
		player = document.createElement("div");
		u.ac(player, "audioplayer");
	}
	player.audio = u.ae(player, "audio");
	player.audio.controls = false;
	if(typeof(player.audio.play) == "function") {
		player.load = function(src) {
			if(this.playing) {
				this.stop();
			}
			if(src) {
				this.audio.src = this.correctSource(src);
				this.audio.load();
			}
		}
		player.play = function(position) {
			this.playing = true;
			position = position ? position : 0;
			if(this.audio.src) {
				this.audio.play();
			}
		}
		player.loadAndPlay = function(src, position) {
			this.load(src);
			this.play(position);
		}
		player.pause = function() {
			this.playing = false;
			this.audio.pause();
		}
		player.stop = function() {
			this.pause();
		}
		player._loadstart = function(event) {
			u.removeClass(this.parentNode, "ready")
			u.addClass(this.parentNode, "loading");
		}
		u.e.addEvent(player.audio, "loadstart", player._loadstart);
		player._canplaythrough = function(event) {
			u.removeClass(this.parentNode, "loading")
			u.addClass(this.parentNode, "ready");
		}
		u.e.addEvent(player.audio, "canplaythrough", player._canplaythrough);
		player._loadeddata = function(event) {
			this.parentNode.videoLoaded = true;
			if(typeof(this.parentNode.loadeddata) == "function") {
				this.parentNode.loadeddata(event);
			}
		}
		u.e.addEvent(player.audio, "loadeddata", player._loadeddata);
		player._ended = function(event) {
			u.rc(this, "playing|paused");
			if(typeof(this.parentNode.ended) == "function") {
				this.parentNode.ended(event);
			}
		}
		u.e.addEvent(player.audio, "ended", player._ended);
		player._loadedmetadata = function(event) {
			u.bug("1", "loadedmetadata:duration:" + this.duration);
			u.bug("1", "loadedmetadata:currentTime:" + this.currentTime);
		}
	}
	else if(typeof(u.audioPlayerFallback) == "function") {
		player.removeChild(player.video);
		player = u.audioPlayerFallback(player);
	}
	else {
		player.load = function() {}
		player.play = function() {}
		player.loadAndPlay = function() {}
		player.pause = function() {}
		player.stop = function() {}
	}
	player.correctSource = function(src) {
		src = src.replace(/.mp3|.ogg|.wav/, "");
		if(this.audio.canPlayType("audio/mpeg")) {
			return src+".mp3";
		}
		else if(this.audio.canPlayType("audio/ogg")) {
			return src+".ogg";
		}
		else {
			return src+".wav";
		}
	}
	return player;
}

/*beta-u-video.js*/
Util.videoPlayer = function(_options) {
	var player;
	// 
		player = document.createElement("div");
		u.ac(player, "videoplayer");
	player.ff_skip = 2;
	player.rw_skip = 2;
	player._default_playpause = false;
	player._default_zoom = false;
	player._default_volume = false;
	player._default_search = false;
	if(typeof(_options) == "object") {
		var argument;
		for(argument in _options) {
			switch(argument) {
				case "playpause"	: player._default_playpause		= _options[argument]; break;
			}
		}
	}
	player.flash = false;
	player.video = u.ae(player, "video");
	if(typeof(player.video.play) == "function") {
		player.load = function(src, _options) {
			player._controls_playpause = player._default_playpause;
			player._controls_zoom = player._default_zoom;
			player._controls_volume = player._default_volume;
			player._controls_search = player._default_search;
			if(typeof(_options) == "object") {
				var argument;
				for(argument in _options) {
					switch(argument) {
						case "playpause"	: player._controls_playpause	= _options[argument]; break;
					}
				}
			}
			this.setup();
			if(this.className.match("/playing/")) {
				this.stop();
			}
			if(src) {
				this.video.src = this.correctSource(src);
				this.video.load();
				this.video.controls = false;
			}
		}
		player.play = function(position) {
			if(this.video.currentTime && position !== undefined) {
				this.video.currentTime = position;
			}
			if(this.video.src) {
				this.video.play();
			}
		}
		player.loadAndPlay = function(src, _options) {
			var position = 0;
			if(typeof(_options) == "object") {
				var argument;
				for(argument in _options) {
					switch(argument) {
						case "position"		: position		= _options[argument]; break;
					}
				}
			}
			this.load(src, _options);
			this.play(position);
		}
		player.pause = function() {
			this.video.pause();
		}
		player.stop = function() {
			this.video.pause();
			if(this.video.currentTime) {
				this.video.currentTime = 0;
			}
		}
		player.ff = function() {
			if(this.video.src && this.video.currentTime && this.videoLoaded) {
				this.video.currentTime = (this.video.duration - this.video.currentTime >= this.ff_skip) ? (this.video.currentTime + this.ff_skip) : this.video.duration;
				this.video._timeupdate();
			}
		}
		player.rw = function() {
			if(this.video.src && this.video.currentTime && this.videoLoaded) {
				this.video.currentTime = (this.video.currentTime >= this.rw_skip) ? (this.video.currentTime - this.rw_skip) : 0;
				this.video._timeupdate();
			}
		}
		player.togglePlay = function() {
			if(this.className.match(/playing/g)) {
				this.pause();
			}
			else {
				this.play();
			}
		}
		player.setup = function() {
			if(u.qs("video", this)) {
				var video = this.removeChild(this.video);
				delete video;
			}
			this.video = u.ie(this, "video");
			this.video.player = this;
			this.setControls();
			this.currentTime = 0;
			this.duration = 0;
			this.videoLoaded = false;
			this.metaLoaded = false;
			this.video._loadstart = function(event) {
				u.ac(this.player, "loading");
				if(typeof(this.player.loading) == "function") {
					this.player.loading(event);
				}
			}
			u.e.addEvent(this.video, "loadstart", this._loadstart);
			this.video._canplaythrough = function(event) {
				u.rc(this.player, "loading");
				if(typeof(this.player.canplaythrough) == "function") {
					this.player.canplaythrough(event);
				}
			}
			u.e.addEvent(this.video, "canplaythrough", this.video._canplaythrough);
			this.video._playing = function(event) {
				u.rc(this.player, "loading|paused");
				u.ac(this.player, "playing");
				if(typeof(this.player.playing) == "function") {
					this.player.playing(event);
				}
			}
			u.e.addEvent(this.video, "playing", this.video._playing);
			this.video._paused = function(event) {
				u.rc(this.player, "playing|loading");
				u.ac(this.player, "paused");
				if(typeof(this.player.paused) == "function") {
					this.player.paused(event);
				}
			}
			u.e.addEvent(this.video, "pause", this.video._paused);
			this.video._stalled = function(event) {
				u.rc(this.player, "playing|paused");
				u.ac(this.player, "loading");
				if(typeof(this.player.stalled) == "function") {
					this.player.stalled(event);
				}
			}
			u.e.addEvent(this.video, "stalled", this.video._paused);
			this.video._ended = function(event) {
				u.rc(this.player, "playing|paused");
				if(typeof(this.player.ended) == "function") {
					this.player.ended(event);
				}
			}
			u.e.addEvent(this.video, "ended", this.video._ended);
			this.video._loadedmetadata = function(event) {
				this.player.duration = this.duration;
				this.player.currentTime = this.currentTime;
				this.player.metaLoaded = true;
				if(typeof(this.player.loadedmetadata) == "function") {
					this.player.loadedmetadata(event);
				}
			}
			u.e.addEvent(this.video, "loadedmetadata", this.video._loadedmetadata);
			this.video._loadeddata = function(event) {
				this.player.videoLoaded = true;
				if(typeof(this.player.loadeddata) == "function") {
					this.player.loadeddata(event);
				}
			}
			u.e.addEvent(this.video, "loadeddata", this.video._loadeddata);
			this.video._timeupdate = function(event) {
				this.player.currentTime = this.currentTime;
				if(typeof(this.player.timeupdate) == "function") {
					this.player.timeupdate(event);
				}
			}
			u.e.addEvent(this.video, "timeupdate", this.video._timeupdate);
		}
	}
	else if(typeof(u.videoPlayerFallback) == "function") {
		player.removeChild(player.video);
		player = u.videoPlayerFallback(player);
	}
	player.correctSource = function(src) {
		src = src.replace(/\?[^$]+/, "");
		src = src.replace(/\.m4v|\.mp4|\.webm|\.ogv|\.3gp|\.mov/, "");
		if(this.flash) {
			return src+".mp4";
		}
		else if(this.video.canPlayType("video/mp4")) {
			return src+".mp4";
		}
		else if(this.video.canPlayType("video/ogg")) {
			return src+".ogv";
		}
		else if(this.video.canPlayType("video/3gpp")) {
			return src+".3gp";
		}
		else {
			return src+".mov";
		}
	}
	player.setControls = function() {
		if(this.showControls) {
			u.e.removeEvent(this, "mousemove", this.showControls);
		}
		if(this._controls_playpause || this._controls_zoom || this._controls_volume || this._controls_search) {
			if(!this.controls) {
				this.controls = u.ae(this, "div", {"class":"controls"});
				this.hideControls = function() {
					this.t_controls = u.t.resetTimer(this.t_controls);
					u.a.transition(this.controls, "all 0.3s ease-out");
					u.a.setOpacity(this.controls, 0);
				}
				this.showControls = function() {
					if(this.t_controls) {
						this.t_controls = u.t.resetTimer(this.t_controls);
					}
					else {
						u.a.transition(this.controls, "all 0.5s ease-out");
						u.a.setOpacity(this.controls, 1);
					}
					this.t_controls = u.t.setTimer(this, this.hideControls, 1500);
				}
			}
			else {
				u.as(this.controls, "display", "block");
			}
			if(this._controls_playpause) {
				if(!this.controls.playpause) {
					this.controls.playpause = u.ae(this.controls, "a", {"class":"playpause"});
					this.controls.playpause.player = this;
					u.e.click(this.controls.playpause);
					this.controls.playpause.clicked = function(event) {
						this.player.togglePlay();
					}
				}
				else {
					u.as(this.controls.playpause, "display", "block");
				}
			}
			else if(this.controls.playpause) {
				u.as(this.controls.playpause, "display", "none");
			}
			if(this._controls_zoom && !this.controls.zoom) {}
			else if(this.controls.zoom) {}
			if(this._controls_volume && !this.controls.volume) {}
			else if(this.controls.volume) {}
			if(this._controls_search && !this.controls.search) {}
			else if(this.controls.search) {}
			u.e.addEvent(this, "mousemove", this.showControls);
		}
		else if(this.controls) {
			u.as(this.controls, "display", "none");
		}
	}
	return player;
}

/*beta-u-keys.js*/
Util.Keys = u.k = new function() {
	this.shortcuts = new Array();
	this.onkeydownCatcher = function(event) {
		u.k.catchKey(event);
	}
	this.addKey = function(key, action) {
		if(!this.shortcuts.length) {
			u.e.addEvent(document, "keydown", this.onkeydownCatcher);
		}
		if(!this.shortcuts[key.toString().toUpperCase()]) {
			this.shortcuts[key.toString().toUpperCase()] = new Array();
		}
		this.shortcuts[key.toString().toUpperCase()].push(action);
	}
	this.catchKey = function(event) {
		var action, i, key;
		event = event ? event : window.event;
		key = String.fromCharCode(event.keyCode);
		u.bug("e:" + key + ":"+event.keyCode+":" + this.shortcuts.length)
		if((event.ctrlKey || event.metaKey) && this.shortcuts[key]) {
			u.e.kill(event);
			action = this.shortcuts[key].pop();
				if(typeof(action) == "object") {
					action.clicked();
				}
				else if(typeof(action) == "function") {
					action();
				}
				else {
					eval(action);
				}
		}
		if(event.keyCode == 27 && this.shortcuts["ESC"]) {
			u.e.kill(event);
			action = this.shortcuts["ESC"].pop();
				u.bug("esc:"+action + "::" + u.nodeId(action) + ", " + typeof(action));
				if(typeof(action) == "object") {
					action.clicked();
				}
				else if(typeof(action) == "function") {
					action();
				}
				else {
					eval(action);
				}
		}
	}
}


/*i-page.js*/
u.bug_console_only = true;
Util.Objects["page"] = new function() {
	this.init = function(page) {
		var i, node;
		page.hN = u.qs("#header", page);
		page.cN = u.qs("#content", page);
		page.nN = u.qs("#navigation", page);
		if(page.nN) {
			page.nN = page.hN.appendChild(page.nN);
		}
		page.fN = u.qs("#footer", page);
		u.notifier(page);
		u.navigation(page);
		u.addClass(page, "ready");
	}
}
u.e.addDOMReadyEvent(u.init)


/*i-form.js*/
Util.Objects["addPrices"] = new function() {
	this.init = function(div) {
		var form = u.qs("form", div);
		u.f.init(form);
		var i, field, actions;
		form.submitted = function(event) {
			this.response = function(response) {
				if(response.cms_status == "success") {
					location.reload();
				}
				else {
					alert(response.cms_message[0]);
				}
			}
			u.request(this, this.action, {"method":"post", "params":u.f.getParams(this)});
		}
	}
}
Util.Objects["formAddPrices"] = new function() {
	this.init = function(form) {
		u.f.init(form);
		var i, field, actions;
		field = form.fields["prices"].field;
		actions = u.qs(".actions", form);
		actions = field.insertBefore(actions, u.ns(field._input));
		form.submitted = function(event) {
			this.response = function(response) {
				if(response.cms_status == "success") {
					location.reload();
				}
				else {
					alert(response.cms_message[0]);
				}
			}
			u.request(this, this.action, {"method":"post", "params":u.f.getParams(this)});
		}
	}
}
Util.Objects["formAddTags"] = new function() {
	this.init = function(form) {
		var i, field, actions;
		u.f.init(form);
		field = form.fields["tags"].field;
		actions = u.qs(".actions", form);
		actions = field.insertBefore(actions, u.ns(field._input));
		form.submitted = function(event) {
			this.response = function(response) {
				if(response.cms_status == "success") {
					location.reload();
				}
				else {
					alert(response.cms_message[0]);
				}
			}
			u.request(this, this.action, {"method":"post", "params":u.f.getParams(this)});
		}
	}
}
Util.Objects["addMedia"] = new function() {
	this.init = function(div) {
		var form = u.qs("form.upload", div);
		u.f.init(form);
		var file_input = u.qs("input[type=file]", form);
		file_input.changed = function() {
			this.response = function(response) {
				response = JSON.parse(this.responseText);
				if(response.cms_status == "success" && response.cms_object) {
					location.reload();
				}
				else if(response.cms_message) {
					page.notify(response.cms_message);
				}
			}
			this.responseError = function(response) {
				response = JSON.parse(this.responseText);
				if(response.cms_status == "success") {
					location.reload();
				}
				else {
					alert(response.cms_message[0]);
				}
			}
			var fd = new FormData();
			var i, file;
			for(i = 0; file = this.files[i]; i++) {
				fd.append(this.name+"["+i+"]", file);
			}
			this.HTTPRequest = u.createRequestObject();
			this.HTTPRequest.node = this;
			u.e.addEvent(this.HTTPRequest, "load", this.response);
			u.e.addEvent(this.HTTPRequest, "error", this.responseError);
			this.HTTPRequest.open("POST", this.form.action);
			this.HTTPRequest.send(fd);
		}
		div.media_list = u.qs("ul.media", div.media_list);
		if(u.hc(div, "sortable") && div.media_list) {
			u.s.sortable(div.media_list);
			div.media_list.picked = function() {}
			div.media_list.dropped = function() {
				var url = this.getAttribute("data-save-order");
				this.nodes = u.qsa("li.media", this);
				for(i = 0; node = this.nodes[i]; i++) {
					url += "/"+u.cv(node, "media_id");
				}
				this.response = function(response) {
					page.notify(response.cms_message);
				}
				u.request(this, url);
			}
		}
	}
}
Util.Objects["deleteMedia"] = new function() {
	this.init = function(form) {
		u.f.init(form);
		var bn_delete = u.qs("input.delete", form);
		if(bn_delete) {
			bn_delete.org_value = bn_delete.value;
			u.e.click(bn_delete);
			bn_delete.restore = function(event) {
				this.value = this.org_value;
				u.rc(this, "confirm");
			}
			bn_delete.inputStarted = function(event) {
				u.e.kill(event);
			}
			bn_delete.clicked = function(event) {
				u.e.kill(event);
				if(!u.hc(this, "confirm")) {
					u.ac(this, "confirm");
					this.value = "Confirm";
					this.t_confirm = u.t.setTimer(this, this.restore, 3000);
				}
				else {
					u.t.resetTimer(this.t_confirm);
					this.response = function(response) {
						if(response.cms_status == "success") {
							if(response.cms_object && response.cms_object.constraint_error) {
								page.notify(response.cms_message);
								this.value = this.org_value;
								u.ac(this, "disabled");
							}
							else {
								location.reload();
							}
						}
						else {
							page.notify(response.cms_message);
						}
					}
					u.request(this, this.form.action, {"method":"post", "params" : u.f.getParams(this.form)});
				}
			}
		}
	}
}


/*i-defaultlist.js*/
Util.Objects["defaultList"] = new function() {
	this.init = function(div) {
		u.bug("init defaultList 22")
		var i, node;
		div.list = u.qs("ul.items", div);
		if(!div.list) {
			div.list = u.ae(div, "ul", {"class":"items"});
		}
		div.nodes = u.qsa("li.item", div);
		div.scrolled = function() {
			var scroll_y = u.scrollY()
			var browser_h = u.browserH();
			var i, node, abs_y;
			for(i = 0; node = this.nodes[i]; i++) {
				abs_y = u.absY(node);
				if(!node._ready && abs_y - 200 < scroll_y+browser_h && abs_y + 200 > scroll_y) {
					this.buildNode(node);
				}
			}
		}
		div._scrollHandler = function() {
			var div = u.qs(".scrollingListHandler");
			u.t.resetTimer(div.t_scroll);
			div.scrolled();
		}
		u.ac(div, "scrollingListHandler");
		u.e.addEvent(window, "scroll", div._scrollHandler);
		div.buildNode = function(node) {
			u.bug("build node")
			node._item_id = u.cv(node, "item_id");
			node._variant = u.cv(node, "variant");
			node._actions = u.qsa(".actions li", node);
			var i, action, form, bn_detele, form_disable, form_enable;
			for(i = 0; action = node._actions[i]; i++) {
				if(u.hc(action, "status")) {
					if(!action.childNodes.length) {
						form_disable = u.f.addForm(action, {"action":"/janitor/admin/items/status/"+node._item_id+"/0", "class":"disable"});
						u.f.addAction(form_disable, {"value":"Disable", "class":"button status"});
						form_enable = u.f.addForm(action, {"action":"/janitor/admin/items/status/"+node._item_id+"/1", "class":"enable"});
						u.f.addAction(form_enable, {"value":"Enable", "class":"button status"});
					}
					else {
						form_disable = u.qs("form.disable", action);
						form_enable = u.qs("form.enable", action);
					}
					if(form_disable && form_enable) {
						u.f.init(form_disable);
						form_disable.submitted = function() {
							this.response = function(response) {
								page.notify(response.cms_message);
								if(response.cms_status == "success") {
									u.ac(this.parentNode, "disabled");
									u.rc(this.parentNode, "enabled");
								}
							}
							u.request(this, this.action);
						}
						u.f.init(form_enable);
						form_enable.submitted = function() {
							this.response = function(response) {
								page.notify(response.cms_message);
								if(response.cms_status == "success") {
									u.rc(this.parentNode, "disabled");
									u.ac(this.parentNode, "enabled");
								}
							}
							u.request(this, this.action);
						}
					}
				}
				else if(u.hc(action, "delete")) {
					if(!action.childNodes.length) {
						form = u.f.addForm(action, {"action":"/janitor/admin/items/delete/"+node._item_id, "class":"delete"});
						form.node = node;
						bn_delete = u.f.addAction(form, {"value":"Delete", "class":"button delete", "name":"delete"});
					}
					else {
						form = u.qs("form", action);
						form.node = node;
					}
					if(form) {
						u.f.init(form);
						form.restore = function(event) {
							this.actions["delete"].value = "Delete";
							u.rc(this.actions["delete"], "confirm");
						}
						form.submitted = function() {
							if(!u.hc(this.actions["delete"], "confirm")) {
								u.ac(this.actions["delete"], "confirm");
								this.actions["delete"].value = "Confirm";
								this.t_confirm = u.t.setTimer(this, this.restore, 3000);
							}
							else {
								u.t.resetTimer(this.t_confirm);
								this.response = function(response) {
									page.notify(response.cms_message);
									if(response.cms_status == "success") {
										if(response.cms_object && response.cms_object.constraint_error) {
											this.value = "Delete";
											u.ac(this, "disabled");
										}
										else {
											this.node.parentNode.removeChild(this.node);
										}
									}
								}
								u.request(this, this.action);
							}
						}
					}
				}
			}
			node._image = u.cv(node, "image");
			node._width = u.cv(node, "width");
			node._height = u.cv(node, "height");
			if(node._image && node._width && node._height) {
				u.ac(node, "image");
				node._image_src = "/images/"+node._item_id+"/"+(node._variant ? node._variant+"/" : "")+node._width+"x"+node._height+"."+node._image;
			}
			else if(node._image && node._width) {
				u.ac(node, "image");
				node._image_src = "/images/"+node._item_id+"/"+(node._variant ? node._variant+"/" : "")+node._width+"x."+node._image;
			}
			else if(node._image && node._height) {
				u.ac(node, "image");
				node._image_src = "/images/"+node._item_id+"/"+(node._variant ? node._variant+"/" : "")+"x"+node._height+"."+node._image;
			}
			if(node._image_src) {
				u.as(node, "backgroundImage", "url("+node._image_src+")");
			}
			node._audio = u.cv(node, "audio");
			if(node._audio) {
				u.ac(node, "audio");
				if(!page.audioplayer) {
					page.audioplayer = u.audioPlayer();
				}
				var audio = u.ie(node, "div", {"class":"audio"});
				audio.scene = this;
				audio.url = "/audios/"+node._item_id+"/128."+node._audio;
				u.e.click(audio);
				audio.clicked = function(event) {
					if(!u.hc(this.parentNode, "playing")) {
						var node, i;
						for(i = 0; node = this.scene.nodes[i]; i++) {
							u.rc(node, "playing");
						}
						page.audioplayer.loadAndPlay(this.url);
						u.ac(this.parentNode, "playing");
					}
					else {
						page.audioplayer.stop();
						u.rc(this.parentNode, "playing");
					}
				}
			}
			node._video = u.cv(node, "video");
			if(node._video) {
			}
			node._ready = true;
		}
		if(u.hc(div, "taggable")) {
			u.bug("init taggable")
			div.tagsResponse = function(response) {
				if(response.cms_status == "success" && response.cms_object) {
					this.all_tags = response.cms_object;
					var i, node, tag, j, bn_add, context, value;
					for(i = 0; node = this.nodes[i]; i++) {
						node._tags = u.qs("ul.tags", node);
						if(!node._tags) {
							node._tags = u.ae(node, "ul", {"class":"tags"});
						}
						node._bn_add = u.ae(node._tags, "li", {"class":"add","html":"+"});
						node._bn_add.div = this;
						node._bn_add.node = node;
						u.e.click(node._bn_add);
						node._bn_add.clicked = function() {
							this.div._taggableNode(this.node);
						}
					}
				}
				else {
					page.notify(response.cms_message);
				}
			}
			u.request(div, "/janitor/admin/items/tags", {"callback":"tagsResponse"});
			div._taggableNode = function(node) {
				u.ac(node, "addtags");
				node._bn_add.innerHTML = "-";
				node._bn_add.clicked = function() {
					this.innerHTML = "+";
					u.rc(this.node, "addtags");
					this.node._tag_options.parentNode.removeChild(this.node._tag_options);
					this.clicked = function() {
						this.div._taggableNode(this.node);
					}
				}
				node._tag_options = u.ae(node, "div", {"class":"tagoptions"});
				node._tag_options._field = u.ae(node._tag_options, "div", {"class":"field"});
				node._tag_options._tagfilter = u.ae(node._tag_options._field, "input", {"class":"filter ignoreinput"});
				node._tag_options._tagfilter.node = node;
				node._tag_options._tagfilter.onkeyup = function() {
					if(this.node._new_tags) {
						var tags = u.qsa(".tag", this.node._new_tags);
						var i, tag;
						for(i = 0; tag = tags[i]; i++) {
							if(tag.textContent.toLowerCase().match(this.value.toLowerCase())) {
								u.as(tag, "display", "inline-block");
							}
							else {
								u.as(tag, "display", "none");
							}
						}
					}
				}
				node._new_tags = u.ae(node._tag_options, "ul", {"class":"tags"});
				var itemTags = u.qsa("li:not(.add)", node._tags);
				var usedTags = {};
				for(j = 0; tag = itemTags[j]; j++) {
					tag._context = u.qs(".context", tag).innerHTML;
					tag._value = u.qs(".value", tag).innerHTML;
					if(!usedTags[tag._context]) {
						usedTags[tag._context] = {}
					}
					if(!usedTags[tag._context][tag._value]) {
						usedTags[tag._context][tag._value] = tag;
					}
				}
				for(tag in this.all_tags) {
					context = this.all_tags[tag].context;
					value = this.all_tags[tag].value.replace(/ & /, " &amp; ");
					if(usedTags && usedTags[context] && usedTags[context][value]) {
						tag_node = usedTags[context][value];
					}
					else {
						tag_node = u.ae(node._new_tags, "li", {"class":"tag"});
						tag_node._context = context;
						tag_node._value = value;
						u.ae(tag_node, "span", {"class":"context", "html":tag_node._context});
						u.ae(tag_node, "span", {"class":"value", "html":tag_node._value});
					}
					tag_node.new_tags = this;
					tag_node._id = this.all_tags[tag].id;
					tag_node.node = node;
					u.e.click(tag_node);
					tag_node.clicked = function() {
						if(u.hc(this.node, "addtags")) {
							if(this.parentNode == this.node._tags) {
								this.response = function(response) {
									if(response.cms_status == "success") {
										u.ae(this.node._new_tags, this);
									}
									page.notify(response.cms_message);
								}
								u.request(this, "/janitor/admin/items/tags/delete/"+this.node._item_id+"/" + this._id);
							}
							else {
								this.response = function(response) {
									if(response.cms_status == "success") {
										u.ie(this.node._tags, this);
									}
									page.notify(response.cms_message);
								}
								u.request(this, "/janitor/admin/items/update/"+this.node._item_id, {"method":"post", "params":"tags="+this._id});
							}
						}
					}
				}
			}
		}
		if(u.hc(div, "filters")) {
			div._filter = u.ie(div, "div", {"class":"filter"});
			var i, node;
			for(i = 0; node = div.nodes[i]; i++) {
				node._c = node.textContent.toLowerCase();
			}
			div._filter._field = u.ae(div._filter, "div", {"class":"field"});
			u.ae(div._filter._field, "label", {"html":"Filter"});
			div._filter._input = u.ae(div._filter._field, "input", {"class":"filter ignoreinput"});
			div._filter._input._div = div;
			div._filter._input.onkeydown = function() {
				u.t.resetTimer(this._div.t_filter);
			}
			div._filter._input.onkeyup = function() {
				this._div.t_filter = u.t.setTimer(this._div, this._div.filter, 500);
				u.ac(this._div._filter, "filtering");
			}
			div.filter = function() {
				var i, node;
				if(this._current_filter != this._filter._input.value.toLowerCase()) {
					this._current_filter = this._filter._input.value.toLowerCase();
					for(i = 0; node = this.nodes[i]; i++) {
						if(node._c.match(this._current_filter)) {
							u.as(node, "display", "block", false);
						}
						else {
							u.as(node, "display", "none", false);
						}
					}
				}
				u.rc(this._filter, "filtering");
				this.scrolled();
			}
		}
		if(u.hc(div, "sortable")) {
			u.s.sortable(div.list);
			div.list.picked = function() {}
			div.list.dropped = function() {
				var url = this.getAttribute("data-save-order");
				this.nodes = u.qsa("li.item", this);
				for(i = 0; node = this.nodes[i]; i++) {
					url += "/"+u.cv(node, "id");
				}
				this.response = function(response) {
					page.notify(response.cms_message);
				}
				u.request(this, url);
			}
		}
		div.scrolled();
	}
}


/*i-defaultedit.js*/
Util.Objects["defaultEdit"] = new function() {
	this.init = function(div) {
		div._item_id = u.cv(div, "item_id");
		var form = u.qs("form", div);
		u.f.init(form);
		form.actions["cancel"].clicked = function(event) {
			location.href = this.url;
		}
		form.submitted = function(iN) {
			this.response = function(response) {
				page.notify(response.cms_message);
			}
			u.request(this, this.action, {"method":"post", "params" : u.f.getParams(this)});
		}
	}
}

/*i-defaulteditstatus.js*/
Util.Objects["defaultEditStatus"] = new function() {
	this.init = function(node) {
		node._item_id = u.cv(node, "item_id");
		var action = u.qs("li.status");
		if(action) {
			if(!action.childNodes.length) {
				form_disable = u.f.addForm(action, {"action":"/janitor/admin/items/disable/"+node._item_id, "class":"disable"});
				u.f.addAction(form_disable, {"value":"Disable", "class":"button status"});
				form_enable = u.f.addForm(action, {"action":"/janitor/admin/items/enable/"+node._item_id, "class":"enable"});
				u.f.addAction(form_enable, {"value":"Enable", "class":"button status"});
			}
			else {
				form_disable = u.qs("form.disable", action);
				form_enable = u.qs("form.enable", action);
			}
			if(form_disable && form_enable) {
				u.f.init(form_disable);
				form_disable.submitted = function() {
					this.response = function(response) {
						page.notify(response.cms_message);
						if(response.cms_status == "success") {
							u.ac(this.parentNode, "disabled");
							u.rc(this.parentNode, "enabled");
						}
					}
					u.request(this, this.action);
				}
				u.f.init(form_enable);
				form_enable.submitted = function() {
					this.response = function(response) {
						page.notify(response.cms_message);
						if(response.cms_status == "success") {
							u.rc(this.parentNode, "disabled");
							u.ac(this.parentNode, "enabled");
						}
					}
					u.request(this, this.action);
				}
			}
		}
	}
}

/*i-defaulteditactions.js*/
Util.Objects["defaultEditActions"] = new function() {
	this.init = function(node) {
		node._item_id = u.cv(node, "item_id");
		var cancel = u.qs("li.cancel a");
		var action = u.qs("li.delete");
		if(action && cancel && cancel.href) {
			if(!action.childNodes.length) {
				form = u.f.addForm(action, {"action":"/janitor/admin/items/delete/"+node._item_id, "class":"delete"});
				form.node = node;
				bn_delete = u.f.addAction(form, {"value":"Delete", "class":"button delete", "name":"delete"});
			}
			else {
				form = u.qs("form", action);
			}
			if(form) {
				u.f.init(form);
				form.cancel_url = cancel.href;
				form.restore = function(event) {
					this.actions["delete"].value = "Delete";
					u.rc(this.actions["delete"], "confirm");
				}
				form.submitted = function() {
					if(!u.hc(this.actions["delete"], "confirm")) {
						u.ac(this.actions["delete"], "confirm");
						this.actions["delete"].value = "Confirm";
						this.t_confirm = u.t.setTimer(this, this.restore, 3000);
					}
					else {
						u.t.resetTimer(this.t_confirm);
						this.response = function(response) {
							page.notify(response.cms_message);
							if(response.cms_status == "success") {
								if(response.cms_object && response.cms_object.constraint_error) {
									this.value = "Delete";
									u.ac(this, "disabled");
								}
								else {
									location.href = this.cancel_url;
								}
							}
						}
						u.request(this, this.action);
					}
				}
			}
		}
	}
}

/*i-defaulttags.js*/
Util.Objects["defaultTags"] = new function() {
	this.init = function(div) {
		div.item_id = u.cv(div, "item_id");
		div._tags_form = u.qs("form", div);
		div._tags_form.div = div;
		u.f.init(div._tags_form);
		div._tags_form.fields["tags"].focused = function() {
			this.form.div.enableTagging();
		}
		div._tags_form.fields["tags"].updated = function() {
			if(this.form.div._new_tags) {
				var tags = u.qsa(".tag", this.form.div._new_tags);
				var i, tag;
				for(i = 0; tag = tags[i]; i++) {
					if(tag.textContent.toLowerCase().match(this.value.toLowerCase())) {
						u.as(tag, "display", "inline-block");
					}
					else {
						u.as(tag, "display", "none");
					}
				}
			}
		}
		div._tags_form.submitted = function(iN) {
			this.response = function(response) {
				if(response.cms_status == "success") {
					location.reload();
				}
				else {
					alert(response.cms_message[0]);
				}
			}
			u.request(this, this.action, {"method":"post", "params" : u.f.getParams(this)});
		}
		div._tags = u.qs("ul.tags", div);
		if(!div._tags) {
			div._tags = u.ae(div._tags, "ul", {"class":"tags"});
		}
		div._tags.div = div;
		div._tags.tagsResponse = function(response) {
			if(response.cms_status == "success" && response.cms_object) {
				this._alltags = response.cms_object;
				var bn_add;
				this._bn_add = u.ae(this, "li", {"class":"add","html":"+"});
				this._bn_add.div = this.div;
				u.e.click(this._bn_add);
				this._bn_add.clicked = function() {
					this.div.enableTagging();
				}
			}
			else {
				page.notify(response.cms_message);
			}
		}
		u.request(div._tags, "/janitor/admin/items/tags", {"callback":"tagsResponse"});
		div.enableTagging = function() {
			u.bug("enable tagging")
			if(!this._tag_options) {
				this._tags._bn_add.innerHTML = "-";
				this._tags._bn_add.clicked = function() {
					this.innerHTML = "+";
					u.rc(this.div, "addtags");
					this.div._tag_options.parentNode.removeChild(this.div._tag_options);
					this.div._tag_options = false;
					this.clicked = function() {
						this.div.enableTagging();
					}
				}
				u.ac(this, "addtags");
				this._tag_options = u.ae(this, "div", {"class":"tagoptions"});
				this._new_tags = u.ae(this._tag_options, "ul", {"class":"tags"});
				var usedtags = {};
				var itemTags = u.qsa("li:not(.add)", this._tags);
				var i, tag, context, value;
				for(i = 0; tag = itemTags[i]; i++) {
					tag._context = u.qs(".context", tag).innerHTML;
					tag._value = u.qs(".value", tag).innerHTML;
					if(!usedtags[tag._context]) {
						usedtags[tag._context] = {}
					}
					if(!usedtags[tag._context][tag._value]) {
						usedtags[tag._context][tag._value] = tag;
					}
				}
				for(tag in this._tags._alltags) {
					context = this._tags._alltags[tag].context;
					value = this._tags._alltags[tag].value.replace(/ & /, " &amp; ");
					if(usedtags && usedtags[context] && usedtags[context][value]) {
	// 					
						tag_node = usedtags[context][value];
					}
					else {
						tag_node = u.ae(this._new_tags, "li", {"class":"tag"});
						tag_node._context = context;
						tag_node._value = value;
						u.ae(tag_node, "span", {"class":"context", "html":tag_node._context});
						u.ae(tag_node, "span", {"class":"value", "html":tag_node._value});
					}
					tag_node._id = this._tags._alltags[tag].id;
					tag_node.div = this;
	 				u.e.click(tag_node);
	 				tag_node.clicked = function() {
						if(u.hc(this.div, "addtags")) {
							if(this.parentNode == this.div._tags) {
								this.response = function(response) {
									if(response.cms_status == "success") {
										u.ae(this.div._new_tags, this);
									}
									page.notify(response.cms_message);
								}
								u.request(this, "/janitor/admin/items/tags/delete/"+this.div.item_id+"/" + this._id);
							}
							else {
								this.response = function(response) {
									if(response.cms_status == "success") {
										u.ie(this.div._tags, this)
									}
									page.notify(response.cms_message);
								}
								u.request(this, "/janitor/admin/items/update/"+this.div.item_id, {"method":"post", "params":"tags="+this._id});
							}
						}
					}
				}
			}
		}
	}
}


/*i-form_defaultnew.js*/
Util.Objects["formDefaultNew"] = new function() {
	this.init = function(form) {
		u.f.init(form);
		form.actions["cancel"].clicked = function(event) {
			location.href = this.url;
		}
		form.submitted = function(iN) {
			this.response = function(response) {
				if(response.cms_status == "success" && response.cms_object) {
					location.href = this.actions["cancel"].url.replace("\/list", "/edit/"+response.cms_object.item_id);
				}
				else if(response.cms_message) {
					page.notify(response.cms_message);
				}
			}
			u.request(this, this.action, {"method":"post", "params" : u.f.getParams(this)});
		}
	}
}

/*i-form_defaultstatus.js*/
Util.Objects["formDefaultStatus"] = new function() {
	this.init = function(form) {
		u.f.init(form);
		var bn_status = u.qs("input.status", form);
		if(bn_status) {
			u.e.click(bn_status)
			bn_status.clicked = function(event) {
				u.e.kill(event);
				this.response = function(response) {
					if(response.cms_status == "success") {
						if(response.cms_message.message.length && response.cms_message.message[0].match(/enabled/i)) {
							window.scrollTo(0,0);
						}
						location.reload();
					}
					else {
						alert(response.cms_message[0]);
					}
				}
				u.request(this, this.form.action);
			}
		}
	}
}


/*i-form_defaultdelete.js*/
Util.Objects["formDefaultDelete"] = new function() {
	this.init = function(form) {
		u.f.init(form);
		var bn_delete = u.qs("input.delete", form);
		if(bn_delete) {
			bn_delete.org_value = bn_delete.value;
			u.e.click(bn_delete);
			bn_delete.restore = function(event) {
				this.value = this.org_value;
				u.rc(this, "confirm");
			}
			bn_delete.inputStarted = function(event) {
				u.e.kill(event);
			}
			bn_delete.clicked = function(event) {
				u.e.kill(event);
				if(!u.hc(this, "confirm")) {
					u.ac(this, "confirm");
					this.value = "Confirm";
					this.t_confirm = u.t.setTimer(this, this.restore, 3000);
				}
				else {
					u.t.resetTimer(this.t_confirm);
					this.response = function(response) {
						if(response.cms_status == "success") {
							if(response.cms_object && response.cms_object.constraint_error) {
								page.notify(response.cms_message);
								this.value = this.org_value;
								u.ac(this, "disabled");
							}
							else {
								location.reload();
							}
						}
						else {
							page.notify(response.cms_message);
						}
					}
					u.request(this, this.form.action, {"method":"post", "params" : u.f.getParams(this.form)});
				}
			}
		}
	}
}

/*i-users.js*/
Util.Objects["usernames"] = new function() {
	this.init = function(div) {
		u.bug("div usernames")
		var form = u.qs("form", div);
		u.f.init(form);
		form.submitted = function(iN) {
			this.response = function(response) {
				page.notify(response.cms_message);
			}
			u.request(this, this.action, {"method":"post", "params" : u.f.getParams(this)});
		}
	}
}
Util.Objects["password"] = new function() {
	this.init = function(div) {
		var password_state = u.qs("div.password_state", div);
		var new_password = u.qs("div.new_password", div);
		var a_create = u.qs(".password_missing a");
		var a_change = u.qs(".password_set a");
		a_create.new_password = new_password;
		a_change.new_password = new_password;
		a_create.password_state = password_state;
		a_change.password_state = password_state;
		u.ce(a_create);
		u.ce(a_change);
		a_create.clicked = a_change.clicked = function() {
			u.as(this.password_state, "display", "none");
			u.as(this.new_password, "display", "block");
		}
		var form = u.qs("form", div);
		form.password_state = password_state;
		form.new_password = new_password;
		u.f.init(form);
		form.submitted = function(iN) {
			this.response = function(response) {
				if(response.cms_status == "success") {
					u.ac(this.password_state, "set");
					u.as(this.password_state, "display", "block");
					u.as(this.new_password, "display", "none");
				}
				page.notify(response.cms_message);
			}
			u.request(this, this.action, {"method":"post", "params" : u.f.getParams(this)});
			this.fields["password"].val("");
		}
	}
}
Util.Objects["formAddressNew"] = new function() {
	this.init = function(form) {
		u.f.init(form);
		form.actions["cancel"].clicked = function(event) {
			location.href = this.url;
		}
		form.submitted = function(iN) {
			this.response = function(response) {
				if(response.cms_status == "success" && response.cms_object) {
					location.href = this.actions["cancel"].url.replace("\/list", "/edit/"+response.cms_object.item_id);
				}
				else if(response.cms_message) {
					page.notify(response.cms_message);
				}
			}
			u.request(this, this.action, {"method":"post", "params" : u.f.getParams(this)});
		}
	}
}

/*u-notifier.js*/
u.notifier = function(node) {
	var notifications = u.qs("div.notifications", node);
	if(!notifications) {
		node.notifications = u.ae(node, "div", {"id":"notifications"});
	}
	node.notifications.hide = function() {
		this.transitioned = function() {
			u.a.transition(this, "none");
		}
		u.a.transition(this, "all 0.5s ease-in-out");
		u.a.translate(this, 0, -this.offsetHeight);
	}
	node.notify = function(message, _options) {
		var class_name = "message";
		if(typeof(_options) == "object") {
			var argument;
			for(argument in _options) {
				switch(argument) {
					case "class"	: class_name	= _options[argument]; break;
				}
			}
		}
		var output;
		u.bug("message:" + typeof(message) + "; " + message);
		if(typeof(message) == "object") {
			for(type in message) {
				u.bug("typeof(message[type]:" + typeof(message[type]) + "; " + type);
				if(typeof(message[type]) == "string") {
					output = u.ae(this.notifications, "div", {"class":class_name, "html":message[type]});
				}
				else if(typeof(message[type]) == "object" && message[type].length) {
					var node, i;
					for(i = 0; _message = message[type][i]; i++) {
						output = u.ae(this.notifications, "div", {"class":class_name, "html":_message});
					}
				}
			}
		}
		else if(typeof(message) == "string") {
			output = u.ae(this.notifications, "div", {"class":class_name, "html":message});
		}
		u.t.setTimer(this.notifications, this.notifications.hide, 3500);
	}
}



/*i-form.js*/
Util.Objects["searchDevice"] = new function() {
	this.init = function(form) {
		u.f.init(form);
		form.submitted = function() {
			var params = u.f.getParams(this);
			var tags = u.qsa("li:not(.add)", this._tags._list);
			if(tags) {
				params += "&tags=";
				var tag_array = [];
				var i, tag;
				for(i = 0; tag = tags[i]; i++) {
					if(!tag._context) {
						tag._context = u.qs(".context", tag).innerHTML;
						tag._value = u.qs(".value", tag).innerHTML;
					}
					tag_array.push(tag._context+":"+tag._value);
				}
				params += tag_array.join(";");
			}
			this.response = function(response) {
				var list = u.qs(".all_items");
				list.parentNode.replaceChild(u.qs(".all_items", response), list);
				u.init();
			}
			var list = u.qs(".all_items");
			u.a.transition(list, "opacity 0.5s ease-in");
			u.a.setOpacity(list, 0.2);
			this.disableTaggedSearch();
			u.request(this, this.action, {"params":params, "method":"post"})
		}
		form._tags = u.qs("div.tags", form);
		if(!form._tags) {
			form._tags = u.ae(form, "div", {"class":"tags"});
		}
		form._tags._form = form;
		form._tags._list = u.qs("ul.tags", form);
		if(!form._tags._list) {
			form._tags._list = u.ae(form._tags, "ul", {"class":"tags"});
		}
		form._tags.tagsResponse = function(response) {
			if(response.cms_status == "success" && response.cms_object) {
				this._alltags = response.cms_object;
				this._bn_add = u.ae(this._list, "li", {"class":"add","html":"+"});
				this._bn_add._form = this._form;
				u.e.click(this._bn_add);
				this._bn_add.clicked = function() {
					this._form.enableTaggedSearch();
				}
			}
			else {
			}
		}
		u.request(form._tags, "/janitor/admin/items/tags", {"callback":"tagsResponse"});
		form.enableTaggedSearch = function() {
			u.ac(this._tags, "addTags");
			this._tags.field = u.ae(this._tags, "div", {"class":"field"});
			this._tags._tagfilter = u.ae(this._tags.field, "input", {"class":"filter ignoreinput"});
			this._tags._tagfilter._tags = this._tags;
			this._tags._tagfilter.onkeyup = function() {
				if(this._tags._taglist) {
					var tags = u.qsa(".tag", this._tags._taglist);
					var i, tag;
					for(i = 0; tag = tags[i]; i++) {
						if(tag.textContent.toLowerCase().match(this.value.toLowerCase())) {
							u.as(tag, "display", "inline-block");
						}
						else {
							u.as(tag, "display", "none");
						}
					}
				}
			}
			this._tags._bn_add.innerHTML = "-";
			this._tags._bn_add.clicked = function() {
				this._form.disableTaggedSearch();
			}
			this._tags._usedtags = {};
			var itemTags = u.qsa("li:not(.add)", this._tags._list);
			var i, tag, context, value;
			for(i = 0; tag = itemTags[i]; i++) {
				tag._context = u.qs(".context", tag).innerHTML;
				tag._value = u.qs(".value", tag).innerHTML;
				if(!this._tags._usedtags[tag._context]) {
					this._tags._usedtags[tag._context] = {}
				}
				if(!this._tags._usedtags[tag._context][tag._value]) {
					this._tags._usedtags[tag._context][tag._value] = tag;
				}
			}
			this._tags._taglist = u.ae(this._tags, "ul", {"class":"taglist"});
			for(tag in this._tags._alltags) {
				context = this._tags._alltags[tag].context;
				value = this._tags._alltags[tag].value.replace(/ & /, " &amp; ");
				if(this._tags._usedtags && this._tags._usedtags[context] && this._tags._usedtags[context][value]) {
					tag_node = this._tags._usedtags[context][value];
				}
				else {
					tag_node = u.ae(this._tags._taglist, "li", {"class":"tag"});
					tag_node._context = context;
					tag_node._value = value;
					u.ae(tag_node, "span", {"class":"context", "html":tag_node._context});
					u.ae(tag_node, "span", {"class":"value", "html":tag_node._value});
				}
				tag_node._tags = this._tags;
				tag_node._id = this._tags._alltags[tag].id;
 				u.e.click(tag_node);
 				tag_node.clicked = function() {
						if(u.hc(this.parentNode, "tags")) {
							u.ae(this._tags._taglist, this);
						}
						else {
							u.ie(this._tags._list, this);
						}
				}
			}
		}
		form.disableTaggedSearch = function() {
			this._tags._bn_add.innerHTML = "+";
			this._tags._bn_add.clicked = function() {
				this._form.enableTaggedSearch();
			}
			if(this._tags._taglist) {
				u.rc(this._tags, "addTags");
				this._tags.removeChild(this._tags.field);
				this._tags.removeChild(this._tags._taglist);
				this._tags._taglist = false;
			}
		}
	}
}
Util.Objects["cloneDevice"] = new function() {
	this.init = function(li) {
		u.ce(li);
		li.clicked = function() {
			this.response = function(response) {
				if(response.cms_status == "success" && response.cms_object.id) {
					location.href = "/janitor/device/edit/"+response.cms_object.id;
				}
				else {
					page.notify(response.cms_message);
				}
			}
			u.request(this, this.url);
		}
	}
}
Util.Objects["editUseragents"] = new function() {
	this.init = function(div) {
		div.item_id = u.cv(div, "item_id");
		div._form = u.qs("form", div);
		u.f.init(div._form);
		div._form.submitted = function(iN) {
			this.response = function(response) {
				if(response.cms_status == "success") {
					location.reload();
				}
				else {
					alert(response.cms_message[0]);
				}
			}
			u.request(this, this.action, {"method":"post", "params" : u.f.getParams(this)});
		}
		div._uas = u.qsa("li.useragent", div);
		var i, ua, bn_delete;
		for(i = 0; ua = div._uas[i]; i++) {
			ua.ua_id = u.cv(ua, "ua_id")
			bn_delete = u.ae(ua, "div", {"class":"delete"});
			bn_delete.ua = ua;
			bn_delete.div = div;
			u.e.click(bn_delete);
			bn_delete.clicked = function(event) {
				this.response = function(response) {
					if(response.cms_status == "success") {
						this.ua.parentNode.removeChild(this.ua);
					}
					else {
					}
				}
				u.request(this, "/janitor/admin/items/device/"+this.div.item_id+"/deleteUseragent/"+this.ua.ua_id);
			}
		}
	}
}
Util.Objects["searchUnidentified"] = new function() {
	this.init = function(form) {
		u.f.init(form);
		form.submitted = function() {
			if(form.fields["search_string"].val() == form.fields["search_string"].default_value) {
				form.fields["search_string"].val("");
			}
			form.submit();
		}
	}
}
Util.Objects["unidentifiedList"] = new function() {
	this.init = function(div) {
		u.bug("init unidentifiedList")
		var i, node;
		div.list = u.qs("ul.items", div);
		div.nodes = u.qsa("li.item", div.list);
		document.body.unidentified_div = div;
		div.bn_all = u.ie(div.list, "li", {"class":"all", "html":"Select all"});
		div.bn_all._checkbox = u.ie(div.bn_all, "input", {"type":"checkbox"});
		div.bn_all.div = div;
		div.bn_all._checkbox.div = div;
		u.e.click(div.bn_all);
		div.bn_all.clicked = function() {
			var i, node;
			var inputs = u.qsa("li:not(.all) input:checked", this.div.list);
			for(i = 0; node = div.nodes[i]; i++) {
				if(inputs.length) {
					node._checkbox.checked = false;
				}
				else if(!node._hidden) {
					node._checkbox.checked = true;
				}
			}
			this.div.toggleAddToOption();
		}
		for(i = 0; node = div.nodes[i]; i++) {
			node.ua_id = u.cv(node, "ua_id");
			node.div = div;
			node._checkbox = u.ie(node, "input", {"type":"checkbox"});
			node._checkbox.node = node;
			u.e.click(node._checkbox);
			node._checkbox.onclick = function(event) {u.e.kill(event);}
			node._checkbox.inputStarted = function(event) {
				u.e.kill(event);
				if(this.checked) {
					this.checked = false;
					document.body._multideselection = true;
				}
				else {
					this.checked = true;
					document.body._multiselection = true;
				}
				document.body.onmouseup = function() {
					this.onmouseup = null;
					this._multiselection = false;
					this._multideselection = false;
					this.unidentified_div.toggleAddToOption();
				}
			}
			node._checkbox.onmouseover = function() {
				if(document.body._multiselection) {
					this.checked = true;
				}
				else if(document.body._multideselection) {
					this.checked = false;
				}
			}
			u.e.click(node);
			node.clicked = function() {
				if(!this._ul) {
					this.response = function(response) {
						if(response.cms_status == "success") {
							var action = u.ae(this, "ul", {"class":"actions"});
							var li = u.ae(action, "li", {"class":"delete"});
							this._delete = u.ae(li, "input", {"class":"button delete", "type":"button", "value":"delete"})
							this._delete.node = this;
							u.e.click(this._delete);
							this._delete.restore = function(event) {
								this.value = "Delete";
								u.rc(this, "confirm");
							}
							this._delete.clicked = function(event) {
								u.e.kill(event);
								if(!u.hc(this, "confirm")) {
									u.ac(this, "confirm");
									this.value = "Confirm";
									this.t_confirm = u.t.setTimer(this, this.restore, 3000);
								}
								else {
									u.t.resetTimer(this.t_confirm);
									u.bug("node.ua_id:" + this.node.ua_id);
									this.response = function(response) {
										if(response.cms_status == "success") {
											this.node.parentNode.removeChild(this.node);
										}
										page.notify(response.cms_message);
									}
									u.request(this, "/janitor/device/deleteUnidentified/"+this.node.ua_id);
								}
							}
							this._ul = u.ae(this, "ul", {"class":"info"});
							u.ae(this._ul, "li", {"class":"visits", "html":response.cms_object.length})
							u.ae(this._ul, "li", {"class":"identified_as", "html":response.cms_object[0].identified_as_device})
							var i, node;
							for(i = 0; node = response.cms_object[i]; i++) {
								var ul = u.ae(this, "ul", {"class":"info"});
								u.ae(ul, "li", {"class":"identified_at", "html":node.identified_at})
								u.ae(ul, "li", {"class":"comment", "html":node.comment})
							}
						}
						else {
							if(typeof(page.notify) == "function") {
								page.notify(response.cms_message);
							}
							else {
								alert(response.cms_message[0]);
							}
						}
					}
					u.request(this, "/janitor/device/unidentifiedUseragentDetails/"+this.ua_id);
				}
				else {
					var uls = u.qsa("ul", this);
					var i, ul;
					for(i = 0; ul = uls[i]; i++) {
						this.removeChild(ul);
					}
					this._ul = false;
				}
			}
		}
		if(u.hc(div, "filters")) {
			div._filter = u.ie(div, "div", {"class":"filter"});
			var i, node;
			for(i = 0; node = div.nodes[i]; i++) {
				node._c = node.textContent.toLowerCase();
			}
			div._filter._field = u.ae(div._filter, "div", {"class":"field"});
			u.ae(div._filter._field, "label", {"html":"Filter"});
			div._filter._input = u.ae(div._filter._field, "input", {"class":"filter ignoreinput"});
			div._filter._input._div = div;
			div._filter._input.onkeydown = function() {
				u.t.resetTimer(this._div.t_filter);
			}
			div._filter._input.onkeyup = function() {
				this._div.t_filter = u.t.setTimer(this._div, this._div.filter, 1500);
				u.ac(this._div._filter, "filtering");
			}
			div.filter = function() {
				var i, node;
				if(this._current_filter != this._filter._input.value.toLowerCase()) {
					this._current_filter = this._filter._input.value.toLowerCase();
					for(i = 0; node = this.nodes[i]; i++) {
						if(node._c.match(this._current_filter)) {
							node._hidden = false;
							u.as(node, "display", "block", false);
						}
						else {
							node._hidden = true;
							u.as(node, "display", "none", false);
							node._checkbox.checked = false;
						}
					}
				}
				u.rc(this._filter, "filtering");
			}
		}
		div.addOption = function(option) {
			if(this._add_to.identified_options.indexOf(option.id) == -1) {
				this._add_to.identified_options.push(option.id);
				var li_option = u.ae(this._add_to._list, "li", {"html":option.name});
				li_option.details = option;
				li_option.div = this;
				li_option.device_id = option.id;
				u.e.click(li_option);
				li_option.clicked = function() {
					if(!this._info) {
						var i;
						var info_string = "";
						info_string = this.details["method"];
						if(this.details["guess"]) {
							info_string += ", " + this.details["guess"];
						}
						if(this.details["tags"]) {
							for(i in this.details["tags"]) {
								if(this.details["tags"][i]["context"] == "brand") {
									info_string += ", " + this.details["tags"][i]["value"]
								}
							}
						}
						info_string += ", " + this.details["description"];
						this._info = u.ae(this, "div", {"class":"info", "html":info_string});
						if(this.device_id != "unknown") {
							this._selected = u.ae(this, "div", {"class":"selected", "html":"Add all SELECTED"});
							this._selected.option = this;
							this._matching = u.ae(this, "div", {"class":"matching", "html":"Add all MATCHING"});
							this._matching.option = this;
							this._addtoclone = u.ae(this, "div", {"class":"addtoclone", "html":"Add SELECTED to CLONE"});
							this._addtoclone.option = this;
							u.e.click(this._selected);
							this._selected.clicked = function() {
								if(this.t_execute) {
									var inputs = u.qsa("li:not(.all) input:checked", this.option.div.list);
									for(i = 0; input = inputs[i]; i++) {
										input.node.response = function(response) {
											this.parentNode.removeChild(this);
											this.div.toggleAddToOption();
										}
										u.request(input.node, "/janitor/device/addUnidentifiedToDevice/"+this.option.device_id+"/"+input.node.ua_id);
									}
								}
								else {
									this.t_execute = u.t.setTimer(this, this.not_confirmed, 1500);
									this._content = this.innerHTML;	
									this.innerHTML = "Sure?";
									u.ac(this, "confirm");
								}
							}
							u.e.click(this._matching);
							this._matching.clicked = function() {
								if(this.t_execute) {
									var inputs = u.qsa("li:not(.all) input:checked", this.option.div.list);
									for(i = 0; input = inputs[i]; i++) {
										if(input.node._identified.id == this.option.device_id) {
											input.node.response = function(response) {
												this.parentNode.removeChild(this);
												this.div.toggleAddToOption();
											}
											u.request(input.node, "/janitor/device/addUnidentifiedToDevice/"+this.option.device_id+"/"+input.node.ua_id);
										}
									}
								}
								else {
									this.t_execute = u.t.setTimer(this, this.not_confirmed, 1500);
									this._content = this.innerHTML;	
									this.innerHTML = "Sure?";
									u.ac(this, "confirm");
								}
							}
							u.e.click(this._addtoclone);
							this._addtoclone.clicked = function() {
								if(this.t_execute) {
									this.response = function(response) {
										if(response.cms_status == "success" && response.cms_object.id) {
											var inputs = u.qsa("li:not(.all) input:checked", this.option.div.list);
											var i, input;
											for(i = 0; input = inputs[i]; i++) {
												input.node.response = function(response) {
													this.parentNode.removeChild(this);
													this.div.toggleAddToOption();
												}
												u.request(input.node, "/janitor/device/addUnidentifiedToDevice/"+response.cms_object.id+"/"+input.node.ua_id);
											}
										}
										else {
											page.notify(response.cms_message);
										}
									}
									u.request(this, "/janitor/device/cloneDevice/"+this.option.device_id);
								}
								else {
									this.t_execute = u.t.setTimer(this, this.not_confirmed, 1500);
									this._content = this.innerHTML;	
									this.innerHTML = "Sure?";
									u.ac(this, "confirm");
								}
							}
							this._matching.not_confirmed = this._addtoclone.not_confirmed = this._selected.not_confirmed = function() {
								u.rc(this, "confirm");
								this.innerHTML = this._content;
								this.t_execute = false;
							}
						}
						else {
							this._error = u.ae(this, "div", {"class":"info", "html":"identification did not return a valid device id"});
						}
					}
					else {
						if(this._info) {
							this._info.parentNode.removeChild(this._info);
							this._info = false;
						}
						if(this._selected) {
							this._selected.parentNode.removeChild(this._selected);
							this._selected = false;
						}
						if(this._matching) {
							this._matching.parentNode.removeChild(this._matching);
							this._matching = false;
						}
						if(this._addtoclone) {
							this._addtoclone.parentNode.removeChild(this._addtoclone);
							this._addtoclone = false;
						}
						if(this._error) {
							this._error.parentNode.removeChild(this._error);
							this._error = false;
						}
					}
				}
			}
		}
		div.toggleAddToOption = function() {
			var inputs = u.qsa("li:not(.all) input:checked", this.list);
			if(inputs.length) {
				if(!this._add_to) {
					this._add_to = u.ae(document.body, "div", {"class":"addToDevice"});
					u.ae(this._add_to, "h2", {"html":"Add to device"});
					var count_div = u.ae(this._add_to, "div", {"html":"Selected useragents:", "class":"counter"});
					this._add_to._count = u.ae(count_div, "span", {"class":"count"});
					u.as(page, "width", parseInt(u.gcs(page, "width")) - this._add_to.offsetWidth + "px");
					var search_option = u.ae(this._add_to, "div", {"class":"field search"});
					search_option.div = this;
					var search_input = u.ae(search_option, "input", {"class":"search default ignoreinput"});
					search_input.div = this;
					search_input.search_result = u.ae(search_option, "ul", {"class":"results"});
					search_input._default_value = "Search";
					search_input.value = search_input._default_value;
					search_input.onfocus = function() {
						u.rc(this, "default");
						if(this.value == this._default_value) {
							this.value = "";
						}
					}
					search_input.onblur = function() {
						if(this.value == "") {
							u.ac(this, "default");
							this.value = this._default_value;
						}
					}
					search_input.onkeyup = function() {
						u.t.resetTimer(this.t_search);
						this.t_search = u.t.setTimer(this, this.search, 1000);
					}
					search_input.onkeydown = function() {
						u.t.resetTimer(this.t_search);
					}
					search_input.search = function() {
						search_input.search_result.innerHTML = "";
						if(this.value && this.value != this._default_value) {
							search_input.response = function(response) {
								var items = u.qsa(".all_items li.item", response);
								if(items.length) {
									var i, node;
									for(i = 0; node = items[i]; i++) {
										node = this.search_result.appendChild(node);
										node.div = this.div;
										node.device_id = u.cv(node, "item_id");
										u.e.click(node);
										node.clicked = function() {
											if(!this._info) {
												var i, info_string;
												var brand = u.qs("ul.tags li.brand .value", this);
												if(brand) {
													info_string = brand.innerHTML;
												}
												this._info = u.ae(this, "div", {"class":"info", "html":info_string});
												this._selected = u.ae(this, "div", {"class":"selected", "html":"Add all SELECTED"});
												this._selected.option = this;
												this._addtoclone = u.ae(this, "div", {"class":"addtoclone", "html":"Add SELECTED to CLONE"});
												this._addtoclone.option = this;
												u.e.click(this._selected);
												this._selected.clicked = function() {
													if(this.t_execute) {
														var inputs = u.qsa("li:not(.all) input:checked", this.option.div.list);
														var i, input;
														for(i = 0; input = inputs[i]; i++) {
															input.node.response = function(response) {
																this.parentNode.removeChild(this);
																this.div.toggleAddToOption();
															}
															u.request(input.node, "/janitor/device/addUnidentifiedToDevice/"+this.option.device_id+"/"+input.node.ua_id);
														}
													}
													else {
														this.t_execute = u.t.setTimer(this, this.not_confirmed, 1500);
														this._content = this.innerHTML;	
														this.innerHTML = "Sure?";
														u.ac(this, "confirm");
													}
												}
												u.e.click(this._addtoclone);
												this._addtoclone.clicked = function() {
													if(this.t_execute) {
														this.response = function(response) {
															if(response.cms_status == "success" && response.cms_object.id) {
																var inputs = u.qsa("li:not(.all) input:checked", this.option.div.list);
																var i, input;
																for(i = 0; input = inputs[i]; i++) {
																	input.node.response = function(response) {
																		this.parentNode.removeChild(this);
																		this.div.toggleAddToOption();
																	}
																	u.request(input.node, "/janitor/device/addUnidentifiedToDevice/"+response.cms_object.id+"/"+input.node.ua_id);
																}
															}
															else {
																page.notify(response.cms_message);
															}
														}
														u.request(this, "/janitor/device/cloneDevice/"+this.option.device_id);
													}
													else {
														this.t_execute = u.t.setTimer(this, this.not_confirmed, 1500);
														this._content = this.innerHTML;	
														this.innerHTML = "Sure?";
														u.ac(this, "confirm");
													}
												}
												this._selected.not_confirmed = this._addtoclone.not_confirmed = function() {
													u.rc(this, "confirm");
													this.innerHTML = this._content;
													this.t_execute = false;
												}
											}
											else {
												if(this._info) {
													this._info.parentNode.removeChild(this._info);
													this._info = false;
												}
												if(this._selected) {
													this._selected.parentNode.removeChild(this._selected);
													this._selected = false;
												}
												if(this._addtoclone) {
													this._addtoclone.parentNode.removeChild(this._addtoclone);
													this._addtoclone = false;
												}
											}
										}
		 							}
								}
								u.rc(this, "loading");
							}
							u.ac(search_input, "loading");
							u.request(search_input, "/janitor/device/list", {"params":"search=1&search_string="+this.value, "method":"post"})
						}
					}
					this._add_to._list = u.ae(this._add_to, "ul", {"class":"options"});
				}
				this._add_to._count.innerHTML = inputs.length;
				this._add_to._list.innerHTML = "";
				this._add_to.identified_options = [];
				var i, ua, ua_id
				this.wait_for_uas = inputs.length;
				u.ac(this._add_to, "loading");
				for(i = 0; ua = inputs[i]; i++) {
					if(!ua.node._identified) {
						ua.node.response = function(response) {
							if(response.cms_status == "success") {
								if(response.cms_object.id) {
									this._identified = response.cms_object;
								}
							}
							else {
								this._identified = {};
								this._identified.id = "unknown";
								this._identified.name = "Unknown";
							}
							this.div.addOption(this._identified);
							this.div.wait_for_uas--;
							if(!this.div.wait_for_uas) {
								u.rc(this.div._add_to, "loading");
							}
						}
						u.request(ua.node, "/janitor/device/identifyUnidentifiedId/"+ua.node.ua_id);
					}
					else {
						this.addOption(ua.node._identified);
						this.wait_for_uas--;
						if(!this.wait_for_uas) {
							u.rc(this._add_to, "loading");
						}
					}
				}
			}
			else {
				if(this._add_to) {
					this._add_to.parentNode.removeChild(this._add_to);
					this._add_to = false;
					u.as(page, "width", "auto");
				}
			}
		}
	}
}
Util.Objects["uniqueMatchList"] = new function() {
	this.init = function(div) {
		u.bug_force = true;
		u.bug("init uniqueMatchList")
		var i, node;
		div.list = u.qs("ul.items", div);
		div.nodes = u.qsa("li.item", div.list);
		document.body.unidentified_div = div;
		div.bn_all = u.ie(div.list, "li", {"class":"all", "html":"Select all"});
		div.bn_all._checkbox = u.ie(div.bn_all, "input", {"type":"checkbox"});
		div.bn_all.div = div;
		div.bn_all._checkbox.div = div;
		u.e.click(div.bn_all);
		div.bn_all.clicked = function() {
			var i, node;
			var inputs = u.qsa("li:not(.all) input:checked", this.div.list);
			for(i = 0; node = div.nodes[i]; i++) {
				if(inputs.length) {
					node._checkbox.checked = false;
				}
				else if(!node._hidden) {
					node._checkbox.checked = true;
				}
			}
			this.div.toggleAddToOption();
		}
		for(i = 0; node = div.nodes[i]; i++) {
			node.ua_id = u.cv(node, "ua_id");
			node.div = div;
			node._checkbox = u.ie(node, "input", {"type":"checkbox"});
			node._checkbox.node = node;
			u.e.click(node._checkbox);
			node._checkbox.onclick = function(event) {u.e.kill(event);}
			node._checkbox.inputStarted = function(event) {
				u.e.kill(event);
				if(this.checked) {
					this.checked = false;
					document.body._multideselection = true;
				}
				else {
					this.checked = true;
					document.body._multiselection = true;
				}
				document.body.onmouseup = function() {
					this.onmouseup = null;
					this._multiselection = false;
					this._multideselection = false;
					this.unidentified_div.toggleAddToOption();
				}
			}
			node._checkbox.onmouseover = function() {
				if(document.body._multiselection) {
					this.checked = true;
				}
				else if(document.body._multideselection) {
					this.checked = false;
				}
			}
			u.e.click(node);
			node.clicked = function() {
				if(!this._ul) {
					this.response = function(response) {
						if(response.cms_status == "success") {
							this._ul = u.ae(this, "ul", {"class":"info"});
							u.ae(this._ul, "li", {"class":"visits", "html":response.cms_object.length})
							u.ae(this._ul, "li", {"class":"identified_as", "html":response.cms_object[0].identified_as_device})
							var i, node;
							for(i = 0; node = response.cms_object[i]; i++) {
								var ul = u.ae(this, "ul", {"class":"info"});
								u.ae(ul, "li", {"class":"identified_at", "html":node.identified_at})
								u.ae(ul, "li", {"class":"comment", "html":node.comment})
							}
						}
						else {
							page.notify(response.cms_message);
						}
					}
					u.request(this, "/janitor/device/unidentifiedUseragentDetails/"+this.ua_id);
				}
				else {
					var uls = u.qsa("ul", this);
					var i, ul;
					for(i = 0; ul = uls[i]; i++) {
						this.removeChild(ul);
					}
					this._ul = false;
				}
			}
		}
		if(u.hc(div, "filters")) {
			div._filter = u.ie(div, "div", {"class":"filter"});
			var i, node;
			for(i = 0; node = div.nodes[i]; i++) {
				node._c = node.textContent.toLowerCase();
			}
			div._filter._field = u.ae(div._filter, "div", {"class":"field"});
			u.ae(div._filter._field, "label", {"html":"Filter"});
			div._filter._input = u.ae(div._filter._field, "input", {"class":"filter ignoreinput"});
			div._filter._input._div = div;
			div._filter._input.onkeydown = function() {
				u.t.resetTimer(this._div.t_filter);
			}
			div._filter._input.onkeyup = function() {
				this._div.t_filter = u.t.setTimer(this._div, this._div.filter, 1500);
				u.ac(this._div._filter, "filtering");
			}
			div.filter = function() {
				var i, node;
				if(this._current_filter != this._filter._input.value.toLowerCase()) {
					this._current_filter = this._filter._input.value.toLowerCase();
					for(i = 0; node = this.nodes[i]; i++) {
						if(node._c.match(this._current_filter)) {
							node._hidden = false;
							u.as(node, "display", "block", false);
						}
						else {
							node._hidden = true;
							u.as(node, "display", "none", false);
							node._checkbox.checked = false;
						}
					}
				}
				u.rc(this._filter, "filtering");
			}
		}
		div.addOption = function(option) {
			if(this._delete_uas.identified_options.indexOf(option.name) == -1) {
				this._delete_uas.identified_options.push(option.name);
				var li_option = u.ae(this._delete_uas._list, "li", {"html":option.name});
				li_option.details = option;
				li_option.div = this;
			}
		}
		div.toggleAddToOption = function() {
			var inputs = u.qsa("li:not(.all) input:checked", this.list);
			if(inputs.length) {
				if(!this._delete_uas) {
					this._delete_uas = u.ae(document.body, "div", {"class":"deleteUnique"});
					u.ae(this._delete_uas, "h2", {"html":"Delete useragents"});
					var count_div = u.ae(this._delete_uas, "div", {"html":"Selected useragents:", "class":"counter"});
					this._delete_uas._count = u.ae(count_div, "span", {"class":"count"});
					u.as(page, "width", parseInt(u.gcs(page, "width")) - this._delete_uas.offsetWidth + "px");
					this._delete_uas._list = u.ae(this._delete_uas, "ul", {"class":"options"});
					this._delete = u.ae(this._delete_uas, "div", {"class":"delete", "html":"Delete selected"})
					this._delete.node = this;
					u.e.click(this._delete);
					this._delete.restore = function(event) {
						this.value = "Delete selected";
						u.rc(this, "confirm");
					}
					this._delete.clicked = function(event) {
						u.e.kill(event);
						if(!u.hc(this, "confirm")) {
							u.ac(this, "confirm");
							this.value = "Confirm";
							this.t_confirm = u.t.setTimer(this, this.restore, 3000);
						}
						else {
							u.t.resetTimer(this.t_confirm);
							var inputs = u.qsa("li:not(.all) input:checked", this.list);
							var i, ua;
							if(inputs.length) {
								for(i = 0; ua = inputs[i]; i++) {
									if(ua.node.ua_id) {
										ua.node.response = function(response) {
											if(response.cms_status == "success") {
												this.parentNode.removeChild(this);
											}
											page.notify(response.cms_message);
											this.div.toggleAddToOption();
										}
										u.request(ua.node, "/janitor/device/deleteUnidentified/"+ua.node.ua_id);
									}
								}
							}
						}
					}
				}
				this._delete_uas._count.innerHTML = inputs.length;
				this._delete_uas._list.innerHTML = "";
				this._delete_uas.identified_options = [];
				var i, ua, ua_id
				this.wait_for_uas = inputs.length;
				u.ac(this._delete_uas, "loading");
				for(i = 0; ua = inputs[i]; i++) {
					if(!ua.node._identified) {
						ua.node.response = function(response) {
							if(response.cms_status == "success") {
								if(response.cms_object) {
									this._identified = response.cms_object;
								}
							}
							else {
								this._identified = {};
								this._identified.name = "Unknown";
							}
							this.div.addOption(this._identified);
							this.div.wait_for_uas--;
							if(!this.div.wait_for_uas) {
								u.rc(this.div._delete_uas, "loading");
							}
						}
						u.request(ua.node, "/janitor/device/identifyUnidentifiedId/"+ua.node.ua_id);
					}
					else {
						this.addOption(ua.node._identified);
						this.wait_for_uas--;
						if(!this.wait_for_uas) {
							u.rc(this._delete_uas, "loading");
						}
					}
				}
			}
			else {
				if(this._delete_uas) {
					this._delete_uas.parentNode.removeChild(this._delete_uas);
					this._delete_uas = false;
					u.as(page, "width", "auto");
				}
			}
		}
	}
}
