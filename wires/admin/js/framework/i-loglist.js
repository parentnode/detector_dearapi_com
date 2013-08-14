
// extenders
Util.Objects["loglist"] = new function() {
	this.init = function(e) {

		var i, year, month;

		// enable open/close of years
		var years = u.qsa(".years li h3", e);
		for(i = 0; year = years[i]; i++) {
			year.onclick = function(event) {
				u.toggleClass(this.parentNode, "open");
			}
		}

		// enable select-all on months headers
		var months = u.qsa(".months li h4", e);
		for(i = 0; month = months[i]; i++) {
			month.title = "Select/Deselect month";
			month.onclick = function(event) {
				var i, state;
				var days = u.qsa("input[type=checkbox]", this.parentNode);

				if(days[0].checked) {
					state = false;
				}
				else {
					state = true;
				}

				for(i = 0; day = days[i]; i++) {
					day.checked = state;
				}
			}
		}

	}
}


// extenders
Util.Objects["logview"] = new function() {
	this.init = function(e) {
		

	}
}