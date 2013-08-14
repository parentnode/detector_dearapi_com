<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
<?
	$language_id = getVar("language_id");
	$language = $this->getTemplateObject()->languageClass->getItemName($language_id);

	// populate translations array
	$translate = $this->getTemplateObject()->findTranslations($language_id);
	// find required translation
	$translation = $this->getTemplateObject()->getTranslation($language_id, $id);
	
//	print $this->getTemplateObject()->translations[$language_id]->saveXML();
?>
	<fieldset>
		<?= $HTML->head($this->translate("Translate to ###$language###")) ?>
		<?= $HTML->inputHidden("language_id", $language_id) ?>

		<?= $HTML->inputHidden("id", $id) ?>
		<?= $HTML->inputHidden("page_status", "update") ?>
		<?//= $this->getTemplateObject()->editItem() ?>

		<div class="c">
			<div class="ci50">
				<?= $this->block($this->translate("Text id"), $id) ?>
				<? print_r($translate) ?>
			</div>
			<div class="ci50">
				<?= $this->input($this->translate("###$language### translation"), $id, $translation) ?>
			</div>
		</div>
<?

//				$language = $this->languageClass->getItemName($id);

				$_ = '';

				// edit individually
				if($this->getStatus("edit_single")) {



				}
				// edit all
				else {



				}

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
				?>

		<?= $HTML->smartButton($this->translate("Update"), "update", "update", "fright key:s") ?>
		<?= $HTML->smartButton($this->translate("Cancel"), "cancel", "cancel", "fleft key:esc") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>