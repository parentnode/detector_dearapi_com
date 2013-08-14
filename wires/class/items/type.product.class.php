<?php
/**
* @package framework
*/
include_once("class/items/type.product.core.class.php");
/**
* typeProduct
*
* inline TEMPLATES
*
* itemtype.list -> itemtype class
* itemtype.view -> itemtype class
* itemtype.edit -> itemtype class
*/
class TypeProduct extends TypeProductCore {
	
	//public $limits;

	/**
	* Default settings
	*/
	function __construct() {
		parent::__construct();
		$this->addTranslation(__FILE__);

		$this->validator = new Validator($this);

		$this->varnames["name"] = $this->translate("Product name");
		$this->validator->rule("name", "txt");

		$this->varnames["classname"] = $this->translate("Class");
		$this->validator->rule("classname", "txt");

		$this->varnames["image"] = $this->translate("Photo");
//		$this->validator->rule("image", "image");

		$this->vars = getVars($this->varnames);

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
				$query->sql("SELECT name FROM ".$this->db." WHERE item_id = ".$value);
				$item->item["name"][$key] = $query->getQueryResult(0, "name");
			}
		}
		return $item;
	}

	function mixedList($item, $status) {

		$id = $item->item["id"][0];
		$_ = '';

		if($item->item()) {
			$item = $this->getItem($item);
			$item->getTagList();

			$class = $this->makeAttribute("class", "id:$id", "image", ($status ? "status:$status" : ''), "product");
			$_ .= '<li'.$class.'>';
//			$_ .= '<li class="id:'.$id." image ".($status ? ' status:'.$status : '').' product">';
			$_ .= '<!--img src="/images/'.$id.'/600x.jpg" alt="'.$item->item["name"][0].'" /-->';
			$_ .= '<p>'.$item->item["name"][0].'</p>';
			$_ .= '<span class="tags">'.$item->item["tag_list"][0].'</span>';
			$_ .= '</li>';
		}

		return $_;
		
	}

	function getSelectValue($item) {

		$id = $item->item["id"][0];

		if($item->item()) {
			$item = $this->getItem($item);
			return $item->item["name"][0]." (".$this->itemtype.")";
		}
		else {
			return "";
		}
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

		if($item->item()) {

			$item = $this->getItem($item);
			$_ .= $HTML->p("Add a new ". $item->itemtype, "hint status:link:/items/items_new.php?itemtype_id=".$item->itemtype_id);
			$item->getTagList();
			
			
			$_ .= '<ul class="photo'.($classname ? ' '.$classname : '').'">';
			if($validate && Session::getLogin()->validatePage($validate)) {
				$status = $link;
			}
			foreach($item->item["id"] as $key => $item_id) {

				$_ .= '<li class="id:'.$item_id.($status ? ' status:'.$status : '').' product">';
				$_ .= '<img src="/images/'.$item_id.'/600x.jpg" alt="'.$item->item["name"][$key].'" />';
				$_ .= '<p>'.$item->item["name"][$key ].'</p>';
				$_ .= '<span class="tags">'.$item->item["tag_list"][$key].'</span>';
				$_ .= '</li>';
			}
			$_ .= '</ul>';
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

		$_ .= $HTML->editImg($this->varnames["image"], "page,file", "image", $id, $item->item["name"][0], "/images/".$id."/490x.jpg", $this->translate("Click to edit"));
		$_ .= $HTML->editInput($this->varnames["name"], "update", "name", $id, $item->item["name"][0], $this->translate("Click to edit"));
//		$_ .= $HTML->separator();
//		$_ .= $HTML->smartButton($this->translate("Edit"), "edit", "edit", "fright");

		return $_;
	}


	/**
	* Edit item, compiles the info for this itemtype in item edit view and returns HTML
	*
	* @return String HTML
	*/
	function editItem() {
		return $this->viewItem();
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
		$type = $this->type_name;
		$_ .= $HTML->head($this->translate("New ###$type###"));

		$_ .= $HTML->inputHidden("itemtype_id", $this->itemtype);

		$_ .= $HTML->p("Name your product and choose your image!", "info");

		$_ .= $HTML->input($this->varnames["name"], "name", stringOr($this->vars["name"]));
		$_ .= $HTML->inputFile($this->varnames["image"], "image", "init:file");
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


		if($this->validator->validateList("name")) {

			$file_tmp = $_FILES["image"]['tmp_name'];
			$file = $_FILES["image"]['name'];

//			$name = stringOr($this->vars['name'], substr(str_replace("_", " ", $file), 0, -4));

			if($this->save($item_id, $file_tmp, $this->vars['name'])) {
				messageHandler()->addStatusMessage($this->translate("Product saved"));
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

//		print $id;
//		print "hep";
		$atr = getVar("atr");
		$value = $this->vars[$atr];

		if($this->validator->validateList($atr)) {

			if($atr == "name") {
				$query = new Query();

				$vars = $atr."='$value'";

				if($query->sql("UPDATE ".$this->db." SET $vars WHERE item_id = $id", true)) {
					messageHandler()->addStatusMessage($this->translate("###$value### updated"));
					return true;
				}
				else {
					messageHandler()->addErrorMessage($query->dbError());
					return false;
				}
			}
			else {
			}
			
		}
		else {
			messageHandler()->addErrorMessage($this->translate("Please complete missing information"));
			return false;
		}
	}
	
	function updateItemFile() {
		global $id;

		if($this->validator->validateList("image")) {

			$file_tmp = $_FILES["image"]['tmp_name'];
			$file = $_FILES["image"]['name'];

			$this->updateFiles($id, $file_tmp);

			messageHandler()->addStatusMessage($this->translate("Image saved"));
			return true;
		}
		else {

			messageHandler()->addErrorMessage($this->translate("Image NOT saved - invalid format!"));
			return false;

		}

	}

}

?>