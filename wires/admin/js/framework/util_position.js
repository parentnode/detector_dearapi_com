// Get absolute left position
Util.absoluteLeft = function(e) {
	// Now gives error in Safari (which still gives wrong TR response)
	//if(Util.safari() && e.nodeName == "TR") {
	//	return e.getElementsByTagName("TD")[0].offsetLeft + Util.absoluteLeft(Util.getParentTag("TABLE", e));
	//}
	//else
	if(e.offsetParent) {
		return e.offsetLeft + Util.absoluteLeft(e.offsetParent);
	}
	return e.offsetLeft;
} 
// Get absolute top position
Util.absoluteTop = function(e) {
	// Now gives error in Safari (which still gives wrong TR response)
	//if(Util.safari() && e.nodeName == "TR") {
	//	return e.getElementsByTagName("TD")[0].offsetTop + Util.absoluteTop(Util.getParentTag("TABLE", e));
	//}
	//else
	if(e.offsetParent) {
		return e.offsetTop + Util.absoluteTop(e.offsetParent);
	}
	return e.offsetTop;
}

// Get document viewable width (inside browser)
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
// Get document viewable height (inside browser)
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
