<?php
/**
* @package framework
*/
include_once("class/items/type.bundle.core.class.php");
/**
* typeProduct
*
* inline TEMPLATES
*
* itemtype.list -> itemtype class
* itemtype.view -> itemtype class
* itemtype.edit -> itemtype class
*/
class TypeBundle extends TypeBundleCore {
	
	//public $limits;

	/**
	* Default settings
	*/
	function __construct() {
		parent::__construct();
		$this->addTranslation(__FILE__);

		$this->validator = new Validator($this);

		$this->varnames["name"] = $this->translate("Bundle/box name");
		$this->validator->rule("name", "txt");

		$this->varnames["classname"] = $this->translate("Class");
		$this->validator->rule("classname", "txt");

		$this->varnames["image"] = $this->translate("Photo");

		$this->varnames["items"] = $this->translate("Items");
//		$this->varnames["quantity"] = $this->translate("Quantity");
		$this->varnames["value_pct"] = $this->translate("Value (%)");

		$this->varnames["value_base"] = $this->translate("Calculation base");

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
				$query->sql("SELECT name FROM ".UT_ITT_BUN." WHERE item_id = ".$value);
				$item->item["name"][$key] = $query->getQueryResult(0, "name");

				if($query->sql("SELECT value_pct, item_id FROM ".UT_ITT_BUN_ITE." WHERE bundle_item_id = ".$value)) {
					for($i = 0; $i < $query->getQueryCount(); $i++) {
						$item->item["items"][$key]["value_pct"][] = $query->getQueryResult($i, "value_pct");
						$item->item["items"][$key]["item_id"][] = $query->getQueryResult($i, "item_id");
					}
				}
				else {
					$item->item["items"][$key] = false;
				}
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
//			$_ .= '<li class="id:'.$id.($status ? ' status:'.$status : '').' product">';
			$_ .= '<!--img src="/images/'.$id.'/600x.jpg" alt="'.$item->item["name"][0].'" /-->';
			$_ .= '<p>'.$item->item["name"][0].'</p>';
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

		$_ .= $HTML->separator();

		$_ .= '<div class="c">';

		$_ .= $HTML->head("Items", "2");

		if($item->item["items"][0]) {
			foreach($item->item["items"][0]["value_pct"] as $key => $value) {
				
				$it = new Item();
				$it->getItem($item->item["items"][0]["item_id"][$key]);
				$item_name = $it->getTypeObject()->getSelectValue($it);
				
				$_ .= $HTML->p("$item_name: $value%");
				//$key -> $value -> ".$item->item["items"][0]["item_id"][$key]." -> $item_name <br>";
			}
		}
		else {
			$_ .= $HTML->p("No items in this bundle");
		}

		$_ .= '</div>';

		$_ .= $HTML->smartButton($this->translate("Edit"), "edit", "edit", "fright key:e");

		$_ .= $HTML->separator();

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

		$id = $item->item["id"][0];
		
		$_ = "";
		$_ .= $HTML->inputHidden("id", $id);
		$_ .= $HTML->inputHidden("page_status", "edit");

		$_ .= $HTML->editImg($this->varnames["image"], "page,file", "image", $id, $item->item["name"][0], "/images/".$id."/490x.jpg", $this->translate("Click to edit"));
		$_ .= $HTML->editInput($this->varnames["name"], "update", "name", $id, $item->item["name"][0], $this->translate("Click to edit"));

		$_ .= $HTML->separator();

		$_ .= '<div class="c init:bundlevalue">';

		$_ .= $HTML->head("Edit items", "2");

		$table = $this->table("incremental");
		$table->setHeader(0, $this->varnames["items"]);
		$table->setHeader(1, $this->varnames["value_pct"]);
		$table->setHeader(2, "~");

		$table->setColumnType(0, "select");
		$table->setColumnType(1, "input");

		$default_id[] = "";
		$default_value[] = "Select";

		$column_0[0] = "items";
		$column_1[0] = "value_pct";
		$column_2[0] = "";


		$items["id"] = array();
		$items["values"] = array();

		$it = new Item();
		$it->getItems("product");

		if($it->item()) {
			foreach($it->item["id"] as $item_id) {
				$it->getItem($item_id);
				$items["id"][] = $item_id;
				$items["values"][] = $it->getTypeObject()->getSelectValue($it);
			}
		}

		if($item->item["items"][0]) {

			$column_0[1] = $item->item["items"][0]["item_id"];
			$column_0[2] = $items["id"];
			$column_0[3] = $items["values"];
			
			$column_1[1] = $item->item["items"][0]["value_pct"];
		}
		else {
			$column_0[1] = false;
			$column_0[2] = $items["id"];
			$column_0[3] = $items["values"];
		}


		$table->setColumnValues($column_0, $column_1, $column_2);
		$_ .= $table->build();

		$calculation_base["id"] = array();
		$calculation_base["values"] = array();

		$query = new Query();
		if($query->sql("SELECT item_prices.price, price_groups.name, item_prices.country_id FROM ".UT_ITE_PRI." AS item_prices, ".UT_PRI." AS price_groups WHERE price_groups.uid = item_prices.price_group_uid AND item_prices.item_id = '".$id."'")) {
			for($i = 0; $i < $query->getQueryCount(); $i++) {
				$cb_price = $query->getQueryResult($i, "price");
				$cb_country_id = $query->getQueryResult($i, "country_id");
				$cb_name = $query->getQueryResult($i, "name");

				$calculation_base["id"][] = $cb_price;
				$calculation_base["values"][] = $cb_country_id."-".$cb_name.": ".$cb_price." ".$it->countryClass->getCurrency($cb_country_id, "abbreviation");
			}
		}

		$_ .= $HTML->select($this->varnames["value_base"], "value_base", $calculation_base);

		$_ .= $HTML->smartButton($this->translate("Cancel"), "edit_cancel", "edit_cancel", "key:esc");
		$_ .= $HTML->smartButton($this->translate("Update"), "update", "update", "fright key:s", "bundlevalueUpdate");

		$_ .= '</div>';


		$_ .= $HTML->separator();

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
		$HTML->details(1);
		$this->details(1);

		$_ = "";
		$type = $this->type_name;
		$_ .= $HTML->head($this->translate("New ###$type###"));

		$_ .= $HTML->inputHidden("itemtype_id", $this->itemtype);

		$_ .= $HTML->p("Name your bundle/box and choose your image!", "info");

		$_ .= $this->input("name");

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

			if($this->save($item_id, $file_tmp, $this->vars['name'])) {
				messageHandler()->addStatusMessage($this->translate("Bundle saved"));
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

		$query = new Query();

		$atr = getVar("atr");

		// individual update
		if($atr) {

			$value = $this->vars[$atr];

			if($this->validator->validateList($atr)) {

				if($atr == "name") {

					$vars = $atr."='$value'";

					if($query->sql("UPDATE ".UT_ITT_BUN." SET $vars WHERE item_id = $id", true)) {
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
		// bundle price update
		else {

			// delete bundle items
			$query->sql("DELETE FROM ".UT_ITT_BUN_ITE." WHERE bundle_item_id = $id");
			
			// save bundle items
			foreach($this->vars["items"] as $key => $item_id) {
				print "INSERT INTO ".UT_ITT_BUN_ITE." VALUES(DEFAULT, ".$this->vars["value_pct"][$key].", $id, $item_id)";
				$query->sql("INSERT INTO ".UT_ITT_BUN_ITE." VALUES(DEFAULT, ".$this->vars["value_pct"][$key].", $id, $item_id)");
			}

			return true;
			
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