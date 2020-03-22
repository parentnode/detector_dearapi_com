Util.Modules["deleteLostUseragents"] = new function() {
	this.init = function(div) {
		u.bug("init deleteLostUseragents")

		div.csrf_token = div.getAttribute("data-csrf-token");
		div.url_useragent_delete_lost = div.getAttribute("data-useragent-delete-lost");

		var scene = u.qs(".scene");

		var actions = u.ae(div, "ul", {"class":"actions"});
		var li = u.ae(actions, "li", {"class":"delete"});
		var link = u.ae(li, "a", {"class":"button primary", "html":"Delete all"});
		link.div = div;

		scene.insertBefore(actions, div);

		u.e.click(link);
		link.clicked = function() {

			this.div.response = function(response) {

				location.reload();

			}
			u.request(this.div, this.div.url_useragent_delete_lost, {"method":"post", "data":"csrf-token="+this.div.csrf_token})
		}

	}
}


Util.Modules["deleteLostDevices"] = new function() {
	this.init = function(div) {
		u.bug("init deleteLostDevices")

		div.csrf_token = div.getAttribute("data-csrf-token");
		div.url_devices_delete_lost = div.getAttribute("data-devices-delete-lost");

		var scene = u.qs(".scene");

		var actions = u.ae(div, "ul", {"class":"actions"});
		var li = u.ae(actions, "li", {"class":"delete"});
		var link = u.ae(li, "a", {"class":"button primary", "html":"Delete all"});
		link.div = div;

		scene.insertBefore(actions, div);

		u.e.click(link);
		link.clicked = function() {

			this.div.response = function(response) {

				location.reload();

			}
			u.request(this.div, this.div.url_devices_delete_lost, {"method":"post", "data":"csrf-token="+this.div.csrf_token})
		}

	}
}




// OLD MAINTENANCE

Util.Modules["uniqueMatchList"] = new function() {
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
						u.request(this, this.div.useragent_details+"/"+this.ua_id, {"method":"post","data":"csrf-token=" + this.div.csrf_token});
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
				div._filter.field = u.ae(div._filter, "div", {"class":"field"});
				u.ae(div._filter.field, "label", {"html":"Filter"});

				div._filter.input = u.ae(div._filter.field, "input", {"class":"filter ignoreinput"});
				div._filter.input._div = div;

				div._filter.input.onkeydown = function() {
	//				u.bug("reset timer")
					u.t.resetTimer(this._div.t_filter);
				}
				div._filter.input.onkeyup = function() {
	//				u.bug("set timer")
					this._div.t_filter = u.t.setTimer(this._div, this._div.filter, 1500);
					u.ac(this._div._filter, "filtering");
				}
				div.filter = function() {

					var i, node;
					if(this._current_filter != this._filter.input.value.toLowerCase()) {
	//					u.bug("filter by:" + this._filter.input.value)

						this._current_filter = this._filter.input.value.toLowerCase();
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
											u.request(ua.node, ua.node.div.useragent_delete+"/"+ua.node.ua_id, {"method":"post","data":"csrf-token=" + ua.node.div.csrf_token});

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
							u.request(ua.node, ua.node.div.useragent_identify+"/"+ua.node.ua_id, {"method":"post","data":"csrf-token=" + ua.node.div.csrf_token});
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

