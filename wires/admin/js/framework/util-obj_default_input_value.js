Util.Objects["defaultInputValue"] = new function() {

	this.init = function(input) {

		// set default input value
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
