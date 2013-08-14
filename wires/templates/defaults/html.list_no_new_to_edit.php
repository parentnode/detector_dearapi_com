<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url.($this->getTargetId() ? ' form:target:'.$this->getTargetId() : '') ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("id", "") ?>
		<?= $HTML->inputHidden("page_status", "") ?>
		<?= $this->getTemplateObject()->listItems("edit", "edit") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>