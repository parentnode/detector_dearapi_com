// Returns the first parent occurence of tag
Util.getParentTag = function(tag, e) {
	if(element.nodeName != tag && e.nodeName != "BODY") {
		e = Util.getParentTag(tag, e.parentNode);
	} 
	return element;
}
// insert element in wrap-element and return wrapper
Util.wrapElement = function(e, wrap) {
	wrap = e.parentNode.insertBefore(document.createElement(wrap), e);
	wrap.appendChild(e);
	return wrap;
}
// Get elements in optional content with classname (default content is content)
Util.getElementsByClass = function(classname, content) {
	var e, i, elements, regexp, return_array = new Array();
	elements = content ? (typeof(content) == "string" ? document.getElementById(content).getElementsByTagName("*") : content.getElementsByTagName("*")) : document.getElementById("content").getElementsByTagName("*");
	// IE < 6 needs a bit of help getting elements
	elements = elements.length ? elements : (Util.explorer() ? document.all : elements);
	regexp = new RegExp("(^|\\s)" + classname + "(\\s|$|\:)");
	for(i = 0; e = elements[i]; i++) {
		if(regexp.test(e.className)) {
			return_array[return_array.length] = e;
		}
	}
	return return_array;
}
// Get elements in optional content with attribute (default content is content)
//Util.getElementsByAttribute = function(attribute, content) {
//	var e, i, elements, return_array;
//	return_array = new Array(); 
//	elements = content ? (typeof(content) == "string" ? document.getElementById(content).getElementsByTagName("*") : content.getElementsByTagName("*")) : document.getElementById("content").getElementsByTagName("*");
	// IE < 6 needs a bit of help getting elements
//	elements = elements.length ? elements : (Util.explorer() ? document.all : elements);
//	for(i = 0; e = elements[i]; i++) {
//		if(e.getAttribute(attribute)) {
//			return_array[return_array.length] = e;
//		}
//	}
//	return return_array;
//}
// Returns previous sibling, not counting text nodes as siblings (also ignoring optional exclude=classname or exclude=nodeName)
Util.previousRealSibling = function(e, exclude) {
	var regexp, previous, undefined;
	exclude = exclude ? exclude : false;
	regexp = new RegExp("(^|\\s)" + exclude + "(\\s|$)");
	previous = e.previousSibling;

	if(exclude) {
		while(previous && (previous.nodeType == 3 || previous.className.match(regexp) || previous.nodeName == exclude)) {
			previous = previous.previousSibling;
		}
	}
	else {
		while(previous && previous.nodeType == 3) {
			previous = previous.previousSibling;
		}
	}
	return previous;
}
// Returns next sibling, not counting text nodes as siblings (also ignoring exclude=classname or exclude=nodeName)
Util.nextRealSibling = function(e, exclude) {
	var regexp, next, undefined;
	exclude = exclude ? exclude : false;
	regexp = new RegExp("(^|\\s)" + exclude + "(\\s|$)");
	next = e.nextSibling;

	if(exclude) {
		while(next && (next.nodeType == 3 || next.className.match(regexp) || next.nodeName == exclude)) {
			next = next.nextSibling;
		}
	}
	else {
		while(next && next.nodeType == 3) {
			next = next.nextSibling;
		}
	}
	return next;
}
// Check for init:javascript value of element. Defined by identifier[:type]:value
Util.getIJ = function(id, e) {
	var regexp = new RegExp(id + ":[?=\\w/\\#~:.?+=?&%@!\\-]*");
	if(e.className.match(regexp)) {
		return e.className.match(regexp)[0].replace(id + ":", "");
	}
	return false;
}
// Get elements computed style value for css attribute
//Util.getStyleValue = function(element, attribute) {
	// Correct W3C method (Mozilla)
//	if(document.defaultView && document.defaultView.getComputedStyle) {
//		return document.defaultView.getComputedStyle(element, null).getPropertyValue(attribute);
//	}
	// Internet Explorer only
//	else if(document.body.currentStyle) {
//		attribute = attribute.replace(/(-\w)/g, function(word){return word.replace(/-/, "").toUpperCase()});
//		return element.currentStyle[attribute];
//	}
//	return false;
//}

// Add classname to element
Util.addClass = function(e, classname) {
	if(classname) {
		var regexp = new RegExp("(^|\\s)" + classname + "(\\s|$|\:)");
		if(!regexp.test(e.className)) {
			e.className += e.className ? " " + classname : classname;
		}
	}
}
// Remove classname from element
Util.removeClass = function(e, classname) {
	if(classname) {
		var regexp = new RegExp(classname + " | " + classname + "|" + classname);
		e.className = e.className.replace(regexp, "");
	}
}

// Remove classname from element
Util.toggleClass = function(e, classname) {
	var regexp = new RegExp("(^|\\s)" + classname + "(\\s|$|\:)");
	if(regexp.test(e.className)) {
		Util.removeClass(e, classname);
	}
	else {
		Util.addClass(e, classname);
	}
}
