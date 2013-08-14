// Object.toSource replacement (gecko only)
Object.prototype.asSource = function() {
	var string = "({";
	for(index in this) {
		if(typeof(this[index]) == "string") {
			string += '"' + index + '":"' + this[index] + '",';
		}
		else if(typeof(this[index]) == "object") {
			string += '"' + index + '":' + this[index].asSource() + ',';
		}
	}
	string += "})";
	return string;
}