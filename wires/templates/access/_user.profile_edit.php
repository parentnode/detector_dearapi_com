<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("id", $id) ?>
		<?= $HTML->inputHidden("page_status", "update") ?>
		<?= $this->getTemplateObject()->editProfile() ?>
		<?= $HTML->smartButton($this->translate("Update"), "update", "update", "fright key:s") ?>
		<?= $HTML->smartButton($this->translate("Cancel"), "view", "view", "fleft key:esc") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>