<?php
/**
* @package framework
*/
include_once("class/system/image_tools.class.php");
include_once("class/items/type.product.core.class.php");

/**
* www typePhoto
*
*/
class TypeProduct extends TypeProductCore {
	
//	public $limits;

	/**
	* Default settings
	*/
	function __construct() {
		parent::__construct();

		$this->addTranslation(__FILE__);

//		$this->limits["list"] = 30;
//		$this->limits["preview"] = 12;
	}

	/*
	function viewItem() {
		global $page;

		$item = $page->getObject("Item");

		$item = $this->getItem($item);

		$_ = '';
		if($item->item()) {
			$item->getTags();
			$item->getTagList();
//			$item = $this->getItem($item);
//			foreach($item->item["id"] as $key => $item_id)  {
//				$_ .= '<li class="id:'.$item->item["id"][0].'">';
				$_ .= '<div class="product">';
//				$_ .= '<a href="/view.php?id='.$item_id.'">';

				if(!file_exists(PUBLIC_FILE_PATH.$item->item["id"][0]."/660x.jpg")) {
					ImageTools::scaleImage(BACKUP_FILE_PATH.$item->item["id"][0]."/jpg", PUBLIC_FILE_PATH.$item->item["id"][0]."/", 660, false, 2, "660x.jpg");
				}

				$_ .= '<img src="/images/'.$item->item["id"][0].'/660x.jpg" alt="'.$item->item["name"][0].'" />';
				$_ .= '<p>'.$item->item["name"][0].'</p>';
				$_ .= '<p>'.$item->item["tag_list"][0].'</p>';
//				$_ .= '</a>';
				$_ .= '</div>';
//				$_ .= '</li>';
//			}
//			$_ .= '</ul>';
		}
		return $_;
	}
	*/

	/*
	function viewCart() {
		global $page;

		$item = $page->getObject("Item");
		$item = $this->getItem($item);

		$_ = '';
		if($item->item()) {
			$_ .= '<div class="product">';

			if(!file_exists(PUBLIC_FILE_PATH.$item->item["id"][0]."/60x.jpg")) {
				ImageTools::scaleImage(BACKUP_FILE_PATH.$item->item["id"][0]."/jpg", PUBLIC_FILE_PATH.$item->item["id"][0]."/", 60, false, 2, "60x.jpg");
			}

			$_ .= '<img src="/images/'.$item->item["id"][0].'/60x.jpg" alt="'.$item->item["name"][0].'" />';
			$_ .= '<p>'.$item->item["name"][0].'</p>';
			$_ .= '</div>';
		}
		return $_;
	}
	*/

	/*
	function mixedList($item) {

		$_ = '';

		if($item->item()) {
			$item = $this->getItem($item);

			$_ .= '<li class="id:'.$item->item["id"][0].' product">';
			$_ .= '<a href="/view.php?id='.$item->item["id"][0].'">';

			if(!file_exists(PUBLIC_FILE_PATH.$item->item["id"][0]."/600x.jpg")) {
				ImageTools::scaleImage(BACKUP_FILE_PATH.$item->item["id"][0]."/jpg", PUBLIC_FILE_PATH.$item->item["id"][0]."/", 600, false, 2, "600x.jpg");
			}

			$_ .= '<img src="/images/'.$item->item["id"][0].'/600x.jpg" alt="'.$item->item["name"][0].'" />';
			$_ .= '</a>';
			$_ .= '<p>'.$item->item["name"][0].'</p>';
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

		$_ = '';

		if($item->item()) {
			$_ .= '<ul class="product">';
			$item = $this->getItem($item);
			foreach($item->item["id"] as $key => $item_id)  {

				$_ .= '<li class="id:'.$item_id.'">';
				$_ .= '<a href="/view.php?id='.$item_id.'">';

				if(!file_exists(PUBLIC_FILE_PATH.$item_id."/280x.jpg")) {
					ImageTools::scaleImage(BACKUP_FILE_PATH.$item_id."/jpg", PUBLIC_FILE_PATH.$item_id."/", 280, false, 2, "280x.jpg");
				}

				$_ .= '<img src="/images/'.$item_id.'/280x.jpg" alt="'.$item->item["name"][$key].'" />';
				$_ .= '<p>'.$item->item["name"][$key].'</p>';
				$_ .= '</a>';
				$_ .= '</li>';
			}
			$_ .= '</ul>';
		}
//		else {
//			$_ .= Generic::listItems($link, $validate, false, $this->types_name);
//		}

		return $_;
	}
	*/

	/*
	function viewTeaser() {
		global $page;

		$item = $page->getObject("Item");

		$item = $this->getItem($item);

		$_ = '';
		if($item->item()) {
			$_ .= '<ul class="product teaser">';
			$item = $this->getItem($item);
			foreach($item->item["id"] as $key => $item_id)  {
				$_ .= '<li class="id:'.$item_id.'">';
				$_ .= '<a href="/view.php?id='.$item_id.'">';

				if(!file_exists(PUBLIC_FILE_PATH.$item_id."/130x.jpg")) {
					ImageTools::scaleImage(BACKUP_FILE_PATH.$item_id."/jpg", PUBLIC_FILE_PATH.$item_id."/", 130, false, 2, "130x.jpg");
				}

				$_ .= '<img src="/images/'.$item_id.'/130x.jpg" alt="'.$item->item["name"][$key].'" />';
				$_ .= '</a>';
				$_ .= '</li>';
			}
			$_ .= '</ul>';
		}
		return $_;
	}
	*/


}

?>