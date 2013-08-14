<?php
/**
* @package framework
*/
include_once("class/items/type.news.core.class.php");

/**
* www typeNews
*
*/
class TypeNews extends TypeNewsCore  {
	
//	public $limits;
//	public $itemtype;
//	public $item_name;
//	public $items_name;

	/**
	* Default settings
	*/
	function __construct() {
		parent::__construct();
		
		$this->addTranslation(__FILE__);
	}

	/*
	function mixedList($item) {

		$_ = '';

		if($item->item()) {
			$item = $this->getItem($item);
			
			$_ .= '<li class="id:'.$item->item["id"][0].'">';
			$_ .= '<span class="timestamp">'.$item->item["timestamp"][0].'</span>';
			$_ .= HTML::head($item->item["name"][0], 2);
			$_ .= HTML::p($item->item["text"][0]);
			$_ .= '</li>';

		}
		return $_;
	}
	*/

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
		$item->getTagList();

		$_ = '';
		if($item->item()) {
			$_ .= '<ul class="news">';
			$item = $this->getItem($item);
			foreach($item->item["id"] as $key => $value)  {

				$_ .= '<li class="id:'.$value.'">';
				$_ .= '<span class="timestamp">'.$item->item["timestamp"][$key].'</span>';
				$_ .= HTML::head($item->item["name"][0], 3);
				$_ .= HTML::p($item->item["text"][0]);
//				$_ .= '<span class="tags">'.$item->item["tag_list"][$key].'</span>';
				$_ .= '</li>';
			}
			$_ .= '</ul>';
		}

		return $_;
	}
	*/

	/*
	function viewTeaser() {
		global $page;

		$item = $page->getObject("Item");
		$item->getTagList();

		$item = $this->getItem($item);

		$_ = '';
		if($item->item()) {
			$_ .= '<ul class="blog teaser">';
			$item = $this->getItem($item);
			foreach($item->item["id"] as $key => $value)  {
				if($key == 0) {
					$_ .= '<li class="id:'.$value.'">';
					$_ .= '<span class="timestamp">'.$item->item["timestamp"][$key].'</span>';
					$_ .= '<h3><a href="list.php?tags=blog&id='.$value.'">'.$item->item["name"][$key].'</a></h3>';
//					$_ .= '<h3>'.$item->item["name"][$key].'</h3>';
					$_ .= HTML::p($item->item["text"][0]);
//					$_ .= '<span class="tags">'.$item->item["tag_list"][$key].'</span>';
					$_ .= '</li>';
				}
				else {
					$_ .= '<li class="id:'.$value.'">';
					$_ .= '<span class="timestamp">'.$item->item["timestamp"][$key].'</span>';
					$_ .= '<h3><a href="list.php?tags=blog&id='.$value.'">'.$item->item["name"][$key].'</a></h3>';
					$_ .= '</li>';
				}
			}
			$_ .= '</ul>';
		}
		return $_;
	}
	*/





	/**
	* View item, compiles the info for this itemtype in item view and returns HTML
	*
	* @return String HTML
	*/
	/*
	function viewItem() {
		global $page;
		global $HTML;

		$item = $page->getObject("Item");
		$item = $this->getItem($item);

		$_ = "";

		$_ = "";
		$_ .= '<div class="c">';
			$_ .= '<div class="ci66 init:form form:action:'.$page->url.'" id="container:edit">';
				$_ .= $HTML->inputHidden("id", $item->item["id"][0]);
				$_ .= $HTML->inputHidden("page_status", "view");
				$_ .= $HTML->head($item->item["name"][0], 3);
				$_ .= $HTML->p(stringOr($item->item["text"][0], "-"));
				$_ .= $HTML->separator();
				$_ .= $HTML->smartButton($this->translate("Edit"), "edit", "edit", "fright");
			$_ .= '</div>';
			$_ .= '<div class="ci33">';
				$_ .= $HTML->block("Rating", stringOr($item->getAvgRate($item->item["id"][0]), "-"));
				$_ .= $HTML->separator();
				$_ .= $item->listTags();
			$_ .= '</div>';
		$_ .= '</div>';

		return $_;
	}
	*/

}

?>