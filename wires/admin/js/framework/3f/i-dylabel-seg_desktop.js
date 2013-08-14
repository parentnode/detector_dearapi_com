// dynamic labels
Util.Objects["dylabel"] = new function() {
	this.init = function(e) {
		var i, label, input;
		var labels = e.getElementsByTagName("label");
		for(i = 0; label = labels[i]; i++) {

			if(label.getAttribute("for") && document.getElementById(label.getAttribute("for"))) {

				input = document.getElementById(label.getAttribute("for"));
				if(input && (input.type == "text" && (!input.value || input.value == label.title.trim())) || (input.type == "textarea" && (!input.value || input.value == label.title.trim()))) {
					input.value = label.title.trim();
					input.defaultValue = input.value;
					Util.addClass("default", input);

					input.onfocus = function() {
						if(this.value == this.defaultValue) {
							this.value = "";
							Util.removeClass(this, "default");
						}
					}
					input.onblur = function() {
						if(this.value == "") {
							this.value = this.defaultValue;
							Util.addClass(this, "default");
						}
					}
				}
			}
		}
	}
}
