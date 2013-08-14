Util.Objects["form"] = new function() {

	// init buttons for element
	this.init = function(form) {
		var i, button, input, pseudo_input;

//		Util.debug("f:" + form);
//		Util.addClass(form, "section");

		// Ajax form
		if(Util.getIJ("form", form)) {
			form.inputs = form.getElementsByTagName("input");
			for(i = 0; input = form.inputs[i]; i++) {
				// identify default status element
				input.submit_form = form;
				if(input.type == "hidden" && input.name == "page_status") {
					form.status_input = input;
				}
				// id element
				else if(input.type == "hidden" && input.name == "id") {
					form.id_input = input;
				}
				else if(input.type == "text") {
					// set submit on correct event, needs to be the last event executed to control flow
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
			// find all buttons (elements with a status:identifier)
			form.buttons = Util.getElementsByClass("status([:a-z_0-9])+", form);
			for(i = 0; button = form.buttons[i]; i++) {
				button.status_value = Util.getIJ("status", button);
				button.status_input = form.status_input ? form.status_input : false;
				button.id_value = Util.getIJ("id", button);
				button.link_value = Util.getIJ("status:link", button);
				button.id_input = form.id_input ? form.id_input : false;
				button.submit_form = form;
				Util.addClass(button, "clickable");
				// js delete confirm
				if(button.status_value == "delete_confirm") {
					Util.addClass(button, "delete");
					button.onclick = function() {
						Util.Ajax.deleteConfirm(this.submit_form);
					}
				}
				else {
					button.onclick = function() {
						if(this.link_value) {
							location.href = this.link_value;
						}
						else {
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
				button.shortcut = Util.getIJ("key", button);
				if(button.shortcut) {
					Util.Onkeydown.addShortcut(button.shortcut, button);
				}
			}
		}
		// find all buttons (elements with a status:identifier)
		form.pseudo_inputs = Util.getElementsByClass("type:input", form);
		for(i = 0; pseudo_input = form.pseudo_inputs[i]; i++) {
			pseudo_input.submit_form = form;
		}
		// Classic form
		/*
		else if(form.nodeName == "FORM") {
			inputs = form.getElementsByTagName("input");
			for(i = 0; input = inputs[i]; i++) {
				// identify default status element
				if(input.type == "hidden" && input.name == "page_status") {
					form.status_input = input;
				}
				// id element
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
		*/
	}
}

Util.Objects["bundlevalue"] = new function() {

	// init buttons for element
	this.init = function(e) {
		var input, i = 0;

		this.inputs = e.getElementsByTagName("table")[0].getElementsByTagName("input");
		this.button = document.getElementById("bundlevalueUpdate");
//		alert(this.button);

		for(i = 0; input = this.inputs[i]; i++) {
			input.onchange = function() {
				Util.Objects["bundlevalue"].updateValues();
			}
		}
		this.value_base = document.getElementById("value_base");

		this.value_base.onchange = function() {
			Util.Objects["bundlevalue"].updateValues();
		}
		this.updateValues();
	}

	this.updateValues = function() {
		var value_base = this.value_base.options.length ? this.value_base.options[this.value_base.selectedIndex].value : 0;
		var total = 0;
		for(i = 0; input = this.inputs[i]; i++) {
			if(input.value) {
				total = total + parseFloat(input.value);
				var node = Util.nextRealSibling(input.parentNode);
				node.innerHTML = Math.round((value_base*(input.value/100))*100)/100;
				//node.innerHTML = Math.round((value_base*(input.value/100))*100)/100;
			}
		}

		if(total == 100) {
			Util.removeClass(this.button, "disabled");
			this.button.disabled = false;
		}
		else {
			Util.addClass(this.button, "disabled");
			this.button.disabled = true;
		}
	}

}
