<?php
/**
* This file contains HTML-elements
*
*
*/
include_once("class/system/html.core.class.php");

class HTML extends HTMLCore {

	private $show_details = 0;

	/**
	* Set detail state
	* Defines whether validation should be indicated
	*/
	function details($state) {
		$this->show_details = $state;
	}



	/**
	* Label can be an array containing label value, class and error
	* This function checks for valid values and returns comformed array
	* 
	* @param string $label Label, optional label array with error, class and validation info.
	* @return string Conformed label array.
	*/
	function makeLabel($label, $class=false) {
		if(is_array($label)) {
			if(!$this->show_details) {
				$label_array[0] = "";
			}
			else {
				$label_array[0] = (isset($label["error"]) && $label["error"]) ? '<span class="error">'.$label["error"].'</span>' : (isset($label["validation"]) && $label["validation"] ? '<span>'.$label["validation"].'</span>' : '');
			}
			$label_array[1] = (isset($label["class"]) && $label["class"]) ? $this->makeAttribute("class", $label["class"], $class) : '';
			$label_array[2] = $label["value"];
		}
		else {
			$label_array[0] = "";
			$label_array[1] = $this->makeAttribute("class", $class);
			$label_array[2] = $label;
		}
		return $label_array;
	}

	/**
	* Simple header <h$type> element
	*
	* @param string $text Head text
	* @param string $type (Optional) Header type (default $type=1)
	* @param string $class (Optional) Header class
	* @return string <h$type [class="$class"]>element.
	*/
	function head($text, $type=1, $class=false) {
		$id = $this->makeAttribute("id", superNormalize($text));
		$class = $this->makeAttribute("class", $class);
		return '<h'.$type.$class.$id.'>'.$text.'</h'.$type.'>';
	}

	

	/**
	* Advanced label <label> element
	*
	* @param string $text Paragraph text
	* @param string $class (Optional) Paragraph classname
	* @return string <p>element with specified class.
	*/
	function label($label, $for="", $class="") {
		$for = $this->makeAttribute("for", $for);
		//$for ? ' for="'.($id ? $id : $name).'"';

		list($label_error, $label_class, $label_value) = $this->makeLabel($label, $class);
//		print $label_class;
		return $label ? '<label'.$for.$label_class.'>'.$label_value.$label_error.'</label>' : '';
	}

	/**
	* Simple image <img> element
	*
	* @param string $src Image source
	* @param string $alt Alt text
	* @param string $class (Optional) Image classname
	* @return string <img>element with specified class.
	*/
	function img($src, $alt, $title=false, $class=false) {

		$src = $this->makeAttribute("src", $src);
		$alt = $this->makeAttribute("alt", $alt);
		$title = $this->makeAttribute("title", $title);
		$class = $this->makeAttribute("class", stringOr($class, "picture"));

		//$class = ' class="' . ($class ? $class : 'picture') .'"';
		return '<img'.$src.$class.$alt.$title.' />';
	}

	/**
	* Editable image <img> element
	*
	* @param string $src Image source
	* @param string $alt Alt text
	* @param string $class (Optional) Image classname
	* @return string <img>element with specified class.
	*/
	function editImg($label, $validate, $name, $id, $alt="", $src, $title=false, $class=false) {

//		$class = $this->makeAttribute("class", stringOr($class, "picture"));
//		$title = $this->makeAttribute("title", $title);
//		$class = ' class="' . ($class ? $class : 'picture') .'"';

		$_ = '';
		$_ .= '<div class="init:editImage type:input" id="'.$validate.':'.$name.':'.$id.'">';

			$_ .= $this->img($src, $alt, $title, $class);


//		$_ .= $this->p($value, "", $this->translate("Click to edit"));
//		$_ .= $this->inputFile($label, $name, $class);

		$_ .= '</div>';

		return $_; //'<img src="'.$src.'"'.$class.' alt="'.$alt.'" title="" />';
	}

	/**
	* Simple text block with label header, p body
	*
	* @param string|array $label (Optional) Label or label array with indexes "error", "class" and "value"
	* @param string $text (Optional) Body text
	* @return string <label /><p /> block.
	*/
	function block($label=false, $text=false) {
		$_ = '';
		$_ .= $this->label($label);
		$_ .= $this->p($text);
		return $_;

	}

	/**
	* Simple text block with label header, p body
	*
	* @param string|array $label (Optional) Label or label array with indexes "error", "class" and "value"
	* @param string $text (Optional) Body text
	* @return string <label /><p /> block.
	*/
	function htmlView($label=false, $text) {
		list($label_error, $label_class, $label_value) = $this->makeLabel($label);
		$_ = '';
		$_ .= $label ? '<label'.$label_class.'>'.$label_value.$label_error.'</label>' : '';
		$_ .= '<div class="htmlView">'.$text.'</div>';

		return $_;
	}

	/**
	* Basic input element
	*
	* @param string|array $label (Optional) Input label or label array with indexes "error", "class" and "value"
	* @param string $name Input element name.
	* @param string $value (Optional) Input element value.
	* @param string $class (Optional) Input classname. (disabled automatically adds disabled="disabled", readonly automatically adds readonly="readonly").
	* @param string $id (Optional) Input element id.
	* @param string $special (Optional) Special feature like onclick event.
	* @param integer $max_length (Optional) max lenght.
	* @return string Input element
	*
	*
	* Alternate usage:
	* Short hand function from extending objects
	*
	* @param string $index index in object vars
	* @param string $class Input classname. (disabled automatically adds disabled="disabled", readonly automatically adds readonly="readonly").
	* @param string $id (Optional) Input element id.
	* @param integer $max_length (Optional) max lenght.
	* @return string Input element
	*/
	function input($index=false, $class=false, $id=false, $max_length=false, $old_id=false, $old_special=false, $old_max_length=false) {
		$_ = '';

		if(isset($this->varnames)) {

			$for = ($id ? $id : $index);
			$id = $this->makeAttribute("id", ($id ? $id : $index));
			$value = $this->makeAttribute("value", ($this->vars[$index] ? $this->vars[$index] : ''));
			$name = $this->makeAttribute("name", $index);
			$class_att = $this->makeAttribute("class", "text", $class);

			$disabled = strstr($class, "disabled") ? $this->makeAttribute("disabled", $disabled) : '';
			$readonly = strstr($class, "readonly") ? $this->makeAttribute("readonly", $readonly) : '';
			$max_length = $this->makeAttribute("maxlength", $max_length);

			$_ .= $this->label($this->varnames[$index], $for, $class);
			$_ .= '<input type="text"'.$name.$id.$value.$class_att.$disabled.$readonly.$max_length.' />';



			//$label = $this->varnames[$index];
			//print $label;
//			print_r( $this->varnames[$index]);
			return $_;
		}
		
		$label = $index;
		$name = $class;
		$value = $id;
		$class = $max_length;
		$id = $old_id;
		$special = $old_special;
		$max_length = $old_max_length;

		list($label_error, $label_class, $label_value) = $this->makeLabel($label);
		$for = ' for="'.($id ? $id : $name).'"';
		$id = ' id="'.($id ? $id : $name).'"';
//		$value = ' value="'.($value ? $value : '').'"';
		$value = $this->makeAttribute("value", $value);
		$name = ' name="'.$name.'"';
		$disabled = strstr($class, "disabled") ? ' disabled="disabled"' : '';
		$readonly = strstr($class, "readonly") ? ' readonly="readonly"' : '';
		$max_length = $this->makeAttribute("maxlength", $max_length);
		$class = ' class="text'.($class ? " $class" : '').'"';

		// special feature, like onupdate
		$special = $special ? ' '.$special : '';

		$_ .= $label ? '<label'.$for.$label_class.'>'.$label_value.$label_error.'</label>' : '';
		$_ .= '<input type="text"'.$name.$id.$value.$class.$disabled.$readonly.$special.$max_length.' />';
		return $_;
	}

	/**
	* Basic input element
	*
	* @param string|array $label (Optional) Input label or label array with indexes "error", "class" and "value"
	* @param string $name Input element name.
	* @param string $value (Optional) Input element value.
	* @param string $class (Optional) Input classname. (disabled automatically adds disabled="disabled", readonly automatically adds readonly="readonly").
	* @param string $id (Optional) Input element id.
	* @param string $special (Optional) Special feature like onclick event.
	* @return string Input element
	*/
	function inputTimestamp($label, $name, $value=false, $class=false, $id=false) {

		$_ = '';
		$_ .= $this->label($label, ($id ? $id : $name));

		$id = $this->makeAttribute("id", $id ? $id : $name);
		$value = $this->makeAttribute("value", $value);
		$name = $this->makeAttribute("name", $name);

		$disabled = strstr($class, "disabled") ? ' disabled="disabled"' : '';
		$readonly = strstr($class, "readonly") ? ' readonly="readonly"' : '';

		$class = $this->makeAttribute("class", "init:timestamp", $class);
		$_ .= '<input type="text"'.$name.$id.$value.$class.$disabled.$readonly.' />';

		return $_;
	}
	/**
	* Editable text / input element
	*
	* @param string|array $label (Optional) Input label or label array with indexes "error", "class" and "value"
	* @param Array $validate (Optional) Access validation for the button. Page_status value
	* @param string $name Input element name.
	* @param string $id (Optional) Input element id.
	* @param string $value (Optional) Input element value.
	* @param string $class (Optional) Input classname. (disabled automatically adds disabled="disabled", readonly automatically adds readonly="readonly").
	* @param string $max_length (Optional) Max length value
	* @return string Input element
	*/
	function editInput($label=false, $validate, $name, $id, $value="", $title=false, $class=false, $max_length=false) {
		$_ = '';

		if(Session::getLogin()->validatePage($validate)) {
			$_ .= '<div class="init:editInput type:input" id="'.$validate.':'.$name.':'.$id.'">';
			if(is_array($label) && isset($label["error"])) {
				$_ .= $this->input($label, $name, $value, $class, $id, false, $max_length);
			}
			else {
				$_ .= $this->p(stringOr($value, $title), "", $title);
			}
			$_ .= '</div>';
		}
		else {
			$_ .= $this->p($value);
		}

		return $_;
	}


	/**
	* button input element
	*
	* @param string $name Input element name.
	* @param string $value (Optional) Input element value.
	* @param string $id (Optional) Input element id.
	* @return string Hidden input element
	*/
	function inputSubmit($attributes=array()) {

		// check for required default classes
		if(isset($attributes["class"])) {
			$attributes["class"] .= !preg_match("/button/", $attributes["class"]) ? " button" : "";
			$attributes["class"] .= !preg_match("/submit/", $attributes["class"]) ? " submit" : "";
		}
		else {
			$attributes["class"] = "button submit";
		}
		
		$_ = "";
		foreach($attributes as $attribute => $value) {
			$_ .= $this->makeAttribute($attribute, $value);
		}

		return '<input type="submit"'.$_.' />';
	}

	/**
	* Hidden input element
	*
	* @param string $name Input element name.
	* @param string $value (Optional) Input element value.
	* @param string $id (Optional) Input element id.
	* @return string Hidden input element
	*/
	function inputHidden($name, $value="", $id=false) {

//		if(isset($this->varnames)) {
			$id = $this->makeAttribute("id", $id);
			$value = $this->makeAttribute("value", $value);
			$class = $this->makeAttribute("class", "hidden", $name);
			$name = $this->makeAttribute("name", $name);
			return '<input type="hidden"'.$name.$id.$value.$class.' />';
//		}

		$name = $index;
		$value = $id;
		$id = $old_id;

		$id = $id ? ' id="'.$id.'"' : '';
		$value = ' value="'.($value ? $value : '').'"';
		$name = ' name="'.$name.'"';
		$class = ' class="hidden"';

		return '<input type="hidden"'.$name.$id.$value.$class.' />';
	}

	/**
	* Password input element
	*
	* @param string|array $label (Optional) Password input label or label array with indexes "error", "class" and "value".
	* @param string $name Password input element name.
	* @param string $value (Optional) Password input element value.
	* @param string $class (Optional) Password input classname.
	* @param string $id (Optional) Password input element id.
	* @return string Password input element
	*/
	function inputPassword($label=false, $name, $value=false, $class=false, $id=false) {
		list($label_error, $label_class, $label_value) = $this->makeLabel($label);
		$for = ' for="'.($id ? $id : $name).'"';
		$id = ' id="'.($id ? $id : $name).'"';
		$value = ' value="'.($value ? $value : '').'"';
		$name = ' name="'.$name.'"';
		$class = $class ? ' class="'.$class.'"' : '';

		$_ = '';
		$_ .= $label ? '<label'.$for.$label_class.'>'.$label_value.$label_error.'</label>' : '';
		$_ .= '<input type="password"'.$name.$id.$value.$class.' />';

		return $_;
	}

	/**
	* Basic file input element
	* Form dynamically inserted with JavaScript, overriding ajax (cannot submit file with ajax)
	*
	* @param string|array $label (Optional) Fileinput label or label array with indexes "error", "class" and "value".
	* @param string $name Input element name.
	* @param string $class Fileinput classname.
	* @return string Input file element, with tricker for javascript
	*/
	function inputFile($label=false, $name, $class=false) {
		list($label_error, $label_class, $label_value) = $this->makeLabel($label);
		$name = ' name="'.$name.'"';
		$class = ' class="'.($class ? ' '.$class.'' : '').'"';
 	
		$_ = '';
		$_ .= $label ? '<label'.$label_class.'>'.$label_value.$label_error.'</label>' : '';
		$_ .= '<input type="file"'.$name.$class.' />';
		return $_;
	}

	/**
	* Basic checkbox element
	* Default value = 1
	* 
	* @param string|array $label Checkbox element label or array of labels for multible Checkbox elements.
	* @param string|array $name Checkbox element name or array of labels for multible Checkbox elements.
	* @param bool|array $checked (Optional) True for checked element(s).
	* @param string $class (Optional) Checkbox classname, appended to default value checkbox. (disabled automatically adds disabled="disabled").
	* @param string|array $id (Optional) Checkbox element id or array of ids for multible radio elements.
	* @param string $special (Optional) Special feature like onclick event.
	* @return string Checkbox element(s).
	*/
	function checkbox($label, $name, $checked=false, $class=false, $id=false, $special=false) {
		$value = ' value="1"';
		$disabled = strstr($class, "disabled") ? ' disabled="disabled"' : '';
		$class = ' class="checkbox '. ($class ? $class : '').'"';

		// special feature, like onclick
		$special = $special ? ' '.$special : '';

		$_ = '';
		$_ .= '<div class="checkbox '.$class.'">';

		// multible checkboxes in a row
		if(is_array($label)) {
			for($i = 0; $i < count($label); $i++) {
				$for = ' for="'.(is_array($id) ? $id[$i] : $name[$i]).'"';
				$id = ' id="'.(is_array($id) ? $id[$i] : $name[$i]).'"';
				$checks = is_array($checked) && $checked[$i] ? ' checked="checked"' : '';
				$names = ' name="'.$name[$i].'"';
				$labels = $label[$i];

				$_ .= '<input type="checkbox"'.$names.$id.$value.$class.$disabled.$checks.$special.' tabindex="1" />';
				$_ .= '<label'.$for.'>'.$labels.'</label>';
			}
		}
		// only one checkbox
		else {
			$for = ' for="'.($id ? $id : $name).'"';
			$id = ' id="'.($id ? $id : $name).'"';
			$checked = $checked ? ' checked="checked"' : '';
			$name = ' name="'.$name.'"';

			$_ .= '<input type="checkbox"'.$name.$id.$value.$class.$disabled.$checked.$special.' />';
			$_ .= '<label'.$for.'>'.$label.'</label>';
		}
		$_ .= '</div>';

		return $_;
	}

	/**
	* Basic radio button element
	* 
	* @param string|array $label Radio element label or array of labels for multible radio elements.
	* @param string $name Radio element name.
	* @param string|array $value Radio element value or array of values for multible radio elements.
	* @param string $checked (Optional) Value of checked element.
	* @param string $class (Optional) Radio classname, appended to default value radio. (disabled automatically adds disabled="disabled").
	* @param string|array $id (Optional) Radio element id or array of ids for multible radio elements.
	* @param string $special (Optional) Special feature like onclick event.
	* @return string Radio element(s).
	*/
	function radio($label, $name, $value, $checked=false, $class=false, $id=false, $special=false) {
		$disabled = strstr($class, "disabled") ? ' disabled="disabled"' : '';
//		$class = ' class="radio '. ($class ? $class : '').'"';
		$class = $this->makeAttribute("class", "radio", $class);

		$names = ' name="'.$name.'"';

		// special feature, like onclick
		$special = $special ? ' '.$special : '';

		$_ = '';
		$_ .= '<div'.$class.'>';

		// multible radiobuttons in a row
		if(is_array($label)) {
			for($i = 0; $i < count($label); $i++) {
				$for = ' for="'.(is_array($id) ? $id[$i] : $name.$value[$i]).'"';
				$id = ' id="'.(is_array($id) ? $id[$i] : $name.$value[$i]).'"';
				$checks = $checked == $value[$i] ? ' checked="checked"' : '';
				$values = ' value="'.$value[$i].'"';
				$labels = $label[$i];

				$_ .= '<input type="radio"'.$names.$id.$values.$class.$disabled.$checks.$special.' />';
				$_ .= '<label'.$for.'>'.$labels.'</label>';
			}
		}
		// only one radiobutton
		else {
			$for = ' for="'.($id ? $id : $name.$value).'"';
			$id = ' id="'.($id ? $id : $name.$value).'"';
			$checked = $checked == $value ? ' checked="checked"' : '';
			$value = ' value="'.($value ? $value : '').'"';

			$_ .= '<input type="radio"'.$names.$id.$value.$class.$disabled.$checked.$special.' />';
			$_ .= '<label'.$for.'>'.$label.'</label>';
		}
		$_ .= '</div>';

		return $_;
	}

	/**
	* Basic select element
	*
	* @param string|array $label (Optional) Select label or label array with indexes "error", "class" and "value".
	* @param string $name Select name.
	* @param array $values Select values and text as two dimentional array. array["id"] and array["values"]. 
	* @param integer $selected (Optional) Selected index.
	* @param array $default_value Array with default value and text. Added as first entry. array("value","text").
	* @param string $update Javascript action executed onchange event.
	* @param string $class (Optional) Select classname. (disabled automatically adds disabled="disabled", multiple automatically adds multiple="multiple").
	* @param string $id (Optional) Select element id.
	* @return string Select element.
	*/
	function select($label=false, $name, $values, $selected=false, $default_value=false, $update=false, $class=false, $id=false) {
		list($label_error, $label_class, $label_value) = $this->makeLabel($label);
		$for = ' for="'.($id ? $id : $name).'"';
		$id = ' id="'.($id ? $id : $name).'"';
		$name = ' name="'.$name.'"';
		$disabled = strstr($class, "disabled") ? ' disabled="disabled"' : '';
		$multiple = strstr($class, "multiple") ? ' multiple="multiple" size="12"' : '';
		$class = $class ? ' class="'.$class.'"' : '';
		$update = $update ? ' onchange="'.$update.'"' : '';

		$default_value = $default_value ? '<option value="'.$default_value[0].'">'.$default_value[1].'</option>' : '';

		$_ = '';
		$_ .= $label ? '<label'.$for.$label_class.'>'.$label_value.$label_error.'</label>' : '';
		$_ .= '<select '.$name.$id.$class.$disabled.$multiple.$update.'>';
		$_ .= $default_value;
		for($i = 0; $i < count($values["values"]); $i++) {
			settype($selected, "string");
			settype($values["id"][$i], "string");
			$_ .= '<option value="'.$values["id"][$i].'"'.(($selected !== false && $values["id"][$i] === "$selected") ? ' selected="selected"' : '').'>'.$values["values"][$i].'</option>';
		}
		$_ .= '</select>';

		return $_;
	}

	/**
	* Basic textarea element
	*
	* @param string|array $label (Optional) Textarea label or label array with indexes "error", "class" and "value".
	* @param string $name Textarea name.
	* @param string $value (Optional) Textarea value.
	* @param string $max_length (Optional) Max text length. (adds javascript counter).
	* @param string $class (Optional) Textarea classname.
	* @param string $id (Optional) Textarea element id.
	* @return string Textarea element.
	*/
	function textarea($label=false, $name, $value=false, $max_length=false, $class=false, $id=false) {
		list($label_error, $label_class, $label_value) = $this->makeLabel($label);
		$for = ' for="'.($id ? $id : $name).'"';
		$id = ' id="'.($id ? $id : $name).'"';
		$max_counter = $max_length ? '<span id="counter:'.$name.'">('.$max_length.')</span>' : '';
 		$max_event = $max_length ? ' onkeyup="Util.textCounter('.$max_length.',this);"' : '';
		$name = ' name="'.$name.'"';
		$class = $class ? ' class="'.$class.'"' : '';

		$_ = '';
		$_ .= $label ? '<label'.$for.$label_class.'>'.$label_value.$max_counter.$label_error.'</label>' : '';
		$_ .= '<textarea'.$name.$class.$id.$max_event.' cols="10" rows="5">'.$value.'</textarea>';

		return $_;
	}
	
	/**
	* Basic button element
	*
	* @param String $label Button text.
	* @param Array $validate (Optional) Access validation for the button. Page_status value
	* @param String $action (Optional) Button javacript action.
	* @param String $class (Optional) Button classname. (disabled automatically adds disabled="disabled").
	* @param String $title (Optional) Button title.
	* @param String $id (Optional) Button element id.
	* @param String $type (Optional) Button type. (Default = submit, if action default = button).
	* @return String Button element.
	*/
	function button($label, $validate=false, $action=false, $class=false, $title=false, $id=false, $type=false) {
		if($validate) {
			if(!Session::getLogin()->validatePage($validate)) {
//			if(Session::getLogin() && !Session::getLogin()->validatePoint($validate[0], $validate[1])) {
				return '';
			}
		}

		$class = ' class="init:button'.($class ? ' '.$class : '').'"';
		$title = $title ? ' title="'.$title.'"' : '';
		$disabled = strstr($class, "disabled") ? ' disabled="disabled"' : '';
		$id = $id ? ' id="'.$id.'"' : '';
		$type = ' type="'.($type ? $type : ($action ? 'button' : 'submit')).'"';
		$action = $action ? ' onclick="'.$action.'"' : '';
		
		return '<button'.$action.$class.$type.$id.$title.$disabled.'><span><span>'.$label.'</span></span></button>';
	}

	/**
	* Submit button element
	*
	* @param String $label Button text.
	* @param Array $validate (Optional) Access validation for the button. Page_status value
	* @param String $classname (Optional) Button classname. (disabled automatically adds disabled="disabled").
	* @return String Button element.
	*/
	function submit($label, $validate=false, $classname=false) {
		if($validate) {
			if(!Session::getLogin()->validatePage($validate)) {
				return '';
			}
		}
		$class = $this->makeAttribute("class", "button", $classname);
		$disabled = preg_match("/disabled/i", $class) ? $this->makeAttribute("disabled", "disabled") : '';
		$type = $this->makeAttribute("type=", "submit");
		return '<button'.$class.$type.$disabled.'><span><span>'.$label.'</span></span></button>';
	}

	/**
	* Smart button element
	* Interacts with enclosing form, automatically initiated with JavaScript 
	*
	* @param String $label Button text.
	* @param Array $validate Access validation for the button. Array containing $url and $page_status
	* @param String $status Form action class identifier.
	* @param String $class (Optional) Button classname. (disabled automatically adds disabled="disabled").
	* @return String Button element.
	*/
	function smartButton($label, $validate, $status, $class=false, $id=false, $title=false) {
		if($validate) {
			if(!Session::getLogin()->validatePage($validate)) {
//			if(!Session::getLogin()->validatePoint($validate[0], $validate[1])) {
//			if(Session::getLogin() && !Session::getLogin()->validatePoint($validate[0], $validate[1])) {
				return '';
			}
		}

		$id = $this->makeAttribute("id", $id);

		$class = ' class="init:button '.($class ? $class.' ' : '').'status:'.$status.'"';
		$disabled = strstr($class, "disabled") ? ' disabled="disabled"' : '';
		$type = ' type="button"'; 
		$title = $this->makeAttribute("title", $title);
		
		return '<button'.$class.$type.$disabled.$id.$title.'><span><span>'.$label.'</span></span></button>';
	}

	/**
	* Separator element
	*
	* @return string Separator element.
	*/
	function separator() {
		return '<div class="separator">&nbsp;</div>';
	}

	/**
	* clear:both element
	*
	* @param bool $break Optional break after clear element
	* @return string Clear element.
	*/
	function clear($break=false) {
		$_ = '<div class="clear"></div>';
		$_ .= $break ? '<br />' : '';
		return $_;
	}



	/**
	* @todo cleanup
	*/
	function imageList($names, $images, $item_status, $ids, $status, $validate) {
		$_ = '<ul class="imagelist init:imagelist">';
		for($i = 0; $i < count($images); $i++) {
			if($status &&  (!$validate || Session::getLogin()->validatePage($validate))) {
//				$_ .= '<li class="status:'.$status.' id:'.$ids[$i].'"><img src="'.$images[$i].'" alt="'.$names[$i].'" /><div>'.$names[$i].' ('.$item_status[$i].')</div></li>';
				$_ .= '<li class="status:'.$status.' id:'.$ids[$i].'"><img src="'.$images[$i].'" alt="'.$names[$i].'" /><div title="'.$names[$i].'">'.$names[$i].'</div><div>'.$item_status[$i].'</div></li>';
			}
			else {
//				$_ .= '<li><img src="'.$images[$i].'" alt="'.$names[$i].'" /><div>'.$names[$i].' ('.$item_status[$i].')</div></li>';
				$_ .= '<li><img src="'.$images[$i].'" alt="'.$names[$i].'" /><div>'.$names[$i].'</div><div>'.$item_status[$i].'</div></li>';
			}
		}
		$_ .= '</ul>';
		return $_;

	}

	/**
	* Create/return table object.
	* Init can be true or a initialization value, which is then appended the table along with the init value.
	* As example table("search") will create a table with class="list init search".
	*
	* @param string|bool $init Defines the initialization of the table. Default=true for basic autoinitialization.
	* @return object New HTML Table object.
	*/
	function table($init=true) {
		include_once("html.table.class.php");
		return new Table($init);
	}

}

$HTML = new HTML();

?>