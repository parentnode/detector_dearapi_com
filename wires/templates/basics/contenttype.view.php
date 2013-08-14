<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?>" id="<?= $this->getContainerId() ?>">
<?php
	$this->details(1);

	$this->vars = $this->getTemplateObject()->vars;
	$this->varnames = $this->getTemplateObject()->varnames;

	$name = $this->getTemplateObject()->getQueryResult(0, "name");
	$contenttype = $this->getTemplateObject()->getQueryResult(0, "contenttype");
?>
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("id", $id) ?>
		<?= $HTML->inputHidden("itemtype_id", getVar("itemtype_id") ? getVar("itemtype_id") : $id) ?>
		<?= $HTML->inputHidden("page_status", "edit") ?>

		<?= $HTML->head($name) ?>
		<?= $HTML->block($this->varnames["name"], $name) ?>
		<?= $HTML->block($this->varnames["contenttype"], $contenttype) ?>

		<? $in_use = method_exists($this->getTemplateObject(), "checkUsage") ? $this->getTemplateObject()->checkUsage($id) ? " disabled" : "" : "";?>
		<?= $HTML->smartButton($this->translate("Delete"), "delete", "delete_confirm", "fright$in_use") ?>
		<?= $HTML->smartButton($this->translate("Edit"), "edit", "edit", "fright key:e") ?>
		<?= $HTML->smartButton($this->translate("Done"), "done", "done", "fleft key:esc") ?>
	</fieldset>
	<div class="deleteConfirm">
		<?php $item_name = $this->getTemplateObject()->getItemName($id) ?>
		<?= $HTML->block("", $this->translate("Are you sure you want to delete ###$item_name###?")) ?>
		<?= $HTML->smartButton($this->translate("DELETE"), "delete", "delete", "fright") ?>
		<?= $HTML->button($this->translate("CANCEL"), false, "Util.Ajax.deleteCancel();", "fleft") ?>
	</div>
<?= $this->designFooter() ?>
</div>
