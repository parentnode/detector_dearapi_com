<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?> noPrint" id="<?= $this->getContainerId() ?>">
<?php
	$this->details(1);

	$this->vars = $this->getTemplateObject()->vars;
	$this->varnames = $this->getTemplateObject()->varnames;
?>
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("page_status", "save") ?>
		<?= $HTML->head($this->translate("New itemtype")) ?>

		<?= $HTML->input($this->varnames["name"], "name", $this->vars["name"]) ?>

		<?= $HTML->smartButton($this->translate("Save"), "save", "save", "fright key:s") ?>
		<?= $HTML->smartButton($this->translate("Cancel"), "done", "done", "fleft key:esc") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>