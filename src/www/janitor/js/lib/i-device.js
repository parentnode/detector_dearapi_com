
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
		div.url_marker_update = div.getAttribute("data-marker-update");

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

		div.enableEditing = function(node) {

			node.marker_id = u.cv(node, "marker_id")
			bn_edit = u.qs("span", node);
			bn_edit.node = node;
			bn_edit.div = node.div;

			bn_edit.changed = function(event) {
//				u.t.resetTimer(this.t_save);;
//				this.t_save = u.t.setTimer(this, this.save, 1000);

				event.stopPropagation();
			}

			// field lost focus
			bn_edit.blurred = function(event) {
				u.rc(this, "editable");
				this.contentEditable = false;

				this.save();
			}

			u.e.click(bn_edit);
			bn_edit.clicked = function(event) {

				u.ac(this, "editable");
				this.contentEditable = true;

				u.e.addEvent(this, "keydown", this.changed)
				u.e.addEvent(this, "blur", this.blurred)

				this.focus();

			}

			bn_edit.save = function(event) {

				this.response = function(response) {

					page.notify(response);

					if(response.cms_status != "success") {
						u.ac(this, "error");
					}
					else {
						u.rc(this, "error");
					}
				}
				u.request(this, this.div.url_marker_update+"/"+this.node.marker_id, {"method":"post", "params":"csrf-token="+this.div.csrf_token+"&marker="+encodeURIComponent(this.innerHTML)});
			}

		}

		var i, marker, bn_delete;
		for(i = 0; marker = div._markers[i]; i++) {
			marker.div = div;

			if(div.url_marker_delete) {
				div.enableDeletion(marker);
			}
			if(div.url_marker_update) {
				div.enableEditing(marker);
			}

			div.enableDeletion(marker);
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
		div.url_exception_update = div.getAttribute("data-exception-update");

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



		div.enableEditing = function(node) {

			node.exception_id = u.cv(node, "exception_id")
			bn_edit = u.qs("span", node);
			bn_edit.node = node;
			bn_edit.div = node.div;

			bn_edit.changed = function(event) {
//				u.t.resetTimer(this.t_save);;
//				this.t_save = u.t.setTimer(this, this.save, 1000);

				event.stopPropagation();
			}

			// field lost focus
			bn_edit.blurred = function(event) {
				u.rc(this, "editable");
				this.contentEditable = false;

				this.save();
			}

			u.e.click(bn_edit);
			bn_edit.clicked = function(event) {

				u.ac(this, "editable");
				this.contentEditable = true;

				u.e.addEvent(this, "keydown", this.changed)
				u.e.addEvent(this, "blur", this.blurred)

				this.focus();

			}

			bn_edit.save = function(event) {

				this.response = function(response) {

					page.notify(response);

					if(response.cms_status != "success") {
						u.ac(this, "error");
					}
					else {
						u.rc(this, "error");
					}
				}
				u.request(this, this.div.url_exception_update+"/"+this.node.exception_id, {"method":"post", "params":"csrf-token="+this.div.csrf_token+"&exception="+encodeURIComponent(this.innerHTML)});
			}

		}


		var i, exception, bn_delete;
		for(i = 0; exception = div._exceptions[i]; i++) {
			exception.div = div;

			if(div.url_exception_delete) {
				div.enableDeletion(exception);
			}
			if(div.url_exception_update) {
				div.enableEditing(exception);
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

//						u.bug("not_matched:" + not_matched.length)

						// add list for not matched results
						if(not_matched.length) {
							this.div.not_matched_header = u.ae(this.div, "h3", {"class":"not", "html":"The markers did NOT match these useragents ("+not_matched.length+")"});
							this.div.not_matched_result = u.ae(this.div, "ul", {"class":"results not"});

							for(i = 0; node = not_matched[i]; i++) {

								n_node = u.ae(this.div.not_matched_result, "li", {"class":"result"});
								n_node._device = u.ae(n_node, "h4", {"html":(node.useragent ? node.useragent : "--BLANK--")})

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

							// add option to delete all unmatched 
							// (needed when shifting a lot of useragents from one device to another)
							this.div.unmatched_actions = u.ae(this.div, "ul", {"class":"actions"});
							this.div.unmatched_delete_all = u.ae(this.div.unmatched_actions, "li", {"class":"delete", "html":"Delete ALL unmatched useragents"});
							this.div.unmatched_delete_all.div = this.div;

							u.e.click(this.div.unmatched_delete_all);
							this.div.unmatched_delete_all.reset = function() {
								u.t.resetTimer(this.t_confirm);

								this.innerHTML = this.org_text;
								this._confirm = false;
							}
							this.div.unmatched_delete_all.clicked = function() {
								u.t.resetTimer(this.t_confirm);

								if(this._confirm) {
									u.bug("delete all unmatched")

									this.deleteAll();

								}
								else {

									this.org_text = this.innerHTML;
									this.innerHTML = "Confirm deleting all unmatched?";
									this._confirm = true;
									this.t_confirm = u.t.setTimer(this, this.reset, 2000);
								}
							}

							this.div.unmatched_delete_all.deleteAll = function(event) {

								u.as(this, "display", "none");
								var nodes = u.qsa("li.result", this.div.not_matched_result);
								var i, node;
								for(i = 0; node = nodes[i]; i++) {
									u.bug("delete node:" + node._device.innerHTML)
									node._delete_ua.clicked();
								}
							}
							

						}
						else {

							this.div.not_matched_header = u.ae(this.div, "h3", {"html":"NO unmatched useragents"});
							
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
						else {

							this.div.bad_matched_header = u.ae(this.div, "h3", {"html":"NO cross device matches"});

						}




					}

					u.rc(this.div, "loading");
				}

				u.ac(this.div, "loading");


				// empty result list
				if(this.div.not_matched_header) {
					this.div.not_matched_header.parentNode.removeChild(this.div.not_matched_header);
				}
				if(this.div.not_matched_result) {
					this.div.not_matched_result.parentNode.removeChild(this.div.not_matched_result);
				}

				if(this.div.bad_matched_header) {
					this.div.bad_matched_header.parentNode.removeChild(this.div.bad_matched_header);
				}
				if(this.div.bad_matched_result) {
					this.div.bad_matched_result.parentNode.removeChild(this.div.bad_matched_result);
				}
				if(this.div.bad_match_actions) {
					this.div.bad_match_actions.parentNode.removeChild(this.div.bad_match_actions);
				}


				// perform search
				u.request(this, this.div.url_device_test+"/"+this.div.item_id, {"params":"csrf-token="+this.div.csrf_token, "method":"post"})
			// }
		}

	}
}

