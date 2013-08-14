<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url.($this->getTargetId() ? ' form:target:'.$this->getTargetId() : '') ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
	<fieldset>
		<?= $HTML->inputHidden("id", "$id") ?>
		<?= $HTML->inputHidden("page_status", "navigation_items_update") ?>
		<?
			$item = $this->getObject("Item");

			$query = new Query();
			$query->sql("SELECT tags, sindex FROM ".UT_NAV." WHERE id = $id");
			$tags = $query->getQueryResult(0, "tags");
			if($tags) {
				$sindex = $query->getQueryResult(0, "sindex");
				$item->getItems(false, false, $tags, $sindex);
				print $item->listItems(false, false, "init:lisort");
			}
		?>
	</fieldset>
<?= $this->designFooter() ?>
</div>