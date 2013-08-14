// Browser definition utilities
Util.explorer = function(version, scope) {
	if(document.all) {
		var undefined;
		var current_version = navigator.userAgent.match(/(MSIE )(\d+.\d)/i)[2];
		if(scope && !eval(current_version + scope + version)){
			return false;
		}
		else if(version && current_version != version) {
			return false;
		}
		else {
			return current_version;
		}
	}
	else {
		return false;
	}
}
// 522 -> 3+
Util.safari = function(version, scope) {
	if(navigator.userAgent.indexOf("Safari") >= 0) {
		var undefined;
		var current_version = navigator.userAgent.match(/(Safari\/)(\d+)(.\d)/i)[2];
		if(scope && !eval(current_version + scope + version)){
			return false;
		}
		else if(scope && version && current_version != version) {
			return false;
		}
		else {
			return current_version;
		}
	}
	else {
		return false;
	}
}
Util.firefox = function(version, scope) {
	if(navigator.userAgent.indexOf("Firefox") >= 0) {
		var undefined;
		var current_version = navigator.userAgent.match(/(Firefox\/)(\d+\.\d+)(\.\d+)/i)[2];
		if(scope && !eval(current_version + scope + version)){
			return false;
		}
		else if(version && current_version != version) {
			return false;
		}
		else {
			return current_version;
		}
	}
	else {
		return false;
	}
}

Util.opera = function() {
	return (navigator.userAgent.indexOf("Opera") >= 0) ? true : false;
}

// OS definition utilities
Util.windows = function() {
	return (navigator.userAgent.indexOf("Windows") >= 0) ? true : false;
}
Util.osx = function() {
	return (navigator.userAgent.indexOf("OS X") >= 0) ? true : false;
}

// Compiles mailto link based on name and domain
Util.otliam = function(name, dom){
	document.write('<a onclick="Util.otliamNoise(\''+name+'\', \''+dom+'\')">'+name+'<span>@</span>'+dom+'</a>');
}
Util.otliamNoise = function(name, dom){
	location.href = "ma"+"ilto:"+name+"@"+dom;
}

// Save cookie
Util.saveCookie = function(name, value) {
	document.cookie = name + "=" + value +";"
}
// Get cookie
Util.getCookie = function(name){
	var cookie_id, cookie_position, cookie_value, cookie_value_start, cookie_value_end;
	cookie_id = name + "=";
	cookie_position = document.cookie.indexOf(cookie_id);
	if(cookie_position != -1) {
		cookie_value_start = cookie_position + cookie_id.length;
		cookie_value_end = document.cookie.indexOf(';', cookie_value_start);
		cookie_value_end = cookie_value_end > 0 ? cookie_value_end : document.cookie.length;
		cookie_value = document.cookie.substring(cookie_value_start, cookie_value_end);
		return unescape(cookie_value);
	}
	return false;
}
// Delete cookie
Util.delCookie = function(name) {
	document.cookie = name + "=;expires=Thu, 01-Jan-70 00:00:01 GMT";
}

// Create popup (default scrollbars enabled)
Util.popUp = function(url, name, w, h, extra) {
	var p;
	name = name ? name : "POPUP_" + new Date().getHours() + "_" + new Date().getMinutes() + "_" + new Date().getMilliseconds();
	w = w ? w : 330;
	h = h ? h : 150;
	p = "width=" + w + ",height=" + h;
	p += ",left=" + (screen.width-w)/2;
	p += ",top=" + ((screen.height-h)-20)/2;
	p += extra ? "," + extra : ",scrollbars";
	document[name] = window.open(url, name, p);
}
// Extracting variable from location search
Util.getVar = function(s) {
	var p = location.search;
	var start_index = (p.indexOf("&" + s + "=") > -1) ? p.indexOf("&" + s + "=") + s.length + 2 : ((p.indexOf("?" + s + "=") > -1) ? p.indexOf("?" + s + "=") + s.length + 2 : false);
	var end_index = (p.substring(start_index).indexOf("&") > -1) ? p.substring(start_index).indexOf("&") + start_index : false;
	var return_string = start_index ? p.substring(start_index,(end_index ? end_index : p.length)): "";
	return return_string;
}
// Extracting value for parameter s from location hash
Util.getHashVar = function(s) {
	var h = location.hash;
	var values, index, list;
	values = h.substring(1).split("&");
	for(index in values) {
		list = values[index].split("=");
		if(list[0] == s) {
			return list[1];
		}
	}
	return false;
}

// Flash writer
Util.flash = function(url, w, h, background, name, id, print) {
	var s;
	background = background ? background : "#FFFFFF";
	name = name ? name : "flash_" + new Date().getHours() + "_" + new Date().getMinutes() + "_" + new Date().getMilliseconds();
	id = id ? id : name;
	s = '<object id="'+name+'" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="'+w+'" height="'+h+'" name="'+name+'" align="middle">';
	s += '<param name="allowScriptAccess" value="always" />';
	s += '<param name="movie" value="'+url+'" />';
	s += '<param name="quality" value="high" />';
	s += '<param name="bgcolor" value="'+background+'" />';
	s += '<param name="wmode" value="transparent" />';
	s += '<param name="menu" value="false" />';
	s += '<param name="scale" value="noscale" />';
	s += '<embed id="'+name+'" src="'+url+'" menu="false" scale="noscale" quality="high" bgcolor="'+background+'" wmode="transparent" width="'+w+'" height="'+h+'" name="'+name+'" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />';
	s += '</object>';
	if(print) {
		document.write(s);
		return "";
	}
	return s;
}



Util.Mem = new Object();
Util.Mem.set = function(key, value) {
	var view_id = this.getViewId();
	var memories = eval(Util.getCookie("memories"));

	//Util.debug("MEM:set:" + key + ":" + value);
	//Util.debug("MEM:view_id:" + view_id);

	if(!memories) {
		memories = new Object();
	}
	if(!memories[view_id]) {
		memories[view_id] = new Object();
	}

	memories[view_id][key] = value ? value : "";
//	document.cookie = "memories=" + memories.toSource() +";"
	document.cookie = "memories=" + memories.asSource() +";"
//	Util.saveCookie("memory", value);
}

Util.Mem.get = function(key) {
	var view_id = this.getViewId();
	var memories = eval(Util.getCookie("memories"));

//	Util.debug("MEM:get:"+key);
//	Util.debug("MEM:view_id:"+view_id);

	if(memories && memories[view_id]) {
//		Util.debug("MEM:get:"+key+":" + memories[view_id][key]);
		return memories[view_id][key] ? memories[view_id][key] : "";
	}
	return false;
}
// get relevant part of url to use as memory key
Util.Mem.getViewId = function() {
	var url = location.href.substring(location.href.indexOf("//")+2);
	return view_id = url.substring(url.indexOf("/"), (url.indexOf(",id=") > -1 ? url.indexOf(",id=") : url.length));
}
