// limits length of string and adds dots
String.prototype.cutString = function(l) {
	var length_compensation, matches, i;

	length_compensation = 0;
	// find entity matches
	matches = this.match(/(\&)([\w\d]+)(\;)/g);
	// calculate length compensation
	for (i = 0; match = matches[i]; i++){
		// only compensate if entity is within shown length
		if(this.indexOf(match) < l){
			l += match.length-1;
		}
	}

	return this.substring(0, l) + (this.length > l ? "..." : "");
}

// trim whitespace and crlf
String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g, "");
}
