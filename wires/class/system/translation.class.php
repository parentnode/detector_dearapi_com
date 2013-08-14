<?php

//include_once("class/system/xml.class.php");
include_once("class/system/query.class.php");


class Translation extends Query {

	//private $file;
	//public $translations = array();
	//private $translationsXML;
	//private $language;

	/**
	* Get XML doc with translations for current template or class
	*/
	function __construct() {

	}

	function addTranslation($file) {

		// create translation DOM on if required
		if(!isset($this->translations)) {
			$this->translations = new DOMDocument('1.0', 'UTF-8');
			$this->translations->loadXML('<?xml version="1.0" encoding="utf-8"?><page name="global" lang="'.Session::getLanguageISO().'"></page>');
			$this->translations->schemaValidate(FRAMEWORK_PATH."/library/translations/page.xsd");
		}

		list($path, $file) = removeKnownPaths($file);
		/*
		$file = realpath($file);

		$item->url = preg_replace("/\A\/admin/", "", removeKnownPaths($url, true));

		if(strpos($file, LOCAL_PATH) !== false) {
			$file = str_replace(LOCAL_PATH, "", $file);
			$path = LOCAL_PATH;
		}
		else if(strpos($file, REGIONAL_PATH) !== false) {
			$file = str_replace(REGIONAL_PATH, "", $file);
			$path = REGIONAL_PATH;
		}
		else if(strpos($file, GLOBAL_PATH) !== false) {
			$file = str_replace(GLOBAL_PATH, "", $file);
			$path = GLOBAL_PATH;
		}
		else if(strpos($file, FRAMEWORK_PATH) !== false) {
			$file = str_replace(FRAMEWORK_PATH, "", $file);
			$path = FRAMEWORK_PATH;
		}
		else {
			return;
		}
*/
		$file = "$path/library/translations$file.".strtolower(Session::getLanguageISO()).".xml";

		if(!file_exists($file)) {
			return;
		}
		else {
			$translation_xml = new DOMDocument('1.0', 'UTF-8');
			$translation_xml->load($file);

			$translations = $translation_xml->getElementsByTagName('translation');
			foreach($translations as $translation) {

				if(!$this->translations->getElementById($translation->getAttribute("id"))) {

					try {
						$node = $this->translations->importNode($translation, true);
						$this->translations->firstChild->appendChild($node);
						$this->translations->schemaValidate(FRAMEWORK_PATH."/library/translations/page.xsd");
					}
					catch (DOMException $e) {
						//print $e;
					}
				}
			}
		}

	}

	function translate($string_id, $max_length=false) {
		if($this->translations->firstChild->hasChildNodes()) {
			$this->counter = 0;
			
			$scope_id = preg_replace('/(###)([^###]*)(###)/e', '$this->replace(\'$2\')', strToLower($string_id));
			$scope_id = preg_replace('/[\.\:\ \?\!\;\/-]/', '_', $scope_id);

//			print $scope_id."<br>";
			$node = $this->translations->getElementById($scope_id);
			if($node) {
				return preg_replace('/(###)([^###]*)(###)/e', '$this->restore(\'$2\')', $node->firstChild->nodeValue);
			}

			// bad ids (windows)
			$translations = $this->translations->getElementsByTagName('translation');
			foreach($translations as $node) {
				if($node->getAttribute("id") == $scope_id) {
					return preg_replace('/(###)([^###]*)(###)/e', '$this->restore(\'$2\')', $node->firstChild->nodeValue);
				}
			}

		}
		return preg_replace('/(###)([^###]*)(###)/e', '$this->replaceUntranslated(\'$2\')', $string_id);
	}

	/**
	* Restore the values to their original state
	*/
	function restore($index) {
		return isset($this->replacements[$index]) ? $this->replacements[$index] : "";
	}

	/**
	* Replace value for untranslated content
	*/
	function replaceUntranslated($value) {
		return $value;
	}

	/**
	* Replace var holders with id equivalents
	*/
	function replace($value) {
		$this->replacements[$this->counter] = $value;
		return "VAR".+$this->counter++;
	}

	/**
	* Create replacement value
	*/
	/*
	function replace($value) {
		$this->replacements[$this->counter] = $value;
		return "VAR".$this->counter++;
	}
	*/

	/**
	* Get replacement value
	*/
	/*
	function restore($index) {
		return isset($this->replacements[$index]) ? $this->replacements[$index] : "";
	}
	*/

}

?>