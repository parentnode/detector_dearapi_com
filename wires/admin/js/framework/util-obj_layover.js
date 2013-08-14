
Util.layOverUrl = function(url) {
	var layOver = Util.makeLayOver();
	var layIn = document.createElement("div");
	layIn.id = "layIn";
	layOver.appendChild(layIn);

	Util.Ajax.loadContainer(url, layIn.id);
	this.Objects["center"].init(layOver);

	Util.initElements(layIn);
}

Util.makeLayOver = function() {

	if(!document.getElementById("layOverBg")) {
		var layOverBg = document.createElement("div");
		layOverBg.id = "layOverBg";
	}
	else {
		layOverBg = document.getElementById("layOverBg");
	}

	var layOver = document.createElement("div");
	layOver.id = "layOver";
//	layOver.className = "init:center";

	var bn_close = document.createElement("a");
	bn_close.innerHTML = "Luk vindue";
	bn_close.className = "close";
	bn_close.onclick = Util.destroyLayOver;

	layOver.appendChild(bn_close);

	layOverBg.appendChild(layOver);
	//body_form = document.getElementById("aspnetForm");

	//alert(document.body.getElement)
	//body_form.appendChild(layOverBg);
	document.body.appendChild(layOverBg);

	layOverBg.style.height = document.body.offsetHeight+10 + 'px';

	if(Util.explorer(6, "<=")) {
		layOverBgIframe = layOverBg.appendChild(document.createElement("iframe"));
		layOverBgIframe.id = "layOverIframe";
//		layOverBgIframe.className = "init:center";
		layOverBgIframe.style.visibility = "hidden";
	}

	layOverBg.style.display = "block";
	//$("#layOverBg").fadeIn("slow");

	return layOver;

}
Util.destroyLayOver = function() {
	var layOverBg = document.getElementById("layOverBg");
	Util.removeLayOver();
	//$("#layOverBg").fadeOut("slow", Util.removeLayOver);

	if(Util.explorer(6, "<=")) {
		var layOverBgIframe = document.getElementById("layOverIframe");
		layOverBgIframe.parentNode.removeChild(layOverBgIframe);

	}
}
Util.removeLayOver = function() {
	var layOverBg = document.getElementById("layOverBg");
	if(layOverBg.container_id) {
		document.getElementById(layOverBg.container_id).appendChild(document.getElementById(layOverBg.container_id+"Content"));
	}
	layOverBg.parentNode.removeChild(layOverBg);
}


Util.Objects["center"] = new function() {
	this.init = function(element) {
		element.style.position = "absolute";
		element.style.top = ((Util.explorer()) ? document.documentElement.scrollTop : window.pageYOffset)+((Util.docHeight()-element.offsetHeight)/2)+'px';
		element.style.left = ((Util.docWidth()-element.offsetWidth)/2) + 'px';
		element.style.visibility = "visible";
		if(Util.explorer(6, "<=") && element.id == "layOver") {
			Util.ie6iframe(element);
		}
	}
}

Util.ie6iframe = function(element) {
	var layOverBgIframe = document.getElementById("layOverIframe");
	layOverBgIframe.style.height = element.offsetHeight + 'px';
	layOverBgIframe.style.width = element.offsetWidth + 'px';
	layOverBgIframe.style.left = element.offsetLeft + 'px';
	layOverBgIframe.style.top = element.offsetTop + 'px';
	layOverBgIframe.style.visibility = "visible";
}
