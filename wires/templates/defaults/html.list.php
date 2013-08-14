<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url.($this->getTargetId() ? ' form:target:'.$this->getTargetId() : '') ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("id", "") ?>
		<?= $HTML->inputHidden("page_status", "new") ?>
		<?= $this->getTemplateObject()->listItems("view", "view") ?>
		<?= $HTML->smartButton($this->translate("New"), "new", "new", "fright key:n") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>