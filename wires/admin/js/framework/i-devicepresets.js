// extenders
Util.Objects["devicepresets"] = new function() {
	this.init = function(e) {
		var i, preset;

		var presets = u.ges("li", e);
		for(i = 0; preset = presets[i]; i++) {
			
			preset.onclick = function() {
				document.getElementById('pattern').value = this.innerHTML;
				Util.selectValue('brand_id', Util.getIJ("brand", this));
				Util.Ajax.submitContainer("container:item");
			}
		}
	}
}

Util.Objects["autosearch"] = new function() {
	this.init = function(e) {

		e.results = u.ge("patternsearchresult");

		e.dosearch = function() {
			Util.Ajax.send("/devices/devices_unidentified.php", this.searched, this, "page_status=find_devices_by_pattern&pattern=" + this.value);
		}
		e.searched = function(response) {
			this.results.innerHTML = "";

			if(response) {
				this.results.innerHTML = response.resultText;

				var devices = u.ges("device", this.results);
				for(i = 0; device = devices[i]; i++) {
					device.autosearch = this;
					device.onclick = function() {

						this.autosearch.brand_id = u.ge("brand_id");
						this.autosearch.device_id = u.ge("device_id");
						if(this.autosearch.brand_id) {
							this.autosearch.brand_id.parentNode.removeChild(this.autosearch.brand_id);
						}
						if(this.autosearch.device_id) {
							this.autosearch.device_id.parentNode.removeChild(this.autosearch.device_id);
						}

						this.autosearch.brand_id = this.autosearch.results.appendChild(document.createElement("input"));
						this.autosearch.brand_id.name = "brand_id";
						this.autosearch.brand_id.value = u.getIJ("brand", this);

						this.autosearch.device_id = this.autosearch.results.appendChild(document.createElement("input"));
						this.autosearch.device_id.name = "device_id";
						this.autosearch.device_id.value = u.getIJ("id", this);

						Util.Ajax.submitContainer("container:item");
					}
				}
			}


		}

		e.t_search = false;
		e.onkeydown = function() {
			Util.Ontimeout.resetTimer(this.t_search);
		}

		e.onkeyup = function() {
			Util.Ontimeout.resetTimer(this.t_search);
			this.t_search = Util.Ontimeout.setTimer(this, this.dosearch, 500);
		}
	}
}
