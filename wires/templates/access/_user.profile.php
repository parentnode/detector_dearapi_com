<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("id", $id) ?>
		<?= $HTML->inputHidden("page_status", "edit") ?>
		<?= $this->getTemplateObject()->viewProfile() ?>
		<? $in_use = $this->getTemplateObject()->checkUsage($id) ? " disabled" : "";?>
		<?= $HTML->smartButton($this->translate("Password"), "password", "password", "fright key:p") ?>
		<?= $HTML->smartButton($this->translate("Edit"), "edit", "edit", "fright key:e") ?>
		<?= $HTML->smartButton($this->translate("Done"), "done", "done", "fleft key:esc") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>