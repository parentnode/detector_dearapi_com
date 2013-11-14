<?php
$action = $this->actions();

$IC = new Item();
$all_items = $IC->getItems(array("itemtype" => "product", "order" => "status DESC"));

?>
<div class="scene i:defaultList defaultList productsList">
	<h1>Products</h1>

	<ul class="actions">
		<li class="new"><a href="/admin/products/new" class="button primary">New product</a></li>
	</ul>

	<div class="all_items">
<?		if($all_items): ?>
		<ul class="items i:productListImages taggable">
<?			foreach($all_items as $item): 
				$item = $IC->getCompleteItem($item["id"]);
				$image = $item["images"] ? $item["images"][0]["variant"] : "";
				 ?>
			<li class="item item_id:<?= $item["id"] ?> variant:<?= $image ?> format:<?= $item["images"][0]["format"] ?>">
				<h3><?= $item["name"] ?></h3>

<?				if($item["tags"]): ?>
				<ul class="tags">
<?					foreach($item["tags"] as $tag): ?>
<?//						if($tag["context"] == "category"): ?>
					<li><span class="context"><?= $tag["context"] ?></span>:<span class="value"><?= $tag["value"] ?></span></li>
<?//						endif; ?>
<?					endforeach; ?>
				</ul>
<?				endif; ?>

<?				if($item["prices"]): ?>
				<ul class="prices">
<?					foreach($item["prices"] as $price): ?>
					<li><?= formatPrice($price["price"], $price["currency"]) ?></li>
<?					endforeach; ?>
				</ul>
<?				endif; ?>

				<ul class="actions">
					<li class="edit"><a href="/admin/products/edit/<?= $item["id"] ?>" class="button">Edit</a></li>
					<li class="delete">
						<form action="/admin/cms/delete/<?= $item["id"] ?>" class="i:formDefaultDelete" method="post" enctype="multipart/form-data">
							<input type="submit" value="Delete" class="button delete" />
						</form>
					</li>
					<li class="status">
						<form action="/admin/cms/<?= ($item["status"] == 1 ? "disable" : "enable") ?>/<?= $item["id"] ?>" class="i:formDefaultStatus" method="post" enctype="multipart/form-data">
							<input type="submit" value="<?= ($item["status"] == 1 ? "Disable" : "Enable") ?>" class="button status" />
						</form>
					</li>
				</ul>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No products.</p>
<?		endif; ?>
	</div>

</div>
