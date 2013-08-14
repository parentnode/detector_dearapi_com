<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>

	<?
		$this->vars = $this->getTemplateObject()->vars;
		$this->varnames = $this->getTemplateObject()->varnames;

		$model = $this->getTemplateObject()->getQueryResult(0, "model");
		$brand = $this->getTemplateObject()->getQueryResult(0, "brand");
	?>

	<?//= $this->getTemplateObject()->viewItem() ?>
	<?= $HTML->head($brand.", ".$model) ?>

	<fieldset>

		<?= $HTML->inputHidden("id", $id) ?>
			<?//= $HTML->inputHidden("item_id", $id) ?>
		<?= $HTML->inputHidden("page_status", "done") ?>

		<div class="c">
			<div class="ci50">
				<?= $this->block($this->varnames["model"], $model) ?>
			</div>
			<div class="ci50">
				<?= $this->block($this->varnames["brand_id"], $brand) ?>
			</div>

			<div class="c">
				<?
					$contenttypes = $this->getTemplateObject()->getContenttypes($id);
					if($contenttypes) {

						$table = $HTML->table();
						for($i = 0; $i < count($contenttypes["id"]); $i++) {
//							$ids[] = $contenttypes["id"][$i];
							$column_0[] = $contenttypes["values"][$i];
							$column_1[] = $contenttypes["contenttype"][$i];
						}
						$table->setHeader(0, $this->varnames["contenttype"]);
//						$table->setRowId($ids);
						$table->setColumnValues($column_0, $column_1);
						print $table->build();
					}
					else {
						print $HTML->p("No contenttypes");
					}
				?>
			</div>
		</div>

		<?= $HTML->separator() ?>

		<div class="useragents">
			<?
			$useragents = $this->getTemplateObject()->getUseragents($id);

			$table = $this->table();
			$table->setHeader(0, $this->varnames["useragent"], "max");
			$column = array();
			if(isset($useragents["values"])) {
				foreach($useragents["values"] as $useragent) {
					$column[] = $useragent;
				}
			}
			$table->setColumnClass(0, "max");
			if($column) {
				$table->setColumnValues($column);
			}
			else {
				$table->setColumnValues(array($this->translate("No useragents has been defined")));	
			}
			print $table->build();
			?>
		</div>

		<?= $HTML->separator() ?>

		<?= $HTML->smartButton($this->translate("Edit"), "edit", "edit", "fright key:e") ?>
		<? $in_use = method_exists($this->getTemplateObject(), "checkUsage") ? $this->getTemplateObject()->checkUsage($id) ? " disabled" : "" : ""; ?>
		<?= $HTML->smartButton($this->translate("Delete"), "delete", "delete_confirm", "fright$in_use") ?>
		<?= $HTML->smartButton($this->translate("Back to list"), "done", "done", "fleft key:esc") ?>

		<div class="deleteConfirm">
			<?= $HTML->block("", $this->translate("Are you sure you want to delete")) ?>
			<?= $HTML->smartButton($this->translate("DELETE"), "delete", "delete", "fright") ?>
			<?= $HTML->button($this->translate("CANCEL"), false, "Util.Ajax.deleteCancel();", "fleft") ?>
		</div>
	</fieldset>
<?= $this->designFooter() ?>
</div>