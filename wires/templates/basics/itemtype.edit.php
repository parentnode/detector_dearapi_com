<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?>" id="<?= $this->getContainerId() ?>">
<?php
	$this->details(1);

	$this->vars = $this->getTemplateObject()->vars;
	$this->varnames = $this->getTemplateObject()->varnames;

	$name = $this->getTemplateObject()->getQueryResult(0, "name");
?>
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("id", $id) ?>
		<?= $HTML->inputHidden("page_status", "update") ?>
		<?= $HTML->head("Edit ". $this->getQueryResult(0, "name")) ?>
		<?= $HTML->input($this->varnames["name"], "name", stringOr($this->vars["name"], $name)) ?>
		<?= $HTML->smartButton($this->translate("Update"), "update", "update", "fright key:s") ?>
		<?= $HTML->smartButton($this->translate("Cancel"), "view", "view", "fleft key:esc") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>