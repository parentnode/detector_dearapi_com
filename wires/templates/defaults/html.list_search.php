<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url.($this->getTargetId() ? ' form:target:'.$this->getTargetId() : '') ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("id", "") ?>
		<?= $HTML->inputHidden("page_status", "new") ?>
		<?= $this->getTemplateObject()->listItems("view", "view") ?>
		<?= $HTML->smartButton($this->translate("New"), "new", "new", "fright key:n") ?>
		<?//= $HTML->button($this->translate("Search"), "search", "Util.Ajax.loadContainer('$this->url', 'item_search', 'page_status=search')", "fright") ?>

		<?= $HTML->smartButton($this->translate("Search"), "search_init", "search_init", "fright key:f") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>