Util.Objects = u.o = new Array();

// Initialize any element with the init-class within container
Util.initElements = function(container) {
	// ij_value = init:javascript value
	var i, e, elements, ij_value, scripts;
	this.setLoadStatus("Initiating", "init");
	// fix IE 6
	if(Util.explorer(6, "<=")) {
		this.IEsucks(container);
	}

	scripts = container.getElementsByTagName("script");
	if(container.id != "page" && scripts.length) {
		if(scripts[0].firstChild && scripts[0].firstChild.nodeValue) {
			script = eval(scripts[0].firstChild.nodeValue);
		}
		// IE
		else if(Util.explorer() && scripts[0].text) {
			script = eval(scripts[0].text);
		}
		scripts[0].parentNode.innerHTML = script;
	}

	// additional initializations based on class name
	elements = this.getElementsByClass("init([:a-zA-Z])+", container);
	// add container to elements
	if(this.getIJ("init", container)){
		elements[elements.length] = container;
	}

	for(i = 0; e = elements[i]; i++) {
		// get init definition
		ij_value = this.getIJ("init", e);
//		Util.debug(ij_value);
		// if object exists, init element

		if(ij_value && typeof(this.Objects[ij_value]) == "object") {
//			Util.debug(ij_value);
			this.Objects[ij_value].init(e);
		}
	}

	this.focusOnFirstInput(container);
	this.setLoadStatus("Done", "done");

	// add dev class
	if(location.href.indexOf("http://mkn.") != -1 || location.href.indexOf("http://w.") != -1) {
		Util.addClass(document.body, "dev");
	}
}
