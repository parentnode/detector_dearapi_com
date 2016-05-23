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


