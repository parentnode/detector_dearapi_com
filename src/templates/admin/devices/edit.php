<?php

$action = $this->actions();

$IC = new Item();
$model = $IC->typeObject("product");

$item = $IC->getCompleteItem($action[1]);
$item_id = $item["item_id"];

?>
<div class="scene defaultEdit">
	<h1>Edit product</h1>

	<ul class="actions">
		<li class="cancel"><a href="/admin/products/list" class="button">Back</a></li>
	</ul>

	<div class="item">
		<form action="/admin/cms/update/<?= $item_id ?>" class="i:formDefaultEdit labelstyle:inject" method="post" enctype="multipart/form-data">
			<fieldset>
				<?= $model->input("name", array("value" => $item["name"])) ?>
				<?= $model->input("description", array("class" => "autoexpand", "value" => $item["description"])) ?>
			</fieldset>

			<ul class="actions">
				<li class="cancel"><a href="/admin/products/list" class="button">Back</a></li>
				<li class="save"><input type="submit" value="Update" class="button primary" /></li>
			</ul>
		</form>
	</div>

	<h2>Prices</h2>
	<div class="prices">
		<form action="/admin/cms/update/<?= $item_id ?>" class="i:formAddPrices labelstyle:inject" method="post" enctype="multipart/form-data">
			<fieldset>
				<?= $model->input("prices") ?>
			</fieldset>

			<ul class="actions">
				<li class="save"><input type="submit" value="Add price" class="button primary" /></li>
			</ul>
		</form>

		<ul class="prices">
<?		if($item["prices"]): ?>
<?			foreach($item["prices"] as $index => $price): ?>
			<li class="price">
				<h3><?= formatPrice($price["price"], $price["currency"]) ?></h3>
				<form action="/admin/cms/prices/delete/<?= $item_id ?>/<?= $price["id"] ?>" class="i:formDefaultDelete" method="post" enctype="multipart/form-data">
					<input type="submit" value="Delete" class="delete" />
				</form>
			</li>
<?			endforeach; ?>
<?		endif; ?>
		</ul>
	</div>

	<h2>Tags</h2>
	<div class="tags i:defaultTags">
		<form action="/admin/cms/update/<?= $item_id ?>" class="i:formAddTags labelstyle:inject" method="post" enctype="multipart/form-data">
			<fieldset>
				<?= $model->input("tags") ?>
			</fieldset>

			<ul class="actions">
				<li class="save"><input type="submit" value="Add tag" class="button primary" /></li>
			</ul>
		</form>

		<ul class="tags">
<?		if($item["tags"]): ?>
<?			foreach($item["tags"] as $index => $tag): ?>
			<li class="tag">
				<h3><span class="context"><?= $tag["context"] ?></span>:<span class="value"><?= $tag["value"] ?></span></h3>
				<form action="/admin/cms/tags/delete/<?= $item_id ?>/<?= $tag["id"] ?>" class="i:formDefaultDelete" method="post" enctype="multipart/form-data">
					<input type="submit" value="Delete" class="delete" />
				</form>
			</li>
<?			endforeach; ?>
<?		endif; ?>
		</ul>
	</div>

	<h2>Images</h2>
	<div class="images">
		<form action="/admin/cms/update/<?= $item_id ?>" class="i:formAddImages labelstyle:inject" method="post" enctype="multipart/form-data">
			<fieldset>
				<?= $model->input("files") ?>
			</fieldset>

			<ul class="actions">
				<li class="save"><input type="submit" value="Add image" class="button primary" /></li>
			</ul>

		</form>

		<ul class="images">
<?		if($item["images"]): ?>
<?			foreach($item["images"] as $index => $image): ?>
			<li class="image">
				<img src="/images/<?= $item_id ?><?= ($image["variant"] ? "/".$image["variant"] : "") ?>/x150.<?= $image["format"] ?>" />
				<form action="/admin/cms/product/<?= $item_id ?>/deleteImage/<?= $image["variant"] ?>" class="i:formDefaultDelete" method="post" enctype="multipart/form-data">
					<input type="submit" value="Delete" class="delete" />
				</form>
			</li>
<?			endforeach; ?>
<?		endif; ?>
		</ul>
	</div>

</div>
