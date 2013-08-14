<?php

//include_once("class/system/filesystem.class.php");
include_once("class/basics/language.class.php");


include_once("class/system/xml.class.php");
include_once("class/system/generic.class.php");
//include_once("class/system/filesystem.class.php");
//include_once("class/system/translation.class.php");
//include_once("class/system/query.class.php");


include_once("class/translation/translation_tool.view.class.php");

class TranslationTool extends TranlationToolView {

/*
	public $files_to_translate;
	public $merged_translations_file;
	private $search_paths;
	
	private $xml_class;
	private $filesystem_class;
	
	private $_translation_files; //array of translation files
*/

	
	function __construct() {

		$this->addTranslation(__FILE__);
		parent::__construct();

		$this->languageClass = new Language();

		$this->translations = array();
		//$this->translations = new DOMDocument();


		$this->translations_paths = array(
			FRAMEWORK_PATH."/templates",
			FRAMEWORK_PATH."/class",
			GLOBAL_PATH."/templates", 
			GLOBAL_PATH."/class", 
			REGIONAL_PATH."/templates", 
			REGIONAL_PATH."/class", 
			LOCAL_PATH."/templates",
			LOCAL_PATH."/class"
		);


		$this->xml_class = new Xml();
//		$this->filesystem_class = new FileSystem();
		
//		$this->search_paths = array($_SERVER["FRAMEWORK_PATH"], $_SERVER["GLOBAL_PATH"]);
		
//		$this->merged_translations_file = $_SERVER["FRAMEWORK_PATH"]."/library/translations/merged_translations.en.xml";
//		$this->files_to_translate = false;
//		$this->frontend_original_strings = false;
//		$this->merged_translation_en = false;

	}


	function getTranslations($file, $language_id) {

		if(defined("LOCAL_PATH") && strpos($file, LOCAL_PATH) !== false) {
			$file = str_replace(LOCAL_PATH, "", $file);
			$path = LOCAL_PATH;
		}
		else if(defined("REGIONAL_PATH") && strpos($file, REGIONAL_PATH) !== false) {
			$file = str_replace(REGIONAL_PATH, "", $file);
			$path = REGIONAL_PATH;
		}
		else if(defined("GLOBAL_PATH") && strpos($file, GLOBAL_PATH) !== false) {
			$file = str_replace(GLOBAL_PATH, "", $file);
			$path = GLOBAL_PATH;
		}
		else if(defined("FRAMEWORK_PATH") && strpos($file, FRAMEWORK_PATH) !== false) {
			$file = str_replace(FRAMEWORK_PATH, "", $file);
			$path = FRAMEWORK_PATH;
		}
		else {
			return;
		}

		$file = strtolower("$path/library/translations$file.".$language_id.".xml");

		if(!isset($this->translations[$language_id])) {
			//print "reset<br>";
			$this->translations[$language_id] = new DOMDocument();
			$this->translations[$language_id]->loadXML('<?xml version="1.0" encoding="utf-8"?><page name="global" lang="'.$language_id.'"></page>');
			$this->translations[$language_id]->schemaValidate("$path/library/translations/page.xsd");
		}

		if(file_exists($file)) {
			$translation_xml = new DOMDocument();
			$translation_xml->load($file);
			if(!$translation_xml->schemaValidate("$path/library/translations/page.xsd")) {
				print "delete $file<br>";
				unlink($file);
			}

	//		print_r($trans->saveXML());

		    $translations = $translation_xml->getElementsByTagName('translation');
		    foreach($translations as $translation) { 
			/*
			if($translation->childNodes->length) { 
				foreach($translation->childNodes as $i) { 
					print "item->childNodes:" . $i->nodeValue . ":";
					print "item->id:" . $translation->getAttribute("id") . "<br>";
				} 
			} 
			*/

				if(!$this->translations[$language_id]->getElementById($translation->getAttribute("id"))) {
					//print "add".$this->translations[$language_id]->firstChild->nodeName."<br>";
	//				print $item->nodeName;
					try {
						$node = $this->translations[$language_id]->importNode($translation, true);
//						print "node: ";
//						print $translation->getAttribute("id") . "::" . $node->nodeValue;
//						print "<br>";
						$this->translations[$language_id]->firstChild->appendChild($node);
						$this->translations[$language_id]->schemaValidate("$path/library/translations/page.xsd");
//						print_r($this->translations);
						//print "done<br>";
					}
					catch (DOMException $e) {
						print $e;
					}
				}
			}
		}



		/*

//			print "item:" . $item . "<br>";
	        if($item->childNodes->length) { 
	            foreach($item->childNodes as $i) { 
					print "item->childNodes:" . $i->nodeValue . "<br>";
					print "item->id:" . $item->getAttribute("id") . "<br>";

//	                $headline[$i->nodeName] = $i->nodeValue; 
	            } 
	        } 

//	        $headlines[] = $headline; 
	    }
//	print_r($headlines);
		*/

		//$trans = new SimpleXMLElement(file_get_contents($file));
		
//		print $file."<br>";
		//print_r($trans->saveXML());

//		$node = $trans->getElementById("edit");
//		print "NODETEST<br>";
//		$node = $trans->getElementById("fisk");
//		print $node->firstChild->nodeValue;
//		print_r($node);
//		print "NODETEST<br>";
		
//		->firstChild->textContent."<br>";
//		print $trans->getElementById('edit')->tagName."<br>";
		
		//print_r($translations->saveXML());

		//print "<br>getting:" . $file . "::". $language_id ."<br>";
		//print_r($this->translations);
	}

	function getTranslation($language_id, $id) {
//		print $id."<br>";
//		print $this->translations[$language_id]->saveXML();
		if(isset($this->translations[$language_id])) {
//			print $this->translations[$language_id]->saveXML();
			//preg_replace("/[,\.-]/", "_", strtolower())
			$node = $this->translations[$language_id]->getElementById(str_replace(" ", "_", strtolower($id)));
		//	print "id:$id:$node<br>";
			if($node) {
		//		print $node->firstChild->nodeValue;
				return $node->firstChild->nodeValue;
			}

		}
		return $id;
	}

	/**
	*
	*/
	function findTranslations($language_id) {

		$files = array();
		$translate = array();

		foreach($this->translations_paths as $path) {
			$files = FileSystem::folderIterator($path, "", array(), false, $files);
		}

//		print_r($files);

		foreach($files as $file) {
			// get translate strings
			$string = file_get_contents($file);
			preg_match_all("/translate\(\"([^\"]*)[^\d\)]*([\d]*)/",  $string, $matches, PREG_SET_ORDER);

			// get existing translations
			$this->getTranslations($file, $language_id);

			// [text_id][max_length] = translation
			foreach($matches as $translation) {
				if(!array_search($translation[1], $translate)) {
					$translation[2] = $translation[2] ? "_".$translation[2] : "";
					$translation[1] = $this->replaceVariables($translation[1]);
					//$translate[$language_id][$translation[1]][$translation[2]] = 1;
//					print $translation[1];
					$translate[$translation[1].$translation[2]] = $this->getTranslation($language_id, $translation[1]);

/*
					$translations[] = $translation[1];
					if($translation[2] && !array_search($translation[2], $translations_max_length[$file])) {
						$translations_max_length[$translation[1]][] = $translation[2];
					}
					*/
				}
			}

//			print_r($translations_max_length);

		}
//		print "this->trans:<br>";
//		print $this->translations[$language_id]->saveXML();

//		print_r($translate);

		return $translate;

		//$translation_files = array();
		
	}

	function replace($var) {
		return "###".str_replace("$", "", $var)."###";
	}

	function replaceVariables($string) {
		return preg_replace("/(###)([^#{3}]*)(###)/e", '$this->replace(\'$2\')', $string);
	}






	/**
	* This function finds all files that uses Tranlation, finds the strings that is to be translated,
	* and stores them in a file, named like it self, prefixed '.en.xml', and is saved in library/translations in a subfolder matching matching 
	* the original files placement relative to it's code level
	*
	* @return array Array of paths to translation files    
	*/
	function createTranslationXMLFiles($merge=true){
		//global $FILESYSTEM;
		
		$translation_files = array();
		
		foreach( $this->search_paths as $search_key => $root ) {
			$this->files_to_translate = false;
			//$files = $this->filesystem_class->readDir($root);
			$files = FileSystem::folderIterator($root, "", array());
			
			$this->deleteIllegalTranslations($files);
			if($this->files_to_translate == false){
				$this->findRelevantFiles($files);
			}
			if($this->files_to_translate){
				foreach( $this->files_to_translate as $key => $filepath ) {
					//FIND LIBRARY TO PLACE XML-FILE
					for ( $i=strlen($filepath)-1; $i > 0; $i--) { 
						if($filepath{$i} === "/"){
							$library = substr($filepath,0, $i)."/library";
							if(file_exists($library)){
								$region = substr($filepath,0, $i);
								$translation_folder = $region."/library/translations";
								$xml_file_path = $translation_folder.substr($filepath, strlen($region), strlen($filepath)).".en.xml";	
								if(file_exists($xml_file_path)) {
									unlink($xml_file_path);
								}
								if($this->createXmlFile($filepath, $xml_file_path, substr($filepath, strlen($region), strlen($filepath)))) {
									$translation_files[] = $xml_file_path;
								}
								break;
							}
						}
					}
				}
			}
		}
		if($merge){
			$this->mergeXmlFiles($translation_files);
		}
		$this->_translation_files = $translation_files;
		return $translation_files;
	}
	
	/**
	* This function reads through a file, finds all the calls for Translation::translate, 
	* grabs the string to translate and stores it in a xml-file that is saved in library/translations . xml_file_path
	*
	* @param String $file_to_parse Full path to the file to parse
	* @param String $xml_file_path Full path to the library where in the xml-file is to be saved
 	* @param String $file_to_parse_relative Relative path to the file (full path - the first path that is the path to the code leven) 
	*
	* @return bool
	*/
	function createXmlFile($file_to_parse, $xml_file_path, $file_to_parse_relative) {
		$string = file_get_contents($file_to_parse);
		preg_match_all("/(translate\(\")([^\"]*)([^\d\)]*)([\d]*)/",  $string, $translation_strings, PREG_SET_ORDER);
		if(is_array($translation_strings) && isset($translation_strings[0])) {
			FileSystem::mkdirr($xml_file_path);
			// create file with base structure
			$fp = fopen($xml_file_path, "a+");
			fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>');
			fwrite($fp, "\n" . '<page name="'.$file_to_parse_relative.'">');

			foreach( $translation_strings as $key => $matches) {
				$this->counter = 0;
				$string = $this->replaceVariables($matches[2]);
				$max_length = isset($matches[4]) ? $matches[4] : false;
				$this->writeTranslationXmlTag($fp, $string, $string, $max_length);
			}
			fwrite($fp, "\n" . '</page>');
			fclose($fp);
			//print "Saving file: ". $xml_file_path . "<br/>";
			return true;
		}
		return false;
	}
	
	
	/**
	*Finds all files that is to be parsed by this::createXMLFile, saves the full path to the file in $this->files_to_translate
	*/
	/*
	function findRelevantFiles($file_array, $parent_dir=false) {
		foreach( $file_array as $filepath => $file ) {
			$exploded = explode("/",$filepath);
			$filename = $exploded[count($exploded)-1];
			if(is_array($file) && $filename !== "admin" && $filename !== "config" && $filename !== "library") {
				//RECURSION
				$this->findRelevantFiles($file, $filepath);
			}
			
			//SEARCH THIS FOLDER FOR FILES TO TRANSLATE	
			if($file == "file" && substr($filepath, -3) === "php") {
				//IF FILE CONTAINS CALLS FOR TRANSLATOR, ADD TO RELEVANT FILE
				if(strstr(file_get_contents($parent_dir."/".$filepath), "->translate"."(") !== false) {
					$this->files_to_translate[] = $parent_dir."/".$filepath;
				}
			}
		}
	}
	*/
	
	/**
	* Finds all translation files and merges them into one xml file only containing unike values 
	* @param array $translation_files Array of paths to XML translation files
	*/
	function mergeXmlFiles($translation_files) {
		$merged_file_path = $this->merged_translations_file;

		$translation_array = array();

		foreach($translation_files as $key => $translation_file) {

			$temp_translation_array = $this->xml_class->parse($translation_file);
			foreach( $temp_translation_array["PAGE"]["children"] as $temp_key => $value ) {	
				$original = $value["ELEMENT"]["children"][0]["ORIGINAL"]["value"];
				$translation = $value["ELEMENT"]["children"][1]["TRANSLATION"]["value"];
				$length = $value["ELEMENT"]["children"][0]["ORIGINAL"]["attributes"]["MAXLEN"];
				$translation_array[$original][$length] = $translation;
			}
		}
		if(file_exists($merged_file_path)) {
			unlink($merged_file_path);
		}

		FileSystem::mkdirr(dirname($merged_file_path));
		
		$fp = fopen($merged_file_path, "a+");
		fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>');
		fwrite($fp, "\n" . '<page name="'.$merged_file_path.'">');
		
		foreach( $translation_array as $original => $translation) {
			foreach($translation as $maxlength => $value) {
				$this->writeTranslationXmlTag($fp, $this->replaceVariables($original), $this->replaceVariables($value), $maxlength);
			}
		}
		fwrite($fp, "\n" . '</page>');
		fclose($fp);
		
	}
	
	
	/**
	* returns an array containing original and translated strings in a given language, 
	* if no merged file for that language exists english translations is returned
	*
	* @param String $language_iso Language identifier following the ISO standart
	* @return array
	*/
	function getMergedFile($language_iso) {
		if($language_iso == "en" && $this->merged_translation_en != false) {
			print "getting english translation file";
			return $this->merged_translation_en;
		}
		
		$translation_array = array();
		$some_language_translation = substr($this->merged_translations_file, 0, strlen($this->merged_translations_file)-6).$language_iso.".xml";
		if(!file_exists($some_language_translation)) {
			//$some_language_translation = $this->merged_translations_file;
			return false;
		}
		//print $some_language_translation;
		$temp_translation_array = $this->xml_class->parse($some_language_translation);
		if(isset($temp_translation_array["PAGE"]["children"])) {
			foreach($temp_translation_array["PAGE"]["children"] as $temp_key => $value ) {
				$original = $value["ELEMENT"]["children"][0]["ORIGINAL"]["value"];
				$length = $value["ELEMENT"]["children"][0]["ORIGINAL"]["attributes"]["MAXLEN"];
				//$translation_array[$original]["maxlen"] = "";
				$translation_array[$original][$length] = $value["ELEMENT"]["children"][1]["TRANSLATION"]["value"];
			}
		}
		if($language_iso == "en" && $this->merged_translation_en == false) {
			$this->merged_translation_en = $translation_array;
		}
		
		return $translation_array;
	}
	
	
	/**
	* updates a merged translation xml file in a given language, 
	*
	* @param String $language_iso Language identifier following the ISO standart
	* @return true
	*/
	function updateTranslation($language_iso) {

		$translation_array = getVar("translation");
		//$lengths = getVar("length");
		$new_translation_file = substr($this->merged_translations_file, 0, strlen($this->merged_translations_file)-6).$language_iso.".xml";
		
		if(file_exists($new_translation_file)) {	
			//update single text: find the text;
			
			foreach( $translation_array as $original => $translations ) {
				foreach($translations as $maxlen => $translation) {
					$translation_file = file($new_translation_file);
					$found = false;
					if($translation){
						$_ = '';
						for($i=0; $i < count($translation_file); $i++) { 
							if(stristr($translation_file[$i], '<original maxlen="'.($maxlen ? $maxlen : "0") . '">'.$original.'</original>')){
								$_ .= "\n\t\t".'<original maxlen="'.($maxlen ? $maxlen : "0") . '">'.$this->replaceVariables($original).'</original>';
								$_ .= "\n\t\t".'<translation>'.$this->replaceVariables($translation).'</translation>';
								$_ .= "\n\t" . '</element>'."\n";
								$this->saveTranslationHistory($language_iso, $this->replaceVariables($original), $this->replaceVariables($translation), $maxlen ? $maxlen : "0");
								$found = true;
								$i = $i+2;
							}
							else if(stristr($translation_file[$i], "</page>") === false) {
								if(stristr($translation_file[$i], "<")){
									$_ .= $translation_file[$i];
								}
							}
						}
						if(!$found) {
							$_ .= "\n\t" . '<element>';
							$_ .= "\n\t\t" . '<original maxlen="'.($maxlen ? $maxlen : "0") . '">'.$this->replaceVariables($original).'</original>';
							$_ .= "\n\t\t" . '<translation>'.$this->replaceVariables($translation).'</translation>';
							$_ .= "\n\t" . '</element>';

							$this->saveTranslationHistory($language_iso, $this->replaceVariables($original), $this->replaceVariables($translation), $maxlen ? $maxlen : "0");
						}
						$_ .= "\n" . '</page>';
						unlink($new_translation_file); 
						$fp = fopen($new_translation_file, "w+");
						fwrite($fp, $_);
						fclose($fp);
					}
				}
			}
		}
		else {
			
			//Writing all translations
			FileSystem::mkdirr($new_translation_file);
			
			$fp = fopen($new_translation_file, "a+");
			fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>');
			fwrite($fp, "\n" . '<page name="'.$new_translation_file.'">');
			foreach( $translation_array as $original => $translations ) {	
				foreach($translations as $maxlen => $translation) {
					if($translation) {
						$this->writeTranslationXmlTag($fp, $this->replaceVariables($original), $this->replaceVariables($translation), $maxlen);
						$this->saveTranslationHistory($language_iso, $this->replaceVariables($original), $this->replaceVariables($translation), $maxlen? $maxlen : "0");
					}
				}
			}
			fwrite($fp, "\n" . '</page>');
		}
		
		fclose($fp);
		
		$this->splitMergedFile($language_iso);
		messageHandler()->addStatusMessage($this->translate("Translation updated"));
		return true;
	}
	
	/**
	* splits a merged xml file in a given language into separate files 
	*
	* @param String $language_iso language identifier following the ISO standart
	*
	*/
	function splitMergedFile($language_iso) {
		
		$translation_array = getVar("translation");
		//print_r($translation_array);
		//$this->xml_class->parse($translation_file);
		//$lengths = getVar("length");
		$translation_files = $this->_translation_files ? $this->_translation_files : $this->createTranslationXMLFiles(false);
		$this->_translation_files = $translation_files;
		
		foreach( $translation_files as $key => $translation_file) {
			//print "translation_file: ".$translation_file."<br/>";
			$translation_written = false;
			$org_translation_array = $this->xml_class->parse($translation_file);
			$org_file = $org_translation_array["PAGE"]["attributes"]["NAME"];
			$new_translation_file = substr($translation_file, 0, strlen($translation_file)-6).$language_iso.".xml";
			
			if(file_exists($new_translation_file)) {
				$translation = false;
				$temp_translation_array = $this->xml_class->parse($new_translation_file);
				
				unlink($new_translation_file);
				
				$fp = fopen($new_translation_file, "a+");
				fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>');
				fwrite($fp, "\n" . '<page name="'.$org_file.'">');

				foreach( $org_translation_array["PAGE"]["children"] as $temp_key => $value ) {	
					$original = $value["ELEMENT"]["children"][0]["ORIGINAL"]["value"];
					$maxlen = $value["ELEMENT"]["children"][0]["ORIGINAL"]["attributes"]["MAXLEN"];
					$translation = $value["ELEMENT"]["children"][1]["TRANSLATION"]["value"];

					if(array_key_exists($original, $translation_array) && isset($translation_array[$original][$maxlen]) && $translation_array[$original][$maxlen]) {
						$this->writeTranslationXmlTag($fp, $this->replaceVariables($original), $this->replaceVariables($translation_array[$original][$maxlen]), $maxlen);
						$translation_written = true;
					}
				}
				
				foreach( $temp_translation_array["PAGE"]["children"] as $temp_key => $value ) {	
					$original = $value["ELEMENT"]["children"][0]["ORIGINAL"]["value"];
					$maxlen = $value["ELEMENT"]["children"][0]["ORIGINAL"]["attributes"]["MAXLEN"];
					$translation = $value["ELEMENT"]["children"][1]["TRANSLATION"]["value"];
					
					if(!array_key_exists($original, $translation_array) && !isset($translation_array[$original][$maxlen]) || (array_key_exists($original, $translation_array) && !$translation_array[$original][$maxlen])) { // 
						$this->writeTranslationXmlTag($fp, $this->replaceVariables($original), $this->replaceVariables($translation), $maxlen);
						$translation_written = true;
					}
				}
				
				fwrite($fp, "\n" . '</page>');
				fclose($fp);
				if(!$translation){
					unlink($new_translation_file);
				}
			}
			else{
				FileSystem::mkdirr($new_translation_file);
				$translation = false;

				$fp = fopen($new_translation_file, "a+");
				fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>');
				fwrite($fp, "\n" . '<page name="'.$org_file.'">');

				$temp_translation_array = $this->xml_class->parse($translation_file);
				foreach( $temp_translation_array["PAGE"]["children"] as $temp_key => $value )
				{	
					$original = $value["ELEMENT"]["children"][0]["ORIGINAL"]["value"];
					$maxlen = $value["ELEMENT"]["children"][0]["ORIGINAL"]["attributes"]["MAXLEN"];
					$translation = $value["ELEMENT"]["children"][1]["TRANSLATION"]["value"];

					if(array_key_exists($original, $translation_array) && isset($translation_array[$original][$maxlen]) && $translation_array[$original][$maxlen]) {
						$translation = true;
						$this->counter = 0;
						$this->writeTranslationXmlTag($fp, $this->replaceVariables($original), $this->replaceVariables($translation_array[$original][$maxlen]), $maxlen);
					}
				}
				fwrite($fp, "\n" . '</page>');
				fclose($fp);
				if(!$translation) {
					unlink($new_translation_file);
				}
			}
		}
	}
	
	/**
	*Function used by this::createXMLFile to replace variables in translation strings 
	* @return String 
	*/
	/*
	function replace() {
		return "###".$this->counter++."###";
	}
	
	function replaceVariables($string) {
		$this->counter = 0;
		$new_string = preg_replace("/(###)([^#{3}]*)(###)/e", '$this->replace()', $string);
		return $new_string;
	}
	*/
	
	
	/**
	* searches through all files, and deletes translation files that has no matching file 
	*
	* @param array $file_array Array of files 
	*/
	function deleteIllegalTranslations($file_array, $parent_dir=false) {
		foreach( $file_array as $filepath => $file ){
			$exploded = explode("/",$filepath);
			$filename = $exploded[count($exploded)-1];
			
			if(is_array($file)) {
				//RECURSION
				$this->deleteIllegalTranslations($file, $filepath);
			}
			
			//SEARCH THIS FOLDER FOR FILES TO TRANSLATE	
			if(!is_array($file) && $file == "file" && strstr($parent_dir, "library") !== false && strstr($parent_dir, "translations") !== false) {
				if(substr($filepath,-3) === "xml") {
					if(strstr($filepath, "merged_translations") === false) {
						
						$parsed_file_array = $this->xml_class->parse($parent_dir."/".$filepath);
						if(isset($parsed_file_array["PAGE"]["attributes"]["NAME"]) && $parsed_file_array["PAGE"]["attributes"]["NAME"]) {
							if(isset($parsed_file_array["PAGE"]["children"])) {
								if(!file_exists(substr($parent_dir,0 , strpos($parent_dir, "/library")).$parsed_file_array["PAGE"]["attributes"]["NAME"])) {
									if(file_exists($filepath)) {
										unlink($filepath);
									}
								}
							}
							else{
								unlink($parent_dir."/".$filepath);
							}
						}
						else {
							unlink($parent_dir."/".$filepath);
						}
					}
					else{
						if(strlen($filepath) !== strlen("merged_translations.en.xml")) {
							unlink($parent_dir."/".$filepath);
						}
					}
				}
				else{
					unlink($parent_dir."/".$filepath);
				}			
			}
		}
	}
	
	/**
	* writes a translation-xml-tag in a given file 
	*
	* @param handle $filepointer A pointer to file to write in
	* @param String $original_text Original text
	* @param String $translation_text Translation text
	*
	*/
	private function writeTranslationXmlTag($filepointer, $original_text, $translation_text, $max_length=false) {
		fwrite($filepointer, "\n\t" . '<element>');
		fwrite($filepointer, "\n\t\t" . '<original maxlen="'.($max_length ? $max_length : "0").'">'.$original_text.'</original>');
		fwrite($filepointer, "\n\t\t" . '<translation>'.$translation_text.'</translation>');
		fwrite($filepointer, "\n\t" . '</element>');
	}
	
	function isInFrontEnd($original) {
		if(! $this->frontend_original_strings) {
			if(!$this->_translation_files) {
				$this->createTranslationXMLFiles();
			}
			foreach( $this->_translation_files as $key => $translation_file) {
				if(stristr($translation_file, "/www/")){
					$org_translation_array = $this->xml_class->parse($translation_file);
					foreach( $org_translation_array["PAGE"]["children"] as $temp_key => $value ) {	
						$this->frontend_original_strings[] = $value["ELEMENT"]["children"][0]["ORIGINAL"]["value"];
						/*
						if($original == $value["ELEMENT"]["children"][0]["ORIGINAL"]["value"]) {
							return true;
						}
						*/
					}
				}
			}
		}
		
		if(array_search($original, $this->frontend_original_strings)){
			return true;
		}
		
		return false;
	} 
	
	/**
	* saves translation history into db
	*
	* @param string $language_iso Language iso
	* @param string $original Original text 
	* @param string $translation Translation
	* @param string $maxlen Maximum length for translation			 
	*
	*/
	function saveTranslationHistory($language_iso, $original, $translation, $maxlen){
		$this->sql("SELECT id FROM ".UT_TRA_HIS." WHERE original = '$original' AND translation = '$translation' AND max_length = '$maxlen' AND language_iso = '$language_iso'");
		
		if($this->getQueryCount() < 1){
			$this->sql("INSERT INTO ".UT_TRA_HIS." VALUES (DEFAULT, '$language_iso', '$original', '$translation', '$maxlen', '".$_SESSION["loginClass"]->getUserId()."', CURRENT_TIMESTAMP)");
		}
	}
	
	function hasHistory($original, $translation, $language_id, $maxlen) {
		$this->sql("SELECT id FROM ".UT_TRA_HIS." WHERE original = '$original' AND language_iso = '$language_id' AND translation != '$translation' AND max_length = '$maxlen'");
		
		if($this->getQueryCount() > 0) {
			return true;
		}
		
		return false;
	}
	
	function getHistory($original, $language_id, $maxlen){
		$translations = array();
		$this->sql("SELECT history.id, history.translation, history.timestamp, users.name  FROM ".UT_TRA_HIS." AS history, ".UT_USE." AS users WHERE users.id = history.user_id AND history.original = '$original' AND history.language_iso = '$language_id' AND max_length = '$maxlen' ORDER BY timestamp");
		
		for ($i=0; $i < $this->getQueryCount(); $i++) { 
			$translations["id"][] = $this->getQueryResult($i, "id"); 
			$translations["values"][] = $this->getQueryResult($i, "translation"); 
			$translations["user_name"][] = $this->getQueryResult($i, "name"); 
			$translations["timestamp"][] = $this->getQueryResult($i, "timestamp"); 
		}
		return $translations;
	}
	
	
	/**
	* Returns an array of languages or if id is set, a string that contains a language name 
	*
	* @param string $id Optional parameter, language identifier following the ISO standart
	* @return array|String
	*/
	/*
	function getLanguages($id=false) {
		$language_class = new Language();
		$languages = $language_class->getItems();
//		foreach($languages["id"] as $key => $language_id) {
//			$languages["id"][$key] = $language_class->getIsoCode($language_id);
//		}
		if($id){
			$index = array_search($id, $languages["id"]);
			if($index){
				return $languages["values"][$index];
			}
			else{
				return false;
			}
		}
		else{
			return $languages;
		}
	}
	*/

}
?>