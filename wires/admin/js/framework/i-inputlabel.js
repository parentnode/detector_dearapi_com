Util.Objects["inputlabel"] = new function() {

	this.init = function(input) {

		// set default input value
		input.defaultValue = input.value;

		input.onfocus = function() {
			u.removeClass(this, "default");
			if(this.value == this.defaultValue) {
				this.value = "";
			}
		}
		input.onblur = function() {
			if(this.value == "") {
				u.addClass(this, "default");
				this.value = this.defaultValue;
			}
		}
		u.addClass(input, "default");
	}

}
