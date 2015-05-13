//u.bug_force = true;


// quick toggle header with simplified memory (cross item memory)
u.toggleHeader = function(div, header) {

	header = header ? header : "h2";

	// add collapsable header
	div._toggle_header = u.qs(header, div);
	div._toggle_header_id = div.className.replace(/item_id:[0-9]+/, "").trim();

	div._toggle_header.div = div;
	u.e.click(div._toggle_header);
	div._toggle_header.clicked = function() {
		if(this.div._toggle_is_closed) {
			u.as(this.div, "height", "auto");
			this.div._toggle_is_closed = false;
			u.saveCookie(this.div._toggle_header_id+"_open", 1);
		}
		else {
			u.as(this.div, "height", this.offsetHeight+"px");
			this.div._toggle_is_closed = true;
			u.saveCookie(this.div._toggle_header_id+"_open", 0);
		}
	}
	var state = u.getCookie(div._toggle_header_id+"_open");
	if(state == "0") {
		div._toggle_header.clicked();
	}
}




// device search 
Util.Objects["searchDevice"] = new function() {
	this.init = function(div) {

//		u.bug("searchDevice");

		var form = u.qs("form", div);
		form.div = div;
		div.csrf_token = div.getAttribute("data-csrf-token");
		div.add_tag_url = div.getAttribute("data-tag-add");
		div.delete_tag_url = div.getAttribute("data-tag-delete");
		div.get_tags_url = div.getAttribute("data-tag-get");

		u.f.init(form);

		form.submitted = function() {
//			u.bug("submitted")

			var params = u.f.getParams(this);
//			u.xInObject(params);
			var tags = u.qsa("li:not(.add)", this._tags._list);
//			u.bug("tags:" + tags.length)

			if(tags) {
				params += "&tags=";
				var tag_array = [];
				var i, tag;
				for(i = 0; tag = tags[i]; i++) {

					if(!tag._context) {
						tag._context = u.qs(".context", tag).innerHTML;
						tag._value = u.qs(".value", tag).innerHTML;
					}
//					u.bug("add tag to params:" + tag + "; " + tag._value)

					tag_array.push(tag._context+":"+tag._value);
				}
				params += tag_array.join(";");
			}
			
			this.response = function(response) {

				var list = u.qs(".all_items");
				list.parentNode.replaceChild(u.qs(".all_items", response), list);

				// remove existing scroll handler
				u.e.removeWindowScrollEvent(list, list.scroll_event_id);

				// reinitialize
				u.init();
				
			}
			var list = u.qs(".all_items");
			u.a.transition(list, "opacity 0.5s ease-in");
			u.a.setOpacity(list, 0.2);
			this.disableTaggedSearch();
			u.request(this, this.action, {"params":params, "method":"post"})
		}

		form._tags = u.qs("div.tags", form);
		if(!form._tags) {
			form._tags = u.ae(form, "div", {"class":"tags"});
		}
		form._tags._form = form;

		// tags list for existing tags
		form._tags._list = u.qs("ul.tags", form);
		if(!form._tags._list) {
			form._tags._list = u.ae(form._tags, "ul", {"class":"tags"});
		}

		// get all tags from server
		form._tags.tagsResponse = function(response) {

			if(response.cms_status == "success" && response.cms_object) {
				this._alltags = response.cms_object;

				// add "add" button when tags are ready
				this._bn_add = u.ae(this._list, "li", {"class":"add","html":"+"});
				this._bn_add._form = this._form;
				u.e.click(this._bn_add);
				this._bn_add.clicked = function() {
					this._form.enableTaggedSearch();
				}

			}
			else {
				// TODO: no tags?
			}
		}
		// get tags
		u.request(form._tags, div.get_tags_url, {"callback":"tagsResponse", "method":"post", "params":"csrf-token=" + div.csrf_token});


		form.enableTaggedSearch = function() {

			u.ac(this._tags, "addTags");

			// insert tags filter
			this._tags.field = u.ae(this._tags, "div", {"class":"field"});

			this._tags._tagfilter = u.ae(this._tags.field, "input", {"class":"filter ignoreinput"});
			this._tags._tagfilter._tags = this._tags;

			this._tags._tagfilter.onkeyup = function() {

				if(this._tags._taglist) {
					var tags = u.qsa(".tag", this._tags._taglist);
					var i, tag;
					for(i = 0; tag = tags[i]; i++) {

	//					u.bug(tag.textContent)
						if(tag.textContent.toLowerCase().match(this.value.toLowerCase())) {
							u.as(tag, "display", "inline-block");
						}
						else {
							u.as(tag, "display", "none");
						}
					}
				}
				
			}

			this._tags._bn_add.innerHTML = "-";
			this._tags._bn_add.clicked = function() {
				this._form.disableTaggedSearch();
			}


			// index existing tags
			this._tags._usedtags = {};
			var itemTags = u.qsa("li:not(.add)", this._tags._list);

			var i, tag, context, value;

			for(i = 0; tag = itemTags[i]; i++) {
				tag._context = u.qs(".context", tag).innerHTML;
				tag._value = u.qs(".value", tag).innerHTML;

//				u.bug("exist context:value:" + tag._context + ":" + tag._value)

				if(!this._tags._usedtags[tag._context]) {
					this._tags._usedtags[tag._context] = {}
				}
				if(!this._tags._usedtags[tag._context][tag._value]) {
					this._tags._usedtags[tag._context][tag._value] = tag;
				}
			}

			// add list with complete tags
			this._tags._taglist = u.ae(this._tags, "ul", {"class":"taglist"});

			// loop through all tags
			for(tag in this._tags._alltags) {

				// tag context
				context = this._tags._alltags[tag].context;
				// tag value - replace single & with entity or it is not recognized
				value = this._tags._alltags[tag].value.replace(/ & /, " &amp; ");
//				u.bug("context:value:" + context + ":" + value)

				if(this._tags._usedtags && this._tags._usedtags[context] && this._tags._usedtags[context][value]) {
// 					// 	u.ac(node, "selected");
					tag_node = this._tags._usedtags[context][value];
				}
				else {
					tag_node = u.ae(this._tags._taglist, "li", {"class":"tag"});
					tag_node._context = context;
					tag_node._value = value;
					u.ae(tag_node, "span", {"class":"context", "html":tag_node._context});
					u.ae(tag_node, "span", {"class":"value", "html":tag_node._value});
				}

				tag_node._tags = this._tags;

//				tag_node._taglist = this._tags._taglist;
				tag_node._id = this._tags._alltags[tag].id;
// 				tag_node.node = node;


// 
 				u.e.click(tag_node);
 				tag_node.clicked = function() {
// 					u.bug("tag clicked:" + tag_node._context+":"+tag_node._value);

						// tag is in existing tags list
						// remove tag
						if(u.hc(this.parentNode, "tags")) {
							// TODO: remove from used tags
							u.ae(this._tags._taglist, this);

						}
						// else add tag
						else {

							// TODO: remove from used tags
							u.ie(this._tags._list, this);

// 
						}

				}

			}

			
		}
		form.disableTaggedSearch = function() {

			// change button
			this._tags._bn_add.innerHTML = "+";
			this._tags._bn_add.clicked = function() {
				this._form.enableTaggedSearch();
			}

			// remove extra tags list
			if(this._tags._taglist) {
				u.rc(this._tags, "addTags");
				this._tags.removeChild(this._tags.field);
				this._tags.removeChild(this._tags._taglist);
				this._tags._taglist = false;
			}
			
		}

	}
}


Util.Objects["cloneDevice"] = new function() {
	this.init = function(li) {

		li.csrf_token = li.parentNode.getAttribute("data-csrf-token");
		u.ce(li);
		li.clicked = function() {
			
			this.response = function(response) {
				if(response.cms_status == "success" && response.cms_object.id) {

					// load cloned device
					location.href = location.href.replace(/[\d]+$/, response.cms_object.id);

				}
				else {
					page.notify(response);
				}
			}
			u.request(this, this.url, {"method":"post", "params":"csrf-token="+li.csrf_token});
		}
	}
}



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
		u.bug("init unidentifiedList")

		var i, node;

		div.list = u.qs("ul.items", div);
		div.nodes = u.qsa("li.item", div.list);
		
		div.csrf_token = div.getAttribute("data-csrf-token");
		div.useragent_delete = div.getAttribute("data-useragent-delete");
		div.useragent_details = div.getAttribute("data-useragent-details");
		div.useragent_identify = div.getAttribute("data-useragent-identify");
		div.useragent_add = div.getAttribute("data-useragent-add");
		div.device_clone = div.getAttribute("data-device-clone");
		div.device_list = div.getAttribute("data-device-list");

		document.body.unidentified_div = div;


		// add select all option
		div.bn_all = u.ie(div.list, "li", {"class":"all", "html":"Select all"});
		div.bn_all._checkbox = u.ie(div.bn_all, "input", {"type":"checkbox"});
		div.bn_all.div = div;
		div.bn_all._checkbox.div = div;
		u.e.click(div.bn_all);
		div.bn_all.clicked = function() {
			var i, node;
			// figure out wether to select or deselect (if one is selected, de-select all)
			var inputs = u.qsa("li:not(.all) input:checked", this.div.list);

			for(i = 0; node = div.nodes[i]; i++) {
				if(inputs.length) {
					node._checkbox.checked = false;
				}
				else if(!node._hidden) {
					node._checkbox.checked = true;
				}
			}

			this.div.toggleAddToOption();
		}


		// add checkboxes to all rows
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
				}
				else {
					this.checked = true;
					document.body._multiselection = true;
				}

				document.body.onmouseup = function() {
					this.onmouseup = null;
					this._multiselection = false;
					this._multideselection = false;

					// show or no-show add option
					this.unidentified_div.toggleAddToOption();
				}

//				this.node.div.toggleAddToOption();
			}
			node._checkbox.onmouseover = function() {
				if(document.body._multiselection) {
					this.checked = true;
				}
				else if(document.body._multideselection) {
					this.checked = false;
				}
			}

			// enable node expansion
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
	
									u.bug("node.ua_id:" + this.node.ua_id);

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
							u.ae(this._ul, "li", {"class":"identified_as", "html":response.cms_object[0].identified_as_device})

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
					var uls = u.qsa("ul", this);
					var i, ul;
					for(i = 0; ul = uls[i]; i++) {
						this.removeChild(ul);
					}
					this._ul = false;
				}
			}
		}


		// add filter to list
		if(u.hc(div, "filters")) {
			
			div._filter = u.ie(div, "div", {"class":"filter"});

			// index list, to speed up filtering process
			var i, node;
			for(i = 0; node = div.nodes[i]; i++) {
				node._c = node.textContent.toLowerCase();
			}

			// insert tags filter
			div._filter._field = u.ae(div._filter, "div", {"class":"field"});
			u.ae(div._filter._field, "label", {"html":"Filter"});

			div._filter._input = u.ae(div._filter._field, "input", {"class":"filter ignoreinput"});
			div._filter._input._div = div;

			div._filter._input.onkeydown = function() {
//				u.bug("reset timer")
				u.t.resetTimer(this._div.t_filter);
			}
			div._filter._input.onkeyup = function() {
//				u.bug("set timer")
				this._div.t_filter = u.t.setTimer(this._div, this._div.filter, 1500);
				u.ac(this._div._filter, "filtering");
			}
			div.filter = function() {

				var i, node;
				if(this._current_filter != this._filter._input.value.toLowerCase()) {
//					u.bug("filter by:" + this._filter._input.value)

					this._current_filter = this._filter._input.value.toLowerCase();
					for(i = 0; node = this.nodes[i]; i++) {

						if(node._c.match(this._current_filter)) {
							node._hidden = false;
							u.as(node, "display", "block", false);
						}
						else {
							node._hidden = true;
							u.as(node, "display", "none", false);
							node._checkbox.checked = false;
						}
					}
				}

				u.rc(this._filter, "filtering");
			}
		}


		// add option to options list - if it does not already exist
		div.addOption = function(option) {

			// check options index for current option
			if(this._add_to.identified_options && this._add_to.identified_options.indexOf(option.id) == -1) {
				this._add_to.identified_options.push(option.id);
				var li_option = u.ae(this._add_to._list, "li", {"html":option.name});
				li_option.details = option;
				li_option.div = this;
				li_option.device_id = option.id;

				u.e.click(li_option);
				li_option.clicked = function() {

					// add advanced options menu
					// add all SELECTED
					// add SELECTED to CLONE
					if(!this._info) {

						var i;
						var info_string = "";
						info_string = this.details["method"];
						if(this.details["guess"]) {
							info_string += ", " + this.details["guess"];
						}
						if(this.details["tags"]) {
							for(i in this.details["tags"]) {
								if(this.details["tags"][i]["context"] == "brand") {
									info_string += ", " + this.details["tags"][i]["value"]
								}
							}
						}
						info_string += ", " + this.details["description"];
						this._info = u.ae(this, "div", {"class":"info", "html":info_string});

						if(this.device_id != "unknown") {
							this._selected = u.ae(this, "div", {"class":"selected", "html":"Add all SELECTED"});
							this._selected.option = this;
							this._matching = u.ae(this, "div", {"class":"matching", "html":"Add all MATCHING"});
							this._matching.option = this;
							this._addtoclone = u.ae(this, "div", {"class":"addtoclone", "html":"Add SELECTED to CLONE"});
							this._addtoclone.option = this;


							// ADD SELECTED handler
							u.e.click(this._selected);
							this._selected.clicked = function() {

								if(this.t_execute) {
							
									var inputs = u.qsa("li:not(.all) input:checked", this.option.div.list);

									// selected items in list?
									for(i = 0; input = inputs[i]; i++) {

										input.node.response = function(response) {

											// and remove node
											this.parentNode.removeChild(this);
											this.div.toggleAddToOption();
										}
										u.request(input.node, input.node.div.useragent_add+"/"+this.option.device_id+"/"+input.node.ua_id, {"method":"post", "params":"csrf-token="+input.node.div.csrf_token});

									}
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

									var inputs = u.qsa("li:not(.all) input:checked", this.option.div.list);

									// selected items in list?
									for(i = 0; input = inputs[i]; i++) {
										if(input.node._identified.id == this.option.device_id) {

											input.node.response = function(response) {

												// and remove node
												this.parentNode.removeChild(this);
												this.div.toggleAddToOption();
											}
											u.request(input.node, input.node.div.useragent_add+"/"+this.option.device_id+"/"+input.node.ua_id, {"method":"post", "params":"csrf-token="+input.node.div.csrf_token});
										}
									}
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
						else {
							this._error = u.ae(this, "div", {"class":"info", "html":"identification did not return a valid device id"});
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
						if(this._matching) {
							this._matching.parentNode.removeChild(this._matching);
							this._matching = false;
						}
						if(this._addtoclone) {
							this._addtoclone.parentNode.removeChild(this._addtoclone);
							this._addtoclone = false;
						}
						if(this._error) {
							this._error.parentNode.removeChild(this._error);
							this._error = false;
						}
					}
				}
			}
		}

		// show or hide "Add To" options, depending on whether useragents are selected or not
		div.toggleAddToOption = function() {
			var inputs = u.qsa("li:not(.all) input:checked", this.list);

			// selected items in list?
			if(inputs.length) {

				// does add to panel exist
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
					search_input._default_value = "Search";
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
				}

				// set counter
				this._add_to._count.innerHTML = inputs.length;
				// empty options list
				this._add_to._list.innerHTML = "";



				// options based on selected useragents
				this._add_to.identified_options = [];

				var i, ua, ua_id
				this.wait_for_uas = inputs.length;
				u.ac(this._add_to, "loading");

				for(i = 0; ua = inputs[i]; i++) {

					if(!ua.node._identified) {
//						u.bug("not id'ed yet:" + u.nodeId(ua.node))

						// device identification response
						ua.node.response = function(response) {
							if(response.cms_status == "success") {

								if(response.cms_object.id) {
									this._identified = response.cms_object;
								}

							}
							// bad result - device not identified
							if(!this._identified) {
								this._identified = {};
								this._identified.id = "unknown";
								this._identified.name = "Unknown";
							}

							// add new option to the options list
							this.div.addOption(this._identified);

							// check load status
							this.div.wait_for_uas--;
							if(!this.div.wait_for_uas && this.div._add_to) {
								u.rc(this.div._add_to, "loading");
							}

						}
						// request identification
						u.request(ua.node, ua.node.div.useragent_identify+"/"+ua.node.ua_id, {"method":"post", "params":"csrf-token="+ua.node.div.csrf_token});
					}
					// already identified
					else {
//						u.bug("id'ed already:" + u.nodeId(ua.node))

						// add option
						this.addOption(ua.node._identified);

						// check load status
						this.wait_for_uas--;
						if(!this.wait_for_uas) {
							u.rc(this._add_to, "loading");
						}
					}
				}
			}
			// no selected useragents - remove "Add to" if it exists
			else {
				if(this._add_to) {
					this._add_to.parentNode.removeChild(this._add_to);
					this._add_to = false;

					// adjust page width
					u.as(page, "width", "auto");
				}
			}

		}

	}
}


Util.Objects["uniqueMatchList"] = new function() {
	this.init = function(div) {
		u.bug_force = true;

		u.bug("init uniqueMatchList")

		var i, node;

		div.list = u.qs("ul.items", div);
		if(div.list) {

			div.nodes = u.qsa("li.item", div.list);

			div.csrf_token = div.getAttribute("data-csrf-token");
			div.useragent_delete = div.getAttribute("data-useragent-delete");
			div.useragent_details = div.getAttribute("data-useragent-details");
			div.useragent_identify = div.getAttribute("data-useragent-identify");
			div.useragent_add = div.getAttribute("data-useragent-add");
			div.device_clone = div.getAttribute("data-device-clone");
			div.device_list = div.getAttribute("data-device-list");

			document.body.unidentified_div = div;


			// add select all option
			div.bn_all = u.ie(div.list, "li", {"class":"all", "html":"Select all"});
			div.bn_all._checkbox = u.ie(div.bn_all, "input", {"type":"checkbox"});
			div.bn_all.div = div;
			div.bn_all._checkbox.div = div;
			u.e.click(div.bn_all);
			div.bn_all.clicked = function() {
				var i, node;
				// figure out wether to select or deselect (if one is selected, de-select all)
				var inputs = u.qsa("li:not(.all) input:checked", this.div.list);

				for(i = 0; node = div.nodes[i]; i++) {
					if(inputs.length) {
						node._checkbox.checked = false;
					}
					else if(!node._hidden) {
						node._checkbox.checked = true;
					}
				}

				this.div.toggleAddToOption();
			}


			// add checkboxes to all rows
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
					}
					else {
						this.checked = true;
						document.body._multiselection = true;
					}

					document.body.onmouseup = function() {
						this.onmouseup = null;
						this._multiselection = false;
						this._multideselection = false;

						// show or no-show add option
						this.unidentified_div.toggleAddToOption();
					}

	//				this.node.div.toggleAddToOption();
				}
				node._checkbox.onmouseover = function() {
					if(document.body._multiselection) {
						this.checked = true;
					}
					else if(document.body._multideselection) {
						this.checked = false;
					}
				}

				// enable node expansion
				u.e.click(node);
				node.clicked = function() {

					if(!this._ul) {
						this.response = function(response) {
							if(response.cms_status == "success") {

								// add useragent details
								this._ul = u.ae(this, "ul", {"class":"info"});
								u.ae(this._ul, "li", {"class":"visits", "html":response.cms_object.length})
								u.ae(this._ul, "li", {"class":"identified_as", "html":response.cms_object[0].identified_as_device})

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
						var uls = u.qsa("ul", this);
						var i, ul;
						for(i = 0; ul = uls[i]; i++) {
							this.removeChild(ul);
						}
						this._ul = false;
					}
				}
			}


			// add filter to list
			if(u.hc(div, "filters")) {
			
				div._filter = u.ie(div, "div", {"class":"filter"});

				// index list, to speed up filtering process
				var i, node;
				for(i = 0; node = div.nodes[i]; i++) {
					node._c = node.textContent.toLowerCase();
				}

				// insert tags filter
				div._filter._field = u.ae(div._filter, "div", {"class":"field"});
				u.ae(div._filter._field, "label", {"html":"Filter"});

				div._filter._input = u.ae(div._filter._field, "input", {"class":"filter ignoreinput"});
				div._filter._input._div = div;

				div._filter._input.onkeydown = function() {
	//				u.bug("reset timer")
					u.t.resetTimer(this._div.t_filter);
				}
				div._filter._input.onkeyup = function() {
	//				u.bug("set timer")
					this._div.t_filter = u.t.setTimer(this._div, this._div.filter, 1500);
					u.ac(this._div._filter, "filtering");
				}
				div.filter = function() {

					var i, node;
					if(this._current_filter != this._filter._input.value.toLowerCase()) {
	//					u.bug("filter by:" + this._filter._input.value)

						this._current_filter = this._filter._input.value.toLowerCase();
						for(i = 0; node = this.nodes[i]; i++) {

							if(node._c.match(this._current_filter)) {
								node._hidden = false;
								u.as(node, "display", "block", false);
							}
							else {
								node._hidden = true;
								u.as(node, "display", "none", false);
								node._checkbox.checked = false;
							}
						}
					}

					u.rc(this._filter, "filtering");
				}
			}


			// add option to options list - if it does not already exist
			div.addOption = function(option) {

				u.xInObject(option, {"objects":true});
				// check options index for current option
				if(this._delete_uas.identified_options.indexOf(option.name) == -1) {
					this._delete_uas.identified_options.push(option.name);
					var li_option = u.ae(this._delete_uas._list, "li", {"html":option.name});
					li_option.details = option;
					li_option.div = this;
				}
			}

			// show or hide "Delete useragents" options, depending on whether useragents are selected or not
			div.toggleAddToOption = function() {
				var inputs = u.qsa("li:not(.all) input:checked", this.list);

				// selected items in list?
				if(inputs.length) {

					// does deleteUnique panel exist
					if(!this._delete_uas) {

						// Append add to layer to body
						this._delete_uas = u.ae(document.body, "div", {"class":"deleteUnique"});

						// add basic elements
						u.ae(this._delete_uas, "h2", {"html":"Delete useragents"});
						var count_div = u.ae(this._delete_uas, "div", {"html":"Selected useragents:", "class":"counter"});
						this._delete_uas._count = u.ae(count_div, "span", {"class":"count"});


						// adjust page width
						u.as(page, "width", parseInt(u.gcs(page, "width")) - this._delete_uas.offsetWidth + "px");

						// add list for identified options
						this._delete_uas._list = u.ae(this._delete_uas, "ul", {"class":"options"});


						// add delete button
						this._delete = u.ae(this._delete_uas, "div", {"class":"delete", "html":"Delete selected"})

						this._delete.node = this;
						u.e.click(this._delete);
						this._delete.restore = function(event) {
							this.value = "Delete selected";
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

	//							u.bug("node.ua_id:" + this.node.ua_id);

								// loop through all selected useragents and delete
								var inputs = u.qsa("li:not(.all) input:checked", this.list);
								var i, ua;

								// selected items in list?
								if(inputs.length) {
									for(i = 0; ua = inputs[i]; i++) {
										if(ua.node.ua_id) {

											ua.node.response = function(response) {
												if(response.cms_status == "success") {
													this.parentNode.removeChild(this);
												}
												page.notify(response);
												this.div.toggleAddToOption();
											}
											u.request(ua.node, ua.node.div.useragent_delete+"/"+ua.node.ua_id, {"method":"post","params":"csrf-token=" + ua.node.div.csrf_token});

										}
									}
								}
							}
						}


					}

					// set counter
					this._delete_uas._count.innerHTML = inputs.length;
					// empty options list
					this._delete_uas._list.innerHTML = "";



					// options based on selected useragents
					this._delete_uas.identified_options = [];

					var i, ua, ua_id
					this.wait_for_uas = inputs.length;
					u.ac(this._delete_uas, "loading");

					for(i = 0; ua = inputs[i]; i++) {

	//					u.bug("loop through selected inputs");

						if(!ua.node._identified) {
	//						u.bug("not id'ed yet:" + u.nodeId(ua.node))

							// device identification response
							ua.node.response = function(response) {
								if(response.cms_status == "success") {

									if(response.cms_object) {
										this._identified = response.cms_object;
									}

								}
								// bad result - device not identified
								else {
									this._identified = {};
									this._identified.name = "Unknown";
								}

								// add new option to the options list
								this.div.addOption(this._identified);

								// check load status
								this.div.wait_for_uas--;
								if(!this.div.wait_for_uas) {
									u.rc(this.div._delete_uas, "loading");
								}

							}
							// request identification
							u.request(ua.node, ua.node.div.useragent_identify+"/"+ua.node.ua_id, {"method":"post","params":"csrf-token=" + ua.node.div.csrf_token});
						}
						// already identified
						else {
	//						u.bug("id'ed already:" + u.nodeId(ua.node))

							// add option
							this.addOption(ua.node._identified);

							// check load status
							this.wait_for_uas--;
							if(!this.wait_for_uas) {
								u.rc(this._delete_uas, "loading");
							}
						}
					}
				}
				// no selected useragents - remove "Add to" if it exists
				else {
					if(this._delete_uas) {
						this._delete_uas.parentNode.removeChild(this._delete_uas);
						this._delete_uas = false;

						// adjust page width
						u.as(page, "width", "auto");
					}
				}

			}
		}


	}
}




Util.Objects["editUseragents"] = new function() {
	this.init = function(div) {

		div.item_id = u.cv(div, "item_id");


		u.toggleHeader(div);


		div._form = u.qs("form", div);
		u.f.init(div._form);

		div._form.submitted = function(iN) {

			this.response = function(response) {
				if(response.cms_status == "success") {
					location.reload();
				}
				else {
					page.notify(response);
				}
			}
			u.request(this, this.action, {"method":"post", "params" : u.f.getParams(this)});
		}



		div._uas = u.qsa("li.useragent", div);
		div.csrf_token = div.getAttribute("data-csrf-token");
		div.useragent_delete = div.getAttribute("data-useragent-delete");

		if(div.useragent_delete) {
			var i, ua, bn_delete;
			for(i = 0; ua = div._uas[i]; i++) {
				ua.ua_id = u.cv(ua, "ua_id")
				bn_delete = u.ae(ua, "div", {"class":"delete"});
				bn_delete.ua = ua;
				bn_delete.div = div;

				ua._c = ua.textContent.toLowerCase();

				u.e.click(bn_delete);
				bn_delete.clicked = function(event) {
		
					this.response = function(response) {

						page.notify(response);

						if(response.cms_status == "success") {
							this.ua.parentNode.removeChild(this.ua);
						}
					}
					u.request(this, this.div.useragent_delete+"/"+this.ua.ua_id, {"method":"post", "params":"csrf-token=" + this.div.csrf_token});

				}
			}
		}

		div._ua_ul = u.qs("ul.useragents", div);
		div._filter = u.ae(div, "div", {"class":"filter"});
		div.insertBefore(div._filter, div._ua_ul);
		
		div._filter_form = u.f.addForm(div._filter, {"class":"labelstyle:inject"});
		div._filter_field = u.f.addField(div._filter_form, {"label":"Filter useragents"});
		div._filter_field.div = div;
		u.f.init(div._filter_form)
		u.bug("field:" + div._filter_field)

		div._filter_field._input.onkeydown = function() {
//				u.bug("reset timer")
			u.t.resetTimer(this.field.div.t_filter);
		}
		div._filter_field._input.onkeyup = function() {
//				u.bug("set timer")
			this.field.div.t_filter = u.t.setTimer(this.field.div, this.field.div.filter, 500);
			u.ac(this.field.div._filter, "filtering");
		}
		div.filter = function() {
			var i, node;
			if(this._current_filter != this._filter_field._input.value.toLowerCase()) {
//					u.bug("filter by:" + this._filter._input.value)

				this._current_filter = this._filter_field._input.value.toLowerCase();
				for(i = 0; node = this._uas[i]; i++) {

					if(node._c.match(this._current_filter)) {
						u.as(node, "display", "block", false);
					}
					else {
						u.as(node, "display", "none", false);
					}
				}
			}

			u.rc(this._filter, "filtering");
		}
	}
}


Util.Objects["editMarkers"] = new function() {
	this.init = function(div) {

		div.item_id = u.cv(div, "item_id");

		u.toggleHeader(div);


		div._form = u.qs("form", div);
		div._form.div = div;
		u.f.init(div._form);

		div._form.submitted = function(iN) {

			this.response = function(response) {
				if(response.cms_status == "success") {
					var li = u.ae(this.div._markers_ul, "li", {"html":response.cms_object.marker, "class":"marker marker_id:"+response.cms_object.id});
					li.div = this.div;
					this.fields["marker"].val("");
					this.fields["marker"].focus();
					this.fields["marker"].blur();
					this.div.enableDeletion(li);

				}
				else {
					page.notify(response);
				}
			}
			u.request(this, this.action, {"method":"post", "params" : u.f.getParams(this)});
		}

		div._markers = u.qsa("li.marker", div);
		div._markers_ul = u.qs("ul.markers", div);

		div.csrf_token = div.getAttribute("data-csrf-token");
		div.url_marker_delete = div.getAttribute("data-marker-delete");

		div.enableDeletion = function(node) {
		
			node.marker_id = u.cv(node, "marker_id")
			bn_delete = u.ae(node, "div", {"class":"delete"});
			bn_delete.node = node;
			bn_delete.div = node.div;

			u.e.click(bn_delete);
			bn_delete.clicked = function(event) {
	
				this.response = function(response) {

					page.notify(response);

					if(response.cms_status == "success") {
						this.node.parentNode.removeChild(this.node);
					}
				}
				u.request(this, this.div.url_marker_delete+"/"+this.node.marker_id, {"method":"post", "params":"csrf-token=" + this.div.csrf_token});

			}
		}

		if(div.url_marker_delete) {
			var i, marker, bn_delete;
			for(i = 0; marker = div._markers[i]; i++) {
				marker.div = div;
				div.enableDeletion(marker);
			}
		}

	}
}


Util.Objects["editExceptions"] = new function() {
	this.init = function(div) {

		div.item_id = u.cv(div, "item_id");


		u.toggleHeader(div);


		div._form = u.qs("form", div);
		div._form.div = div;
		u.f.init(div._form);

		div._form.submitted = function(iN) {

			this.response = function(response) {
				if(response.cms_status == "success") {
					var li = u.ae(this.div._exceptions_ul, "li", {"html":response.cms_object.exception, "class":"exception exception_id:"+response.cms_object.id});
					li.div = this.div;
					this.fields["exception"].val("");
					this.fields["exception"].focus();
					this.fields["exception"].blur();
					this.div.enableDeletion(li);
				}
				else {
					page.notify(response);
				}
			}
			u.request(this, this.action, {"method":"post", "params" : u.f.getParams(this)});
		}

		div._exceptions = u.qsa("li.exception", div);
		div._exceptions_ul = u.qs("ul.exceptions", div);

		div.csrf_token = div.getAttribute("data-csrf-token");
		div.url_exception_delete = div.getAttribute("data-exception-delete");


		div.enableDeletion = function(node) {

			node.exception_id = u.cv(node, "exception_id")
			bn_delete = u.ae(node, "div", {"class":"delete"});
			bn_delete.node = node;
			bn_delete.div = node.div;

			u.e.click(bn_delete);
			bn_delete.clicked = function(event) {
	
				this.response = function(response) {

					page.notify(response);

					if(response.cms_status == "success") {
						this.node.parentNode.removeChild(this.node);
					}
				}
				u.request(this, this.div.url_exception_delete+"/"+this.node.exception_id, {"method":"post", "params":"csrf-token=" + this.div.csrf_token});
			}
		}

		if(div.url_exception_delete) {
			var i, exception, bn_delete;
			for(i = 0; exception = div._exceptions[i]; i++) {
				exception.div = div;
				div.enableDeletion(exception);
			}
		}



	}
}



Util.Objects["testMarkers"] = new function() {
	this.init = function(div) {
		u.bug("init testMarkers")

		u.ae(div, "h2", {"html":"Test device markers"});


		u.toggleHeader(div);



		div.item_id = u.cv(div, "item_id");

		div.csrf_token = div.getAttribute("data-csrf-token");

		div.url_device_test = div.getAttribute("data-device-test");




		div.test_form = u.f.addForm(div, {"class":"labelstyle:inject"});
		div.test_form.div = div;

		div.bn_test = u.f.addAction(div.test_form, {"value":"Test markers", "class":"button primary"});
		div.bn_test.div = div;


		u.f.init(div.test_form);




		// perform search
		div.test_form.submitted = function() {

			u.bug("perform test")

			// // only do search with valid search string
			// if(this.search_field._input.val() && this.search_field._input.val() != this.current_search_term) {
			//
				// get search response
				this.response = function(response) {

					if(response.cms_status == "success") {
						var not_matched = response.cms_object[0];
						var bad_matched = response.cms_object[1];

						var i, node, n_node;


						// add list for not matched results
						if(not_matched) {
							this.div.not_matched_header = u.ae(this.div, "h3", {"class":"not", "html":"The markers did NOT match these useragents"});
							this.div.not_matched_result = u.ae(this.div, "ul", {"class":"results not"});

							for(i = 0; node = not_matched[i]; i++) {

								n_node = u.ae(this.div.not_matched_result, "li");
								u.ae(n_node, "h4", {"html":node})

							}
						}

						// add list for badly matched results
						if(bad_matched) {
							this.div.bad_matched_header = u.ae(this.div, "h3", {"class":"bad", "html":"The markers also matched these devices"});
							this.div.bad_matched_result = u.ae(this.div, "ul", {"class":"results bad"});

							for(x in bad_matched) {

								node = u.ae(this.div.bad_matched_result, "li", {"class":"device_id:"+x});
								node._device = u.ae(node, "h4", {"html":bad_matched[x].name})
								node._device.node = node;
								node.ua_list = u.ae(node, "ul", {"class":"useragents"});

								for(y in bad_matched[x].useragents) {

									u.ae(node.ua_list, "li", {"html":bad_matched[x].useragents[y]});

								}

								u.e.click(node._device);
								node._device.clicked = function() {
									if(!this.node._devices_open) {
										this.node._devices_open = true;
										u.as(this.node.ua_list, "height", "auto");
									}
									else {
										this.node._devices_open = false;
										u.as(this.node.ua_list, "height", 0);
									}
									u.bug("show useragents");
								}
							}
						}




					}

					u.rc(this.div, "loading");
				}

				u.ac(this.div, "loading");


				// empty result list
				if(this.div.not_matched_header) {
					this.div.not_matched_header.parentNode.removeChild(this.div.not_matched_header);
					this.div.not_matched_result.parentNode.removeChild(this.div.not_matched_result);
				}
				if(this.div.bad_matched_header) {
					this.div.bad_matched_header.parentNode.removeChild(this.div.bad_matched_header);
					this.div.bad_matched_result.parentNode.removeChild(this.div.bad_matched_result);
				}


				// perform search
				u.request(this, this.div.url_device_test+"/"+this.div.item_id, {"params":"csrf-token="+this.div.csrf_token, "method":"post"})
			// }
		}

	}
}



Util.Objects["mergeDevices"] = new function() {
	this.init = function(div) {
		u.bug("init mergeDevices")


		div.item_id = u.cv(div, "item_id");

		u.ae(div, "h2", {"html":"Merge with device"});
		div.search_form = u.f.addForm(div, {"class":"labelstyle:inject"});
		var fieldset = u.f.addFieldset(div.search_form);
		div.search_field = u.f.addField(fieldset, {"name":"search", "label":"Search for device"});
		div.search_field.div = div;
		u.f.init(div.search_form);

		// add list for search results
		div.search_result = u.ae(div, "ul", {"class":"results"});



		u.toggleHeader(div);




		div.csrf_token = div.getAttribute("data-csrf-token");

		div.url_device_list = div.getAttribute("data-device-list");
		div.url_device_edit = div.getAttribute("data-device-edit");
		div.url_device_merge = div.getAttribute("data-device-merge");






		div.search_field._input.onkeyup = function() {
			u.t.resetTimer(this.field.div.t_search);
			this.field.div.t_search = u.t.setTimer(this.field.div, this.field.div.search, 1000);
		}
		div.search_field._input.onkeydown = function() {
			u.t.resetTimer(this.field.div.t_search);
		}

		// perform search
		div.search = function() {


			// only do search with valid search string
			if(this.search_field._input.val() && this.search_field._input.val() != this.current_search_term) {

				// get search response
				this.response = function(response) {

					// get items from result
					var items = u.qsa(".all_items li.item", response);
					if(items.length) {

						var i, node;
						for(i = 0; node = items[i]; i++) {

							if(u.cv(node, "item_id") != this.item_id) {

								node = this.search_result.appendChild(node);
								node.div = this;
								node.device_id = u.cv(node, "item_id");

								u.e.click(node);
								node.clicked = function() {

									if(!this.bn_merge) {

										this.bn_merge = u.ie(this, "div", {"class":"mergewith", "html":"Merge"});
										this.bn_merge.node = this;

										u.e.click(this.bn_merge);
										this.bn_merge.clicked = function() {

											this.response = function(response) {
												if(response.cms_status == "success") {
													location.href = this.node.div.url_device_edit+"/"+this.node.device_id;
												}
												else {
													page.notify(response);
												}
											}
											u.request(this, this.node.div.url_device_merge+"/"+this.node.div.item_id+"/"+this.node.device_id, {"method":"post", "params":"csrf-token="+this.node.div.csrf_token});

										}

									}
									else {
										this.removeChild(this.bn_merge);
										this.bn_merge = false;
									}

								}
							}
						}
					}

					u.rc(this, "loading");
				}

				u.ac(this, "loading");


				this.current_search_term = this.search_field._input.val();
				// empty result list
				this.search_result.innerHTML = "";


				// perform search
				u.request(this, this.url_device_list, {"params":"search=ajax&search_string="+this.current_search_term, "method":"post"})
			}
		}

	}
}



Util.Objects["mergeDevicesList"] = new function() {
	this.init = function(div) {
		u.bug("init mergeDevicesList")


		div.item_id = u.cv(div, "item_id");

		div.li = div.parentNode;

		u.ae(div, "h4", {"html":"Merge with device"});
		div.search_form = u.f.addForm(div, {"class":"labelstyle:inject"});
		var fieldset = u.f.addFieldset(div.search_form);
		div.search_field = u.f.addField(fieldset, {"name":"search", "label":"Search for device"});
		div.search_field.div = div;
		u.f.init(div.search_form);

		// add list for search results
		div.search_result = u.ae(div, "ul", {"class":"results"});


		// add collapsable header
		div._header = u.qs("h4", div);
		div._header.div = div;
		u.e.click(div._header);
		div._header.clicked = function() {
			if(this.div.is_closed) {
				u.as(this.div, "height", "auto");
				this.div.is_closed = false;
			}
			else {
				u.as(this.div, "height", this.offsetHeight+"px");
				this.div.is_closed = true;
			}
		}
		div._header.clicked();



		div.csrf_token = div.getAttribute("data-csrf-token");

		div.url_device_list = div.getAttribute("data-device-list");
		div.url_device_edit = div.getAttribute("data-device-edit");
		div.url_device_merge = div.getAttribute("data-device-merge");


		div.search_field._input.onkeyup = function() {
			u.t.resetTimer(this.field.div.t_search);
			this.field.div.t_search = u.t.setTimer(this.field.div, this.field.div.search, 1000);
		}
		div.search_field._input.onkeydown = function() {
			u.t.resetTimer(this.field.div.t_search);
		}

		// perform search
		div.search = function() {


			// only do search with valid search string
			if(this.search_field._input.val() && this.search_field._input.val() != this.current_search_term) {

				// get search response
				this.response = function(response) {

					// get items from result
					var items = u.qsa(".all_items li.item", response);
					if(items.length) {

						var i, device;
						for(i = 0; device = items[i]; i++) {

							if(u.cv(device, "item_id") != this.li._item_id) {
								device = this.search_result.appendChild(device);
								device.div = this;
								device.device_id = u.cv(device, "item_id");

								u.e.click(device);
								device.clicked = function() {

									if(!this.bn_merge) {

										this.bn_merge = u.ie(this, "div", {"class":"mergewith", "html":"Merge"});
										this.bn_merge.device = this;

										u.e.click(this.bn_merge);
										this.bn_merge.clicked = function() {

											this.response = function(response) {
												if(response.cms_status == "success") {
													this.device.div.li.parentNode.removeChild(this.device.div.li);
	//												location.href = this.device.div.url_device_edit+"/"+this.device.device_id;
												}
												else {
													page.notify(response);
												}
											}
											u.request(this, this.device.div.url_device_merge+"/"+this.device.div.item_id+"/"+this.device.device_id, {"method":"post", "params":"csrf-token="+this.device.div.csrf_token});

										}

									}
									else {
										this.removeChild(this.bn_merge);
										this.bn_merge = false;
									}

								}
							}
						}
					}

					u.rc(this, "loading");
				}

				u.ac(this, "loading");


				this.current_search_term = this.search_field._input.val();
				// empty result list
				this.search_result.innerHTML = "";


				// perform search
				u.request(this, this.url_device_list, {"params":"search=ajax&search_string="+this.current_search_term, "method":"post"})
			}
		}

	}
}