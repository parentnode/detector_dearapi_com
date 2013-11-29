// news list image 
Util.Objects["searchDevice"] = new function() {
	this.init = function(form) {

		u.f.init(form);

	}
}



Util.Objects["deviceEdit"] = new function() {
	this.init = function(scene) {


		scene._item = u.qs("div.item", scene);
		scene.item_id = u.cv(scene._item, "item_id");


		scene._item_form = u.qs("form", scene._item);
		u.f.init(scene._item_form);


		scene._item_form.actions["cancel"].clicked = function(event) {
			location.href = this.url;
		}

		scene._item_form.submitted = function(iN) {

			this.response = function(response) {
				if(response.cms_status == "success") {
					location.href = this.actions["cancel"].url;
				}
				else {
					alert(response.cms_message[0]);
				}
			}
			u.request(this, this.action, {"method":"post", "params" : u.f.getParams(this)});

		}


		// TAGS
		// tags form
		scene._tags = u.qs("div.tags", scene);
		scene._tags_form = u.qs("form", scene._tags);
		u.f.init(scene._tags_form);

		// show all tags when tag input has focus
		scene._tags_form.fields["tags"].focused = function() {
			scene.enableTagging();
		}
		// hide all tags when tag input looses focus
		scene._tags_form.fields["tags"].updated = function() {
		 	scene._filterTags(this.val());
		}

		scene._tags_form.submitted = function(iN) {

			this.response = function(response) {
				if(response.cms_status == "success") {
					location.reload();
				}
				else {
					alert(response.cms_message[0]);
				}
			}
			u.request(this, this.action, {"method":"post", "params" : u.f.getParams(this)});

		}


		// tags list of existing tags
		scene._tags._list = u.qs("ul.tags", scene._tags);
		if(!scene._tags._list) {
			scene._tags._list = u.ae(scene._tags, "ul", {"class":"tags"});
		}

		// get all tags from server
		scene._tags.tagsResponse = function(response) {

			if(response.cms_status == "success" && response.cms_object) {
				this._alltags = response.cms_object;

				// add "add" button when tags are ready
				this._list._bn_add = u.ae(scene._tags._list, "li", {"class":"add","html":"+"});
				u.e.click(this._list._bn_add);
				this._list._bn_add.clicked = function() {
					scene.enableTagging();
				}

			}
			else {
				// TODO: no tags?
			}
		}
		// get tags
		u.request(scene._tags, "/admin/cms/tags", {"callback":"tagsResponse"});

		// enable tagging
		scene.enableTagging = function() {
			u.bug("enable tagging")

			if(!this._tags._taglist) {

				// reset tags filter
//				document.body._current_tags_filter = "";

				// change button
				this._tags._list._bn_add.innerHTML = "-";
				this._tags._list._bn_add.clicked = function() {
					scene.disableTagging();
				}
				u.ac(this._tags, "addTags");


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

					tag_node._taglist = this._tags._taglist;
					tag_node._id = this._tags._alltags[tag].id;
	// 				tag_node.node = node;


	// 
	 				u.e.click(tag_node);
	 				tag_node.clicked = function() {
	// 					u.bug("tag clicked:" + tag_node._context+":"+tag_node._value);

						// only do anything if in addTags mode
						if(u.hc(scene._tags, "addTags")) {

							// tag is in existing tags list
							// remove tag
							if(u.hc(this.parentNode, "tags")) {

								this.response = function(response) {
									if(response.cms_status == "success") {

										// TODO: remove from used tags
										u.ae(scene._tags._taglist, this);
	
										// Notify of event
										if(typeof(page.notify) == "function") {
											page.notify(response.cms_message);
										}
										else {
											alert(response.cms_message[0]);
										}
									}
								}
								u.request(this, "/admin/cms/tags/delete/"+scene.item_id+"/" + this._id);
							}
							// else add tag
							else {
	// 
								this.response = function(response) {
	// 								// TODO: add to used tags
	// //										u.ac(this, "selected");
									u.ae(scene._tags._list, this)
	// 
	// 								// Notify of event
									if(typeof(page.notify) == "function") {
										page.notify(response.cms_message);
									}
									else {
										alert(response.cms_message[0]);
									}
								}
								u.request(this, "/admin/cms/update/"+scene.item_id, {"method":"post", "params":"tags="+this._id});
							}
						}
					}

				}

			}

		}

		// disable tagging
		scene.disableTagging = function() {
//			u.bug("disable tagging")

			// change button
			this._tags._list._bn_add.innerHTML = "+";
			this._tags._list._bn_add.clicked = function() {
				scene.enableTagging();
			}
			u.rc(this._tags, "addTags");

			// remove extra tags list
			this._tags._taglist.parentNode.removeChild(this._tags._taglist);
			this._tags._taglist = false;
		}

		// filter tags - only show tags matching input value
		scene._filterTags = function(string) {
//			u.bug("filter tag");

			if(this._tags._taglist) {
				var tags = u.qsa(".tag", this._tags._taglist);
				var i, tag;
				for(i = 0; tag = tags[i]; i++) {

//					u.bug(tag.textContent)
					if(tag.textContent.toLowerCase().match(string.toLowerCase())) {
						u.as(tag, "display", "inline-block");
					}
					else {
						u.as(tag, "display", "none");
					}
				}
			}

		}


		// useragents
		scene._useragents = u.qs("div.useragents", scene);
		scene._useragents_form = u.qs("form", scene._useragents);
		u.f.init(scene._useragents_form);

		scene._useragents_form.submitted = function(iN) {

			this.response = function(response) {
				if(response.cms_status == "success") {
					location.reload();
				}
				else {
					alert(response.cms_message[0]);
				}
			}
			u.request(this, this.action, {"method":"post", "params" : u.f.getParams(this)});

		}

		scene._useragents._uas = u.qsa("li.useragent", scene._useragents);
		var i, ua, bn_delete;
		for(i = 0; ua = scene._useragents._uas[i]; i++) {
			bn_delete = u.ae(ua, "div", {"class":"delete"});
			bn_delete.ua = ua;

			u.e.click(bn_delete);
			bn_delete.clicked = function(event) {
				
				this.response = function(response) {

					if(response.cms_status == "success") {
						this.ua.parentNode.removeChild(this.ua);
					}
					else {
						// TODO: no tags?
					}
					
				}
				u.request(this, "/admin/cms/device/"+scene.item_id+"/deleteUseragent/"+u.cv(this.ua, "ua_id"));

			}
		}

	}
}
