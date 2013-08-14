<?php
/**
* @package framework
*/
include_once("class/items/type.news.core.class.php");
/**
* typeBlog
*
* inline TEMPLATES
*
* itemtype.list -> itemtype class
* itemtype.view -> itemtype class
* itemtype.edit -> itemtype class
*/
class TypeNews extends TypeNewsCore {
	
	/*
	public $limits;
	public $itemtype;
	public $item_name;
	public $items_name;
	*/

	/**
	* Default settings
	*/
	function __construct() {
		parent::__construct();

		$this->addTranslation(__FILE__);

		$this->validator = new Validator($this);

		$this->varnames["name"] = $this->translate("Title");
//		$this->validator->rule("name", "txt");

		$this->varnames["teaser"] = $this->translate("News teaser");
//		$this->validator->rule("teaser", "txt");

		$this->varnames["text"] = $this->translate("News");
		$this->validator->rule("text", "txt");

		$this->varnames["timestamp"] = $this->translate("Timestamp");
		$this->validator->rule("timestamp", "timestamp");

//		$this->varnames["release"] = $this->translate("Release");
//		$this->validator->rule("release", "timestamp");

//		$this->varnames["expire"] = $this->translate("Expire");
//		$this->validator->rule("expire", "timestamp");


		$this->varnames["language_id"] = $this->translate("Language");
		$this->validator->rule("language_id", "txt");

		$this->vars = getVars($this->varnames);

//		$this->db = UT_ITE_BLO;

//		$this->limits["list"] = 30;
//		$this->limits["preview"] = 12;

//		$this->itemtype = "blog";
		
//		$this->type_name = $this->translate("Blog");
//		$this->types_name = $this->translate("Blogs");

	}


	/**
	* Get selected item(s)
	* Loops through $item->item and adds itemtype values
	*
	* @param object $item Item object
	* @return $item
	*/
	function getItem($item) {
		$query = new Query();

		if($item->item()) {
			foreach($item->item["id"] as $key => $value) {
				$query->sql("SELECT name, teaser, text, UNIX_TIMESTAMP(timestamp) as timestamp, language_id FROM ".$this->db." WHERE item_id = ".$value);
				$item->item["name"][$key] = $query->getQueryResult(0, "name");
				$item->item["teaser"][$key] = $query->getQueryResult(0, "teaser");
				$item->item["text"][$key] = str_replace('&quot;', '"', $query->getQueryResult(0, "text"));
				$item->item["timestamp"][$key] = date("d-m-Y H:i", $query->getQueryResult(0, "timestamp"));
//				$item->item["release"][$key] = date("d-m-Y H:i", $query->getQueryResult(0, "release"));
//				$item->item["expire"][$key] = date("d-m-Y H:i", $query->getQueryResult(0, "expire"));
				$item->item["language_id"][$key] = $query->getQueryResult(0, "language_id");
			}
		}
		return $item;
	}

	function mixedList($item, $status) {

		$_ = '';

		if($item->item()) {
			$item = $this->getItem($item);
			$item->getTagList();
			
			$_ .= '<li class="id:'.$item->item["id"][0].($status ? ' status:'.$status : '').' news">';
			$_ .= '<span class="timestamp">'.$item->item["timestamp"][0].'</span>';
			$_ .= HTML::head($item->item["name"][0], 2);
			$_ .= HTML::p($item->item["text"][0]);
			$_ .= '<span class="tags">'.$item->item["tag_list"][0].'</span>';
			$_ .= '</li>';

		}
		return $_;
	}

	/**
	* List items, compiles the info for this itemtype in list view and returns HTML
	*
	* @param String $list_type Optional listtype (CSS specified types)
	* @return String HTML
	*/
	function listItems($link=false, $validate=false, $classname=false) {
		global $page;
		global $HTML;

		$item = $page->getObject("Item");
		$status = "";

		$_ = '';
		$_ .= $HTML->head($this->types_name);

		$_ .= $HTML->p("Add a new ". $item->itemtype, "hint status:link:/items/items_new.php?itemtype_id=".$item->itemtype_id);

		if($item->item()) {

			$item = $this->getItem($item);
			$item->getTagList();

			$_ .= '<ul class="news'.($classname ? ' '.$classname : '').'">';
			if($validate && Session::getLogin()->validatePage($validate)) {
				$status = $link;
			}
			foreach($item->item["id"] as $key => $item_id) {

				$_ .= '<li class="id:'.$item->item["id"][$key].($status ? ' status:'.$status : '').' blog">';
				$_ .= '<span class="timestamp">'.$item->item["timestamp"][$key].'</span>';
				$_ .= HTML::head($item->item["name"][$key], 2);
				$_ .= HTML::p($item->item["text"][$key]);
				$_ .= '<span class="tags">'.$item->item["tag_list"][$key].'</span>';
				$_ .= '</li>';

			}
			$_ .= '</ul>';
//			$_ .= Generic::listItemsExtended($link, $validate, $item->item["id"], array($item->item["name"], $item->item["tag_list"], $item->item["status_text"]), array($this->types_name, "tags", $this->translate("Search")), array("max", "", "search"));


		}
		return $_;
	}

	/**
	* View item, compiles the info for this itemtype in item view and returns HTML
	*
	* @return String HTML
	*/
	function viewItem() {
		global $page;
		global $HTML;

		$item = $page->getObject("Item");
		$item = $this->getItem($item);

		$id = $item->item["id"][0];

		$_ = "";
		$_ .= $HTML->inputHidden("id", $id);
		$_ .= $HTML->inputHidden("page_status", "edit");

//		$_ .= $HTML->editInput($this->varnames["name"], "update", "name", $id, $item->item["name"][0], $this->translate("Click to edit"));

		$_ .= $HTML->block($this->varnames["name"], $item->item["name"][0]);
		$_ .= $HTML->block($this->varnames["teaser"], $item->item["teaser"][0]);
		$_ .= $HTML->block($this->varnames["text"], stringOr($item->item["text"][0], "-"));
		$_ .= '<div class="ci50">';
			$_ .= $HTML->block($this->varnames["timestamp"], stringOr($item->item["timestamp"][0], "-"));
		$_ .= '</div>';
		$_ .= '<div class="ci50">';
			$_ .= $HTML->block($this->varnames["language_id"], stringOr($item->item["language_id"][0], "-"));
//			$_ .= $HTML->p("Limit the period of this news item?", "info");
//			$_ .= $HTML->block($this->varnames["release"], stringOr($item->item["release"][0], "-"));
//			$_ .= $HTML->block($this->varnames["expire"], stringOr($item->item["expire"][0], "-"));
		$_ .= '</div>';

		$_ .= $HTML->separator();
		$_ .= $HTML->smartButton($this->translate("Edit"), "edit", "edit", "fright key:e");

		return $_;
	}

	/**
	* Edit item, compiles the info for this itemtype in item edit view and returns HTML
	*
	* @return String HTML
	*/
	function editItem() {
		global $page;
		global $HTML;

		$item = $page->getObject("Item");
		$item = $this->getItem($item);

		$_ = "";
		$_ .= $HTML->inputHidden("id", $item->item["id"][0]);
		$_ .= $HTML->inputHidden("page_status", "update");

		$_ .= $HTML->input($this->varnames["name"], "name", stringOr($this->vars["name"], $item->item["name"][0]));
		$_ .= $HTML->input($this->varnames["teaser"], "teaser", stringOr($this->vars["teaser"], $item->item["teaser"][0]));
		$_ .= $HTML->textarea($this->varnames["text"], "text", stringOr($this->vars["text"], $item->item["text"][0]));

		$_ .= '<div class="ci50">';
			$_ .= $HTML->input($this->varnames["timestamp"], "timestamp", stringOr($this->vars["timestamp"], $item->item["timestamp"][0]));
		$_ .= '</div>';
		$_ .= '<div class="ci50">';
			$_ .= $HTML->select($this->varnames["language_id"], "language_id", Page::getItems(UT_BAS_LAN), $item->item["language_id"][0]);
//			$_ .= $HTML->input($this->varnames["release"], "release", stringOr($this->vars["release"], $item->item["release"][0]));
//			$_ .= $HTML->input($this->varnames["expire"], "expire", stringOr($this->vars["expire"], $item->item["expire"][0]));
		$_ .= '</div>';
		$_ .= $HTML->smartButton($this->translate("Update"), "update", "update", "fright key:s");
//		$_ .= $HTML->smartButton($this->translate("Cancel"), "view", "view", "fleft");

		return $_;
	}

	/**
	* New item, compiles the info for this itemtype in item view and returns HTML
	*
	* @param String $list_type Optional listtype (CSS specified types)
	* @return String HTML
	*/
	function newItem() {
		global $page;
		global $HTML;

		$_ = "";
		$_ .= $HTML->head("New item");

		$_ .= $HTML->inputHidden("itemtype_id", $this->itemtype);

		$_ .= $HTML->input($this->varnames["name"], "name", stringOr($this->vars["name"]));
		$_ .= $HTML->input($this->varnames["teaser"], "teaser", stringOr($this->vars["teaser"]));
		$_ .= $HTML->textarea($this->varnames["text"], "text", stringOr($this->vars["text"]));

		$_ .= '<div class="ci50">';
			$_ .= $HTML->inputTimestamp($this->varnames["timestamp"], "timestamp", stringOr($this->vars["timestamp"], date("d-m-Y H:i", time())));
		$_ .= '</div>';


		$_ .= '<div class="ci50">';
			$_ .= $HTML->select($this->varnames["language_id"], "language_id", Page::getItems(UT_BAS_LAN), Session::getLanguageIso());
//			$_ .= $HTML->inputTimestamp($this->varnames["release"], "release", stringOr($this->vars["release"], date("d-m-Y H:i", time())));
//			$_ .= $HTML->inputTimestamp($this->varnames["expire"], "expire", $this->vars["expire"]);
		$_ .= '</div>';

		$_ .= $HTML->smartButton($this->translate("Save"), "save", "save", "fright key:s");

		return $_;
	}

	/**
	* Save new item, based on submitted values
	*
	* @return bool
	* @uses Message
	*/
	function saveItem($item_id) {

		if($this->validator->validateAll("expire")) {

			if($this->save($item_id, $this->vars)){
				messageHandler()->addStatusMessage($this->translate("News saved"));
				return $item_id;
			}
			else {
				messageHandler()->addErrorMessage($this->dbError());
				return false;
			}
		}
		else {
			messageHandler()->addErrorMessage($this->translate("Please complete missing information"));
			return false;
		}
	}


	/**
	* Update item, based on submitted values
	*
	* @return bool
	* @uses Message
	*/
	function updateItem() {
		global $id;

		if($this->validator->validateAll()) {
			$query = new Query();

			$text = $this->vars['text'];

			$name = stringOr($this->vars['name'], cutString($text, 50));
			$teaser = stringOr($this->vars['teaser'], cutString($text, 100));

//			$name = $this->vars['name'];

			$vars = "name='$name'";
			$vars .= ", teaser='$teaser'";
			$vars .= ", text='$text'";
			$vars .= ", timestamp='".mTimestamp($this->vars["timestamp"])."'";
//			$vars .= ", release='".mTimestamp($this->vars["release"])."'";
//			$vars .= $this->vars["expire"] ? ", expire='".mTimestamp($this->vars["expire"])."'" : "";
			$vars .= ", language_id='".$this->vars["language_id"]."'";
			
			if($query->sql("UPDATE ".$this->db." SET $vars WHERE item_id = $id", true)) {
				messageHandler()->addStatusMessage($this->translate("###$name### updated"));
				return true;
			}
			else {
				messageHandler()->addErrorMessage($query->dbError());
				return false;
			}
		}
		else {
			messageHandler()->addErrorMessage($this->translate("Please complete missing information"));
			return false;
		}
	}

}

?>