<?php

$loop = '';
$loop .= $HTML->head("Quod possim quam");
$loop .= $HTML->head("Hendrerit delenit", 2);
$loop .= $HTML->img("../img/gx_test.jpg", "Eleifend congue");
$loop .= $HTML->p("Qui quam videntur dolore eleifend congue. Ut autem per lobortis nostrud quam. Typi insitam nunc eorum et litterarum. Facit erat lorem claritatem est duis. Mazim consectetuer nulla claram vero qui. Placerat nonummy est hendrerit delenit tation.");
$loop .= $HTML->head("Lobortis nostrud quam", 3);
$loop .= $HTML->p("Quam eodem facit duis typi assum. Ut autem per lobortis nostrud quam. Typi insitam nunc eorum et litterarum. Facit erat lorem claritatem est duis. Mazim consectetuer nulla claram vero qui. Placerat nonummy est hendrerit delenit tation.");
$loop .= $HTML->head("Nulla option facit", 4);
$loop .= $HTML->p("In quod possim quam quam qui. Legunt <a href=\"#\">legentis nulla</a> option facit vel. Litterarum delenit quinta in nunc volutpat. Qui quam videntur dolore eleifend congue. Quam eodem facit duis typi assum. Placerat nonummy est hendrerit delenit tation.");
$loop .= $HTML->separator();
$loop .= $HTML->p("Placerat nonummy est hendrerit delenit tation.");

?>
<div class="c200">

	<div class="c200">
		<? $this->response_column = "c200 border"; ?>
		<?= $this->designHeader(); ?>
		<?= $loop ?>
		<?= $this->designFooter(); ?>
	</div>

	<div class="c200">
		<div class="c100 border">
			<? $this->response_column = "c100 border"; ?>
			<?= $this->designHeader(); ?>
			<?= $loop ?>
			<?= $this->designFooter(); ?>
		</div>
		<div class="c100 border">
			<? $this->response_column = "c100 border"; ?>
			<?= $this->designHeader(); ?>
			<?= $loop ?>
			<?= $this->designFooter(); ?>
		</div>
	</div>


</div>

<div class="c100 border">
	<? $this->response_column = "c100 border"; ?>
	<?= $this->designHeader(); ?>
	<?= $loop ?>
	<?= $this->designFooter(); ?>
</div>




<div class="c300">
	<div class="c100 border">
		<? $this->response_column = "c100 border"; ?>
		<?= $this->designHeader(); ?>
		<?= $loop ?>
		<?= $this->designFooter(); ?>
	</div>
	<div class="c200 border">
		<? $this->response_column = "c200 border"; ?>
		<?= $this->designHeader(); ?>
		<?= $loop ?>
		<?= $this->designFooter(); ?>
	</div>
</div>

<div class="c300 border">
	<? $this->response_column = "c300 border"; ?>
	<?= $this->designHeader(); ?>
	<?= $loop ?>
	<?= $this->designFooter(); ?>
</div>

<div class="c300">
	<div class="c100 border">
		<? $this->response_column = "c100 border"; ?>
		<?= $this->designHeader(); ?>
		<?= $loop ?>
		<?= $this->designFooter(); ?>
	</div>
	<div class="c200 border">
		<? $this->response_column = "c200 border"; ?>
		<?= $this->designHeader(); ?>
		<?= $loop ?>
		<?= $this->designFooter(); ?>
	</div>
</div>

<div class="c300">
	<div class="c150 border">
		<? $this->response_column = "c150 border"; ?>
		<?= $this->designHeader(); ?>
		<?= $loop ?>
		<?= $this->designFooter(); ?>
	</div>
	<div class="c75 border">
		<? $this->response_column = "c75 border"; ?>
		<?= $this->designHeader(); ?>
		<?= $loop ?>
		<?= $this->designFooter(); ?>
	</div>
	<div class="c75 banner">
		<?= $HTML->head("<span>Banner, Quod possim quam</span>") ?>
		<?= $HTML->p("<span>Placerat nonummy est hendrerit delenit tation.</span>") ?>
	</div>
</div>
