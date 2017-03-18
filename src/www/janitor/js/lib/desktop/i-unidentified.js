Util.Objects["searchUnidentified"] = new function() {
	this.init = function(form) {

		u.f.init(form);
		
		form.submitted = function() {
			
			if(!form.fields["search_string"].val()) {
				form.fields["search_string"].val("");
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
		div.bn_all._text = u.ae(div.bn_all, "span", {"html":"Select all"})
		div.bn_all._checkbox = u.ie(div.bn_all, "input", {"type":"checkbox"});

		// disable regular onclick event
		div.bn_all.onclick = function(event) {u.e.kill(event);}

		div.bn_all.div = div;
		div.bn_all._checkbox.div = div;

		// handle clicking
		u.e.click(div.bn_all);
		div.bn_all.clicked = function(event) {
			var i, node;
			u.e.kill(event);
			// figure out wether to select or deselect (if one is selected, de-select all)
			var inputs = u.qsa("li:not(.all) input:checked", this.div.list);

			for(i = 0; node = this.div.nodes[i]; i++) {
				if(inputs.length) {
					node._checkbox.checked = false;
				}

				// don't select hidden nodes
				else if(!node._hidden) {
					node._checkbox.checked = true;

				}
			}

			// update options
			this.div.toggleAddToOption();
		}


		// update select all state
		div.bn_all.updateState = function() {

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



		// node is unselected - update option_node references
		div.unselectNode = function(node) {

//			console.log("REMOVE option reference for:" + u.nodeId(node) + ", has option:" + node.option_node)

			// interrupt response handling in case it has not finished
			node.response = null;

			// leave identification mode
			u.rc(node, "identifying");
			node._is_identifying = false;


			// remove option_node references
			if(node.option_node) {

				// remove node from option_node array
				node.option_node.ua_nodes.splice(node.option_node.ua_nodes.indexOf(node), 1);

				// remove mapping class
				u.rc(node, "mapped");
				
				// delete option_node reference
				node.option_node = false;
			}
		}



		// add checkboxes and handlers to all rows
		for(i = 0; node = div.nodes[i]; i++) {
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

					// show or no-show add option
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
			node.clicked = function() {

				if(!this._ul) {
					this.response = function(response) {
						if(response.cms_status == "success") {

							// add delete button
							var action = u.ae(this, "ul", {"class":"actions"});
							var li = u.ae(action, "li", {"class":"delete"});
							this._delete = u.ae(li, "input", {"class":"button delete", "type":"button", "value":"delete"})

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

											this.node.parentNode.removeChild(this.node);
										}
									}
									u.request(this, this.node.div.useragent_delete+"/"+this.node.ua_id, {"method":"post", "params":"csrf-token="+this.node.div.csrf_token});
								}
							}



							// add useragent details
							this._ul = u.ae(this, "ul", {"class":"info"});
							u.ae(this._ul, "li", {"class":"visits", "html":response.cms_object.length})
							u.ae(this._ul, "li", {"class":"identified_as", "html":(response.cms_object[0].identified_as_device ? response.cms_object[0].identified_as_device : "unidentified")})

							var i, node;
							for(i = 0; node = response.cms_object[i]; i++) {
								var ul = u.ae(this, "ul", {"class":"info"});
								u.ae(ul, "li", {"class":"identified_at", "html":node.identified_at})
								u.ae(ul, "li", {"class":"comment", "html":node.comment})
							}
						}
						else {
							page.notify(response);
						}
					}
					u.request(this, this.div.useragent_details+"/"+this.ua_id, {"method":"post","params":"csrf-token=" + this.div.csrf_token});
				}
				else {
					var uls = u.qsa("ul.info", this);
					var i, ul;
					for(i = 0; ul = uls[i]; i++) {
						this.removeChild(ul);
					}
					this._ul = false;
				}
			}

			node.h4_matches = u.qs("h4.matches", node);
			node.ul_matches = u.qs("ul.matches", node);
			if(node.h4_matches && node.ul_matches) {
				node.h4_matches.node = node;
				u.ce(node.h4_matches);
				node.h4_matches.clicked = function() {
					u.toggleClass(this.node.ul_matches, "show");
				}
			}

			node.h4_mismatches = u.qs("h4.mismatches", node);
			node.ul_mismatches = u.qs("ul.mismatches", node);
			if(node.h4_mismatches && node.ul_mismatches) {
				node.h4_mismatches.node = node;
				u.ce(node.h4_mismatches);
				node.h4_mismatches.clicked = function() {
					u.toggleClass(this.node.ul_mismatches, "show");
				}
			}

		}


		// add filter to list
		if(u.hc(div, "filters")) {

			u.defaultFilters(div);

		}


		// add option to options list
		div.addOption = function(option, ua_node) {
//			console.log("## addOption:" + u.nodeId(ua_node) + ", " + option)

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
//					u.bug("closeOption");

					if(this._info) {
						this._info.parentNode.removeChild(this._info);
						this._info = false;
					}
					if(this._show_selected_only) {
						this._show_selected_only.parentNode.removeChild(this._show_selected_only);
						this._show_selected_only = false;
					}
					if(this._selected) {
						this._selected.parentNode.removeChild(this._selected);
						this._selected = false;
					}
					if(this._matching) {
						this._matching.parentNode.removeChild(this._matching);
						this._matching = false;
					}
					if(this._addtoclone) {
						this._addtoclone.parentNode.removeChild(this._addtoclone);
						this._addtoclone = false;
					}

					// remove highlighting of matching nodes
					var i, node;
					for(i = 0; node = this.ua_nodes[i]; i++) {
						u.rc(node, "mapped");
//						node.option_node = false;
					}

				}

				//
				option_node.clicked = function() {

					// show advanced options menu if not already present 
					// (else close it)

					// ONLY SHOW MATCHING
					// add all MATCHING
					// add all SELECTED
					// add SELECTED to CLONE
					if(!this._info) {

						// close other options 
						// (only one can be open at the time, because MATCHING options will be highlighted)
						for(i = 0; li = this.div._add_to.identified_options_lis[i]; i++) {
							if(li != this) {
								li.closeOption();
							}
						}


						// highlight all matching ua_nodes
						var i, node;
						for(i = 0; node = this.ua_nodes[i]; i++) {
							u.ac(node, "mapped");
						}


						// option matches device
						if(this.device_id != "unknown") {

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

							// add filter options
							// this._show_selected_only = u.ae(this, "div", {"class":"showselectedonly", "html":"ONLY SHOW MATCHING"});
							// this._show_selected_only.option = this;

							// add indexing options
							this._selected = u.ae(this, "div", {"class":"selected", "html":"Add all SELECTED"});
							this._selected.option = this;
							this._matching = u.ae(this, "div", {"class":"matching", "html":"Add all MATCHING"});
							this._matching.option = this;
							this._addtoclone = u.ae(this, "div", {"class":"addtoclone", "html":"Add SELECTED to CLONE"});
							this._addtoclone.option = this;

							// u.e.click(this._show_selected_only);
							// this._show_selected_only.clicked = function() {
							//
							// 	// TODO: show selected only
							//
							// 	if(!this._filtered) {
							// 		this.innerHTML
							// 		u.as(this.option._selected, "display", "none");
							// 		u.as(this.option._addtoclone, "display", "none");
							// 		// TODO: hide selected ua_nodes if not a match
							// 	}
							// 	else {
							//
							// 		u.as(this.option._selected, "display", "block");
							// 		u.as(this.option._addtoclone, "display", "block");
							//
							// 		// TODO: show all selected ua_nodes
							// 	}
							//
							// }

							// ADD SELECTED handler
							u.e.click(this._selected);
							this._selected.clicked = function() {

								if(this.t_execute) {

									this.inputs = u.qsa("li:not(.all) input:checked", this.option.div.list);
									this.inputs_i = 0;

									// recursive iteration handler (to control the number of simultaneous requests)
									this.iterateSelections = function() {

										// console.log("this.inputs.length:" + this.inputs.length);
										// console.log("this.input_i:" + this.inputs_i);
										if(this.inputs_i < this.inputs.length) {

											var input = this.inputs[this.inputs_i++];
											// console.log("input exists:" + input)
											// console.log(input)

											input._selected = this;

											// UA was added
											input.response = function(response) {
//													console.log("node loaded:" + this.node.ua_id);

												// update ua list
												if(this.node.option_node) {
													// remove node from option_node array
													this.node.option_node.ua_nodes.splice(this.node.option_node.ua_nodes.indexOf(this.node), 1);
												}

												// and remove node
												this.node.parentNode.removeChild(this.node);
												this.node.div.toggleAddToOption();

												// process next selected
												this._selected.iterateSelections();
											}
											// make request
											u.request(input, input.node.div.useragent_add+"/"+this.option.device_id+"/"+input.node.ua_id, {"method":"post", "params":"csrf-token="+input.node.div.csrf_token});

										}

									}
									this.iterateSelections();

								}
								else {
									this.t_execute = u.t.setTimer(this, this.not_confirmed, 1500);

									this._content = this.innerHTML;	
									this.innerHTML = "Sure?";
									u.ac(this, "confirm");
								}
							}


							// ADD MATCHING handler
							u.e.click(this._matching);
							this._matching.clicked = function() {

								if(this.t_execute) {

									this.inputs = u.qsa("li:not(.all) input:checked", this.option.div.list);
									this.inputs_i = 0;

									// recursive iteration handler (to control the number of simultaneous requests)
									this.iterateSelections = function() {

										// console.log("this.inputs.length:" + this.inputs.length);
										// console.log("this.input_i:" + this.inputs_i);
										if(this.inputs_i < this.inputs.length) {

											var input = this.inputs[this.inputs_i++];
											// console.log("input exists:" + input)
											// console.log(input)

											// continue to look for next input matching this device
											while(input.node._identified.id != this.option.device_id) {
//												console.log("not matching: ("+this.inputs_i+")");
												if(this.inputs_i < this.inputs.length) {
													input = this.inputs[this.inputs_i++];
												}
												else {
													break;
												}
											}

											// if input was found, make request
											if(input && input.node._identified.id == this.option.device_id) {
//												console.log("load node:" + input.node.ua_id);
												input._matching = this;

												// UA was added
												input.response = function(response) {
//													console.log("node loaded:" + this.node.ua_id);

													// update ua list
													if(this.node.option_node) {
														// remove node from option_node array
														this.node.option_node.ua_nodes.splice(this.node.option_node.ua_nodes.indexOf(this.node), 1);
													}

													// and remove node
													this.node.parentNode.removeChild(this.node);
													this.node.div.toggleAddToOption();

													// process next selected
													this._matching.iterateSelections();
												}
												// make request
												u.request(input, input.node.div.useragent_add+"/"+this.option.device_id+"/"+input.node.ua_id, {"method":"post", "params":"csrf-token="+input.node.div.csrf_token});
											}

										}

									}
									this.iterateSelections();

								}
								else {
									this.t_execute = u.t.setTimer(this, this.not_confirmed, 1500);

									this._content = this.innerHTML;	
									this.innerHTML = "Sure?";
									u.ac(this, "confirm");
								}
							}


							// ADD SELECTED TO CLONE handler
							u.e.click(this._addtoclone);
							this._addtoclone.clicked = function() {

								// confirm mechanism in action
								if(this.t_execute) {

									// clone device response
									this.response = function(response) {
										if(response.cms_status == "success" && response.cms_object.id) {

											var inputs = u.qsa("li:not(.all) input:checked", this.option.div.list);
											var i, input;
											// selected items in list?
											for(i = 0; input = inputs[i]; i++) {
												// add response
												input.node.response = function(response) {
													// remove node from list

													if(this.option_node) {
														// remove node from option_node array
														this.option_node.ua_nodes.splice(this.option_node.ua_nodes.indexOf(this), 1);
													}

													this.parentNode.removeChild(this);
													this.div.toggleAddToOption();
												}
												// add useragent to device
												u.request(input.node, input.node.div.useragent_add+"/"+response.cms_object.id+"/"+input.node.ua_id, {"method":"post", "params":"csrf-token="+input.node.div.csrf_token});
											}

										}
										else {
											page.notify(response);
										}
									}
									// clone device
									u.request(this, this.option.div.device_clone+"/"+this.option.device_id, {"method":"post", "params":"csrf-token="+this.option.div.csrf_token});

								}
								// activate confirm mechanism
								else {
									this.t_execute = u.t.setTimer(this, this.not_confirmed, 1500);

									this._content = this.innerHTML;	
									this.innerHTML = "Sure?";
									u.ac(this, "confirm");
								}
							}


							// confirm timeout handler
							this._matching.not_confirmed = this._addtoclone.not_confirmed = this._selected.not_confirmed = function() {
								u.rc(this, "confirm");
								this.innerHTML = this._content;
								this.t_execute = false;
							}
						}


						// option does not match device
						else {
							this._info = u.ae(this, "div", {"class":"info", "html":"identification did not return a valid device id"});
						}


					}

					// remove advanced menu
					else {

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
//			console.log("updateOptions")

			var i, option_node, checkbox;

			// is add_to still open
			if(this._add_to) {
				for(i = 0; option_node = this._add_to.identified_options_lis[i]; i++) {
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

				for(i = 0; checkbox = this.visible_inputs[i]; i++) {
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

					// perform search
					search_input.search = function() {

						// empty result list
						search_input.search_result.innerHTML = "";

						// only do search with valid search string
						if(this.value && this.value != this._default_value) {

							// get search response
							search_input.response = function(response) {

								// get items from result
								var items = u.qsa(".all_items li.item", response);
								if(items.length) {

									var i, node;
									for(i = 0; node = items[i]; i++) {
										node = this.search_result.appendChild(node);
										node.div = this.div;
										node.device_id = u.cv(node, "item_id");

										u.e.click(node);
										node.clicked = function() {

											// add advanced options menu
											// add all SELECTED
											// add SELECTED to CLONE
											if(!this._info) {

												var i, info_string;
												var brand = u.qs("ul.tags li.brand .value", this);
												
												if(brand) {
													info_string = brand.innerHTML;
												}
												//info_string += ", " + this.details["description"];
												this._info = u.ae(this, "div", {"class":"info", "html":info_string});


												this._selected = u.ae(this, "div", {"class":"selected", "html":"Add all SELECTED"});
												this._selected.option = this;
												this._addtoclone = u.ae(this, "div", {"class":"addtoclone", "html":"Add SELECTED to CLONE"});
												this._addtoclone.option = this;


												// ADD SELECTED handler
												u.e.click(this._selected);
												this._selected.clicked = function() {

													// confirm mechanism in action
													if(this.t_execute) {
							
														var inputs = u.qsa("li:not(.all) input:checked", this.option.div.list);
														var i, input;
														// selected items in list?
														for(i = 0; input = inputs[i]; i++) {
															// add response
															input.node.response = function(response) {
																// remove node from list
																this.parentNode.removeChild(this);
																this.div.toggleAddToOption();
															}
															// add useragent to device
															u.request(input.node, input.node.div.useragent_add+"/"+this.option.device_id+"/"+input.node.ua_id, {"method":"post", "params":"csrf-token="+input.node.div.csrf_token});

														}
													}
													// activate confirm mechanism
													else {
														this.t_execute = u.t.setTimer(this, this.not_confirmed, 1500);

														this._content = this.innerHTML;	
														this.innerHTML = "Sure?";
														u.ac(this, "confirm");
													}
												}


												// ADD SELECTED TO CLONE handler
												u.e.click(this._addtoclone);
												this._addtoclone.clicked = function() {

													// confirm mechanism in action
													if(this.t_execute) {

														// clone device response
														this.response = function(response) {
															if(response.cms_status == "success" && response.cms_object.id) {

																var inputs = u.qsa("li:not(.all) input:checked", this.option.div.list);
																var i, input;
																// selected items in list?
																for(i = 0; input = inputs[i]; i++) {
																	// add response
																	input.node.response = function(response) {
																		// remove node from list
																		this.parentNode.removeChild(this);
																		this.div.toggleAddToOption();
																	}
																	// add useragent to device
																	u.request(input.node, input.node.div.useragent_add+"/"+response.cms_object.id+"/"+input.node.ua_id, {"method":"post", "params":"csrf-token="+input.node.div.csrf_token});
																}

															}
															else {
																page.notify(response);
															}
														}
														// clone device
														u.request(this, this.option.div.device_clone+"/"+this.option.device_id, {"method":"post", "params":"csrf-token="+this.option.div.csrf_token});

													}
													// activate confirm mechanism
													else {
														this.t_execute = u.t.setTimer(this, this.not_confirmed, 1500);

														this._content = this.innerHTML;	
														this.innerHTML = "Sure?";
														u.ac(this, "confirm");
													}
												}


												// confirm timeout handler
												this._selected.not_confirmed = this._addtoclone.not_confirmed = function() {
													u.rc(this, "confirm");
													this.innerHTML = this._content;
													this.t_execute = false;
												}

											}
											// remove advanced menu
											else {
												if(this._info) {
													this._info.parentNode.removeChild(this._info);
													this._info = false;
												}
												if(this._selected) {
													this._selected.parentNode.removeChild(this._selected);
													this._selected = false;
												}
												if(this._addtoclone) {
													this._addtoclone.parentNode.removeChild(this._addtoclone);
													this._addtoclone = false;
												}
											}

										}
		 							}
								}

								u.rc(this, "loading");
							}

							u.ac(search_input, "loading");
							// perform search
							u.request(search_input, this.div.device_list, {"params":"search=1&search_string="+this.value, "method":"post"})
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
				u.ac(this._add_to, "loading");

				this.checked_inputs_i = 0;
				this.iterateSelections = function() {
					// console.log("this.iterateSelections");
					// console.log("this.checked_inputs.length:" + this.checked_inputs.length);
					// console.log("this.checked_inputs_i:" + this.checked_inputs_i);

					if(this.checked_inputs_i < this.checked_inputs.length) {

						var input = this.checked_inputs[this.checked_inputs_i++];

						// node is in identification mode
						u.ac(input.node, "identifying");
						input.node._is_identifying = true;


						// useragent has not been identified yet
						if(!input.node._identified) {
//							console.log("not id'ed yet:" + u.nodeId(input.node))

							// device identification response
							input.response = function(response) {
//								console.log("node identified:" + u.nodeId(this.node))


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
								else {
									this.node._identified.id = "unknown";
									this.node._identified.name = "unknown";
									this.node._identified.tags = [];
									this.node._identified.method = "unknown";
									this.node._identified.guess = "unknown";

								}

								// add option to the options list (will audo detect existance)
								this.node.div.addOption(this.node._identified, this.node);


								// check load status
								this.node.div.wait_for_uas--;
								if(!this.node.div.wait_for_uas && this.node.div._add_to) {
									u.rc(this.node.div._add_to, "loading");
								}

								this.node.div.iterateSelections();
							}
							// request identification
							u.request(input, input.node.div.useragent_identify+"/"+input.node.ua_id, {"method":"post", "params":"csrf-token="+input.node.div.csrf_token});
						}

						// already identified
						else {
//							console.log("id'ed already:" + u.nodeId(input.node))

							// leave identification mode
							u.rc(input.node, "identifying");
							input.node._is_identifying = false;

							// add option to the options list (will audo detect existence)
							this.addOption(input.node._identified, input.node);

							// check load status
							this.wait_for_uas--;
							if(!this.wait_for_uas) {
								u.rc(this._add_to, "loading");
							}

							this.iterateSelections();
						}

					}
					else {
//						console.log("done");
					}

				}
				this.iterateSelections();

// 				// loop through all nodes
// 				for(i = 0; ua = this.checked_inputs[i]; i++) {
//
// 					// node is in identification mode
// 					u.ac(ua.node, "identifying");
// 					ua.node._is_identifying = true;
//
//
// 					// useragent has not been identified yet
// 					if(!ua.node._identified) {
// //						u.bug("not id'ed yet:" + u.nodeId(ua.node))
//
// 						// device identification response
// 						ua.node.response = function(response) {
// //							u.bug("node identified:" + u.nodeId(this))
//
//
// 							// leave identification mode
// 							u.rc(this, "identifying");
// 							this._is_identifying = false;
//
// 							// id details object
// 							this._identified = {};
//
// 							// identification was successful
// 							if(response.cms_status == "success" && response.cms_object.id) {
//
// 								this._identified.id = response.cms_object.id;
// 								this._identified.name = response.cms_object.name;
// 								this._identified.tags = response.cms_object.tags;
// 								this._identified.method = response.cms_object.method;
// 								this._identified.guess = response.cms_object.guess;
//
// 							}
//
// 							// bad result - device not identified
// 							else {
// 								this._identified.id = "unknown";
// 								this._identified.name = "unknown";
// 								this._identified.tags = [];
// 								this._identified.method = "unknown";
// 								this._identified.guess = "unknown";
//
// 							}
//
// 							// add option to the options list (will audo detect existance)
// 							this.div.addOption(this._identified, this);
//
//
// 							// check load status
// 							this.div.wait_for_uas--;
// 							if(!this.div.wait_for_uas && this.div._add_to) {
// 								u.rc(this.div._add_to, "loading");
// 							}
//
// 						}
// 						// request identification
// 						u.request(ua.node, ua.node.div.useragent_identify+"/"+ua.node.ua_id, {"method":"post", "params":"csrf-token="+ua.node.div.csrf_token});
// 					}
//
// 					// already identified
// 					else {
// //						u.bug("id'ed already:" + u.nodeId(ua.node))
//
// 						// leave identification mode
// 						u.rc(ua.node, "identifying");
// 						ua.node._is_identifying = false;
//
// 						// add option to the options list (will audo detect existance)
// 						this.addOption(ua.node._identified, ua.node);
//
// 						// check load status
// 						this.wait_for_uas--;
// 						if(!this.wait_for_uas) {
// 							u.rc(this._add_to, "loading");
// 						}
// 					}
// 				}

			}

			// no selected useragents
			else {

				// remove "Add to" if it exists
				if(this._add_to) {
					this._add_to.parentNode.removeChild(this._add_to);
					this._add_to = false;

					// adjust page width
					u.as(page, "width", "auto");
				}
			}

			this.updateOptions();

		}


		this.keepAlive = function() {
			u.request(this, "/janitor/device/keepAlive");
		}

		u.t.setInterval(this, "keepAlive", 300000);



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


		div.response = function(response) {

			page.notify(response);

			if(response.cms_status == "success") {

				this._open = true;

				this.div_results = u.qs(".all_items");
				this.div_stats = u.qs(".stats");


				// add marker filter
				this._filter = u.ae(this, "div", {"class":"filter"});


				this.markers_ul = u.ae(this, "ul", {"class":"markers"});
				var i, node, li;
				for(i = 0; node = response.cms_object[i]; i++) {
					li = u.ae(this.markers_ul, "li", {"html":node.name});
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

						u.bug("test device markers")
						this.response = function(response) {
							page.notify(response);

							if(response.isHTML) {
								// result set div

								// var div_results = u.qs(".all_items");

								// update stats
								this.div.div_stats.innerHTML = u.qs(".stats", response).innerHTML;


								// inject new values
								var new_items = u.qsa(".all_items ul.items li.item", response);
								if(new_items) {
									
									var i, node;
									for(i = 0; node = new_items[i]; i++) {
										u.ae(this.existing_results, node);
									}
									u.o.unidentifiedList.init(this.div.div_results);
								}
								else {
									u.ae(this.existing_results, u.qs(".all_items p", response));
									// no results
								}

								u.rc(this, "loading");

							}
						}
						u.ac(this, "loading");
						u.request(this, this.div.url_device_test, {"params":"csrf-token="+this.div.csrf_token+"&test_marker=true&device_id="+this.item_id, "method":"post"});
					}
				}


				this._markers = u.qsa("li", this.markers_ul);


				// index list, to speed up filtering process
				var i, node;
				for(i = 0; node = this._markers[i]; i++) {
					node._c = node.textContent.toLowerCase();
				}

				// insert tags filter
				this._filter._field = u.ae(this._filter, "div", {"class":"field"});
				u.ae(this._filter._field, "label", {"html":"Filter"});

				this._filter._input = u.ae(this._filter._field, "input", {"class":"filter ignoreinput"});
				this._filter._input._div = this;

				this._filter._input.onkeydown = function() {
	//				u.bug("reset timer")
					u.t.resetTimer(this._div.t_filter);
				}
				this._filter._input.onkeyup = function() {
	//				u.bug("set timer")
					this._div.t_filter = u.t.setTimer(this._div, this._div.filter, 1500);
					u.ac(this._div._filter, "filtering");
				}
				this.filter = function() {

					var i, node;
					if(this._current_filter != this._filter._input.value.toLowerCase()) {
	//					u.bug("filter by:" + this._filter._input.value)

						this._current_filter = this._filter._input.value.toLowerCase();
						for(i = 0; node = this._markers[i]; i++) {

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
			u.request(this, this.url_device_get, {"params":"csrf-token="+this.csrf_token, "method":"post"})
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


				// add marker filter
				this._filter = u.ae(this, "div", {"class":"filter"});


				this.markers_ul = u.ae(this, "ul", {"class":"markers"});
				var i, node, li;
				for(i = 0; node = response.cms_object[i]; i++) {
					li = u.ae(this.markers_ul, "li", {"html":node.name});
					li.div = this;
					li.item_id = node.item_id;

					u.e.click(li);
					li.clicked = function() {

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

						u.bug("crossreference device markers")
						this.response = function(response) {
							page.notify(response);

							if(response.isHTML) {
								// result set div

								// var div_results = u.qs(".all_items");

								// update stats
								this.div.div_stats.innerHTML = u.qs(".stats", response).innerHTML;


								// inject new values
								var new_items = u.qsa(".all_items ul.items li.item", response);
								if(new_items) {
									
									var i, node;
									for(i = 0; node = new_items[i]; i++) {
										u.ae(this.existing_results, node);
									}
									u.o.unidentifiedList.init(this.div.div_results);
								}
								else {
									u.ae(this.existing_results, u.qs(".all_items p", response));
									// no results
								}

								u.rc(this, "loading");

							}
						}
						u.ac(this, "loading");
						u.request(this, this.div.url_device_test, {"params":"csrf-token="+this.div.csrf_token+"&crossreference_marker=true&device_id="+this.item_id, "method":"post"});
					}
				}


				this._markers = u.qsa("li", this.markers_ul);


				// index list, to speed up filtering process
				var i, node;
				for(i = 0; node = this._markers[i]; i++) {
					node._c = node.textContent.toLowerCase();
				}

				// insert tags filter
				this._filter._field = u.ae(this._filter, "div", {"class":"field"});
				u.ae(this._filter._field, "label", {"html":"Filter"});

				this._filter._input = u.ae(this._filter._field, "input", {"class":"filter ignoreinput"});
				this._filter._input._div = this;

				this._filter._input.onkeydown = function() {
	//				u.bug("reset timer")
					u.t.resetTimer(this._div.t_filter);
				}
				this._filter._input.onkeyup = function() {
	//				u.bug("set timer")
					this._div.t_filter = u.t.setTimer(this._div, this._div.filter, 1500);
					u.ac(this._div._filter, "filtering");
				}
				this.filter = function() {

					var i, node;
					if(this._current_filter != this._filter._input.value.toLowerCase()) {
	//					u.bug("filter by:" + this._filter._input.value)

						this._current_filter = this._filter._input.value.toLowerCase();
						for(i = 0; node = this._markers[i]; i++) {

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

			u.request(this, this.url_device_get, {"params":"csrf-token="+this.csrf_token, "method":"post"})
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
