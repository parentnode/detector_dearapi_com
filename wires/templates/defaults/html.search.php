<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?> noPrint" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("page_status", "search") ?>
		<?= $this->getTemplateObject()->searchOptions() ?>
		<?= $HTML->smartButton($this->translate("Search"), "search", "search", "fright key:s") ?>
		<?= $HTML->smartButton($this->translate("Reset"), "search_reset", "search_reset", "fleft key:r") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>