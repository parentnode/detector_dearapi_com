<?php
/**
* @package framework
*/
include_once("class/items/type.log.core.class.php");
/**
* typeLog
*
* inline TEMPLATES
*
* itemtype.list -> itemtype class
* itemtype.view -> itemtype class
* itemtype.edit -> itemtype class
*/
class TypeLog extends TypeLogCore {


	/**
	* Default settings
	*/
	function __construct() {
		parent::__construct();

		$this->addTranslation(__FILE__);

		$this->validator = new Validator($this);

		$this->varnames["name"] = $this->translate("Name");
//		$this->validator->rule("name", "txt");

		$this->varnames["html"] = $this->translate("HTML");
		$this->validator->rule("html", "txt");

		$this->varnames["latitude"] = $this->translate("Latitude");
		$this->validator->rule("latitude", "num");
		$this->varnames["latitude_minutes"] = $this->translate("Minutes");

		$this->varnames["longitude"] = $this->translate("Longitude");
		$this->validator->rule("longitude", "num");
		$this->varnames["longitude_minutes"] = $this->translate("Minutes");

		$this->varnames["timestamp"] = $this->translate("Timestamp");
		$this->validator->rule("timestamp", "timestamp");

		$this->varnames["language_id"] = $this->translate("Language");
		$this->validator->rule("language_id", "txt");

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
				$query->sql("SELECT name, html, latitude, longitude, UNIX_TIMESTAMP(timestamp) as timestamp, language_id FROM ".$this->db." WHERE item_id = ".$value);
				$item->item["name"][$key] = $query->getQueryResult(0, "name");
				$item->item["html"][$key] = str_replace("&quot;", '"', $query->getQueryResult(0, "html"));
				$item->item["latitude"][$key] = $query->getQueryResult(0, "latitude");
				$item->item["longitude"][$key] = $query->getQueryResult(0, "longitude");
				$item->item["timestamp"][$key] = date("d-m-Y H:i", $query->getQueryResult(0, "timestamp"));
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
			
			$_ .= '<li class="id:'.$item->item["id"][0].($status ? ' status:'.$status : '').' log">';
			$_ .= '<span class="timestamp">'.$item->item["timestamp"][0].'</span>';
			$_ .= '<span class="location">'.$item->item["latitude"][0].'/'.$item->item["longitude"][0].'</span>';
			$_ .= HTML::head($item->item["name"][0], 2);
			$_ .= '<div class="preview">'.$item->item["html"][0].'</div>';
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
			$status = "";
			$_ .= '<ul class="log'.($classname ? ' '.$classname : '').'">';
			if($validate && Session::getLogin()->validatePage($validate)) {
				$status = $link;
			}
			foreach($item->item["id"] as $key => $item_id) {

				$_ .= '<li class="id:'.$item->item["id"][$key].($status ? ' status:'.$status : '').' html">';
				$_ .= '<span class="timestamp">'.$item->item["timestamp"][$key].'</span>';
				$_ .= '<span class="location">'.$item->item["latitude"][$key].'/'.$item->item["longitude"][$key].'</span>';
				$_ .= HTML::head($item->item["name"][$key], 2);
				$_ .= '<div class="preview">'.$item->item["html"][$key].'</div>';
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

		$_ .= $HTML->block($this->varnames["name"], $item->item["name"][0]);
		$_ .= '<div class="preview">';
			$_ .= $item->item["html"][0];
		$_ .= '</div>';

		$_ .= '<div class="ci50">';
			$_ .= $HTML->block($this->varnames["timestamp"], stringOr($item->item["timestamp"][0], "-"));
			$_ .= $HTML->block($this->varnames["language_id"], stringOr($item->item["language_id"][0], "-"));
		$_ .= '</div>';

		$_ .= '<div class="ci50">';
			$_ .= $HTML->block($this->varnames["latitude"], stringOr($item->item["latitude"][0], "-"));
			$_ .= $HTML->block($this->varnames["longitude"], stringOr($item->item["longitude"][0], "-"));
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
		$_ .= $HTML->textarea($this->varnames["html"], "html", stringOr($this->vars["html"], $item->item["html"][0]));

		$_ .= '<div class="ci50">';
			$_ .= $HTML->input($this->varnames["timestamp"], "timestamp", stringOr($this->vars["timestamp"], $item->item["timestamp"][0]));
			$_ .= $HTML->select($this->varnames["language_id"], "language_id", Page::getItems(UT_BAS_LAN), $item->item["language_id"][0]);
		$_ .= '</div>';
		$_ .= '<div class="ci50">';
			$_ .= '<div class="location">';
				$_ .= '<div class="deg">';
					$_ .= $HTML->input($this->varnames["latitude"], "latitude", stringOr($this->vars["latitude"], $item->item["latitude"][0]), "latlon");
				$_ .= '</div>';
				$_ .= '<div class="min">';
					$_ .= $HTML->input($this->varnames["latitude_minutes"], "latitude_minutes", stringOr($this->vars["latitude_minutes"], ($item->item["latitude"][0]%1)*60), "latlonmin");
				$_ .= '</div>';
			$_ .= '</div>';
			$_ .= '<div class="location">';
				$_ .= '<div class="deg">';
					$_ .= $HTML->input($this->varnames["longitude"], "longitude", stringOr($this->vars["longitude"], $item->item["longitude"][0]), "latlon");
				$_ .= '</div>';
				$_ .= '<div class="min">';
					$_ .= $HTML->input($this->varnames["longitude_minutes"], "longitude_minutes", stringOr($this->vars["longitude_minutes"], ($item->item["longitude"][0]%1)*60), "latlonmin");
				$_ .= '</div>';
			$_ .= '</div>';
			$_ .= '<div class="location link" onclick="Util.getLocation(\'latitude\', \'longitude\')">get location from browser</div>';
		$_ .= '</div>';

		$_ .= $HTML->smartButton($this->translate("Update"), "update", "update key:s", "fright");
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
		$_ .= $HTML->textarea($this->varnames["html"], "html", stringOr($this->vars["html"]));

		$_ .= '<div class="c">';

			$_ .= '<div class="ci50">';
				$_ .= $HTML->inputTimestamp($this->varnames["timestamp"], "timestamp", stringOr($this->vars["timestamp"], date("d-m-Y H:i", time())));
				$_ .= $HTML->select($this->varnames["language_id"], "language_id", Page::getItems(UT_BAS_LAN), Session::getLanguageIso());
			$_ .= '</div>';
			$_ .= '<div class="ci50">';
				$_ .= '<div class="location">';
					$_ .= '<div class="deg">';
							$_ .= $HTML->input($this->varnames["latitude"], "latitude", stringOr($this->vars["latitude"]), "latlon");
					$_ .= '</div>';
					$_ .= '<div class="min">';
						$_ .= $HTML->input($this->varnames["latitude_minutes"], "latitude_minutes", stringOr($this->vars["latitude_minutes"]), "latlonmin");
					$_ .= '</div>';
				$_ .= '</div>';
				$_ .= '<div class="location">';
					$_ .= '<div class="deg">';
						$_ .= $HTML->input($this->varnames["longitude"], "longitude", stringOr($this->vars["longitude"]), "latlon");
					$_ .= '</div>';
					$_ .= '<div class="min">';
						$_ .= $HTML->input($this->varnames["longitude_minutes"], "longitude_minutes", stringOr($this->vars["longitude_minutes"]), "latlonmin");
					$_ .= '</div>';
				$_ .= '</div>';
				$_ .= '<div class="location link" onclick="Util.getLocation(\'latitude\', \'longitude\')">get location from browser</div>';
			$_ .= '</div>';
		
		$_ .= '</div>';

		$_ .= HTML::smartButton($this->translate("Cancel"), "done", "done", "key:esc");
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

		if($this->validator->validateAll()) {

			if($this->save($item_id, $this->vars)){
				messageHandler()->addStatusMessage($this->translate("Log saved"));
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

			$html = $this->vars['html'];
			if($html == strip_tags($html)) {
				$html = "<p>$html</p>";
			}

			$name = stringOr($this->vars['name'], cutString(strip_tags($html), 50));

			// latitude/longitude definition with minutes
			if($this->vars["latitude_minutes"]) {
				list($degrees) = explode(".", $this->vars["latitude"]);
				$this->vars["latitude"] = $degrees + ($this->vars["latitude_minutes"]/60);
			}
			if($this->vars["longitude_minutes"]) {
				list($degrees) = explode(".", $this->vars["longitude"]);
				$this->vars["longitude"] = $degrees + ($this->vars["longitude_minutes"]/60);
			}

			$vars = "name='$name'";
			$vars .= ", html='$html'";
			$vars .= ", latitude='".$this->vars["latitude"]."'";
			$vars .= ", longitude='".$this->vars["longitude"]."'";
			$vars .= ", timestamp='".mTimestamp($this->vars["timestamp"])."'";
			$vars .= ", language_id='".$this->vars["language_id"]."'";
			
			if($query->sql("UPDATE ".$this->db." SET $vars WHERE item_id = $id", true)) {
				messageHandler()->addStatusMessage($this->translate("Log updated"));
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