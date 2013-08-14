<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url.($this->getTargetId() ? ' form:target:'.$this->getTargetId() : '') ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
<?

	$items = $this->getTemplateObject()->getSearchItems();

?>

	<?= $this->head($this->translate("Dirty devices")) ?>

	<fieldset>
		<?= $this->inputHidden("id", "") ?>
		<?= $this->inputHidden("page_status", "") ?>

		<?= $this->separator() ?>
		<?= $this->smartButton("Manual task", "manual_task", "manual_task") ?>
		<?= $this->separator() ?>


		<?= $this->p($this->translate("Matches:".count($items["id"]))) ?>
		<?

			if($items) {

//				print_r($items);

				$table = $HTML->table();
				$table->setHeader(0, $this->translate("Devices"), "max");
				$table->setHeader(1, "", "search");

				foreach($items["id"] as $key => $id) {
					$ids[] = $id;
					$status[] = "view";
				}

				if(Session::getLogin()->validatePage("view")) {
					$table->setRowStatus($status);
				}
				$table->setRowId($items["id"]);
//				$table->setRowClasses($classes);

				$table->setColumnValues($items["values"], $items["segment"]);
				$table->setColumnClass(0, "max");


				print $table->build();

			}
			else {
				print $this->p("No devices");
			}

		?>

	</fieldset>
<?= $this->designFooter() ?>
</div>