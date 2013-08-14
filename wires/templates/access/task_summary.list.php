<div class="<?= $this->getResponseColumn() ?> init:form" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->head($this->translate("Task Summary")) ?>
		<?= $this->getTemplateObject()->getTaskSummary() ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>