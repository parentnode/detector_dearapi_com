<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url.($this->getTargetId() ? ' form:target:'.$this->getTargetId() : '') ?>" id="<?= $this->getContainerId() ?>">
<?php
	$this->details(1);

	$this->vars = $this->getTemplateObject()->vars;
	$this->varnames = $this->getTemplateObject()->varnames;

	$items = $this->getTemplateObject()->getItems();
?>
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("id", "") ?>
		<?= $HTML->inputHidden("page_status", "view") ?>
		<?= $HTML->head($this->translate("Users")) ?>

		<?= Generic::listItemsExtended("view", "view", $items["id"], array($items["user_id"], $items["access_level"]), array($this->translate("User-ID"), $this->translate("Search")), array("sortby max", "search acenter")) ?>

		<?//= Generic::listItems("view", "view", $items, $this->translate("types")) ?>
		<?//= $HTML->smartButton($this->translate("New"), "new", "new", "fright key:n") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>