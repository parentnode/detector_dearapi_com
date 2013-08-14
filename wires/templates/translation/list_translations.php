<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
<?
	$language = $this->getTemplateObject()->languageClass->getItemName($id);
	$translate = $this->getTemplateObject()->findTranslations($id);

//	print_r($this->getTemplateObject()->translations);
//	print $this->getTemplateObject()->translations[$id]->saveXML();
?>
	<fieldset>
		<?= $this->head($this->translate("###$language### translations")) ?>
		<?= $this->inputHidden("language_id", $id) ?>

		<?= $HTML->inputHidden("id", $id) ?>
		<?= $HTML->inputHidden("page_status", "edit") ?>

		<?= $this->p($this->translate("Select individual element or choose edit below the list to edit all"), "info") ?>
		<?
			$table = $HTML->table();
			$table->setHeader(0, $this->translate("Text ID"), "semi");
			$table->setHeader(1, $this->translate("Search"), "search", "semi");

			$table->setColumnClass(0, "semi");
			$table->setColumnClass(1, "semi");

			foreach($translate as $translation_id => $translations) {
				$columns[0][] = $translation_id;
				$columns[1][] = $translations;

//				$ids[] = urlencode(strtolower(str_replace(" ", "_", $translation_id))); 
				$ids[] = strtolower(preg_replace("/[ ,\.-?!]/", "_", $translation_id)); 
				$status[] = "edit_single";
			}

			$table->setRowStatus($status);
			$table->setRowId($ids);

			$table->setColumnValues($columns[0], $columns[1]);
		?>
		<?= $table->build() ?>

		<?= $HTML->smartButton($this->translate("Edit"), "edit", "edit", "fright key:e") ?>
		<?= $HTML->smartButton($this->translate("Done"), "done", "done", "fleft key:esc") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>