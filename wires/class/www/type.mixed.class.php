<?php
/**
* @package framework
*/


/**
* typeMixed
* @package local
*/
class TypeMixed extends Translation {
	
	public $limits;
	public $itemtype;
	public $item_name;
	public $items_name;

	/**
	* Default settings
	*/
	function __construct() {
		$this->addTranslation(__FILE__);

		$this->itemtype = "mixed";
		
		$this->type_name = $this->translate("Mixed");
		$this->types_name = $this->translate("Mixed");

	}

	/**
	* List items, compiles the info for this itemtype in list view and returns HTML
	*
	* @param String $list_type Optional listtype (CSS specified types)
	* @return String HTML
	*/
	/*
	function listItems($link=false, $validate=false, $classname=false) {
		global $page;
		global $HTML;

		$item = $page->getObject("Item");
		$it = new Item();

		$_ = '';
		
		if($item->item()) {
			$_ .= '<ul class="mixed">';
			foreach($item->item["id"] as $id) {

				$it->getItem($id);


				$_ .= $it->getTypeObject()->mixedList($it);

//				if()

//				$item = $ito->getItem($item);
//				$itemtype = $item->getTypeObject($item->item["itemtype_id"][$key]);
				
//				$item = $itemtype->getItem($item);

//				$_ .= $id . "::" .$item->item["itemtype_id"][$key]. "<br>";
//				print_r($item);
//				print $item->item["name"][$key] . "<br>";
			}
			$_ .= '</ul>';

//			$_ .= Generic::listItemsExtended($link, $validate, $item->item["id"], array($item->item["id"], $item->item["itemtype"]), array($this->types_name, $this->translate("Search")), array("max", "search"));
		}

		return $_;
	}
	*/

}

?>