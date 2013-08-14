<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url.($this->getTargetId() ? ' form:target:'.$this->getTargetId() : '') ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
<?
	$this->vars = $this->getTemplateObject()->vars;
	$this->varnames = $this->getTemplateObject()->varnames;

	$items = $this->getTemplateObject()->getUnidentifiedDevicesByUnique($this->vars["pattern"]);

?>

	<?= $this->head($this->translate("Uniquely identified devices")) ?>
	<fieldset>
		<?= $this->inputHidden("id", "") ?>
		<?= $this->inputHidden("page_status", "pattern") ?>

		<?= $this->smartButton("Back to the Complete list", "list", "list") ?>

		<?= $this->separator() ?>

		<?= $this->p($this->translate("Matches:".count($items["id"]))) ?>
		<?

			$table = $HTML->table();

//			$table->setHeader(0, $this->translate("Useragents"), "");
			$table->setColumnType(0, "checkbox");
			$table->setHeader(1, "", "max search");

			if($items) {
				foreach($items["id"] as $key => $id) {
//					$ids[] = $id;
//					$status[] = "view_unidentified";
					
					$input[0][] = "useragent[".$id."]";
					if($this->vars["useragent"] && isset($this->vars["useragent"][$id]) && $this->vars["useragent"][$id]) {
						$input[1][] =  1;
					}
					else {
						$input[1][] =  0;
					}
					
				}

//				if(Session::getLogin()->validatePage("view_unidentified")) {
//					$table->setRowStatus($status);
//				}
//				$table->setRowId($items["id"]);
//				$table->setRowClasses($classes);

				$table->setColumnValues($input, $items["useragent"]);
				$table->setColumnClass(1, "max");
				print $table->build();

				?>

				<div class="c">
					<?= $HTML->head("Select device for these useragents", 2) ?>
					<?= $this->p("Make sure you have selected only useragents matching this device", "info") ?>
					<fieldset>
						<?= $HTML->select($this->translate("Select brand"), "brand_id", Generic::getItems(UT_BAS_BRA, false, "name"), stringOr(getVar("brand_id")), array("", "-"), "Util.Ajax.submitContainer('container:item');") ?>

						<?= getVar("brand_id") ? $HTML->select($this->translate("Select model"), "device_id", $this->getTemplateObject()->getModels(getVar("brand_id")), stringOr(getVar("device_id")), array("", "-"), "Util.Ajax.submitContainer('container:item');") : "" ?>
						<?= getVar("device_id") ? $HTML->smartButton($this->translate("Select"), "add_by_pattern", "add_by_pattern") : "" ?>

						<? // TODO: add cloning here ?>
					</fieldset>
				</div>

				<?

			}
			else {
				print $this->p("No unidentified devices");
			}
		?>

	</fieldset>
<?= $this->designFooter() ?>
</div>