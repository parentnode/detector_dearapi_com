// obstruction Utility
Util.Obstructions = new Object();

Util.Obstructions.selects = false;
Util.Obstructions.objects = false;
Util.Obstructions.applets = false;

// hide obstructions based on element
Util.Obstructions.hideObstructions = function(element) {
	var i, select, object, applet;

	element.x = Util.absoluteLeft(element);
	element.y = Util.absoluteTop(element);
	element.obstructed_by = new Array();

	if(Util.explorer()) {
		this.selects = this.selects ? this.selects : document.getElementsByTagName("select");
		for(i = 0; select = this.selects[i]; i++) {
			this.resolveObstruction(select, element);
		}
		this.objects = this.objects ? this.objects : document.getElementsByTagName("object");
		for(i = 0; object = this.objects[i]; i++) {
			this.resolveObstruction(object, element);
		}
	}
	this.applets = this.applets ? this.applets : document.getElementsByTagName("applet");
	for(i = 0; applet = this.applets[i]; i++) {
		this.resolveObstruction(applet, element);
	}
}

// resolve obstruction based on element
Util.Obstructions.resolveObstruction = function(obstruction, element) {
	obstruction.x = Util.absoluteLeft(obstruction);
	obstruction.y = Util.absoluteTop(obstruction);

	if(obstruction.x + obstruction.offsetWidth > element.x && element.x + element.offsetWidth > obstruction.x && obstruction.y + obstruction.offsetHeight > element.y && element.y + element.offsetHeight > obstruction.y) {
		obstruction.style.visibility = "hidden";
		obstruction.obstructing = obstruction.obstructing ? ++obstruction.obstructing : 1;
		element.obstructed_by[element.obstructed_by.length] = obstruction;
	}
}

// restore obstructions, element (optional false to restore all)
Util.Obstructions.restoreObstructions = function(element) {
	var i, obstruction, select, object, applet;

	// for one element
	if(element) {
		for(i = 0; obstruction = element.obstructed_by[i]; i++) {
			obstruction.obstructing--;
			if(obstruction.obstructing <= 0) {
				obstruction.style.visibility = "visible";
			}
		}
	}
	// or all
	else {
		if(Util.explorer()) {
			for(i = 0; select = this.selects[i]; i++) {
				select.obstructing = 0;
				select.style.visibility = "visible";
			}
			for(i = 0; object = this.objects[i]; i++) {
				object.obstructing = 0;
				object.style.visibility = "visible";
			}
		}
		for(i = 0; applet = this.applets[i]; i++) {
			applet.obstructing = 0;
			applet.style.visibility = "visible";
		}
	}
}
