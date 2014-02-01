<?php
$action = $this->actions();


$IC = new Item();
$itemtype = "device";
$model = $IC->typeObject($itemtype);

$all_items = $model->searchForUniquePotential();

//print_r($all_items);
?>
<div class="scene defaultList uniquePotentialList">
	<h1>Devices with potential</h1>

	<div class="all_items i:defaultList taggable filters">
<?		if($all_items): ?>
		<ul class="items">
<?			foreach($all_items as $item): 
				$item = $IC->extendItem($item); 
				?>
			<li class="item item_id:<?= $item["id"] ?>">
				<h3><?= $item["name"] ?> (<?= pluralize($item["uas"], "useragent", "useragents") ?>)</h3>

<?				if($item["tags"]): ?>
				<ul class="tags">
<?					foreach($item["tags"] as $tag): ?>
					<li class="<?= $tag["context"] ?>"><span class="context"><?= $tag["context"] ?></span>:<span class="value"><?= $tag["value"] ?></span></li>
<?					endforeach; ?>
				</ul>
<?				endif; ?>

				<ul class="actions">
					<li class="edit"><a href="/admin/<?= $itemtype ?>/edit/<?= $item["id"] ?>" class="button">Edit</a></li>
				</ul>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No devices.</p>
<?		endif; ?>
	</div>

</div>
