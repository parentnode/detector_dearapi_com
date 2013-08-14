// kills click (to prevent click from invoking other events)
Util.nonClick = function(event) {
	event = event ? event : window.event;
	if(event.preventDefault) {event.preventDefault();}
	if(event.stopPropagation) {event.stopPropagation();}
	event.returnValue = false;
	event.cancelBubble = true;
}

// X-Browser add/remove event handlers
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

	// actions to be preformed on onload
	this.actions = new Array();

	// forwarding function (onload event happens on window object)
	this.onloadCatcher = function(event) {
		Util.Onload.execute(event);
	}

	// add new funtion to onload 
	this.addAction = function(action) {

		// start catching event
		if(!this.actions.length) {
			Util.addEventHandler(window, "load", this.onloadCatcher);
		}
		// add action
		this.actions[this.actions.length] = action;
	}

	// execute added function on onload
	this.execute = function() {
		var i, action;

		// Run onload initialization and test functions here
		// Init interface
		Util.initElements(document.getElementById('page'));
		//Util.addEventHandler(document, "keydown", Util.Onkeydown.shortcutCatcher);
		//testIt();

		// functions kept in actions array
		for(i = 0; action = this.actions[i]; i++) {

			// decide type and execute accordingly
			if(typeof(action) == "function") {
				action();
			}
			else {
				eval(action);
			}
		}
	}
}
// Turn on onload event execution
Util.addEventHandler(window, "load", Util.Onload.onloadCatcher);

// Onresize event handler object
//Util.Onresize = new function() {

	// actions to be preformed on onresize
//	this.actions = new Array();

	// forwarding function (onresize event happens on window object)
//	this.onresizeCatcher = function(event) {
//		Util.Onresize.execute(event);
//	}

	// add new funtion to onload 
//	this.addAction = function(action) {

		// start catching event
//		if(!this.actions.length) {
//			Util.addEventHandler(window, "resize", this.onresizeCatcher);
//		}
		// add action
//		this.actions[this.actions.length] = action;
//	}

	// execute added function on onresize
//	this.execute = function() {
//		var i, action;

		// functions kept in actions array
//		for(i = 0; action = this.actions[i]; i++){

			// decide type and execute accordingly
//			if(typeof(action) == "function") {
//				action();
//			}
//			else {
//				eval(action);
//			}
//		}
//	}
//}

// Ontimeout event handler object
Util.Ontimeout = new function() {

	// actions to be preformed on onTimeout
	this.actions = new Array();
	this.objects = new Array();
	this.timers = new Array();

	// Add new timer to object
	this.setTimer = function(object, action, timeout) {
		var id = this.actions.length;
		this.actions[id] = action;
		this.objects[id] = object;
		this.timers[id] = setTimeout("Util.Ontimeout.execute("+id+")", timeout);
		return id;
	}
	// Reset timer
	this.resetTimer = function(id) {
		clearTimeout(this.timers[id]);
	}

	// execute added function on onTimeout
	this.execute = function(id) {
		this.objects[id].exe = this.actions[id];
		this.objects[id].exe();

		// clear timeout info
		this.objects[id].exe = null;
		this.actions[id] = null;
		this.objects[id] = null;
		this.timers[id] = null;
	}
}

// Onkeydown event handler object
Util.Onkeydown = new function() {

	// container for shortcuts
	this.shortcuts = new Array();
//	this.input = "";

	// timer for shortcut timewindow
//	this.timer = null;

	// forwarding function (onkeydown event happens on window object)
	this.onkeydownCatcher = function(event) {
//		Util.nonClick(event);
		Util.Onkeydown.catchKey(event);
	}

	// end time loop (has to be global because setTimeout executes on window object)
//	this.stopSCWindow = function() {
//		Util.Onkeydown.timer = null;
//		Util.Onkeydown.execute();
//	}

	// add new shortcut
	this.addShortcut = function(key, action) {

//		Util.debug("ad");
		// start catching event
		if(!this.shortcuts.length) {
			Util.addEventHandler(document, "keydown", this.onkeydownCatcher);
		}
		
//		if(!this.shortcuts[key.toString().toUpperCase()]) {
//			Util.debug("a"+this.shortcuts[key.toString().toUpperCase()]);
//			this.shortcuts[key.toString().toUpperCase()] = new Array();
//		}

//		this.shortcuts[key.toString().toUpperCase()].push(action);
		this.shortcuts[key.toString().toUpperCase()] = action;
		
//		Util.debug("l:" + this.shortcuts.length)
		
//		Util.debug("a"+this.shortcuts[key.toString().toUpperCase()]);
		// set shortcut if it doesnt exist
//		if(this.shortcuts[key.toString().toUpperCase()] == undefined) {
//		}
//		else{
//			alert("Shortcut for: " + key + "\nconflicts with shortcut\naction: " + this.shortcuts[key].action);
//		}
		// set shortcut if it doesnt exist
//		if(this.shortcuts[key.toString().toUpperCase()] == undefined) {
//			this.shortcuts[key.toString().toUpperCase()] = action;
//		}
//		else{
//			alert("Shortcut for: " + key + "\nconflicts with shortcut\naction: " + this.shortcuts[key].action);
//		}
	}

	// execute 
//	this.execute = function(event) {
//		if(this.shortcuts[this.input] && (event.ctrlKey || event.metaKey)) {
//			Util.nonClick(event);
//			eval(this.shortcuts[this.input]);
//		}
//		this.input = "";
//	}

	// catch key
	this.catchKey = function(event) {
		var action, i, key;
		event = event ? event : window.event;
		key = String.fromCharCode(event.keyCode);

//		Util.debug("e:" + key + "::" + this.shortcuts.length)
		if((event.ctrlKey || event.metaKey) && this.shortcuts[key]) {
			Util.nonClick(event);
			action = this.shortcuts[key];
//			for(i = 0; action = this.shortcuts[key][i]; i++) {
				Util.debug(key+":"+action + "::" + action.parentNode);
				if(typeof(action) == "object") {
					action.click();
				}
				else if(typeof(action) == "function") {
					action();
				}
				else {
					eval(action);
				}
//			}

		}
		if(event.keyCode == 27 && this.shortcuts["ESC"]) {
			Util.nonClick(event);

			action = this.shortcuts["ESC"];
//			for(i = 0; action = this.shortcuts["ESC"][i]; i++) {
				Util.debug("esc:"+action + "::" + action.parentNode);
				if(typeof(action) == "object") {
					action.click();
				}
				else if(typeof(action) == "function") {
					action();
				}
				else {
					eval(action);
				}
//			}
		}
//		alert("Key: " + pressed_key + "\nKeyCode: " + event.keyCode + "\nCtrl:"  + event.ctrlKey + "\nMeta:"  + event.metaKey);

		// if ESC -> start shortcut time window
//		if(event.keyCode == 27) {
//			this.timer = setTimeout('Util.Onkeydown.stopSCWindow()', 2000);
//		}
		// if timewindow is open, react to input
//		else if(this.timer) {
//			this.input += pressed_key;
//		}
//		this.execute(event);
	}
}

//Util.Onkeydown.addShortcut("s", "alert('test')");

