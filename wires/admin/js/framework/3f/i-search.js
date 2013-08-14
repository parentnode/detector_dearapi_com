Util.Objects["search"] = new function() {
	this.init = function(e) {
		e.submit = e.cloneNode(e);
		e.submit.className = "button submit";
		e.parentNode.appendChild(e.submit);
		
		e.onclick = function(event) {
			event = event ? event : window.event;
			if(event.preventDefault) {event.preventDefault();}
			if(event.stopPropagation) {event.stopPropagation();}
			event.returnValue = false;
			event.cancelBubble = true;
			document.getElementById("searchtext").style.display = "block";
			e.submit.style.display = "block";
		}
	}
}
