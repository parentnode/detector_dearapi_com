<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("id", $id) ?>
		<?= $HTML->inputHidden("access_level_id", $id) ?>
		<?= $HTML->inputHidden("page_status", "view") ?>
		<?= $HTML->head($this->translate("Set Access")) ?>
		<?= $HTML->block($this->translate("Set access for").":", $this->getTemplateObject()->getItemName($id)) ?>
		<?= $this->getTemplateObject()->listPointItems($id, "edit_access", "edit_access") ?>
		<?= $HTML->smartButton($this->translate("Done"), "view", "view", "fleft key:esc") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>