<?php
/**
* This file contains HTML-elements
*
*
*/
include_once("class/system/html.core.class.php");

class HTMLCore {
	
	/**
	* Make html tag attribute
	* Classes passed as speparate parameters
	* if class = false, it is not added.
	* 
	* @param string $attribute_name Name of attribute.
	* @param strings Optional strings to become value of attribute.
	* @return string Complete attribute with value.
	*/
	function makeAttribute($attribute_name) {
		$args = func_get_args();
		$attribute_value = false;
		for($i = 1; $i < count($args); $i++) {
			if($args[$i] !== false && $args[$i] !== "") {
				$attribute_value = $attribute_value !== false ? $attribute_value." ".$args[$i] : $args[$i];
			}
		}
		if($attribute_value !== false && $attribute_value != "") {
			// make sure we don't get illegal chars in value
			return ' '.$attribute_name.'="'.htmlentities(stripslashes(trim($attribute_value)), ENT_QUOTES, "UTF-8").'"';
		}
		else {
			return '';
		}
	}


	/**
	* Simple paragraph <p> element
	*
	* @param string $text Paragraph text
	* @param string $class (Optional) Paragraph classname
	* @param string $title (Optional) Paragraph title
	* @return string <p>element with specified class.
	*/
	function p($text, $class=false, $title=false) {

		if(preg_match_all('/\bstatus:([a-zA-Z,]+)\b/', $class, $matches)) {
			foreach($matches[1] as $key => $value) {
				if(!Session::getLogin()->validatePage($value)) {
					$class = str_replace($matches[0][$key], "", $class);
				}
			}
		}

		$class = $this->makeAttribute("class", $class);
		$title = $this->makeAttribute("title", $title);
		return '<p'.$class.$title.'>'.nl2br($text).'</p>';
	}

}