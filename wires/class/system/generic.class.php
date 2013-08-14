<?php
/**
* Generic functions
*/
class Generic extends Translation {

	/**
	* Making sure calling class has translater
	*/
	function translater() {
		$this->addTranslation(__FILE__);
		
//		if(!property_exists($this, "translater")) {
//			$this->translater = new Translation(__FILE__);
//		}
//		else {
//			$this->translater->__construct(__FILE__);
//		}
	}

	/**
	* Get selected item value
	*
	* @param int $id Item id
	* @param String $db Database to get item from
	* @param String $value Item value to get from db
	* @return String|false Item name or false on error
	*/
	function getItemValue($id, $db, $value) {
		Generic::translater();
		$query = new Query();
		if($query->sql("SELECT $value FROM $db WHERE id = $id")) {
			return $query->getQueryResult(0, $value);
		}
		else {
			messageHandler()->addErrorMessage($this->translate("The requested item does not exist! ($id, $db)"));
			return false;
		}
	}

	/**
	* Get selected item name
	*
	* @param int $id Item id
	* @param String $db Database to get item from
	* @return String|false Item name or false on error
	*/
	function getItemName($id, $db) {
		Generic::translater();
		$query = new Query();
		if($query->sql("SELECT name FROM $db WHERE id = '$id'")) {
			return $query->getQueryResult(0, "name");
		}
		else {
			messageHandler()->addErrorMessage($this->translate("The requested item does not exist! ($id, $db)"));
			return false;
		}
	}

	/**
	* Get selected item
	* Makes query result available
	*
	* @param Integer $id Item id
	* @param String $db Database to get item from
	* @return bool
	*/
	function getItem($id, $db) {
		Generic::translater();
		if($this->sql("SELECT * FROM $db WHERE id = '$id'")){
			return true;
		}
		else {
			messageHandler()->addErrorMessage($this->translate("The requested item does not exist! ($id, $db)"));
			return false;
		}
	}

	/**
	* Get all items
	*
	* @param String $which Optional limitation of returned result. ("id", "values" or any extended value)
	* @param String $db Database to get items from
	* @param String $order Optional ORDER BY value
	* @param Strings Optional additional values to return
	* @return array|false Item array or false on error
	*/
	function getItems($db, $which, $order=false) {
		$extended_values = array();

		$args = func_get_args();
		for($i = 3; $i < count($args); $i++) {
			if($args[$i]) {
				$extended_values[] = $args[$i];
			}
		}
		$items = array();
		$query = new Query();
		$query->sql("SELECT id, name".(count($extended_values) ? ",".array_list($extended_values) : "")." FROM $db".($order ? " ORDER BY $order" : ""));
		for($i = 0; $i < $query->getQueryCount(); $i++) {
			$items["id"][$i] = $query->getQueryResult($i, "id");
			$items["values"][$i] = $query->getQueryResult($i, "name");
			for($u = 0; $u < count($extended_values); $u++) {
				$items[$extended_values[$u]][$i] = $query->getQueryResult($i, $extended_values[$u]);
			}
		}

		if(!count($items)) {
			return false;
		}
		else if($which) {
			return $items[$which];
		}
		else {
			return $items;
		}
	}

	/**
	* Get status value
	*
	* @param Integer $id Index-id of value
	* @return String Value of Status index
	*/
	function getStatusValue($id, $status) {
		$index = array_search($id, $status["id"]);
		if($index !== false){
			return $status["values"][$index];
		}
		else {
			return "N/A";
		}
	}

	/**
	* Delete selected item
	*
	* @param int $id Item id
	* @param String $db Database to get items from
	* @return bool
	* @uses Message
	*/
	function deleteItem($id, $db) {
		Generic::translater();
		if($this->sql("DELETE FROM $db WHERE id = '$id'")) {
			messageHandler()->addStatusMessage($this->translate("Item deleted"));
			return true;
		}
		else {
			messageHandler()->addErrorMessage($this->dbError());
			return false;
		}
	}

	/**
	* Make simple 1-column table listing of items
	*
	* @param string $status Optional page_status action (value will be used as type:identifier, status:$status)
	* @param array $validate Optional Validation information
	* @param array $items Array of ["id"]'s and ["values"]'s
	* @param string $header Column header
	* @return string HTML view
	*/
	function listItems($status=false, $validate=false, $items, $header) {
		Generic::translater();
		global $HTML;

		// no items
		if(!$items) {
			$table = $HTML->table(false);
			$table->setHeader(0, $header);
			$items["values"][] = $this->translate("No items available");
		}
		// items
		else {
			$table = $HTML->table();
			$table->setHeader(0, $header);

			$sta = array();
			$ids = array();
			// create links
			if($status &&  (!$validate || Session::getLogin()->validatePage($validate))) {
				foreach($items["id"] as $key => $id) {
					$sta[$key] = $status;
					$ids[$key] = $id;
				}
				$table->setRowStatus($sta);
				$table->setRowId($ids);
			}
		}
		$table->setColumnValues($items["values"]);
		return $table->build();
	}

	/**
	* make extended table listing of items
	*
	* @param string $status Optional page_status action (value will be used as type:identifier, status:$status)
	* @param array $validate Optional Validation information
	* @param array $items_id Array of ids
	* @param array $items Array of values
	* @param array $headers Array of headers
	* @param array $extras Array of classnames (headers and columns)
	* @param array $row_classes Array of row classnames
	* @return string HTML view
	*/
	function listItemsExtended($status=false, $validate=false, $items_id=false, $items, $headers, $cell_classes=false, $row_classes=false) {
		Generic::translater();
		global $HTML;

		// no items
		if(!$items[0]) {
			$table = $HTML->table(false);
			$table->setHeader(0, $headers[0]);
			$table->setColumnValues(array($this->translate("No items available")));
		}
		// items
		else {
			$table = $HTML->table();
			for($i = 0; $i < count($headers); $i++) {
				$table->setHeader($i, $headers[$i], isset($cell_classes[$i]) ? $cell_classes[$i] : false);
				$classname = (isset($cell_classes[$i]) ? preg_replace('/ sortby|sortby |sortby| search|search |search/', "", $cell_classes[$i]) : "");
				if($classname) {
					$table->setColumnClass($i, $classname);
				}
			}

			$sta = array();
			$ids = array();
			// create links
			if($status &&  (!$validate || Session::getLogin()->validatePage($validate))) {
				foreach($items_id as $row => $id) {
					$sta[$row] = $status;
					$ids[$row] = $id;
				}
				$table->setRowStatus($sta);
				$table->setRowId($ids);
				if($row_classes) {
					$table->setRowClasses($row_classes);
				}
			}

			for($i = 0; $i < count($items); $i++) {
				$table->setColumnValue($items[$i]);
			}
		}
		return $table->build();
	}

	/**
	* check if a id appears in a column in a list of database tabels
	*
	* @param string id Id to search for
	* @param string column_name Name of column to search in
	* @param string table Databasetabel where column is to be found
	* @return bool true if id is in use else false
	*/
	function checkUsage($id, $column_name) {
		$_ = false;
		$tables = func_get_args();
		for($i = 2; $i < count($tables); $i++) {
			$this->sql("SELECT id FROM ".$tables[$i]." WHERE ".$column_name." = '$id'");
			$this->getQueryCount() || $_ ? $_ = true : $_ = false;
		}
		return $_;
	}

}

?>