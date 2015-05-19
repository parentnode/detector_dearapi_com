u.bug_force = true;


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





Util.Objects["generate"] = new function() {
	this.init = function(div) {

		var form = u.qs("form", div);
		u.f.init(form);
		
		form.submitted = function() {
			
			this.response = function(response) {
				page.notify(response);

				if(response.cms_status == "success") {
					var actions = u.qs(".actions", this);
					actions.parentNode.removeChild(actions);
					u.ae(this, "p", {"html":"Script created"});
				}
			}
			u.request(this, this.action, {"params":u.f.getParams(this), "method":"post"});
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
		div.url_device_edit = div.getAttribute("data-device-edit");
		div.url_device_merge = div.getAttribute("data-device-merge");
		div.url_useragent_delete = div.getAttribute("data-useragent-delete");




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

						var i, node, n_node, li, actions;


						// add list for not matched results
						if(not_matched) {
							this.div.not_matched_header = u.ae(this.div, "h3", {"class":"not", "html":"The markers did NOT match these useragents"});
							this.div.not_matched_result = u.ae(this.div, "ul", {"class":"results not"});

							for(i = 0; node = not_matched[i]; i++) {

								n_node = u.ae(this.div.not_matched_result, "li");
								n_node._device = u.ae(n_node, "h4", {"html":node.useragent})

								n_node.actions = u.ae(n_node, "ul", {"class":"actions"});
								n_node._delete_ua = u.ae(n_node.actions, "li", {"class":"delete", "html":"Delete"});
								n_node._delete_ua.url = this.div.url_useragent_delete+"/"+node.id;
								n_node._delete_ua.n_node = n_node;
								n_node._delete_ua.div = this.div;

								u.e.click(n_node._delete_ua);
								n_node._delete_ua.clicked = function(event) {
		
									this.response = function(response) {

										page.notify(response);

										if(response.cms_status == "success") {
											this.n_node.parentNode.removeChild(this.n_node);
										}
									}
									u.request(this, this.url, {"method":"post", "params":"csrf-token=" + this.div.csrf_token});

								}


								n_node._device.n_node = n_node;
								u.e.click(n_node._device);
								n_node._device.clicked = function() {
									if(!this.n_node._open) {
										this.n_node._open = true;
										u.as(this.n_node.actions, "height", "auto");
									}
									else {
										this.n_node._open = false;
										u.as(this.n_node.actions, "height", 0);
									}
								}


							}
						}

						// add list for badly matched results
						if(Object.keys(bad_matched).length) {
							this.div.bad_matched_header = u.ae(this.div, "h3", {"class":"bad", "html":"The markers also matched these devices"});
							this.div.bad_matched_result = u.ae(this.div, "ul", {"class":"results bad"});

							for(x in bad_matched) {

								node = u.ae(this.div.bad_matched_result, "li", {"class":"device device_id:"+x+(bad_matched[x].marked ? " marked" : "")});

								// does matched device already have markers
								node._marked = bad_matched[x].marked;

								node._device = u.ae(node, "h4", {"html":bad_matched[x].name});
								node.actions = u.ae(node, "ul", {"class":"actions"});

								// add show link
								li = u.ae(node.actions, "li", {"class":"show"});
								node._device_link = u.ae(li, "a", {"href":this.div.url_device_edit+"/"+bad_matched[x].id, "html":"Show device", "target":"_blank"});
//								u.ce(node._device_link, {"type":"link"});

								// add merge link
								node._device_merge = u.ae(node.actions, "li", {"class":"merge", "html":"Merge"});
								node._device_merge.url = this.div.url_device_merge+"/"+bad_matched[x].id+"/"+this.div.item_id;
								node._device_merge.div = this.div;
								node._device_merge.node = node;

								u.e.click(node._device_merge);
								node._device_merge.reset = function() {
									u.t.resetTimer(this.t_confirm);

									this.innerHTML = this.org_text;
									this._confirm = false;
								}
								node._device_merge.clicked = function() {
									u.t.resetTimer(this.t_confirm);

									if(this._confirm) {
										u.bug("merge device")

										this.response = function(response) {
											page.notify(response);

											if(response.cms_status == "success") {
												this.node.parentNode.removeChild(this.node);
											}
										}
										u.request(this, this.url, {"method":"post", "params":"csrf-token="+this.div.csrf_token});

									}
									else {

										this.org_text = this.innerHTML;
										this.innerHTML = "Confirm";
										this._confirm = true;
										this.t_confirm = u.t.setTimer(this, this.reset, 2000);
									}
								}


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
										u.as(this.node.actions, "height", "auto");
									}
									else {
										this.node._devices_open = false;
										u.as(this.node.ua_list, "height", 0);
										u.as(this.node.actions, "height", 0);
									}
									u.bug("show useragents");
								}
							}

							this.div.bad_match_actions = u.ae(this.div, "ul", {"class":"actions"});
							this.div.bad_match_merge = u.ae(this.div.bad_match_actions, "li", {"class":"merge", "html":"Merge all NON-MARKED devices"});
							this.div.bad_match_merge.div = this.div;

							u.e.click(this.div.bad_match_merge);
							this.div.bad_match_merge.reset = function() {
								u.t.resetTimer(this.t_confirm);

								this.innerHTML = this.org_text;
								this._confirm = false;
							}
							this.div.bad_match_merge.clicked = function() {
								u.t.resetTimer(this.t_confirm);

								if(this._confirm) {

									var results = u.qsa("li.device", this.div.bad_matched_result);
									var i, node;
									for(i = 0; node = results[i]; i++) {
										if(!node._marked) {
											node._device_merge._confirm = true;
											node._device_merge.clicked();
										}
									}

								}
								else {

									this.org_text = this.innerHTML;
									this.innerHTML = "Are you absolutely sure??";
									this._confirm = true;
									this.t_confirm = u.t.setTimer(this, this.reset, 2000);
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
					this.div.bad_match_actions.parentNode.removeChild(this.div.bad_match_actions);
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


