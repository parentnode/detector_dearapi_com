<?php

$labels = array("label_1", "label_2");
$values = array("value_1", "value_2");
$vars = array("id"=> $values, "values"=> $labels);

$loop = '';
$loop .= $HTML->head("Quod possim quam");
$loop .= $HTML->checkbox($labels, $values);
$loop .= $HTML->input("label:", "name", "value");
$loop .= $HTML->select("label:", "name", $vars);
$loop .= $HTML->textarea("label:", "name", "value", 120);
$loop .= $HTML->smartButton("Save", false, false, "fright save");
$loop .= $HTML->smartButton("Cancel", false, false, "fright");

?>
<div class="c300 border">
	<?= $this->designHeader(); ?>
	<fieldset>
		<?= $loop ?>
	</fieldset>
	<?= $this->designFooter(); ?>
</div>

<div class="c300">
	<div class="c150 border">
		<? $this->response_column = "c150 border"; ?>
		<?= $this->designHeader(); ?>
		<fieldset>
			<?= $loop ?>
		</fieldset>
		<?= $this->designFooter(); ?>
	</div>
	<div class="c150 border">
		<? $this->response_column = "c150 border"; ?>
		<?= $this->designHeader(); ?>
		<fieldset>
			<?= $loop ?>
		</fieldset>
		<?= $this->designFooter(); ?>
	</div>
</div>

<div class="c300">
	<div class="c225 border">
		<? $this->response_column = "c225 border"; ?>
		<?= $this->designHeader(); ?>
		<fieldset>
			<?= $loop ?>
		</fieldset>
		<?= $this->designFooter(); ?>
	</div>
	<div class="c75 border">
		<? $this->response_column = "c75 border"; ?>
		<?= $this->designHeader(); ?>
		<fieldset>
			<?= $loop ?>
		</fieldset>
		<?= $this->designFooter(); ?>
	</div>
</div>

<div class="c300">
	<div class="c100 border">
		<? $this->response_column = "c100 border"; ?>
		<?= $this->designHeader(); ?>
		<fieldset>
			<?= $loop ?>
		</fieldset>
		<?= $this->designFooter(); ?>
	</div>
	<div class="c200 border">
		<? $this->response_column = "c100 border"; ?>
		<?= $this->designHeader(); ?>
		<fieldset>
			<?= $loop ?>
		</fieldset>
		<?= $this->designFooter(); ?>
	</div>
</div>