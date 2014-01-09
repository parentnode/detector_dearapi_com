<?php
$action = $this->actions();

$tags = getPost("tags");
$search = getPost("search");
$search_string = getPost("search_string");


$IC = new Item();
$itemtype = "device";
$model = $IC->typeObject($itemtype);


if($search) {
	$all_items = $model->searchDevices(array("search_string" => $search_string, "tags" => $tags));
	
}
else {
	$all_items = $IC->getItems(array("itemtype" => $itemtype, "order" => "modified_at DESC", "limit" => 50));
	
}

?>
<div class="scene defaultList <?= $itemtype ?>List">
	<h1>Devices</h1>

	<ul class="actions">
		<li class="new"><a href="/admin/<?= $itemtype ?>/new" class="button primary">New <?= $itemtype ?></a></li>
	</ul>

	<form class="options labelstyle:inject i:searchDevice" action="/admin/<?= $itemtype ?>/list" method="post" novalidate="novalidate">
		<?= $model->input("search", array("type" => "hidden", "value" => "true")) ?>
		<fieldset>
			<?= $model->input("search_string", array("type" => "string", "label" => "Global search (regular expression)", "value" => $search_string)) ?>
		</fieldset>
		<ul class="actions">
			<li><input type="submit" value="Search" class="button" /></li>
		</ul>
	</form>

	<div class="all_items i:defaultList taggable filters">
<?		if($all_items): ?>
		<ul class="items">
<?			foreach($all_items as $item): 
				$item = $IC->getCompleteItem($item["id"]); ?>
			<li class="item item_id:<?= $item["id"] ?>">
				<h3><?= $item["name"] ?></h3>

<?				if($item["tags"]): ?>
				<ul class="tags">
<?					foreach($item["tags"] as $tag): ?>
					<li class="<?= $tag["context"] ?>"><span class="context"><?= $tag["context"] ?></span>:<span class="value"><?= $tag["value"] ?></span></li>
<?					endforeach; ?>
				</ul>
<?				endif; ?>

				<ul class="actions">
					<li class="edit"><a href="/admin/<?= $itemtype ?>/edit/<?= $item["id"] ?>" class="button">Edit</a></li>
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
		<p>No devices.</p>
<?		endif; ?>
	</div>

</div>
