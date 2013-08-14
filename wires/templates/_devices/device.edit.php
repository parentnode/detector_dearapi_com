<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
<?
	$this->details(1);

	$this->vars = $this->getTemplateObject()->vars;
	$this->varnames = $this->getTemplateObject()->varnames;

	$this->vars["model"] = stringOr($this->vars["model"], $this->getTemplateObject()->getQueryResult(0, "model"));
	$this->vars["brand_id"] = stringOr($this->vars["brand_id"], $this->getTemplateObject()->getQueryResult(0, "brand_id"));

?>
	<?= $this->head($this->translate("Edit device")) ?>

	<?= $this->inputHidden("id", $id) ?>
	<?= $this->inputHidden("device_id", $id) ?>
	<?= $this->inputHidden("page_status", "update") ?>

	<div class="c">
		<div class="ci50">
			<fieldset>
				<?= $this->input("model") ?>
			</fieldset>
		</div>

		<div class="ci50">
			<fieldset>
				<?= $this->select($this->varnames["brand_id"], "brand_id", Page::getItems(UT_BRA), $this->vars["brand_id"], array("", "-")) ?>
			</fieldset>
		</div>
	</div>

	<div class="c init:expandable id:contenttypes">
		<?= $this->head($this->translate("Contenttypes"), 2) ?>
		<fieldset>
			<?
				$itemtype_contenttypes = $this->getTemplateObject()->getContenttypes($id);
				$contenttypes = $this->getTemplateObject()->contenttypeClass->getItems();

				$table = $HTML->table();
				$table->setTableId("contenttypes");

				if($contenttypes) {

					foreach($contenttypes["id"] as $key => $value) {
						$column_1[] =  $contenttypes["values"][$key];
						$column_2[] =  $contenttypes["contenttype"][$key];
						$column_0[0][] = "contenttype[".$value."]";

						if($itemtype_contenttypes && array_search($value, $itemtype_contenttypes["id"]) !== false) {
							$column_0[1][] =  1;
						}
						else {
							$column_0[1][] =  0;
						}
					}
					$table->setHeader(1, $this->varnames["contenttype"], "max");
					$table->setHeader(2, $this->translate("Search"), "search");
					$table->setColumnType(0, "checkbox");
					$table->setColumnValues($column_0, $column_1, $column_2);
				}
				else {
					$table->setHeader(0, $this->varnames["contenttype"], "max");
					$table->setColumnValues(array($this->translate("There has not been defined any contenttypes in the system")));
				}

				print $table->build();
			?>
		</fieldset>
	</div>

	<div class="c">
		<fieldset>
			<?= $this->separator() ?>
			<?= $this->smartButton($this->translate("Update"), "update", "update", "fright key:s") ?>
		</fieldset>
	</div>
	<div class="c useragents init:expandable id:useragents">
		<h2><?= $this->varnames["useragent"] ?></h2>
		<fieldset>
			<?
				$useragents = $this->getTemplateObject()->getUseragents($id);

				$table = $this->table();
				$table->setTableId("useragents");

				$table->setHeader(0, $this->varnames["useragent"], "max");
				$table->setHeader(1, "", "search");
				$column = false;
				$ids = false;
				$status = false;

				if(isset($useragents["values"])) {
					foreach($useragents["id"] as $key => $id) {
						$column[] = $useragents["values"][$key];
						$column_search[] = "";
						$ids[] = $id;
						$status[] = "useragent_delete";
					}
				}

				if(!$column) {
					$column[] = "No useragents";
					$table->setColumnValues($column);
				}
				else {
					$table->setRowId($ids);
					$table->setColumnValues($column, $column_search);
					$table->setColumnClass(0, "max");
					if(Session::getLogin()->validatePage("useragent_delete")) {
						$table->setRowStatus($status);
						$table->setRowClass("delete");
					}
				}
				print $table->build();
			?>	

			<?= $this->input("useragent") ?>
			<?= $HTML->smartButton($this->translate("Add useragent"), "update", "update") ?>

		</fieldset>
	</div>
	<div class="c">
		<fieldset>
			<?= $this->separator() ?>
			<?= $this->smartButton($this->translate("Back to list"), "view", "view", "fleft key:esc") ?>
		</fieldset>
	</div>

<?= $this->designFooter() ?>
</div>