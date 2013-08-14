<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?> noPrint" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
<?
	$this->details(1);

	$this->vars = $this->getTemplateObject()->vars;
	$this->varnames = $this->getTemplateObject()->varnames;
?>

	<?= $this->head($this->translate("New device")) ?>
		<fieldset>
			<?= $this->inputHidden("page_status", "save") ?>

			<?= $this->input("model") ?>
			<?= $this->select($this->varnames["brand_id"], "brand_id", Generic::getItems(UT_BRA, false, "name"), $this->vars["brand_id"], array("", "-")) ?>

			<?= $this->input("useragent") ?>
		
			<?= $this->smartButton($this->translate("Save"), "save", "save", "fright key:s") ?>
			<?= $this->smartButton($this->translate("Cancel"), "done", "done", "fleft key:esc") ?>
		</fieldset>
<?= $this->designFooter() ?>
</div>