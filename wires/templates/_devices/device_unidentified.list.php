<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url.($this->getTargetId() ? ' form:target:'.$this->getTargetId() : '') ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
<?
	$this->getTemplateObject()->perf->mark("u-device template start", true);

	$items = $this->getTemplateObject()->getUnidentifiedDevices();

	$this->getTemplateObject()->perf->mark("u-device template elements returned");
?>

	<?= $this->head($this->translate("Unidentified devices")) ?>

	<fieldset>
		<?= $this->inputHidden("id", "") ?>
		<?= $this->inputHidden("page_status", "") ?>

		<?= $this->smartButton("Match by pattern", "pattern", "pattern") ?>
		<?= $this->smartButton("x-reference cleaning", "xref", "xref") ?>

		<?= $this->separator() ?>

		<?= $this->p($this->translate("Matches:".count($items["id"]))) ?>
		<?

			if($items) {

				$this->getTemplateObject()->perf->mark("u-device pre table");

				$table = $HTML->table();
				$table->setHeader(0, $this->translate("Useragents"), "max");
				$table->setHeader(1, $this->translate("Visits"));
				$table->setHeader(2, "", "search");

				foreach($items["id"] as $key => $id) {
					$ids[] = $id;
					$status[] = "view_unidentified";
				}

				if(Session::getLogin()->validatePage("view_unidentified")) {
					$table->setRowStatus($status);
				}
				$table->setRowId($items["id"]);
//				$table->setRowClasses($classes);

				$table->setColumnValues($items["useragent"], $items["visits"], $items["timestamp"]);
				$table->setColumnClass(0, "max");

				$this->getTemplateObject()->perf->mark("u-device pre table build");

				print $table->build();

				$this->getTemplateObject()->perf->mark("u-device post table build");
			}
			else {
				print $this->p("No unidentified devices");
			}


			print $this->getTemplateObject()->perf->result();
		?>

	</fieldset>
<?= $this->designFooter() ?>
</div>