<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("id", getVar("access_level_id")); ?>
		<?= $HTML->inputHidden("point_id", $id) ?>
		<?= $HTML->inputHidden("page_status", "view") ?>
		<?= $HTML->head($this->translate("Change Access")) ?>
		<?= $HTML->block($this->translate("Access level").":", $this->getObject($object)->getItemName(getVar("access_level_id"))) ?>
		<?= $HTML->block($this->translate("Access point").":", $this->getObject($object)->getPointName($id)) ?>
		<?= $this->getObject($object)->listPointActions(getVar("access_level_id"), $id) ?>
		<?= $HTML->smartButton($this->translate("Save"), "edit_access_update", "edit_access_update", "fright key:s") ?>
		<?= $HTML->smartButton($this->translate("Cancel"), "select_point", "select_point", "fleft key:esc") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>