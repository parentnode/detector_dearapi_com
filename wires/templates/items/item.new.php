<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("page_status", "save") ?>
		<?= $this->getTemplateObject()->newItem() ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>