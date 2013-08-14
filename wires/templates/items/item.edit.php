<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("id", $id) ?>
		<?= $HTML->inputHidden("page_status", "update") ?>
		<?= $this->getTemplateObject()->editItem() ?>
		<?//= $HTML->smartButton($this->translate("Update"), "update", "update", "fright") ?>
		<?= $HTML->smartButton($this->translate("Done"), "view", "view", "fleft key:esc") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>