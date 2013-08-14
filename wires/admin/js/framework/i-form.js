Util.Objects["form"] = new function() {

	// init buttons in form node
	this.init = function(e) {
		var buttons = u.ges("button", e);

		for(button in buttons) {
			var b = buttons[button];
			if(b.nodeName){
				b.e = e;
//			if(typeof(b) != "number" && typeof(b) != "function") {
//				u.bug(typeof(b))
				

//				u.bug(b.nodeName)
				if(b.type == "submit") {
					b.setAttribute("type", "button");
				}
				u.e.click(b);
				b.clicked = function() {
					u.bug("clicked");
					this.e.submit();
				}
			}

//			u.bug(buttons[button].type);
		}

		e.submit = function() {
//			u.bug("submit")
			var url = u.getIJ(this, "action") ? u.getIJ(this, "action") : location.href.split("?")[0];

//			Util.Ajax.send(url, e.XMLResponse, e, u.formObjectToString(u.getFormElements(this)));
			u.XMLRequest(url, this, u.getFormElements(this), false);
		}

		e.XMLResponse = function(response) {
//			u.bug(this.id + response);

			response = this.parentNode.replaceChild(response.firstChild, this);
			u.init(response);

//			u.bug("node reponse:" + response.innerHTML)
//			alert(response);
		}
//		u.bug("submit")
		e.submit();
	}

}
