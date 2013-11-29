<?php

$action = $this->actions();

$IC = new Item();
$itemtype = "device";
$model = $IC->typeObject($itemtype);

?>
<div class="scene defaultNew">
	<h1>New <?= $itemtype ?></h1>

	<ul class="actions">
		<li class="cancel"><a href="/admin/<?= $itemtype ?>/list" class="button">Back</a></li>
	</ul>

	<form action="/admin/cms/save/<?= $itemtype ?>" class="i:formDefaultNew labelstyle:inject" method="post" enctype="multipart/form-data">

		<fieldset>
			<?= $model->input("name") ?>
			<?= $model->input("description", array("class" => "autoexpand")) ?>
		</fieldset>

		<ul class="actions">
			<li class="cancel"><a href="/admin/<?= $itemtype ?>/list" class="button">Back</a></li>
			<li class="save"><input type="submit" value="Save" class="button primary" /></li>
		</ul>

	</form>

</div>
