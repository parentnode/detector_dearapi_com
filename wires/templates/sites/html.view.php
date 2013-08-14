<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url.($this->getTargetId() ? ' form:target:'.$this->getTargetId() : '') ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
<?
$this->varnames = $this->getTemplateObject()->varnames;

//$id = $this->getTemplateObject()->getQueryResult(0, "id");
$tags = $this->getTemplateObject()->getQueryResult(0, "tags");
$url = $this->getTemplateObject()->getQueryResult(0, "url");
$name = $this->getTemplateObject()->getQueryResult(0, "name");
$enabled = $this->getTemplateObject()->getQueryResult(0, "enabled");
$hidden = $this->getTemplateObject()->getQueryResult(0, "hidden");
$classname = $this->getTemplateObject()->getQueryResult(0, "classname");
$sindex = $this->getTemplateObject()->getQueryResult(0, "sindex");

if($name == "----") {
 	print $this->head($this->translate("Separator"));
}
else {
	print $this->head($this->translate("Viewing: ###$name###"));
}

?>

	<div class="ci50">
		<fieldset>
			<?= $this->inputHidden("id", $id) ?>
			<?= $this->inputHidden("page_status", "edit") ?>

			<?= $this->block($this->varnames["name"], $name) ?>
			
			<? if($url) {
				print $this->block($this->varnames["url"], $url);
			 }
			else {

				if($tags) {
					$tags = $this->getTemplateObject()->getTags($tags);
					$tag_table = $HTML->table();
					$tag_table->setHeader(0, $this->varnames["tags"]);
					$tag = false;
					foreach($tags["values"] as $tag_value) {
						$tag[] = $tag_value;
					}
					$tag_table->setColumnValues($tag);
					print $tag_table->build();
				}

			} ?>
		</fieldset>
	</div>
	
	<div class="ci50 init:expandable">
		<?= $this->head("Details", 2)?>
		<fieldset>
			<?= $this->block($this->varnames["classname"], stringOr($classname, "-")) ?>
			<?= $this->block($this->varnames["hidden"], ($hidden ? $this->translate("Yes") : $this->translate("No"))) ?>
			<?= $this->block($this->varnames["sindex"], $sindex) ?>
			<?= $this->block($this->translate("Status"), $enabled ? $this->translate("Enabled") : $this->translate("Disabled")) ?>
		</fieldset>
	</div>
	<div class="c">
		<fieldset>
			<?=  $this->separator() ?>
			<?=  $this->smartButton($enabled ? $this->translate("Disable") : $this->translate("Enable"), false , "enable_disable", "fright".($enabled ? " disable" : " enable")) ?>

			<? $in_use = method_exists($this->getTemplateObject(), "checkUsage") ? $this->getTemplateObject()->checkUsage($id) ? " disabled" : "" : "";?>
			<?= $this->smartButton($this->translate("Delete"), "delete", "delete_confirm", "fright$in_use") ?>
			<?= $this->smartButton($this->translate("Edit"), "edit", "edit", "fright key:e") ?>
			<?= $this->smartButton($this->translate("Back to list"), "done", "done", "fleft key:esc") ?>
		</fieldset>
		<div class="deleteConfirm">
			<?php $item_name = $this->getTemplateObject()->getItemName($id) ?>
			<?= $this->block("", $this->translate("Are you sure you want to delete ###$item_name###?")) ?>
			<?= $this->smartButton($this->translate("DELETE"), "delete", "delete", "fright") ?>
			<?= $this->button($this->translate("CANCEL"), false, "Util.Ajax.deleteCancel();", "fleft") ?>
		</div>
	</div>
<?= $this->designFooter() ?>
</div>