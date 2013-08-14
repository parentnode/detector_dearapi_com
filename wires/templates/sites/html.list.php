<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url.($this->getTargetId() ? ' form:target:'.$this->getTargetId() : '') ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
<?
	$items = $this->getTemplateObject()->getItems();
	// exclude frontpage
//	foreach($items as $key => $value) {
//		array_shift($items[$key]);
//	}
?>

	<?= $this->head(SITE_NAME) ?>

	<div class="c">
		<?= $this->p($this->translate("This is the list of your pages as they are positioned in the navigation tree."), "info") ?>

		<? if(!$items || count($items["id"]) < 2) {
			print $this->p($this->translate("Add your first page item"), "hint status:new");
		}
		else if(count($items["id"]) > 1 && count($items["id"]) < 4) {
			print $this->p($this->translate("Add another page or folder"), "hint status:new");
		}
		else {
			print $this->p($this->translate("Drag and drop pages/folders to reorder."), "info");
		} ?>

		<?//= $this->p($this->translate("Keyboard shortcuts are available. Press cmd/ctrl+h for info."), "info") ?>
	</div>
	
	<div class="c">
		<fieldset>
			<?= $this->inputHidden("id", "") ?>
			<?= $this->inputHidden("page_status", "new") ?>

			<? if($items["id"]) {
				$item = $this->getObject("Item");

				$table = $HTML->table("arrange nav");
				$table->setHeader(0, $this->translate("Your navigation tree"));
				foreach($items["id"] as $key => $value) {

					$status[] = "view";
					if($value == 1) {
						$content[] = "Don't! Just Don't!";
						$classes[] = "disabled";
					}
					else if($items["url"][$key] && !$items["hidden"][$key] && $items["enabled"][$key]) {
						$content[] = $items["url"][$key];
						$classes[] = "url";
					}
					else if($items["tags"][$key] && !$items["hidden"][$key] && $items["enabled"][$key]) {
						$item->getItems(false, false, false, $items["sindex"][$key]);
						$content[] = ($item->item() ? count($item->item["id"]) : 0)." items";
						$classes[] = "page";
					}
					else if(!$items["enabled"][$key]) {
						$content[] = 'off';
						$classes[] = "off";
					}
					else if($items["hidden"][$key]) {
						$content[] = 'hidden';
						$classes[] = "trail only";
					}
					else {
						$content[] = '';
						$classes[] = '';
					}
				
//					$content[] = $items["tags"][$key] ? $items["tags"][$key] : ($items["url"][$key] ? $items["url"][$key] : "");

				}
				if(Session::getLogin()->validatePage("view")) {
					$table->setRowStatus($status);
				}
				$table->setRowId($items["id"]);
				$table->setRowClasses($classes);

				$table->setColumnClass(1, "num");
				$table->setColumnType(0, "indent");
				$table->setColumnIndent(0, $items["indent"]);
				$table->setColumnValues($items["name"], $content);
				print $table->build();
			} ?>

		 	<?= $this->button($this->translate("Save structure"), "structure_update","#","arrange:save save:".$this->url."?page_status=structure_update target:".$this->getContainerId()." fright disabled key:s") ?>
			<?= $this->smartButton($this->translate("New"), "new", "new", "fright key:n") ?>
		</fieldset>
	</div>
<?= $this->designFooter() ?>
</div>