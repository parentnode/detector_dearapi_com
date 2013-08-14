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


// update seoindex field when page name is updated
Util.Objects["seoindex"] = new function() {
	this.init = function(input) {
		var target = document.getElementById(Util.getIJ("target", input));
		target.carbon = input;

		// do not alter frontpage item
		if(target.carbon.value != "frontpage") {
			Util.addEventHandler(target, "keyup", function() {
				this.carbon.value = this.value.toLowerCase().replace(/ |&|\.|!|,|\:|\?|;/g, "_");
			});
			Util.addEventHandler(input, "keyup", function() {
				this.value = this.value.toLowerCase().replace(/ |&|\.|!|,|\:|\?|;/g, "_");
			});
		}
	}
}
Util.Objects["file"] = new function() {

	this.init = function(input) {

		input.init = function() {
//			Util.debug("init:" +this + "::" + typeof(this.submit_form) + "#" + this.submit_form);
		
			if(typeof(this.submit_form) == "undefined") {

				Util.Ontimeout.setTimer(this, this.init, 2000);
			}
			else {
				form = Util.wrapElement(this.submit_form, "form");
				this.submit_form.html_form = form;
				form.action = Util.getIJ("form:action", this.submit_form);
				form.method = "post";
				form.enctype = "multipart/form-data";
				this.submit_form.status_input.value = "page,save";

				Util.debug(this.submit_form.html_form);
				
				for(i = 0; input = this.submit_form.inputs[i]; i++) {

					input.onkeyup = function(event) {
						Util.debug("ggg" + this);
						event = event ? event : window.event;
						if(event.keyCode == 13) {
							this.submit_form.html_form.submit();
						}
					}
				}

				for(i = 0; button = this.submit_form.buttons[i]; i++) {
					Util.debug("but:" + button);
					button.onclick = function() {
						Util.debug("sub:" + this);
						this.submit_form.html_form.submit();
					}
				}
			}

		}
		Util.addClass(input, "file");
		input.init();

	}
}

Util.Objects["edits"] = new function() {
	
	this.addEditOptions = function(e) {

		e.save = document.createElement("span");
		e.save.className = "save";
		e.save.e = e;
		e.save.innerHTML = "&#x2714;"; //   ✔

		e.cancel = document.createElement("span");
		e.cancel.className = "cancel";
		e.cancel.e = e;
		e.cancel.innerHTML = "&#x2718;"; //   ✘

		e.save = e.appendChild(e.save);
		e.cancel = e.appendChild(e.cancel);

		e.save.onclick = function(event) {
			Util.nonClick(event);

			Util.debug("click save" + this + "::" + this.e);
			this.e.submitForm(this.e);
		}

		e.cancel.onclick = function(event) {
			Util.nonClick(event);

			Util.debug("click cancel" + this + "::" + this.id);

			this.e.viewMode(event, this.e);
		}

	}

	this.setViewState = function(e) {
		if(e.className.match("editing")) {
			e.editMode(window, e);
		}
		else {
			e.viewMode(window, e);
		}
	}

}


Util.Objects["editInput"] = new function() {

	this.init = function(e) {

		Util.removeClass(e, "init:editInput");
		Util.addClass(e, "edit");

		// check delivered content
		e.html_p = e.getElementsByTagName("p")[0];
		// view state
		if(e.html_p) {
			e.html_input = document.createElement("input");
			e.html_input = e.appendChild(e.html_input);
		}
		// edit state
		else {
			Util.addClass(e, "editing");
		 	e.html_label = e.getElementsByTagName("label")[0];
			e.html_input = e.getElementsByTagName("input")[0];
			e.html_p = document.createElement("p");
			e.html_p = e.appendChild(e.html_p);
			e.html_p.innerHTML = e.html_input.value;
		}

		var proporties = e.id.split(":");
		e.page_status = proporties[0];
		e.html_input.name = proporties[1];
		e.item_id = proporties[2];

		e.html_input.e = e;



		e.editMode = function(event, e) {
			Util.nonClick(event);
			Util.addClass(e, "editing");

			Util.debug("edit input" + e + "::" + e.id);
//			Util.removeClass(e, "clickable");

			e.html_input.value = e.html_p.innerHTML;

			e.html_input.focus();

			e.onclick = null;

			document.editee = e;
			/* exit on document click
			document.onclick = function(event) {
				Util.debug("click document (input)" + this + "::" + this.editee);
				this.editee.viewMode(event, this.editee);
				this.editee = false;
			}
			*/
		}
		e.viewMode = function(event, e) {
			event = event ? event : window.event;
			Util.nonClick(event);
			Util.removeClass(e, "editing");

			Util.debug("view input" + e + "::" + e.id);

			document.editee = false;
			document.onclick = null;

			// set input value
			/*
			if(e.input_element.value != e.p.innerHTML) {
				alert("You have not saved! ok?");
			}
			*/


//			Util.addClass(e, "clickable");

			e.onclick = function(event) {
				if(document.editee) {
					document.editee.viewMode(event, document.editee);
					document.editee = false;
				}

				Util.debug("click input view" + this + "::" + this.id);
				this.editMode(event, this);
			}
			
		}

		e.html_input.onclick = function(event) {
			event = event ? event : window.event;
			Util.nonClick(event);
		}
		e.html_input.onkeydown = function(event) {
			event = event ? event : window.event;

			// Util.debug("Key: " + String.fromCharCode(event.keyCode) + "\nKeyCode: " + event.keyCode + "\nCtrl:"  + event.ctrlKey + "\nMeta:"  + event.metaKey + "\nShift:"  + event.shiftKey);

			// save option
			if(event.keyCode == 83 && event.metaKey) {
				Util.nonClick(event);
				Util.debug("key up" + this + "::" + this.e);
				Util.Ajax.submitElement(this.e);
			}
		}

		e.html_input.onkeyup = function(event) {
			event = event ? event : window.event;
			Util.nonClick(event);

			if(event.keyCode == 13) {
				Util.debug("key up" + this + "::" + this.e);
				Util.Ajax.submitElement(this.e);
			}
			if(event.keyCode == 27) {
				Util.debug("key esc" + this + "::" + this.e);
				this.e.viewMode(event, this.e);
			}
		}
		
		e.submitForm = function(e) {
			Util.Ajax.submitElement(e);
		}

		Util.Objects["edits"].addEditOptions(e);
		Util.Objects["edits"].setViewState(e);

	}
}


Util.Objects["editImage"] = new function() {

	this.init = function(e) {

		Util.removeClass(e, "init:editImage");
		Util.addClass(e, "edit");
		Util.addClass(e, "image");

		// check delivered content
		e.html_input = e.getElementsByTagName("input")[0];
		// view state
		if(!e.html_input) {
			e.html_input = document.createElement("input");
			e.html_input = e.appendChild(e.html_input);
			e.html_input.type = "file";
			Util.addClass(e.html_input, "file");

			/*

			*/
		}
		// edit state
		else {
			Util.addClass(e, "editing");
			/*
		 	e.html_label = e.getElementsByTagName("label")[0];
			e.html_input = e.getElementsByTagName("input")[0];
			e.html_p = document.createElement("p");
			e.html_p = e.appendChild(e.html_p);
			e.html_p.innerHTML = e.html_input.value;
			*/
		}

		var proporties = e.id.split(":");
		e.page_status = proporties[0];
		e.html_input.name = proporties[1];
		e.item_id = proporties[2];



		e.editMode = function(event, e) {
			event = event ? event : window.event;
			Util.nonClick(event);
			Util.addClass(e, "editing");

			Util.debug("edit image" + e + "::" + e.id);

			/*

			*/
			e.onclick = null;
			/*
			e.onclick = function(event) {
				Util.debug("click e (image)" + this + "::" + this.editee);
				Util.nonClick(event);
			}
			*/

			document.editee = e;
			/*
			document.onclick = function(event) {
				Util.debug("click document (image)" + this + "::" + this.editee);
				this.editee.viewMode(event, this.editee);
				this.editee = false;
			}
			*/
		}

		e.viewMode = function(event, e) {
			event = event ? event : window.event;
			Util.nonClick(event);
			Util.removeClass(e, "editing");

			Util.debug("view image" + e + "::" + e.id);

			document.editee = false;
			document.onclick = null;

			e.onclick = function(event) {
				event = event ? event : window.event;

				if(document.editee) {
					document.editee.viewMode(event, document.editee);
					document.editee = false;
				}

				Util.debug("click view image" + this + "::" + this.id);
				this.editMode(event, this);
			}

		}
		e.submitForm = function(e) {

			var form = Util.wrapElement(e.submit_form, "form");
			e.submit_form.html_form = form;
			form.action = Util.getIJ("form:action", e.submit_form);
			form.method = "post";
			form.enctype = "multipart/form-data";
			e.submit_form.status_input.value = e.page_status; //"page,update";
			e.submit_form.id_input.value = e.item_id;
			e.submit_form.html_form.submit();
			

		}


		Util.Objects["edits"].addEditOptions(e);
		Util.Objects["edits"].setViewState(e);

	}
}
