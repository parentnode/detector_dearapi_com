<?php if($this->getTemplateObject()->item()) { ?>
<div class="<?= $this->getResponseColumn() ?> list" id="<?= $this->getContainerId() ?>">
	<?= $this->getTemplateObject()->listItems(); ?>
</div>
<?php } ?>