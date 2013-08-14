<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url.($this->getTargetId() ? ' form:target:'.$this->getTargetId() : '') ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
<? $items = $this->getTemplateObject()->languageClass->getItems(); ?>
	<fieldset>
		<?= $this->head($this->translate("Translation languages")) ?>

		<?= $HTML->inputHidden("id", "") ?>
		<?= $HTML->inputHidden("page_status", "") ?>
		<?= $this->p($this->translate("Select language for translation"), "info") ?>
		<?= Generic::listItemsExtended("list", "list", $items["id"], array($items["values"], array()), array("Language name", "search"), array("max", "search acenter")) ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>
