<?php

$loop = '';
$loop .= $HTML->head("<span>Quod possim quam</span>");
$loop .= $HTML->head("<span>Hendrerit delenit </span>", 2);
$loop .= '<img src="../img/gx_test.jpg" alt="" class="picture" />';
$loop .= $HTML->head("<span>Lobortis nostrud quam</span>", 3);
$loop .= $HTML->head("<span>Nulla option facit</span>", 4);
$loop .= $HTML->p("<span>In quod possim quam quam qui. Legunt legentis nulla option facit vel. Litterarum delenit quinta in nunc volutpat. Qui quam videntur dolore eleifend congue. Quam eodem facit duis typi assum. Ut autem per lobortis nostrud quam. Typi insitam nunc eorum et litterarum. Facit erat lorem claritatem est duis. Mazim consectetuer nulla claram vero qui. Placerat nonummy est hendrerit delenit tation.</span>");
$loop .= $HTML->separator();
$loop .= $HTML->p("<span>Placerat nonummy est hendrerit delenit tation.</span>");

?>
<style>
	h1, h2, h3, h4, p {background: #3a3ffc;}
	h1 span,
	h2 span,
	h3 span,
	h4 span,
	p span {display: block; background: #b13afc; color: #ffffff;}
	.separator {background: #cccccc;}

</style>
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
		<?= $HTML->head("<span>Quod possim quam</span>") ?>
		<?= $HTML->p("<span>Placerat nonummy est hendrerit delenit tation.</span>") ?>
		<p>fasdf</p>
	</div>
</div>

