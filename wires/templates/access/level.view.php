<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("id", $id) ?>
		<?= $HTML->inputHidden("page_status", "edit") ?>
		<?= $this->getTemplateObject()->viewItem() ?>
		<? $in_use = $this->getTemplateObject()->checkUsage($id) ? " disabled" : "";?>
		<?= $HTML->smartButton($this->translate("Delete"), "delete", "delete_confirm", "fright$in_use") ?>
		<?= $HTML->smartButton($this->translate("Set access"), "select_point", "select_point", "fright key:a") ?>
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