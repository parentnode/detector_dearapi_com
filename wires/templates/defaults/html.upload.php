<div class="<?= $this->getResponseColumn() ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
	<fieldset>
		<form name="upload" class="init:form" action="<?= $this->url ?>" method="post" enctype="multipart/form-data">
		<?= $HTML->Head($this->translate("Upload")) ?>
		<?= $HTML->inputHidden("id", $id) ?>
		<?= $HTML->inputHidden("page_status", "upload_save") ?>
		<?= $this->getTemplateObject()->uploadItemInput() ?>
		<?= $HTML->separator() ?>
		<?= $HTML->button($this->translate("Save"), "upload_save", false, "fright") ?>
		<?= $HTML->smartButton($this->translate("Cancel"), "upload_cancel", "upload_cancel", "fleft") ?>
		</form>
	</fieldset>
<?= $this->designFooter() ?>
</div>
