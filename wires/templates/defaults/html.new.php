<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?> noPrint" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("page_status", "save") ?>
		<?= $this->getTemplateObject()->newItem() ?>
		<?= $HTML->smartButton($this->translate("Save"), "save", "save", "fright key:s") ?>
		<?= $HTML->smartButton($this->translate("Cancel"), "done", "done", "fleft key:esc") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>