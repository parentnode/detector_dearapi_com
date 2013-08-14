<?php

/**
* This file contains HTML table object
* Initiated from HTML.class
*/
class Table extends Translation {

	private $init;

	private $row_count = 0;
	private $column_count = 0;
	private $columns;

	private $headers;

	private $table_type = false;
	private $row_types = array();
	private $row_links = array();
	private $column_types = array();

	private $id = false;

	/**
	* Constructor.
	* Sets init value.
	* arrange : will extend the table with drag'n'drop functions and looks for a button with the classname "arrange:save save:url" to enable updating
	* incremental : will extend the table with the option to add more rows by duplicating existing rows
	*
	* @param $init Defines whether table should be auto initaliazed based on classname, Default true.
	*/
	function __construct($init=true) {
		//this->translate
		$this->addTranslation(__FILE__);

		if($init) {
			$this->init = $init === true ? "init:table" : "init:table $init table:$init";
		}
		else {
			$this->init = false;
		}
		// search for special table type
		if(strpos($init, "incremental") !== false) {
			$this->table_type = "incremental";
		}
	}

	/**
	* Set header element attributes.
	* Certain settings are simply specified through classname.
	* Class = sortby : Adds JavaScript sorting on this column.
	* Class = search : Insert search field instead of header. Header text is used as default text in input. Table content is automaically indexed for search. 
	*
	* @param integer $column Defines the column for which the header is set.
	* @param string $value Header text.
	* @param string $class (Optional) Class definition for header.
	*/
	function setHeader($column, $value, $class=false) {
		global $HTML;
		list($label_error, $label_class, $label_value) = $HTML->makeLabel($value);

		$this->headers["value"][$column] = $label_value.$label_error;
		if($class) {
			$this->headers["classname"][$column] = $class;

			// does classname contain definitions
			if(strpos($class, "sortby") !== false) {
				$this->headers["sortby"][$column] = true;
			}
			if(strpos($class, "search") !== false) {
				$this->headers["search"][$column] = true;
			}
		}
	}

	/**
	* Set row class. Class is repeated for each row.
	*
	* @param string $class Row classname.
	*/
	function setRowClass($class) {
		$this->row_types["classname"] = isset($this->row_types["classname"]) ? $this->row_types["classname"]." ".$class : $class;
	}

	/**
	* Set table id
	*
	* @param String $id ID of table
	*/
	function setTableId($id) {
		$this->id = $id;
	}

	/**
	* Set row classes. An array of classes with one entry for each row.
	*
	* @param Array $classes Array of row classname.
	*/
	function setRowClasses($classes) {
		$this->row_links = $classes;
	}

	/**
	* Set row status, array with page_status action for each row.
	*
	* @param array $status Array with page_status actions for each row.
	*/
	function setRowStatus($status) {
		$this->row_types["status"] = $status;
	}

	/**
	* Set row ids, array with id for each row
	*
	* @param array $ids Array with ids for each row.
	*/
	function setRowId($ids) {
		$this->row_types["id"] = $ids;
	}

	/**
	* Set class for column. Class is repeated for specified column for each row, except header
	*
	* @param integer $column Column for classname
	* @param string $class Classname
	*/
	function setColumnClass($column, $class) {
		$this->column_types["classname"][$column] = $class;
	}

	/**
	* Set special column type.
	* checkbox: 2d-array [0]name [1]0/1	(checked/unchecked)
	* select: 6d-array [0]name [1]selected index-option-value [2]array of option-values [3]array of option-texts [4]onchange javascript [5]id
	* input: 2d-array [0]name-array [1]value-array [2]max-length-array
	*
	* @param integer $column Column to specialize.
	* @param string $type Specialization. Valid options "checkbox", "select", "input", "indent".
	*/
	function setColumnType($column, $type) {
		$this->column_types["type"][$column] = $type;
	}

	/**
	* Set column indent, array with indent for each row
	*
	* @param integer $column Column to indent.
	* @param array $indents Array with indenting values for each row.
	*/
	function setColumnIndent($column, $indents) {
		$this->column_types["indent"][$column] = $indents;
	}

	/**
	* Set Column values.
	* One parameter for each column.
	*
	* @param Arrays Optional amount of arrays defining table content.
	*/
	function setColumnValues() {
		$this->columns = func_get_args();
	}

	/**
	* Set Column value
	* Add column.
	*
	* @param Array Array of column content.
	*/
	function setColumnValue($column_value) {
		$this->columns[] = $column_value;
	}

	/**
	* Get row count
	* Sets column_count based on added columns.
	*/
	function setColumnCount() {
		$this->column_count = count($this->columns);
	}

	/**
	* Get row count
	* Sets row_count based on rows in this->columns.
	*/
	function setRowCount() {
		foreach($this->columns as $key => $value) {
			$column_type = $this->getIndexValue($this->column_types, "type", $key);

			// if special type count index 1 (index 0 does not always represent row-count)
			if($column_type == "checkbox" || $column_type == "select" || $column_type == "input") {
				if(count($value[0]) > $this->row_count) {
					$this->row_count = count($value[1]);
				}
			}
			else if(count($key) > $this->row_count) {
				$this->row_count = count($value);
			}
		}
	}

	/**
	* Prepare sortby class value, by stripping/replacing invalid chars. For use internally.
	*
	* @param string $string String to strip for sortby usage.
	* @return string Stripped string.
	*/
	function prepareSortby($string) {
		$string = str_replace(" ", "_", strtolower($string));
		return $string;
	}

	/**
	* Checks for value in multidimentional array
	* Step by Step, checking for existance for each step
	* Indexes passed as sperarete parameters.
	*
	* @param array $element base Array.
	* @param strings Optional índexes to check.
	* @return string|false Value or false if it doesn't exist.
	*/
	function getIndexValue($element) {
		$args = func_get_args();
		if(isset($element)) {
			for($i = 1; $i < count($args); $i++) {
				if(isset($element[$args[$i]])) {
					$element = $element[$args[$i]];
				}
				else {
					return false;
				}
			}
			return $element;
		}
		else {
			return false;
		}
	}

	/**
	* Create definition classname for easier insertion in existing class attribute.
	*
	* @param string $definition Definition identifier
	* @param string $value Definition value
	* @return string Class definition
	*/
	function makeClassDefinition($definition, $value) {
		if($value) {
			return $definition.':'.$value;
		}
		else {
			return '';
		}
	}

	/**
	* Build HTML table based on definitions
	*
	* @return string HTML table
	*/
	function build() {
		global $HTML;

		$this->setColumnCount();
		$this->setRowCount();

		$class = $HTML->makeAttribute("class", "table", $this->init);
		$id = $HTML->makeAttribute("id", $this->id);

		$_ = '';
		$_ .= '<table'.$class.$id.'>';

		// build header
		$_ .= '<tr>';
		for($i = 0; $i < $this->column_count; $i++) {

			$value = $this->getIndexValue($this->headers, "value", $i);
			$classname = $this->getIndexValue($this->headers, "classname", $i);
			$column_type = $this->getIndexValue($this->column_types, "type", $i);

			$search = $this->getIndexValue($this->headers, "search", $i);

			if($search && $this->column_count == 1) {
				$class = $HTML->makeAttribute("class", $classname, "onecol");
			}
			else {
				$class = $HTML->makeAttribute("class", $classname);
			}


			// if column type is checkbox
			if($column_type && $column_type == "checkbox") {
				$_ .= '<th class="selectall"><input type="checkbox" class="checkbox" name="table:selectall" value="" /></th>';
			}
			// if search
			else if($search && $this->column_count == 1) {
				$_ .= '<th'.$class.'><input type="text" name="table:search" value="" />'.$value.'</th>';
			}
			else if($search) {
				$_ .= '<th'.$class.'><input type="text" name="table:search" value="'.$value.'" /></th>';
			}
			// otherwise basic text
			else {
				$_ .= '<th'.$class.'>'.$value.'</th>';
			}
		}
		// if table is incremental, add column for buttons
		if($this->table_type == "incremental") {
			$_ .= '<th colspan="2"></th>';
		}
		$_ .= '</tr>';
		
		// build rows
		for($i = 0; $i < $this->row_count; $i++) {
			$classname = $this->getIndexValue($this->row_links, $i);
//			print $classname;
			if(!$classname) {
//				print $classname;
				$classname = $this->getIndexValue($this->row_types, "classname");
//				print $classname;
			}
			// add linkto value (deprecated)
			Page::codeError("add linkto value (deprecated)");

			$linkto = $this->getIndexValue($this->row_types, "linkto", $i);
			if($linkto) {
				$linkto = $this->makeClassDefinition("linkto", $linkto);
			}
			// add status value
			$status = $this->getIndexValue($this->row_types, "status", $i);
			if($status) {
				$status = $this->makeClassDefinition("status", $status);
			}
			// add id value
			$id = $this->getIndexValue($this->row_types, "id", $i);
			if($id) {
				$id = $this->makeClassDefinition("id", $id);
			}
			$class = $HTML->makeAttribute("class", "tr".($i%2), $classname, $linkto, $status, $id);

			$_ .= '<tr'.$class.'>';

			// build cells
			for($u = 0; $u < $this->column_count; $u++) {
				$value = $this->getIndexValue($this->columns, $u, $i);
				$classname = $this->getIndexValue($this->column_types, "classname", $u);
				$column_type = $this->getIndexValue($this->column_types, "type", $u);

				// add sortby value
				$sortby = $this->getIndexValue($this->headers, "sortby", $u);
				if($sortby) {
					$sortby = $this->makeClassDefinition("sortby", $this->prepareSortby($value));
				}

				$class = $HTML->makeAttribute("class", $classname, $sortby);

				// type is checkbox
				if($column_type == "checkbox") {
					// if type is incremental, add increment info to classname
					if($this->table_type == "incremental") {
						$name = $HTML->makeAttribute("name", $this->getIndexValue($this->columns, $u, 0)."[$i]");
						$element_class = $HTML->makeAttribute("class", "name:".$this->getIndexValue($this->columns, $u, 0));
					}
					else {
						$name = $HTML->makeAttribute("name", $this->getIndexValue($this->columns, $u, 0, $i));
						$element_class = "";
					}
					$checked = $HTML->makeAttribute("checked", ($this->getIndexValue($this->columns, $u, 1, $i) ? "checked" : false));
					$_ .= '<td'.$class.'><input type="checkbox" class="checkbox" value="1"'.$name.$checked.$element_class.' /></td>';
				}
				// type is índent
				else if($column_type == "indent") {
					$indent = $this->getIndexValue($this->column_types, "indent", $u, $i);
					if($indent !== false) {
						$indent = "indent_".$indent;
					}
					else {
						$indent = false;
					}
					// remake class attribute
					$class = $HTML->makeAttribute("class", $classname, $sortby, $indent);
					$_ .= '<td'.$class.'>'.$value.'</td>';
				}
				// type is input
				else if($column_type == "input") {
					// if type is incremental, add increment info to classname
					if($this->table_type == "incremental") {
						$name = $HTML->makeAttribute("name", $this->getIndexValue($this->columns, $u, 0)."[$i]");
						$element_class = $HTML->makeAttribute("class", "name:".$this->getIndexValue($this->columns, $u, 0));
					}
					else {
						$name = $HTML->makeAttribute("name", $this->getIndexValue($this->columns, $u, 0, $i));
						$element_class = "";
					}
					$max_length = $HTML->makeAttribute("maxlength", $this->getIndexValue($this->columns, $u, 2));
					$value = $HTML->makeAttribute("value", $this->getIndexValue($this->columns, $u, 1, $i));
					$_ .= '<td'.$class.'><input'.$name.$value.$element_class.$max_length.' /></td>';
				}
				// type is select
				else if($column_type == "select") {
					// if type is incremental, add increment info to classname
					if($this->table_type == "incremental") {
						$name = $HTML->makeAttribute("name", $this->getIndexValue($this->columns, $u, 0)."[$i]");
						$id = $HTML->makeAttribute("id", $this->getIndexValue($this->columns, $u, 0)."[$i]");
						$element_class = $HTML->makeAttribute("class", "name:".$this->getIndexValue($this->columns, $u, 0));
					}
					else {
						$name = $HTML->makeAttribute("name", $this->getIndexValue($this->columns, $u, 0, $i));
						$id = $HTML->makeAttribute("id", $this->getIndexValue($this->columns, $u, 0)."[$i]");
						$element_class = "";
					}

					$update = $HTML->makeAttribute("onchange", $this->getIndexValue($this->columns, $u, 4));

					// if row contains checkbox :disable row if unchecked 
					$checks = array_search("checkbox", $this->getIndexValue($this->column_types, "type"));
					$select_enabled = $checks !== false ? ($this->getIndexValue($this->columns, $checks, 1, $i) ? "" : $HTML->makeAttribute("disabled", "disabled")) : "";

					$_ .= '<td'.$class.'><select'.$name.$select_enabled.$element_class.$id.$update.'>';

					$option_values = $this->getIndexValue($this->columns, $u, 2);
					$option_texts = $this->getIndexValue($this->columns, $u, 3);
					// check for individual select options
					if(is_array($this->getIndexValue($option_values, $i))) {
						$option_values = $this->getIndexValue($option_values, $i);
						$option_texts = $this->getIndexValue($option_texts, $i);
					}
					for($o = 0; $o < count($option_values); $o++) {
						$value = $HTML->makeAttribute("value", $option_values[$o]);
						$selected = $HTML->makeAttribute("selected", ($this->getIndexValue($this->columns, $u, 1, $i) === $option_values[$o] ? "selected" : false));
						$_ .= '<option'.$value.$selected.'>'.$option_texts[$o].'</option>';
					}
					$_ .= '</select></td>';
				}
				// otherwise basic text
				else {
					$_ .= '<td'.$class.'>'.$value.'</td>';
					//$_ .= '<td'.$class.'>'.substr($value, 0, 120).'</td>';
				}
			}
			// if type is incremental, add increment/decrement buttons to row
			if($this->table_type == "incremental") {
				$_ .= '<td class="incremental"><span class="clickable incremental:remove" title="'.$this->translate("remove").'">&nbsp;</span></td>';
				$_ .= '<td class="incremental"><span class="clickable incremental:add" title="'.$this->translate("add").'">&nbsp;</span></td>';
			}
			$_ .= '</tr>';
		}

		$_ .= '</table>';

		// very simple formatting (for debugging)
		//$_ = preg_replace("/(<\/\w+>)/", "\\1\n",$_);

		return $_;
	}

}

?>