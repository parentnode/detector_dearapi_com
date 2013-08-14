<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url.($this->getTargetId() ? ' form:target:'.$this->getTargetId() : '') ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
<?
	$items = $this->getTemplateObject()->getUnidentifiedDevice($id);
	$device_matches = $this->getTemplateObject()->guessDevice($items["useragent"][0]);
?>

	<fieldset>
		<?= $this->inputHidden("id", "$id") ?>
		<?= $this->inputHidden("page_status", "view_unidentified") ?>
		<?= $this->inputHidden("useragent_id", "$id") ?>
	</fieldset>

	<?= $this->head($this->translate("Unidentified device")) ?>
	<?= $this->p($items["useragent"][0]) ?>

	<?= $this->p($this->translate("Do be careful. Adding a useragent to the wrong devices can have fatal consequences for endusers. However, the same goes for adding new devices. The point is: BE CAREFUL. Regardless."), "info") ?>

	<? //= $this->head("These devices match part of the useragent string:", 3) ?>
	<?
	foreach($device_matches["match"] as $key => $device_id) {
		if($device_id) {
			$device_name = $this->getTemplateObject()->getItemName($device_id);
			print '<div class="c">';
			print $HTML->smartButton($this->translate("Select ###$device_name###"), "add_to_device", "add_to_device", "id:$device_id");
			print $HTML->smartButton($this->translate("Quick select"), "add_to_device_quick", "add_to_device_quick", "id:$device_id", false, $device_matches["match_by"][$key]);
			print $HTML->smartButton($this->translate("Clone device"), "clone_device", "clone_device", "id:$device_id warning fright", false, $device_matches["match_by"][$key]);
			print '</div>';
		}
		else {
			print $this->head($device_matches["match_by"][$key],2);
		}
	}
	?>

	<?= $this->separator() ?>


	<? if(Session::getLogin()->validatePage("add_to_other_device")) { ?>
		<div class="c init:expandable id:select">
			<?= $HTML->head("Select or Clone another existing device for this useragent", 2) ?>
			<fieldset>
				<?= $HTML->input($this->translate("Search for pattern"), "pattern", stringOr(getVar("pattern")), "init:autosearch") ?>
				<div class="patternsearchresult"></div>

				<?= $HTML->select($this->translate("OR Select brand"), "brand_id", Generic::getItems(UT_BAS_BRA, false, "name"), stringOr(getVar("brand_id")), array("", "-"), "Util.Ajax.submitContainer('container:item');") ?>
				<?= getVar("brand_id") ? $HTML->select($this->translate("Select model"), "device_id", $this->getTemplateObject()->getModels(getVar("brand_id")), stringOr(getVar("device_id")), array("", "-"), "Util.Ajax.submitContainer('container:item');") : "" ?>
				<?= getVar("device_id") ? $HTML->smartButton($this->translate("Select"), "add_to_other_device", "add_to_other_device") : "" ?>
				<?= getVar("device_id") ? $HTML->smartButton($this->translate("Clone"), "clone_device", "clone_device", "warning id:".getVar("device_id")) : "" ?>

				<?//= $HTML->button($this->translate("Select another existing device for this useragent"), "select_device", "Util.Ajax.submitContainer('select:device');", "key:n") ?>
			</fieldset>
		</div>
	<? } ?>

	<?//= $this->separator() ?>

	<? if(Session::getLogin()->validatePage("new_device")) { ?>
		<div class="c init:expandable id:create">
			<?= $HTML->head("Create new device for this useragent", 2) ?>
			<fieldset>
				<?= $this->inputHidden("useragent", $items["useragent"][0]) ?>
				<?= $HTML->smartButton($this->translate("Create new device"), "new_device", "new_device", "warning key:n") ?>
			</fieldset>
		</div>
	<? } ?>

	<? if(Session::getLogin()->validatePage("delete_useragent")) { ?>
		<div class="c init:expandable id:delete">
			<?= $HTML->head("Delete this useragent", 2) ?>
			<fieldset>
				<?= $HTML->smartButton($this->translate("Delete"), "delete_useragent", "delete_useragent", "delete") ?>
			</fieldset>
		</div>
	<? } ?>

	<fieldset>
		<?
			$table = $HTML->table();
			$table->setHeader(0, $this->translate("Header"), "max");
			$table->setHeader(1, "Site ID");
			$table->setHeader(2, "", "search");

			if($items) {
				$table->setColumnValues($items["header"], $items["site_id"], $items["timestamp"]);
				$table->setColumnClass(0, "max");
				$table->setColumnClass(1, "max");
				$table->setColumnClass(2, "search");
				print $table->build();
			}
		?>

		<?= $HTML->smartButton($this->translate("Back to list"), "done", "done", "fleft key:esc") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>