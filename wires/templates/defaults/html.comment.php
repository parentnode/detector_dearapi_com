<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("page_status", "comment_save") ?>
		<?= $HTML->inputHidden("id", $id) ?>
		<?= $this->getTemplateObject()->newComment() ?>
		<?= $HTML->smartButton($this->translate("Save"), "comment_save", "comment_save", "fright") ?>
		<?= $HTML->smartButton($this->translate("Cancel"), "comment_cancel", "comment_cancel", "fleft") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>