<?php

class TranlationToolView extends Translation {

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {
		$this->addTranslation(__FILE__);
	}


	/**
	* List translation languages
	*/
	/*
	function listItems($link=false, $validate=false) {
//		global $HTML;

		// get items
//		$this->createTranslationXMLFiles();

		$items = $this->languageClass->getItems();
		$_ = $this->head($this->translate("Translation languages"));
		$_ .= Generic::listItemsExtended($link, $validate, $items["id"], array($items["values"], array()), array("Language name", "search"), array("max", "search acenter"));

		return $_;
	}
	*/


/*
	function viewItem() {
		global $HTML;
		global $id;

		$translate = $this->findTranslations($id);

		$language = $this->languageClass->getItemName($id);


		$_ = '';
		$_ .= $this->head($this->translate("###$language### translations"));
		$_ .= $this->inputHidden("language_id", $id);


//		$translation_array = $this->getMergedFile($id);
		
		$table = $HTML->table();
		$table->setHeader(0, $this->translate("Text ID"), "semi");
		$table->setHeader(1, $this->translate("Search"), "search", "semi");
		
		$table->setColumnClass(0, "semi");
		$table->setColumnClass(1, "semi");
*/		
/*		
		
		$org_translation_array = $this->merged_translation_en ? $this->merged_translation_en : $this->getMergedFile("en");
		
		$word_count = 0;
		$all_strings = '';
		
		if($translation_array) {
			foreach( $org_translation_array as $original => $length) {
				if($this->show($original)) {
					
					//$word_count = $word_count + $this->getWordCount($original);
					
					foreach($length as $max_len => $translation) {
						$column_0[] = $original . ($max_len ? "($max_len)" : "").($this->hasHistory($original, (isset($translation_array[$original][$max_len]) ? $translation_array[$original][$max_len] : ""), $id, $max_len) ? "(H)" : "");
						if(isset($translation_array[$original][$max_len])) {
							$translation = $translation_array[$original][$max_len];

							$column_1[] = $translation;

							$ids[] = urlencode($original)."-#-".urlencode($translation)."-#-".$max_len; 
						}
						else {
							$ids[] = urlencode($original)."-#-0-#-".$max_len; 
							$column_1[] = "-";
						}
						$status[] = "edit_single";
					}
				}
			}
		}
		else {
			foreach( $org_translation_array as $original => $length) {
				foreach($length as $max_len => $translation) {
					if($this->show($original)) {
						//$word_count = $word_count + $this->getWordCount($original);
						
						$column_0[] = $original  . ($max_len ? "($max_len)" : "") . ($this->hasHistory($original, $translation, $id, $max_len) ? "(H)" : "");
						$column_1[] = "-"; 

						$ids[] = urlencode($original)."-#-0-#-".$max_len; 
						$status[] = "edit_single";
					}
				}
			}
		}
		
		$translation_file_table->setRowStatus($status);
		$translation_file_table->setRowId($ids);
		//$_ .= $HTML->block("Word count", $word_count);
*/
/*
		foreach($translate as $translation_id => $translations) {
			$columns[0][] = $translation_id;
			$columns[1][] = $translations;

			$ids[] = urlencode(strtolower(str_replace(" ", "_", $translation_id))); 
			$status[] = "edit_single";
		}

		$table->setRowStatus($status);
		$table->setRowId($ids);

		$table->setColumnValues($columns[0], $columns[1]);
		$_ .= $table->build();
		return  $_;
	}
*/

	/*
	function editItem() {
		global $HTML;
		global $id;
		global $page;


		$language = $this->languageClass->getItemName($id);

		$_ = '';
		$_ .= $HTML->head($this->translate("Translate to ###$language###"));
		$_ .= $HTML->inputHidden("language_id", getVar("language_id"));

		// edit individually
		if($page->getStatus("edit_single")) {


			
		}
		// edit all
		else {


			
		}
*/
//		print $id."<br>";
		//print $this->getStatus()."<br>";
//		print $page->getStatus()."<br>";

		/*
		$language = $this->getLanguages(getVar("language_id"));

		$_ .= $HTML->inputHidden("id", getVar("language_id"));

		$translation_array = $this->getMergedFile(getVar("language_id"));
		$translation_file_table = $HTML->table();
		$org_translation_array = $this->getMergedFile("en");
		if(getVar("page_status") == "edit_single") {
			$text = explode("-#-", $id);
			$original = isset($text[0]) ? $text[0] : false;
			$translation = isset($text[1]) ? $text[1] : false;
			$max_length = isset($text[2]) ? $text[2] : false;
			$original = urldecode($original);
			$translation = urldecode($translation);
			
			$history = $this->getHistory($original, getVar("language_id"), $max_length);
			
			$_ .= $this->printTranslationBox($original, $translation, $max_length);
			
			if((isset($history["id"]) && count($history["id"]) > 1 && $translation) || isset($history["id"]) && count($history["id"]) && !$translation){
				$history_table = $HTML->table();
				$history_table->setHeader(0,"Translation", "max");
				$history_table->setHeader(1,"Search", "search");
				foreach($history["values"] as $key => $historic_translation) {
					if($historic_translation !== $translation) {
						$column_0[] = $historic_translation;
						$column_1[] = $history["timestamp"][$key];
					}	
				}
				$history_table->setColumnValues($column_0, $column_1);
				$_ .= $history_table->build();
			}
		}
		else {
			foreach( $org_translation_array as $original => $length ) {
				foreach( $length as $maxlen => $translation ) {
					$trans = ($translation_array  && isset($translation_array[$original][$maxlen]) ? $translation_array[$original][$maxlen] : false);
					$_ .= $this->printTranslationBox($original, $trans, $maxlen);
				}
			}
		}
		return  $_;
		*/
	/*}*/





	
	function show($original) {
		if($_SESSION["view"] == "front" && $this->isInFrontEnd($original)) {
			return true;
		}
		else if($_SESSION["view"] == "back" && !$this->isInFrontEnd($original)) {
			return true;
		}
		else if($_SESSION["view"] == "all") {
			return true;
		}
		return false;
	}
	

	
	function printTranslationBox($original, $translation, $maxlen=false) {
		global $HTML;
		$_ = '';
		if($this->show($original)) {
			$_ .= '<div class="c150">';
			$_ .= $HTML->block("Original", $original);
			$_ .= '</div>';
			$_ .= '<div class="c150">';
			if(strlen($original) > 25) {
				$_ .= $HTML->textArea($this->translate("Translation"), "translation[$original][$maxlen]", ($translation ? $translation : false), $maxlen); 
			}
			else {
				$_ .= $HTML->input($this->translate("Translation"), "translation[$original][$maxlen]", ($translation ? $translation : false), false, false, false, $maxlen === '0' || $maxlen === 0 ? false : $maxlen); 
			}
			$_ .= '</div>';
			$_ .= $HTML->separator();
		}
		else {
			$_ .= $HTML->inputHidden("translation[$original][$maxlen]", ($translation ? $translation : false)); 
		}
		return $_; 
	}








	
	function getWordCount($string){
		$elements = explode(" ", $string);
		return count($elements);
	}
}
?>