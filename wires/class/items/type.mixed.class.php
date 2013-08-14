<?php
/**
* @package framework
*/

/**
* typeMixed
*/
class TypeMixed extends ItemCore {
	
//	public $limits;
	public $itemtype;
//	public $item_name;
//	public $items_name;

	/**
	* Default settings
	*/
	function __construct() {
		$this->addTranslation(__FILE__);

//		$this->limits["list"] = 30;
//		$this->limits["preview"] = 12;

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
	
	function listItems($link=false, $validate=false, $classname=false) {
		global $page;
		global $HTML;

		$item = $page->getObject("Item");
		$it = new Item();

		$_ = '';

		if($item->item()) {
			$_ .= '<ul class="mixed'.($classname ? ' '.$classname : '').'">';

			$status = false;
			if($validate && Session::getLogin()->validatePage($validate)) {
				$status = $link;
			}

			foreach($item->item["id"] as $id) {

				$it->getItem($id);


				$_ .= $it->getTypeObject()->mixedList($it, $status);

			}
			$_ .= '</ul>';
		}
		else {
			$_ .= $HTML->p("You havent added any items yet. Add a new item by clicking here!", "hint status:link:/items/items_new.php");
		}

		return $_;
	}
	

}

?>