// dynamic labels
Util.Objects["dylabel"] = new function() {
	this.init = function(e) {
		var i, label, input;
		var labels = e.getElementsByTagName("label");
		for(i = 0; label = labels[i]; i++) {

			if(label.getAttribute("for") && document.getElementById(label.getAttribute("for"))) {

				input = document.getElementById(label.getAttribute("for"));
				if(input && (input.type == "text" && (!input.value || input.value == label.firstChild.nodeValue.trim())) || (input.type == "textarea" && (!input.value || input.value == label.firstChild.nodeValue.trim()))) {
					input.value = label.firstChild.nodeValue.trim();
					input.defaultValue = input.value;
					input.onfocus = function() {
						if(this.value == this.defaultValue) {
							this.value = "";
						}
					}
					input.onblur = function() {
						if(this.value == "") {
							this.value = this.defaultValue;
						}
					}
				}
			}
		}
	}
}
