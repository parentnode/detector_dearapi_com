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
		<?= $HTML->inputHidden("page_status", "new") ?>
		<?= $HTML->head($this->translate("Contenttypes")) ?>
		<?
			if($items) {

				$table = $HTML->table();

				$table->setHeader(0, $this->translate("Contenttypes"), "max");
				$table->setHeader(1, "", "search");

				if(Session::getLogin()->validatePage("view")) {
					foreach($items["id"] as $key => $id) {
						$status[] = "view";
					}

					$table->setRowStatus($status);
				}
				$table->setRowId($items["id"]);
//
				$table->setColumnValues($items["values"], $items["contenttype"]);
				$table->setColumnClass(0, "max");
				print $table->build();
			}
			else {
				print $this->p("No contenttypes");
			}
		?>
		<?= $HTML->smartButton($this->translate("New"), "new", "new", "fright key:n") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>