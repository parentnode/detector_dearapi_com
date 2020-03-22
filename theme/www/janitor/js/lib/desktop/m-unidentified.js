Util.Objects["searchUnidentified"] = new function() {
	this.init = function(form) {

		u.f.init(form);
		
		form.submitted = function() {
			
			if(!form.inputs["search_string"].val()) {
				form.inputs["search_string"].val("");
			}

			// manual submit
			form.DOMsubmit();
		}

	}
}


Util.Objects["unidentifiedList"] = new function() {
	this.init = function(div) {
//		console.log("init unidentifiedList")

//		u.bug_force = true;

		var i, node;

		div.list = u.qs("ul.items", div);
		div.nodes = u.qsa("li.item", div.list);
	
		// get all data urls
		div.csrf_token = div.getAttribute("data-csrf-token");
		div.useragent_delete = div.getAttribute("data-useragent-delete");
		div.useragent_details = div.getAttribute("data-useragent-details");
		div.useragent_identify = div.getAttribute("data-useragent-identify");
		div.useragent_add = div.getAttribute("data-useragent-add");
		div.device_clone = div.getAttribute("data-device-clone");
		div.device_list = div.getAttribute("data-device-list");


		// map div for body events
		document.body.unidentified_div = div;


		// add select all option
		div.bn_all = u.ie(div.list, "li", {"class":"all"});
		div.bn_all._text = u.ae(div.bn_all, "span", {"html":"Select all"});
		div.bn_all._checkbox = u.ie(div.bn_all, "input", {"type":"checkbox"});

		// disable regular onclick event
		div.bn_all.onclick = function(event) {u.e.kill(event);}

		div.bn_all.div = div;
		div.bn_all._checkbox.div = div;

		// handle clicking
		u.e.click(div.bn_all._checkbox);
		div.bn_all._checkbox.clicked = function(event) {
			var i, node;
			u.e.kill(event);
			// figure out wether to select or deselect (if one is selected, de-select all)
			var inputs = u.qsa("li:not(.all) input:checked", this.div.list);

//			for(i = 0; node = this.div.nodes[i]; i++) {
			for(i = 0; i < this.div.nodes.length; i++) {
				node = this.div.nodes[i];
				if(inputs.length) {
					node._checkbox.checked = false;
				}

				// don't select hidden nodes
				else if(!node._hidden) {
					node._checkbox.checked = true;

				}
			}

			// update range inputs
			this.div.bn_range._from.value = "";
			this.div.bn_range._to.value = "";

			// update options
			this.div.toggleAddToOption();
		}

		// update select all state
		div.bn_all.updateState = function() {
			u.bug("updateState");

			// figure out what the current state is and deal with it
			this.div.checked_inputs = u.qsa("li:not(.all) input:checked", this.div.list);
			this.div.visible_inputs = u.qsa("li:not(.all):not(.hidden) input", this.div.list);

//			u.bug("checked_inputs:" + checked_inputs.length + ", visible_inputs:" + visible_inputs.length)

			// all is selected
			if(this.div.checked_inputs.length == this.div.visible_inputs.length) {
				this._text.innerHTML = "Deselect all";
				u.rc(this, "deselect");
				this._checkbox.checked = true;
			}
			else if(this.div.checked_inputs.length) {
				this._text.innerHTML = "Deselect all";
				u.ac(this, "deselect");
				this._checkbox.checked = true;
			}
			else {
				this._text.innerHTML = "Select all";
				u.rc(this, "deselect");
				this._checkbox.checked = false;
			}

		}


		// add select range option
		div.bn_range = u.ae(div.bn_all, "div", {class:"range"});
		div.bn_range._text = u.ae(div.bn_range, "span", {html:"Select range:"});
		div.bn_range._from = u.ae(div.bn_range, "input", {type:"text", name:"range_from", maxlength:4});
		div.bn_range._text = u.ae(div.bn_range, "span", {html:"to"});
		div.bn_range._to = u.ae(div.bn_range, "input", {type:"text", name:"range_to", maxlength:4});


		div.bn_range.div = div;
		div.bn_range._from.bn_range = div.bn_range;
		div.bn_range._to.bn_range = div.bn_range;

		// attached to inputs
		div.bn_range._updated = function(event) {


//			console.log(event)
			var key = event.key;
			// console.log(key);
			// console.log(event.code)

//			return;
			// increment
			if(key == "ArrowUp" && event.shiftKey) {
				u.e.kill(event);

				this.value = this.value > 0 ? Number(this.value)+10 : 10;
			}
			else if(key == "ArrowUp") {
				u.e.kill(event);

				this.value = this.value > 0 ? Number(this.value)+1 : 1;
			}

			// decrement
			else if(key == "ArrowDown" && event.shiftKey) {
				u.e.kill(event);

				this.value = this.value > 10 ? Number(this.value)-10 : 1;
			}
			else if(key == "ArrowDown") {
				u.e.kill(event);

				this.value = this.value > 1 ? Number(this.value)-1 : 1;
			}

// 			// kill non-numeric keys
			else if((parseInt(key) != key) && (key != "Backspace" && key != "Delete" && key != "Tab" && key != "ArrowLeft" && key != "ArrowRight" && !event.metaKey && !event.ctrlKey)) {
				u.e.kill(event);
			}

			var value = false;
			var to, from;

			// figure out what the value will be after keyup
			if(parseInt(key) == key) {
				value = this.value.length < 4 ? this.value + key : this.value;
			}
			else if(key == "Backspace") {
				value = this.value.substring(0, this.value.length-1);
			}
			else if(key == "Delete") {
				value = this.value.substring(1);
			}
			else if(key == "ArrowUp" || key == "ArrowDown") {
				value = this.value;
			}

			if(value !== false) {

				value = Number(value);

				// add updated values and correct "sister" values
				if(this.name == "range_from") {

					if(Number(this.bn_range._to.value) < value) {
						this.bn_range._to.value = value;
					}

					from = value;
					to = Number(this.bn_range._to.value);
				}
				else if(this.name == "range_to") {

					if(!this.bn_range._from.value) {
						this.bn_range._from.value = 1;
					}
					else if(Number(this.bn_range._from.value) > value) {
						this.bn_range._from.value = value;
					}

					to = value;
					from = Number(this.bn_range._from.value);
				}

				// input indecies to select between
				to = to-1;
				from = from-1;

				if(!isNaN(from && !isNaN(to))) {
					var inputs = u.qsa("li:not(.all):not(.hidden) input", this.bn_range.div.list);
					var i, input;
					for(i = 0; i < inputs.length; i++) {
						input = inputs[i];
						if(i >= from && i <= to) {
							input.checked = true;
						}
						else {
							input.checked = false;
						}
					}

					// update options
					this.bn_range.div.toggleAddToOption();
				}

			}

		}

		u.e.addEvent(div.bn_range._from, "keypress", div.bn_range._updated);
		u.e.addEvent(div.bn_range._to, "keypress", div.bn_range._updated);


		// inject input for timeout setting
		div.div_timeout = u.ae(div.bn_all, "div", {class:"timeout"});
		div.div_timeout._text = u.ae(div.div_timeout, "span", {html:"Timeout:"});
		div.div_timeout._inut = u.ae(div.div_timeout, "input", {type:"text", name:"timeout", maxlength:5, value:u.getRequestTimeoutSetting()});

		// attached to inputs
		div.div_timeout._updated = function(event) {


//			console.log(event)
			var key = event.key;
			// console.log(key);
			// console.log(event.code)

//			return;
			// increment
			if(key == "ArrowUp") {
				u.e.kill(event);

				this.value = this.value > 0 ? Number(this.value)+100 : 3000;
			}

			// decrement
			else if(key == "ArrowDown") {
				u.e.kill(event);

				this.value = this.value > 100 ? Number(this.value)-100 : 100;
			}

// 			// kill non-numeric keys
			else if((parseInt(key) != key) && (key != "Backspace" && key != "Delete" && key != "Tab" && key != "ArrowLeft" && key != "ArrowRight" && !event.metaKey && !event.ctrlKey)) {
				u.e.kill(event);
			}

			var value = false;

			// figure out what the value will be after keyup
			if(parseInt(key) == key) {
				value = this.value.length < 5 ? this.value + key : this.value;
			}
			else if(key == "Backspace") {
				value = this.value.substring(0, this.value.length-1);
			}
			else if(key == "Delete") {
				value = this.value.substring(1);
			}
			else if(key == "ArrowUp" || key == "ArrowDown") {
				value = this.value;
			}

			if(value !== false) {

				value = Number(value);
				u.setRequestTimeoutSetting(value);

			}

		}

		u.e.addEvent(div.div_timeout._inut, "keypress", div.div_timeout._updated);


		// node is unselected - update option_node references
		div.unselectNode = function(node) {

//			console.log("REMOVE option reference for:" + u.nodeId(node) + ", has option:" + node.option_node)

			// interrupt response handling in case it has not finished
//			node.response = null;
			// delete node.response;
			//
			// // leave identification mode
			// u.rc(node, "identifying", false);
			// node._is_identifying = false;


			// remove option_node references
			if(node.option_node) {

				// remove node from option_node array
				node.option_node.ua_nodes.splice(node.option_node.ua_nodes.indexOf(node), 1);

				// remove mapping class
				u.rc(node, "mapped", false);

				// delete option_node reference
//				node.option_node = false;
				delete node.option_node;
			}

			// // update range inputs
			// this.bn_range._from.value = "";
			// this.bn_range._to.value = "";
		}



		// add checkboxes and handlers to all rows
//		for(i = 0; node = div.nodes[i]; i++) {
		for(i = 0; i < div.nodes.length; i++) {
			node = div.nodes[i];
			node.ua_id = u.cv(node, "ua_id");
			node.div = div;

			// enable selection
			node._checkbox = u.ie(node, "input", {"type":"checkbox"});
			node._checkbox.node = node;

			u.e.click(node._checkbox);
			node._checkbox.onclick = function(event) {u.e.kill(event);}

			// enable multiple selection on drag
			node._checkbox.inputStarted = function(event) {
				u.e.kill(event);

				if(this.checked) {
					this.checked = false;
					document.body._multideselection = true;

					// update node references
					this.node.div.unselectNode(this.node);
				}
				else {
					this.checked = true;
					document.body._multiselection = true;
				}

				// end multi de/selection
				document.body.onmouseup = function(event) {
//					console.log("selection end")

					this.onmouseup = null;
					this._multiselection = false;
					this._multideselection = false;

					// this will potentially start multiple
					this.unidentified_div.toggleAddToOption();
				}

//				this.node.div.toggleAddToOption();
			}

			// select/deselect if state is correct on mouseover
			node._checkbox.onmouseover = function() {
				if(document.body._multiselection) {
					this.checked = true;
				}
				else if(document.body._multideselection) {
					this.checked = false;

					// update node references
					this.node.div.unselectNode(this.node);
				}
			}

			// show id info and option_node relation on mouseover
			node.onmouseover = function() {
				if(this.option_node) {
					u.ac(this.option_node, "mappedto");
					this.id_span = u.ae(this, "span", {"class":"mappedto", "html":this._identified.method + (this._identified.guess ? ", " + this._identified.guess : "")});
				}
			}
			node.onmouseout = function() {
				if(this.option_node) {
					u.rc(this.option_node, "mappedto");
				}
				if(this.id_span) {
					this.removeChild(this.id_span);
					this.id_span = false;
				}
			}

			// enable node expansion (get useragent details)
			u.e.click(node);
			// Don't open when selecting text
			node.moved = function(event) {
				u.e.resetEvents(this);
			}
			// expand/collapse node on click
			node.clicked = function() {

				if(!this._ul) {
					this.response = function(response) {
						if(response.cms_status == "success") {

							// add delete button
							var action = u.ae(this, "ul", {"class":"actions"});
							var li = u.ae(action, "li", {"class":"delete"});
							this._delete = u.ae(li, "input", {"class":"button delete", "type":"button", "value":"Delete", title:"This will delete this useragent permanently"})

							this._delete.node = this;
							u.e.click(this._delete);
							this._delete.restore = function(event) {
								this.value = "Delete";
								u.rc(this, "confirm");
							}
		
							this._delete.clicked = function(event) {
								u.e.kill(event);

								// first click
								if(!u.hc(this, "confirm")) {
									u.ac(this, "confirm");
									this.value = "Confirm";
									this.t_confirm = u.t.setTimer(this, this.restore, 3000);
								}
								// confirm click
								else {
									u.t.resetTimer(this.t_confirm);

									this.response = function(response) {

										page.notify(response);

										if(response.cms_status == "success") {


											// update node references
											this.node.div.unselectNode(this.node);

											// Remove from list
											this.node.parentNode.removeChild(this.node);

											// Update identification list and select state
											this.node.div.bn_all.updateState();
											this.node.div.toggleAddToOption();
											
										}
									}
									u.request(this, this.node.div.useragent_delete+"/"+this.node.ua_id, {"method":"post", "data":"csrf-token="+this.node.div.csrf_token});
								}
							}



							// add useragent details
							this._ul = u.ae(this, "ul", {"class":"info"});
							u.ae(this._ul, "li", {"class":"visits", "html":response.cms_object.length})
							u.ae(this._ul, "li", {"class":"identified_as", "html":(response.cms_object[0].identified_as_device ? response.cms_object[0].identified_as_device : "unidentified")})

							var i, node;
//							for(i = 0; node = response.cms_object[i]; i++) {
							for(i = 0; i < response.cms_object.length; i++) {
								node = response.cms_object[i];
								var ul = u.ae(this, "ul", {"class":"info"});
								u.ae(ul, "li", {"class":"identified_at", "html":node.identified_at})
								u.ae(ul, "li", {"class":"comment", "html":node.comment})
							}
						}
						else {
							page.notify(response);
						}
					}
					u.request(this, this.div.useragent_details+"/"+this.ua_id, {"method":"post","data":"csrf-token=" + this.div.csrf_token});
				}
				else {
					var uls = u.qsa("ul.info,ul.actions", this);
					var i, ul;
//					for(i = 0; ul = uls[i]; i++) {
					for(i = 0; i < uls.length; i++) {
						ul = uls[i];
						this.removeChild(ul);
					}
					this._ul = false;
				}
			}

			// get useragent reference (text in H3)
			// check for text selections
			node.h3 = u.qs("h3", node);
			if(node.h3)  {

				node.h3.CheckSelection = function() {
					// console.log("node.h3.CheckSelection");

					// get selection, to use for deletion
					var selection = window.getSelection(); 
					// console.log(selection, selection.isCollapsed, u.containsOrIs(selection.anchorNode, this), u.containsOrIs(selection.focusNode, this));

					// new selection
					if(selection && !selection.isCollapsed) {
						if(
							(u.containsOrIs(this, selection.anchorNode))
							 && 
							(u.containsOrIs(this, selection.focusNode))
						) {

							// If line has active selected, remove it first
							if(this.span) {
								this.bn_delete.clicked();
							}

							this.span = document.createElement("span");
							this.span.node = this;

							var range = selection.getRangeAt(0);
							try {
								range.surroundContents(this.span);
//								selection.removeAllRanges();

							}
							catch(exception) {
								console.log("Exception:");
								console.log(exception);
							}


							// Add delete selection option
							this.bn_delete = u.ae(document.body, "span", {"class":"delete_selection", "html":"X"});
							this.bn_delete.node = this;

							u.ce(this.bn_delete);
							this.bn_delete.clicked = function(event) {
								u.e.kill(event);

								var fragment = document.createTextNode(this.node.span.innerHTML);
								this.node.replaceChild(fragment, this.node.span);

								// remove actions
								this.node.bn_delete.parentNode.removeChild(this.node.bn_delete);
								this.node.bn_search.parentNode.removeChild(this.node.bn_search);
								u.bug("delete: ", this.node.div_results);
								if(this.node.div_results) {
									this.node.div_results.parentNode.removeChild(this.node.div_results);
								}

								delete this.node.span;
								delete this.node.bn_delete;
								delete this.node.bn_search;
								delete this.node.div_results;

							}

							u.as(this.bn_delete, "top", (u.absY(this.span))+"px");
							u.as(this.bn_delete, "left", (u.absX(this.span)-35)+"px");


							// Add search selection option
							this.bn_search = u.ae(document.body, "span", {"class":"edit_selection", "html":"?"});
							this.bn_search.node = this;

							u.as(this.bn_search, "top", (u.absY(this.span))+"px");
							u.as(this.bn_search, "left", (u.absX(this.span)-17)+"px");

							u.ce(this.bn_search);
							this.bn_search.clicked = function(event) {
								u.e.kill(event);

								if(!this.node.div_results) {

									this.response = function(response) {
										// console.log(response);
										if(typeof(response) == "object") {

											this.node.div_results = u.ae(document.body, "div", {"class":"search_result"});
											this.node.div_results.node = this;
											this.has_results = false;

											u.as(this.node.div_results, "top", (u.absY(this)+20)+"px");
											u.as(this.node.div_results, "left", (u.absX(this)-17)+"px");


											if(response.items && response.items.length) {

												for(i = 0; i < response.items.length; i++) {
											
													result = response.items[i];
		//											console.log(result)
		//											console.log(result.snippet)
													if(result.snippet.match(/[0-9\.\,]+[ \-]?(\"|\″|inch|pulgad)/)) {
														var h3 = u.ae(this.node.div_results, "h3");
														u.ae(h3, "a", {html:result.link, href:result.link, target:"_blank"});
														u.ae(this.node.div_results, "p", {html:result.snippet.replace(/([0-9\.\,]+[ \-]?(\"|\″|inch|pulgad))/, "<span class=\"screen\">$1</span>")})
		//												console.log();
														this.has_results = true;
													}

												}

												if(!this.has_results) {
													u.ae(this.node.div_results, "p", {html:"No sizes in results"});
												}
											}
											else {
												u.ae(this.node.div_results, "p", {html:"Invalid search result – maybe you reached API limit"});
											}


											var h3 = u.ae(this.node.div_results, "h3");
											u.ae(h3, "a", {html:"&quot;"+this.node.span.innerHTML+"&quot; on Google", href:"https://google.com/search?q="+this.node.span.innerHTML+"+display+spec", target:"_blank"});

										}
										else {
											page.notify({"cms_message":{"error":["Invalid search result"]}});
										}
	//									console.log(response);
									}
	//								u.request(this, "/janitor/maintenance/search-for-marker");
									u.request(this, "https://www.googleapis.com/customsearch/v1?cx=006888141968518277707%3Awiqtlhmqi14&key=AIzaSyD2dkkTv2F03M2gi1TO7pAm0jz21o5GFPQ&q="+this.node.span.innerHTML+"+display+specs");
	//								console.log(this.node.span.innerHTML);
								}
							}

							// console.log(selection.toString());
							// console.log("valid selection")

						}
						else {
							page.notify({"cms_message":{"error":["Invalid selection - crossing tag boundaries"]}});
						}
						
					}

				}

				// content has been modified or selected (can happen with mouse or keys)
				u.e.addEvent(node.h3, "mouseup", node.h3.CheckSelection);
			}
			node.h4_matches = u.qs("h4.matches", node);
			node.ul_matches = u.qs("ul.matches", node);
			if(node.h4_matches && node.ul_matches) {
				node.h4_matches.node = node;

				u.ce(node.ul_matches);
				node.ul_matches.clicked = function(event) {
					u.e.kill(event);
				}

				u.ce(node.h4_matches);
				node.h4_matches.clicked = function() {
					u.toggleClass(this.node.ul_matches, "show");
				}
			}

			node.h4_mismatches = u.qs("h4.mismatches", node);
			node.ul_mismatches = u.qs("ul.mismatches", node);
			if(node.h4_mismatches && node.ul_mismatches) {
				node.h4_mismatches.node = node;

				u.ce(node.ul_mismatches);
				node.ul_mismatches.clicked = function(event) {
					u.e.kill(event);
				}
				u.ce(node.h4_mismatches);
				node.h4_mismatches.clicked = function() {
					u.toggleClass(this.node.ul_mismatches, "show");
				}
			}

		}


		// add filter to list
		if(u.hc(div, "filters")) {

			u.defaultFilters(div);

			// callback from list filter
			div.filtered = function() {
				this.bn_all.updateState();
				this.bn_range._to.value = "";
				this.bn_range._from.value = "";
			}

		}




		// Mapped to ADD SELECTED TO buttons
		div._selected_clicked = function() {

			if(this.t_execute) {

				// show that it is working
				u.ac(this.option.div._add_to, "adding");

				// recursive iteration handler (to control the number of simultaneous requests)
				this.iterateSelections = function() {

					// get next option
					var input = u.qs("li:not(.all):not(.hidden) input:checked", this.option.div.list);
					if(input) {

						// map the method/button for iteration
						input._selected = this;

						// UA was added
						input.response = function(response) {

							// update ua list
							if(this.node.option_node) {
								// remove node from option_node array
								this.node.option_node.ua_nodes.splice(this.node.option_node.ua_nodes.indexOf(this.node), 1);
							}

							// and remove node
							this.node.parentNode.removeChild(this.node);

							// adding UA to device was not 100% successful (could be timeout issue)
							if(!response.cms_status) {
								page.notify({"cms_message":{"errors":["The request failed. The UA may reappear in your list after you refresh."]}, "isJSON":true})
							}

							// maybe that was the last element?
							this.node.div.toggleAddToOption();

							// process next selected
							this._selected.iterateSelections();

						}
						// make request
						u.request(input, input.node.div.useragent_add+"/"+this.option.device_id+"/"+input.node.ua_id, {method:"post", timeout:u.getRequestTimeoutSetting(), data:"csrf-token="+input.node.div.csrf_token});
//											u.request(input, "/temp", {"method":"post", timeout:u.getRequestTimeoutSetting(), "data":"csrf-token="+input.node.div.csrf_token});

					}
					else {
						// done
						u.rc(this.option.div._add_to, "adding");
					}

				}
				this.iterateSelections();

			}
			else {
				this.t_execute = u.t.setTimer(this, this.option.div._not_confirmed, 1500);

				this._content = this.innerHTML;	
				this.innerHTML = "Sure?";
				u.ac(this, "confirm");
			}
		}

		// Mapped to ADD MATCHING TO buttons
		div._matching_clicked = function() {

			if(this.t_execute) {

				// show that it is working
				// console.log(this.option);
				u.ac(this.option.div._add_to, "adding");

				// recursive iteration handler (to control the number of simultaneous requests)
				this.iterateSelections = function() {

					var node = this.option.ua_nodes.shift();
					if(node) {
						var input = node._checkbox;
						// console.log(this.option.div._add_to);

						// if input was found, make request
						if(input && input.node._identified.id == this.option.device_id) {
							// console.log("load node:" + input.node.ua_id);
							input._matching = this;

							// UA was added
							input.response = function(response) {

								// and remove node
								this.node.parentNode.removeChild(this.node);

								// adding UA to device was not 100% successful (could be timeout issue)
								if(!response.cms_status) {
									page.notify({"cms_message":{"errors":["The request failed. The UA may reappear in your list after you refresh."]}, "isJSON":true})
								}

								this.node.div.toggleAddToOption();

								// process next selected
								this._matching.iterateSelections();
							}
							// make request
							u.request(input, input.node.div.useragent_add+"/"+this.option.device_id+"/"+input.node.ua_id, {method:"post", timeout:u.getRequestTimeoutSetting(), data:"csrf-token="+input.node.div.csrf_token});
//							u.request(input, "/temp", {"method":"post", timeout:u.getRequestTimeoutSetting(), "data":"csrf-token="+input.node.div.csrf_token});
						}

					}
					else {
						// done
						// console.log(this);
						// console.log(this.option.div._add_to);
						if(this.option.div && this.option.div._add_to) {
							u.rc(this.option.div._add_to, "adding");
						}
					}

				}
				this.iterateSelections();

			}
			else {
				this.t_execute = u.t.setTimer(this, this.option.div._not_confirmed, 1500);

				this._content = this.innerHTML;	
				this.innerHTML = "Sure?";
				u.ac(this, "confirm");
			}
		}

		// Mapped to ADD SELECTED TO CLONE buttons
		div._addtoclone_clicked = function() {

			// confirm mechanism in action
			if(this.t_execute) {

				// show that it is working
				u.ac(this.option.div._add_to, "adding");


				// clone device response
				this.response = function(response) {

					if(response.cms_status == "success" && response.cms_object.id) {

						this.cloned_device_id = response.cms_object.id;

						this.iterateSelections = function() {

							var input = u.qs("li:not(.all):not(.hidden) input:checked", this.option.div.list);

							// if input was found, make request
							if(input) {

								input._clone = this;

								// add response
								input.response = function(response) {
									// remove node from list

									this.node.parentNode.removeChild(this.node);

									// adding UA to device was not 100% successful
									// (could be timeout issue)
									if(!response.cms_status) {
										page.notify({"cms_message":{"errors":["The request failed. The UA may reappear in your list after you refresh."]}, "isJSON":true})
									}


									this.node.div.toggleAddToOption();
								
									// process next selected
									this._clone.iterateSelections();
								
								}
								// add useragent to device
								u.request(input, input.node.div.useragent_add+"/"+input._clone.cloned_device_id+"/"+input.node.ua_id, {method:"post", timeout: u.getRequestTimeoutSetting(), data:"csrf-token="+input.node.div.csrf_token});

							}
							else {
								// done
								u.rc(this.option.div._add_to, "adding");
							}

						}
						this.iterateSelections();

					}
					else {
						page.notify(response);
					}
				}

				// clone device
				u.request(this, this.option.div.device_clone+"/"+this.option.device_id, {method:"post", data:"csrf-token="+this.option.div.csrf_token});

			}
			// activate confirm mechanism
			else {
				this.t_execute = u.t.setTimer(this, this.option.div._not_confirmed, 1500);

				this._content = this.innerHTML;	
				this.innerHTML = "Sure?";
				u.ac(this, "confirm");
			}
		}


		// Mapped to ADD SELECTED TO CLONE buttons
		div._retryIdentification_clicked = function() {

			// confirm mechanism in action
			if(this.t_execute) {

				if(this.option && this.option.ua_nodes) {
					var i, node;
					for(i = 0; i < this.option.ua_nodes.length; i++) {
						node = this.option.ua_nodes[i];
						delete node._identified;
						
					}
				}

				// reset option node references
				this.option.ua_nodes = [];

				// Start over
				this.option.div.toggleAddToOption();

			}
			// activate confirm mechanism
			else {
				this.t_execute = u.t.setTimer(this, this.option.div._not_confirmed, 1500);

				this._content = this.innerHTML;	
				this.innerHTML = "Sure?";
				u.ac(this, "confirm");
			}
		}


		// confirm timeout handler for add buttons 
		div._not_confirmed = function() {
			u.rc(this, "confirm");
			this.innerHTML = this._content;
			this.t_execute = false;
		}


		// add option to options list
		div.addOption = function(option, ua_node) {
			// console.log("## addOption:" + u.nodeId(ua_node) + ", " + option)
			// console.log(option)

			// check options index for current option
			// add if it does not already exist
			if(this._add_to.identified_options.indexOf(option.id) == -1) {

//				console.log("new option:" + option.id)

				// create new option
				var option_node = u.ae(this._add_to._list, "li", {"html":option.name + " (<span>1</span>)", "class":"device_id:"+option.id});
				option_node.span = u.qs("span", option_node);

				option_node.details = option;
				option_node.div = this;
				option_node.device_id = option.id;
				option_node.ua_nodes = [];



				// store matching ua_nodes on option
				option_node.ua_nodes.push(ua_node);

				// store UA id for easy lookup
				this._add_to.identified_options.push(option.id);

				// add option li to options index
				this._add_to.identified_options_lis.push(option_node);



				u.e.click(option_node);
				option_node.closeOption = function() {
					// u.bug("option_node.closeOption");
					// u.bug(option_node);

					if(this._info) {
						this._info.parentNode.removeChild(this._info);
//						this._info = false;
						delete this._info;
					}
					if(this._show_selected_only) {
						this._show_selected_only.parentNode.removeChild(this._show_selected_only);
//						this._show_selected_only = false;
						delete this._show_selected_only;
					}
					if(this._selected) {
						this._selected.parentNode.removeChild(this._selected);
//						this._selected = false;
						delete this._selected;
					}
					if(this._matching) {
						this._matching.parentNode.removeChild(this._matching);
//						this._matching = false;
						delete this._matching;
					}
					if(this._addtoclone) {
						this._addtoclone.parentNode.removeChild(this._addtoclone);
//						this._addtoclone = false;
						delete this._addtoclone;
					}

					// remove highlighting of matching nodes
					var i, node;
//					for(i = 0; node = this.ua_nodes[i]; i++) {
//					console.log(this.ua_nodes)
					for(i = 0; i < this.ua_nodes.length; i++) {
						node = this.ua_nodes[i];
						u.rc(node, "mapped", false);
//						node.option_node = false;
					}

				}

				//
				option_node.clicked = function(event) {
					// console.log("option_node.clicked");
					// console.log(event);
					// show advanced options menu if not already present 
					// (else close it)

					// ONLY SHOW MATCHING
					// add all MATCHING
					// add all SELECTED
					// add SELECTED to CLONE
					if(!this._info) {

						// console.log("build info pane");
						var i, node, li;

						// close other options 
						// (only one can be open at the time, because MATCHING options will be highlighted)
//						for(i = 0; li = this.div._add_to.identified_options_lis[i]; i++) {
						for(i = 0; i < this.div._add_to.identified_options_lis.length; i++) {
							li = this.div._add_to.identified_options_lis[i];
							// close other options
							if(li != this) {
								li.closeOption();
							}
						}

						// highlight all matching ua_nodes
//						for(i = 0; node = this.ua_nodes[i]; i++) {
						for(i = 0; i < this.ua_nodes.length; i++) {
							node = this.ua_nodes[i];
							u.ac(node, "mapped");
						}


						// option matches device
						if(this.device_id && this.device_id != "unknown") {

							// collect info about device
							var info_array = [];

							if(this.details["description"]) {
								info_array.push(this.details["description"]);
							}

							if(this.details["tags"]) {
								for(i in this.details["tags"]) {
									info_array.push(this.details["tags"][i]["value"]);
								}
							}

							// add info to option
							this._info = u.ae(this, "div", {"class":"info", "html":info_array.join(", ")});

							// add indexing options
							this._selected = u.ae(this, "div", {class:"selected", html:"Add all SELECTED", title:"Not including hidden UAs"});
							this._selected.option = this;
							this._matching = u.ae(this, "div", {class:"matching", html:"Add all MATCHING", title:"Not including hidden UAs"});
							this._matching.option = this;
							this._addtoclone = u.ae(this, "div", {class:"addtoclone", html:"Add SELECTED to CLONE", title:"Not including hidden UAs"});
							this._addtoclone.option = this;


							// ADD SELECTED handler
							u.e.click(this._selected);
							this._selected.clicked = this.div._selected_clicked;

							// ADD MATCHING handler
							u.e.click(this._matching);
							this._matching.clicked = this.div._matching_clicked;

							// ADD SELECTED TO CLONE handler
							u.e.click(this._addtoclone);
							this._addtoclone.clicked = this.div._addtoclone_clicked;

						}

						// option does not match device
						else if(this.details.guess == "Request timeout") {
							this._info = u.ae(this, "div", {class:"info", html:this.details.guess ? this.details.guess : "Unknown error"});


							this._retryIdentification = u.ae(this, "div", {class:"retry", html:"Retry", title:"Retry identification"});
							this._retryIdentification.option = this;

							// ADD SELECTED TO CLONE handler
							u.e.click(this._retryIdentification);
							this._retryIdentification.clicked = this.div._retryIdentification_clicked;

						}
						// option does not match device
						else {
							this._info = u.ae(this, "div", {class:"info", html:this.details.guess ? this.details.guess : "Unknown error"});

						}

					}

					// remove advanced menu
					else {
						// console.log("info pane is already open, close info pane")
						this.closeOption();

					}
				}
			}

			// device option already exists
			else {

//				console.log("option exists")

				// get references to option_li
				var option_index = this._add_to.identified_options.indexOf(option.id);
				var option_node = this._add_to.identified_options_lis[option_index];

//				u.bug("ua_node matches:" + u.nodeId(option_node))
//				u.xInObject(option_node.ua_nodes)
				// ua_node is newly selected

//				console.log(option_node.ua_nodes.indexOf(ua_node));

				if(option_node.ua_nodes.indexOf(ua_node) == -1) {

					// store matching ua_nodes on option
					option_node.ua_nodes.push(ua_node);

				}

			}


			// ua_node remembers option_node
			ua_node.option_node = option_node;

			// update option count
			this.updateOptions();
		}


		// update options state
		// - updates count
		// - removes options with no matches
		div.updateOptions = function() {
//			u.bug("updateOptions");

			var i, option_node, checkbox;

			// is add_to still open
			if(this._add_to) {
//				for(i = 0; option_node = this._add_to.identified_options_lis[i]; i++) {
				for(i = 0; i < this._add_to.identified_options_lis.length; i++) {
					option_node = this._add_to.identified_options_lis[i];
					// update option count
//					console.log("option_node.ua_nodes.length:" + option_node.ua_nodes.length)
					if(option_node.ua_nodes.length) {
						option_node.span.innerHTML = option_node.ua_nodes.length;
					}
					else {
//						u.bug("close option")
						// close option (to reset option mappings)
						option_node.closeOption();

						// remove option
						option_node.parentNode.removeChild(option_node);
						this._add_to.identified_options_lis.splice(i, 1);
						this._add_to.identified_options.splice(i, 1);
					}
				}
			}

			// options has been removed - reset all nodes
			else {
//				for(i = 0; checkbox = this.visible_inputs[i]; i++) {
				for(i = 0; i < this.visible_inputs.length; i++) {
					checkbox = this.visible_inputs[i];
					u.rc(checkbox.node, "mapped", false);
					checkbox.node.option_node = false;
				}

			}
		}


		// show or hide "Add To" options, depending on whether useragents are selected or not
		div.toggleAddToOption = function() {
//			console.log("----- toggle add to options")


			// update select all label
			this.bn_all.updateState();


			// updateState will also query checked_inputs and make them available to avoid double query
			// selected items in list?
			if(this.checked_inputs.length) {

				// if "add to" panel doesn't exist, create it
				if(!this._add_to) {

					// Append add to layer to body
					this._add_to = u.ae(document.body, "div", {"class":"addToDevice"});

					// add basic elements
					u.ae(this._add_to, "h2", {"html":"Add to device"});
					var count_div = u.ae(this._add_to, "div", {"html":"Selected useragents:", "class":"counter"});
					this._add_to._count = u.ae(count_div, "span", {"class":"count"});

					// adjust page width
					u.as(page, "width", parseInt(u.gcs(page, "width")) - this._add_to.offsetWidth + "px");


					// add search option
					var search_option = u.ae(this._add_to, "div", {"class":"field search"});
					search_option.div = this;

					var search_input = u.ae(search_option, "input", {"class":"search default ignoreinput"});
					search_input.div = this;

					// add list for search results
					search_input.search_result = u.ae(search_option, "ul", {"class":"results"});

					// initialize search field
					search_input._default_value = "Search for device";
					search_input.value = search_input._default_value;

					search_input.onfocus = function() {
						u.rc(this, "default");
						if(this.value == this._default_value) {
							this.value = "";
						}
					}
					search_input.onblur = function() {
						if(this.value == "") {
							u.ac(this, "default");
							this.value = this._default_value;
						}
					}

					search_input.onkeyup = function() {
						u.t.resetTimer(this.t_search);
						this.t_search = u.t.setTimer(this, this.search, 1000);
					}
					search_input.onkeydown = function() {
						u.t.resetTimer(this.t_search);
					}

					// perform search for other device than the matched one
					search_input.search = function() {

						// empty result list
						search_input.search_result.innerHTML = "";

						// only do search with valid search string
						if(this.value && this.value != this._default_value) {

							// get search response
							search_input.response = function(response) {

								this.options = [];
								// get items from result
								var items = u.qsa(".all_items li.item", response);
								if(items.length) {

									var i, node;
//									for(i = 0; node = items[i]; i++) {
//									console.log(items.length);

									for(i = 0; i < items.length; i++) {
										
//										console.log(i + ", " + items.length);

//										node = ;
										node = this.search_result.appendChild(items[i]);
										node.div = this.div;
										node.search_input = this;
										node.device_id = u.cv(node, "item_id");
										this.options.push(node);

										u.e.click(node);
										node.clicked = function() {

											// add advanced options menu
											// add all SELECTED
											// add SELECTED to CLONE
											if(!this._info) {

												var i, node, li;

												for(i = 0; i < this.search_input.options.length; i++) {
													li = this.search_input.options[i];
													// close other options
													if(li != this) {
														li.closeOption();
													}
												}

												// collect info about device
												var info_array = [];
												
												var tags = u.qsa("ul.tags li .value", this);
												for(i  = 0; i < tags.length; i++) {
													info_array.push(tags[i].innerHTML);
												}

												// add info to option
												this._info = u.ae(this, "div", {"class":"info", "html":info_array.join(", ")});


												this._selected = u.ae(this, "div", {class:"selected", html:"Add all SELECTED", title:"Not including hidden UAs"});
												this._selected.option = this;
												this._addtoclone = u.ae(this, "div", {class:"addtoclone", html:"Add SELECTED to CLONE", title:"Not including hidden UAs"});
												this._addtoclone.option = this;


												// ADD SELECTED handler
												u.e.click(this._selected);
												this._selected.clicked = this.div._selected_clicked;

												// ADD SELECTED TO CLONE handler
												u.e.click(this._addtoclone);
												this._addtoclone.clicked = this.div._addtoclone_clicked;

											}
											// remove advanced menu
											else {
												this.closeOption();
											}

										}
										// close option
										node.closeOption = function() {

											if(this._info) {
												this._info.parentNode.removeChild(this._info);
//												this._info = false;
												delete this._info;
											}
											if(this._selected) {
												this._selected.parentNode.removeChild(this._selected);
//												this._selected = false;
												delete this._selected;
											}
											if(this._addtoclone) {
												this._addtoclone.parentNode.removeChild(this._addtoclone);
//												this._addtoclone = false;
												delete this._addtoclone;
											}
											
										}
		 							}
								}
								// no results
								else {
									u.ae(this.search_result, "li", {html:"No results"});
								}

								u.rc(this, "loading");
							}

							u.ac(search_input, "loading");
							// perform search
							u.request(search_input, this.div.device_list, {"data":"search=1&search_string="+this.value, "method":"post"})
						}
					}

					// add list for identified options
					this._add_to._list = u.ae(this._add_to, "ul", {"class":"options"});

					// options based on selected useragents
					this._add_to.identified_options = [];
					this._add_to.identified_options_lis = [];
				}

				// set global counter
				this._add_to._count.innerHTML = this.checked_inputs.length;


				var i, ua, ua_id
				this.wait_for_uas = this.checked_inputs.length;
				// u.bug("this.wait_for_uas::" + this.wait_for_uas);

				u.ac(this._add_to, "loading");

				this.checked_inputs_i = 0;

				// start identification process
				this.iterateSelections = function() {
					//console.log("this.iterateSelections for id");
					// console.log("this.checked_inputs.length:" + this.checked_inputs.length);
					// console.log("this.checked_inputs_i:" + this.checked_inputs_i);

					this.is_identifying = true;

					// more selected uas?
					if(this.checked_inputs_i < this.checked_inputs.length) {

						var input = this.checked_inputs[this.checked_inputs_i++];

						// node is in identification mode
						u.ac(input.node, "identifying");
						input.node._is_identifying = true;


						// useragent has not been identified yet
						if(!input.node._identified) {
//							console.log("not id'ed yet:" + u.nodeId(input.node))

							// device identification response
							input.response = function(response, request_id) {
//								console.log("node identified:" + u.nodeId(this.node))

								// get rid of response handler
								delete this.response;

								// leave identification mode
								u.rc(this.node, "identifying");
								this.node._is_identifying = false;

								// id details object
								this.node._identified = {};

								// identification was successful
								if(response.cms_status == "success" && response.cms_object.id) {

									this.node._identified.id = response.cms_object.id;
									this.node._identified.name = response.cms_object.name;
									this.node._identified.tags = response.cms_object.tags;
									this.node._identified.method = response.cms_object.method;
									this.node._identified.guess = response.cms_object.guess;

								}

								// bad result - device not identified
								else if(!response.error) {

									this.node._identified.id = "unknown";
									this.node._identified.name = "Unknown device";
									this.node._identified.tags = [];
									this.node._identified.method = "N/A";
									this.node._identified.guess = "Device could not be identified";

								}

								// request timeout
								else if(this[request_id].status == 0) {

									this.node._identified.id = "unknown";
									this.node._identified.name = "Request timeout";
									this.node._identified.tags = [];
									this.node._identified.method = "N/A";
									this.node._identified.guess = "Request timeout";

								}

								// check load status
								this.node.div.wait_for_uas--;
								// u.bug("this.node.div.wait_for_uas countdown:" + this.node.div.wait_for_uas)
								// if(!this.node.div.wait_for_uas && this.node.div._add_to) {
								// 	u.rc(this.node.div._add_to, "loading");
								// }

								// is add_to still open (and us still clicked)
								if(this.node.div._add_to && this.checked) {
									// add option to the options list (will audo detect existance)
									this.node.div.addOption(this.node._identified, this.node);
								}

								// continue
								this.node.div.iterateSelections();

							}
							// request identification
							u.request(input, input.node.div.useragent_identify+"/"+input.node.ua_id, {method:"post", timeout:u.getRequestTimeoutSetting(), data:"csrf-token="+input.node.div.csrf_token});
//							u.request(input, "/temp", {method:"post", timeout:8000, data:"csrf-token="+input.node.div.csrf_token});

						}

						// already identified
						else {
							// u.bug("id'ed already:", input.node);

							// leave identification mode
							u.rc(input.node, "identifying");
							input.node._is_identifying = false;

							// check load status
							this.wait_for_uas--;
							// u.bug("this.wait_for_uas countdown:" + this.wait_for_uas)
							// if(!this.wait_for_uas) {
							// 	u.rc(this._add_to, "loading");
							// }


							// is add_to still open
							if(this._add_to) {

								// add option to the options list (will audo detect existence)
								this.addOption(input.node._identified, input.node);
								/// continue
								this.iterateSelections();

							}
						}

					}
					else {
						// u.bug("this.wait_for_uas:" + this.wait_for_uas);
						// can become -1 if additional selection is made while indexing
						if(this.wait_for_uas <= 0 && this._add_to) {
							u.rc(this._add_to, "loading");
						}

						this.is_identifying = false;

//						console.log("done");
					}

				}

				// start identification of selected uas (if not already running)
				if(!this.is_identifying) {
					this.iterateSelections();
				}

			}

			// no selected useragents
			else {

				// remove "Add to" if it exists
				if(this._add_to) {
					this._add_to.parentNode.removeChild(this._add_to);
					// this._add_to = false;
					delete this._add_to;

					// adjust page width
					u.as(page, "width", "auto");
				}
			}

			this.updateOptions();

		}
		

		if(div.i_keepalive) {
			u.t.resetInterval(div.i_keepalive);
		}

		this.keepAlive = function() {
			u.request(this, "/janitor/device/keepAlive");
		}

		div.i_keepalive = u.t.setInterval(this, "keepAlive", 300000);



	}
}


Util.Objects["testMarkersOnUnidentified"] = new function() {
	this.init = function(div) {
		u.bug("init testMarkersOnUnidentified")

		div._header = u.ae(div, "h2", {"html":"Test device markers"});
		div._header.div = div;

		div.csrf_token = div.getAttribute("data-csrf-token");

		div.url_device_test = div.getAttribute("data-device-test");
		div.url_device_get = div.getAttribute("data-device-get");


		div.auto_order = [
			"bot-crawler-spider-fetcher-indexer",
			"internet-explorer-9",
			"internet-explorer-10",
			"internet-explorer-11",
			"microsoft-edge-desktop",
			"firefox-desktop",
			"chrome-desktop",
			"safari-desktop",

			"internet-explorer-desktop-light",
			"microsoft-edge-desktop-light",
			"firefox-desktop-light",
			"chrome-desktop-light",
			"safari-desktop-light",
			"opera-desktop-light",
			"generic-desktop-light",

			"generic-tv",

			"safari-tablet",
			"firefox-tablet",
			"android-tablet",

			"safari-tablet-light",
			"firefox-tablet-light",
			"android-tablet-light",
			"opera-tablet-light",
			"generic-tablet-light",

			"firefox-smartphone",
			"microsoft-edge-smartphone",
			"safari-smartphone",

			"safari-mobile",
			"internet-explorer-mobile",
			"firefox-mobile",
			"opera-mobile",
			"generic-mobile",

			"lynx",
			"validator",

			"android-smartphone",
			"android-mobile",

			"internet-explorer-mobile-light",
			"opera-mobile-light",
			"generic-mobile-light",

			"fallback-desktop",
			"fallback-desktop-ie10",
			"fallback-desktop-ie9",
			"fallback-desktop-light",
			"fallback-tablet-light",
			"fallback-mobile",
			"fallback-mobile-light",
			"fallback-seo",
		]

		div.response = function(response) {

			page.notify(response);

			if(response.cms_status == "success") {

				this._open = true;

				this.div_results = u.qs(".all_items");
				this.div_stats = u.qs(".stats");


				// add marker filter
				this._filter = u.ae(this, "div", {"class":"filter"});


				this.markers_ul = u.ae(this, "ul", {"class":"markers"});
				// var row_marker = response.cms_object[0].name.substring(0,3);
				var i, node, li;
//				for(i = 0; node = response.cms_object[i]; i++) {
				for(i = 0; i < response.cms_object.length; i++) {
					node = response.cms_object[i];

					// if(row_marker != node.name.substring(0,3)) {
					// 	row_marker = node.name.substring(0,3);
					// 	this.markers_ul = u.ae(this, "ul", {"class":"markers"});
					// }

					li = u.ae(this.markers_ul, "li", {"class":node.sindex, "html":node.name});
					li.div = this;
					li.item_id = node.item_id;
					li.marker_name = node.name;

					u.e.click(li);
					li.clicked = function() {

						// set marker as page title
						u.qs("title", document.head).innerHTML = this.marker_name;

						var i, node;
						for(i = 0; node = this.div._markers[i]; i++) {
							u.rc(node, "selected");
						}
						u.ac(this, "selected");


						// clean up existing values
						// remove filter
						var existing_filter = u.qs("div.filter", this.div.div_results);
						if(existing_filter) {
							existing_filter.parentNode.removeChild(existing_filter);
						}

						// empty exiting results
						this.existing_results = u.qs("ul.items", this.div.div_results);
						if(this.existing_results) {
							
							this.existing_results.innerHTML = "";
						}
						else {
							this.existing_results = u.ae(this.div.div_results, "ul", {"class":"items"});
						}

						// remove "no results" if it exist
						var existing_no_results = u.qs("p", this.div.div_results);
						if(existing_no_results) {
							existing_no_results.parentNode.removeChild(existing_no_results);
						}

						this.div.div_stats.innerHTML = "Loading ...";

						// hide addtoOptions
						this.div.div_results.toggleAddToOption();

//						u.bug("test device markers")
						this.response = function(response) {
//							u.bug("RESPONSE:", response);

							page.notify(response);

							if(response.isHTML) {
								// result set div

								// var div_results = u.qs(".all_items");

								// update stats
								this.div.div_stats.innerHTML = u.qs(".stats", response).innerHTML;


								// inject new values
								var new_items = u.qsa(".all_items ul.items li.item", response);
								if(new_items.length) {

									var i, node;
									for(i = 0; node = new_items[i]; i++) {
										u.ae(this.existing_results, node);
									}
									u.m.unidentifiedList.init(this.div.div_results);

									// Stop autorun
									if(this.div.li_auto) {
										this.div.li_auto.is_auto_running = false;
									}

								}
								else {

									// no results
//									u.ae(this.existing_results, u.qs(".all_items p", response));

									// Continue autoRun
									if(this.div.li_auto && this.div.li_auto.is_auto_running && fun(this.div.li_auto.autoRun)) {
										this.div.li_auto.autoRun();
									}

								}

								u.rc(this, "loading");

							}
						}
						u.ac(this, "loading");
						u.request(this, this.div.url_device_test, {"data":"csrf-token="+this.div.csrf_token+"&test_marker=true&device_id="+this.item_id, "method":"post"});
					}
				}


				this._markers = u.qsa("li", this.markers_ul);


				this.li_auto = u.ae(this.markers_ul, "li", {"class":"auto", "html":"Auto run"});
				this.li_auto.div = this;
				u.e.click(this.li_auto);
				this.li_auto.clicked = function() {
					if(!this.is_auto_running) {
						this.is_auto_running = true;

						this.auto_run_i = 0;

						this.autoRun = function() {
							if(this.auto_run_i < this.div.auto_order.length) {
								var button = u.qs("."+this.div.auto_order[this.auto_run_i], this.div);
								this.auto_run_i++;

								button.clicked();
							}
							else {
								this.is_auto_running = false;
								delete this.autoRun;
							}
						}
						this.autoRun();
					}
				}


				// index list, to speed up filtering process
				var i, node;
//				for(i = 0; node = this._markers[i]; i++) {
				for(i = 0; i < this._markers.length; i++) {
					node = this._markers[i];
					node._c = node.textContent.toLowerCase();
				}

				// insert tags filter
				this._filter.field = u.ae(this._filter, "div", {"class":"field"});
				u.ae(this._filter.field, "label", {"html":"Filter"});

				this._filter.input = u.ae(this._filter.field, "input", {"class":"filter ignoreinput"});
				this._filter.input._div = this;

				this._filter.input.onkeydown = function() {
	//				u.bug("reset timer")
					u.t.resetTimer(this._div.t_filter);
				}
				this._filter.input.onkeyup = function() {
	//				u.bug("set timer")
					this._div.t_filter = u.t.setTimer(this._div, this._div.filter, 1500);
					u.ac(this._div._filter, "filtering");
				}
				this.filter = function() {

					var i, node;
					if(this._current_filter != this._filter.input.value.toLowerCase()) {
	//					u.bug("filter by:" + this._filter.input.value)

						this._current_filter = this._filter.input.value.toLowerCase();
//						for(i = 0; node = this._markers[i]; i++) {
						for(i = 0; i < this._markers.length; i++) {
							node = this._markers[i];

							if(node._c.match(this._current_filter)) {
								u.as(node, "display", "inline-block", false);
							}
							else {
								u.as(node, "display", "none", false);
							}
						}
					}

					// leave filtering mode
					u.rc(this._filter, "filtering");
				}
			}

		}

		div.headerExpanded = function() {
//			u.bug("expanded")

			// load markers
			u.request(this, this.url_device_get, {"data":"csrf-token="+this.csrf_token, "method":"post"})
		}

		div.headerCollapsed = function() {
//			u.bug("collapsed")

			if(this.markers_ul) {
				this.removeChild(this.markers_ul);
			}
			if(this._filter) {
				this.removeChild(this._filter);
			}

		}

	}
}


Util.Objects["crossreferenceUnidentified"] = new function() {
	this.init = function(div) {
		u.bug("init crossreferenceUnidentified")

		div._header = u.ae(div, "h2", {"html":"Crossreference device markers"});
		u.ae(div, "p", {"html":"Crossreferencing takes time and server resources - use with caution."});
		div._header.div = div;

		div.csrf_token = div.getAttribute("data-csrf-token");

		div.url_device_test = div.getAttribute("data-device-test");
		div.url_device_get = div.getAttribute("data-device-get");


		div.response = function(response) {

			page.notify(response);

			if(response.cms_status == "success") {

				this._open = true;

				this.div_results = u.qs(".all_items");
				this.div_stats = u.qs(".stats");



				this.markers_ul = u.ae(this, "ul", {"class":"markers"});
				var i, node, li;
//				for(i = 0; node = response.cms_object[i]; i++) {
				for(i = 0; i < response.cms_object.length; i++) {
					node = response.cms_object[i];

					// Only show Android, Smartphone and Android, Mobile
					if(node.name.match(/Android, [SM]/)) {

						li = u.ae(this.markers_ul, "li", {"html":node.name});
						li.div = this;
						li.item_id = node.item_id;

						u.e.click(li);
						li.clicked = function() {

							var i, node;
	//						for(i = 0; node = this.div._markers[i]; i++) {
							for(i = 0; i < this.div._markers.length; i++) {
								node = this.div._markers[i];
								u.rc(node, "selected");
							}
							u.ac(this, "selected");


							// clean up existing values
							// remove filter
							var existing_filter = u.qs("div.filter", this.div.div_results);
							if(existing_filter) {
								existing_filter.parentNode.removeChild(existing_filter);
							}

							// empty exiting results
							this.existing_results = u.qs("ul.items", this.div.div_results);
							if(this.existing_results) {
							
								this.existing_results.innerHTML = "";
							}
							else {
								this.existing_results = u.ae(this.div.div_results, "ul", {"class":"items"});
							}

							// remove "no results" if it exist
							var existing_no_results = u.qs("p", this.div.div_results);
							if(existing_no_results) {
								existing_no_results.parentNode.removeChild(existing_no_results);
							}

							this.div.div_stats.innerHTML = "Loading ...";

							// hide addtoOptions
							this.div.div_results.toggleAddToOption();

							u.bug("crossreference device markers")
							this.response = function(response) {
								page.notify(response);

								if(response.isHTML) {
									// result set div

									// var div_results = u.qs(".all_items");

									var markers = [];
									// update stats
									this.div.div_stats.innerHTML = u.qs(".stats", response).innerHTML;


									// inject new values
									var new_items = u.qsa(".all_items ul.items li.item", response);
									if(new_items) {
									
										var i, node, marker;
	//									for(i = 0; node = new_items[i]; i++) {
										for(i = 0; i < new_items.length; i++) {
											node = new_items[i];
											u.ae(this.existing_results, node);

											marker = u.qs("h4 em", node);
											if(marker) {
//												console.log(node, u.qs("h4 em", node));
												markers.push(marker.innerHTML);
											}
										}
										this.div.div_stats.innerHTML += "<br />Markers: " + markers.join(", ");

										u.m.unidentifiedList.init(this.div.div_results);
									}
									else {
										u.ae(this.existing_results, u.qs(".all_items p", response));
										// no results
									}

									u.rc(this, "loading");

								}
							}
							u.ac(this, "loading");
							u.request(this, this.div.url_device_test, {"data":"csrf-token="+this.div.csrf_token+"&crossreference_marker=true&device_id="+this.item_id, "method":"post"});
						}
					}

				}


				this._markers = u.qsa("li", this.markers_ul);

			}

		}

		div.headerExpanded = function() {
//			u.bug("expanded")

			u.request(this, this.url_device_get, {"data":"csrf-token="+this.csrf_token, "method":"post"})
		}

		div.headerCollapsed = function() {
//			u.bug("collapsed")

			if(this.markers_ul) {
				this.removeChild(this.markers_ul);
				delete this.markers_ul;
			}
			if(this._filter) {
				this.removeChild(this._filter);
				delete this._filter;
			}

		}

	}
}
