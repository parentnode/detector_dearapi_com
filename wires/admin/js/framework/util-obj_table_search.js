Util.Objects["table"].initSearch = function(table) {

	table.indexContent = function() {
		var cells, element, i, cell, u;

		for(i = 0; element = this.elements[i]; i++) {
			cells = element.getElementsByTagName("td");

			this.search.info[i] = new Array();
			this.search.info[i][0] = "";
			this.search.info[i][1] = true;

			// compile all content text in info array
			for(u = 0; cell = cells[u]; u++) {
				if(cell.firstChild && cell.firstChild.nodeType == 3) {
					this.search.info[i][0] += cell.firstChild.nodeValue.toLowerCase() + " ";
				}
			}
		}
	}

	// update the content table
	table.search.update = function() {
		var info, i, element;

		// define query
		this.query = (this.value ? (this.value != "" ? this.value.toLowerCase() : false) : false);
		Util.Mem.set("q:"+table.id, this.query);

		// do we have any content
		if(this.info.length) {
			// do we have a query
			if(this.query) {
				// loop through content
				for(i = 0; info = this.info[i]; i++) {
					// if content matches query
					if(info[0].match(this.query)) {
						// if content is not already displayed, display it
						if(!info[1]) {
						 	this.table.elements[i].style.display = "";
						 	info[1] = true;
						}
					}
					// else hide content
					else if(info[1]) {
						this.table.elements[i].style.display = "none";
						info[1] = false;
					}
				}
			}
			// no search, display all
			else {
				for(i = 0; element = this.table.elements[i]; i++) {
					element.style.display = "";
					this.info[i][1] = true;
				}
			}
		}
		this.table.resetRowColor();
	}

	Util.defaultInputValue(table.search);

	// selectify input (because header is unselectified by default)
	//Util.selectify(table.header);

	table.search.table = table;
	table.id = table.id ? table.id : "";

	table.search.query = false;
	table.search.info = new Array();

	// begin indexing content
	table.indexContent();

	// run search for every keyup on the search field
	table.search.onkeyup = function() {
		this.update();
	}

	// remember search
	if(Util.Mem.get("q:"+table.id)) {
		table.search.value = Util.Mem.get("q:"+table.id);
		table.search.update();
	}

}
