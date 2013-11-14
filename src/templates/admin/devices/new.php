<?php

$action = $this->actions();

$IC = new Item();
$model = $IC->typeObject("product");

?>
<div class="scene defaultNew">
	<h1>New product</h1>

	<ul class="actions">
		<li class="cancel"><a href="/admin/products/list" class="button">Back</a></li>
	</ul>

	<form action="/admin/cms/save/product" class="i:formDefaultNew labelstyle:inject" method="post" enctype="multipart/form-data">

		<fieldset>
			<?= $model->input("name") ?>
			<?= $model->input("description", array("class" => "autoexpand")) ?>
		</fieldset>

		<ul class="actions">
			<li class="cancel"><a href="/admin/products/list" class="button">Back</a></li>
			<li class="save"><input type="submit" value="Save" class="button primary" /></li>
		</ul>

	</form>

</div>
