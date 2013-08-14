<?php if($this->getTemplateObject()->item()) { ?>
<div class="<?= $this->getResponseColumn() ?> view" id="<?= $this->getContainerId() ?>">
	<?= $this->getTemplateObject()->viewItem(); ?>
</div>
<?php } ?>