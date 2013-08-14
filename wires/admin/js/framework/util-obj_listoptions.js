Util.Objects["listoptions"] = new function() {

	this.init = function(list) {

		Util.removeClass(list, "init:listoptions");
		Util.addClass(list, "listoptions");

		// memory?
		Util.addClass(list, Util.Mem.get("option"));

		list.updateListImages = function() {
			var i, e, id, image;

			Util.debug(this.childNodes.length);
	//		list.elements = list ? list.getElementsByTagName("li") : false;
	//		Util.debug(list.elements.length);
			var img = new Image();

			for(i = 0; e = this.childNodes[i]; i++) {
				var id = Util.getIJ("id", e);
				var regexp = new RegExp("(^|\\s)image(\\s|$|\:)");
				if(regexp.test(e.className) && id) {
	//				Util.debug("/images/"+id+"/x200.jpg");
					if(list.className.match("small")) {
						image = "x35.jpg";
						e.style.height = "35px";
					}
					else if(list.className.match("large")) {
						image = "750x.jpg";
						img.src = "/images/"+id+"/"+image;
						e.style.height = img.height+"px";
					}
					else {
						image = "184x.jpg";
						e.style.height = "184px";
					}

					e.style.backgroundImage = "url(/images/"+id+"/"+image+")";
				}
			}


		}

		list.updateListImages();

		var options = document.createElement("li");

		options.list = list;
		options.className = "options";

		var options_small = document.createElement("span");
		options_small.innerHTML = "small"
		options_small.list = list;
		options_small.className = "small";
		options_small.onclick = function() {
			Util.Mem.set("option", "small");
			Util.addClass(this.list, "small");
			Util.removeClass(this.list, "medium");
			Util.removeClass(this.list, "large");
			this.list.updateListImages();
		}

		var options_medium = document.createElement("span");
		options_medium.innerHTML = "medium"
		options_medium.list = list;
		options_medium.className = "medium";
		options_medium.onclick = function() {
			Util.Mem.set("option", "medium");
			Util.addClass(this.list, "medium");
			Util.removeClass(this.list, "small");
			Util.removeClass(this.list, "large");
			this.list.updateListImages();
		}

		var options_large = document.createElement("span");
		options_large.innerHTML = "large"
		options_large.list = list;
		options_large.className = "large";
		options_large.onclick = function() {
			Util.Mem.set("option", "large");
			Util.addClass(this.list, "large");
			Util.removeClass(this.list, "medium");
			Util.removeClass(this.list, "small");
			this.list.updateListImages();
		}

		options_small = options.appendChild(options_small);
		options_medium = options.appendChild(options_medium);
		options_large = options.appendChild(options_large);

		list.insertBefore(options, list.firstChild);

	}

}
