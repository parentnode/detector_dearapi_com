Util.Objects["autocomplete"] = new function() {

	this.init = function(element) {
		var div, autocomplete;

		element.enableAutoComplete = function(response) {
			var i, options, option_set, option_array;
			options = response.resultText.trim().split("#");
			this.allOptions = new Array();
			this.updateTargets = new Array();
			this.updateValues = new Array();
			// index autocomplete info
			for(i = 0; option_set = options[i]; i++) {
				option_array = option_set.split("->");
				this.allOptions[i] = option_array[0];
				this.updateTargets[i] = new Array();
				this.updateValues[i] = new Array();

				// Additional values are target:value sets, defining targets which should be updated with values
				for(u = 1; u+1 < option_array.length; u++) {
					this.updateTargets[i][this.updateTargets[i].length] = option_array[u++].trim();
					this.updateValues[i][this.updateValues[i].length] = option_array[u].trim();
				}
			}
		}

		// save original onkeyup
		element.orgonkeyup = element.onkeyup;

		// handle key presses
		element.onkeyup = function(event) {
			var i, u, option, regexp, option_div, e, target, allOptionsIndex;
			event = event ? event : window.event;
			Util.nonClick(event);

			// no options -> return without further notice
			if(!this.allOptions.length) {
				this.orgonkeyup(event);
				return;
			}
			// if ESC -> hide options
			else if(event.keyCode == 27) {
				this.autocompleteElement.style.display = "none";
				this.selectedIndex = -1;
			}
			// if ENTER -> use selected value
			else if(event.keyCode == 13) {

				// if options available
				if(typeof(this.selectedIndex) != "undefined" && this.selectedIndex != -1) {

					allOptionsIndex = this.options[this.selectedIndex].allOptionsIndex;
					this.value = this.allOptions[allOptionsIndex];

					// do we need to update targets
					if(this.updateTargets[allOptionsIndex].length) {
						for(i = 0; i < this.updateTargets[allOptionsIndex].length; i++) {
							target = document.getElementById(this.updateTargets[allOptionsIndex][i]);
							// target is select
							if(target.nodeName.toLowerCase() == "select") {
								for(u = 0; u < target.options.length; u++) {
									if(target.options[u].value == this.updateValues[allOptionsIndex][i]){
										target.options[u].selected = true;
									}
								}
							}
							// target is input, type=text
							else if(target.nodeName.toLowerCase() == "input" && target.type == "text") {
								target.value = this.updateValues[allOptionsIndex][i];
							}
							// if target updates something, help it succeed
							if(target.onchange){
								target.onchange();
							}
						}
					}
					this.autocompleteElement.style.display = "none";
					this.selectedIndex = -1;

				}
				// else submit form
				else {
					this.autocompleteElement.style.display = "none";
					this.orgonkeyup(event);
				}

			}
			// if DOWN -> move selection down
			else if(event.keyCode == 40 && this.options.length) {
				if(this.selectedIndex != -1) {
					this.options[this.selectedIndex].className = this.options[this.selectedIndex].className.replace(/ selected|selected |selected/g, "");
				}
				// next option
				if(this.options.length > (this.selectedIndex+1)) {
					this.selectedIndex++;
				}
				// return to first option
				else {
					this.selectedIndex = 0;
				}
				this.options[this.selectedIndex].className = "selected";
				Util.nonClick(event);
			}
			// if UP -> move selection up
			else if(event.keyCode == 38 && this.options.length) {
				if(this.selectedIndex != -1) {
					this.options[this.selectedIndex].className = this.options[this.selectedIndex].className.replace(/ selected|selected |selected/g, "");
				}
				// previous option
				if(this.selectedIndex > 0) {
					this.selectedIndex--;
				}
				// return to last option
				else {
					this.selectedIndex = this.options.length-1;
				}
				this.options[this.selectedIndex].className = "selected";
				Util.nonClick(event);
			}
			// if input value more than 1 chars, populate autocomplete element
			else if(this.value.length > 1) {

				// clear autocomplete element
				this.autocompleteElement.style.display = "none";
				this.autocompleteElement.innerHTML = "";
				this.options = new Array();

				for(i = 0; i < this.allOptions.length; i++) {
//				for(i = 0; option = this.allOptions[i]; i++) {
					option = this.allOptions[i];
//					Util.debug("r" + this.allOptions.length + ":" + option);
					// find matching options
					if(option && option.toLowerCase().indexOf(this.value.toLowerCase()) == 0) {

						// create option div
						option_div = document.createElement("div");
						option_div.innerHTML = option;
						option_div.allOptionsIndex = i;

						// set selected value
						this.selectedIndex = -1;
						// this code for autoselect first value
						//if(this.options.length == 0) {
						//	option_div.className = "selected";
						//	this.selectedIndex = 0;
						//}

						this.autocompleteElement.appendChild(option_div);
						this.autocompleteElement.style.top = Util.absoluteTop(this)+18+"px";
						this.autocompleteElement.style.left = Util.absoluteLeft(this)+"px";
						this.autocompleteElement.style.display = "block";
						this.options[this.options.length] = option_div;
					}
				}
			}
			// otherwise hide options
			else {
				this.autocompleteElement.style.display = "none";
			}

			// make sure autocomplete element is removed appropriately
			element.onblur = function(event) {
				event = event ? event : window.event;
				Util.nonClick(event);
				this.autocompleteElement.style.display = "none";
			}
		}

		// get definitions
		autocomplete = Util.getIJ("autocomplete", element).split(":");
		if(autocomplete.length) {

			// get autocomplete options
			Util.Ajax.send(autocomplete[1], element.enableAutoComplete, element, "page_status="+autocomplete[0]);

			// create options container
			div = document.createElement("div");
			document.body.appendChild(div);
			div.id = "autocompleteOptions";
			div.className = "autocompleteOptions";
			div.style.width = element.offsetWidth-2+"px";
			element.autocompleteElement = div;
		}
	}
}
