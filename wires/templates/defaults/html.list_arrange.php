<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url.($this->getTargetId() ? ' form:target:'.$this->getTargetId() : '') ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("id", "") ?>
		<?= $HTML->inputHidden("page_status", "new") ?>
		<?= $this->getTemplateObject()->listItems("view", "view") ?>
	 	<?= $HTML->button($this->translate("Save structure"), "structure_update","#","arrange:save save:".$this->url."?page_status=structure_update target:".$this->getContainerId()." fright disabled key:s") ?>
		<?= $HTML->smartButton($this->translate("New"), "new", "new", "fright key:n") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>