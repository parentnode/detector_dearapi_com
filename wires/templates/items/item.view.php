<div class="<?= $this->getResponseColumn() ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>

	<?= $HTML->head($this->getTemplateObject()->itemtype, 1) ?>

	<div class="c">
		<div class="ci66 section">
			<?= $this->getTemplateObject()->viewItemType() ?>
		</div>
		<div class="ci33 listTags section">
			<?= $this->getTemplateObject()->viewSIndex() ?>
			<?= $this->getTemplateObject()->listTags() ?>
		</div>
		<div class="c">
			<?= $this->getTemplateObject()->listDescriptions() ?>
			<?= $this->getTemplateObject()->listPrices() ?>
		</div>
	</div>
	<?= $HTML->separator() ?>
	<div class="init:form form:action:<?= $this->url ?> form:target:<?= $this->getContainerId() ?>" id="<?= $this->getContainerId() ?>_controller">
		<fieldset>
			<?= $HTML->inputHidden("id", $id) ?>
			<?//= $HTML->inputHidden("item_id", $id) ?>
			<?= $HTML->inputHidden("page_status", "done") ?>

			<? //= $HTML->smartButton($this->translate("Next"), "next", "next", "fright") ?>
			<? //= $HTML->smartButton($this->translate("Previous"), "prev", "prev") ?>
			<? //= $HTML->separator() ?>

			<? $in_use = method_exists($this->getTemplateObject(), "checkUsage") ? $this->getTemplateObject()->checkUsage($id) ? " disabled" : "" : ""; ?>
			<?= $HTML->smartButton($this->translate("Delete"), "delete", "delete_confirm", "fright$in_use") ?>
			<?= $this->getTemplateObject()->enableDisableSmartButton($id) ?>
			<?= $HTML->smartButton($this->translate("Back to list"), "done", "done", "fleft key:esc") ?>

			<div class="deleteConfirm">
				<?= $HTML->block("", $this->translate("Are you sure you want to delete")) ?>
				<?= $HTML->smartButton($this->translate("DELETE"), "delete", "delete", "fright") ?>
				<?= $HTML->button($this->translate("CANCEL"), false, "Util.Ajax.deleteCancel();", "fleft") ?>
			</div>
		</fieldset>
	</div>

<?= $this->designFooter() ?>
</div>
