<?php
global $action;
global $IC;
global $itemtype;
global $model;

$all_items = $model->listDevicesWithoutBrand();

//print_r($all_items);
?>
<div class="scene defaultList devicesWithoutBrand">
	<h1>Devices without brand</h1>

	<ul class="actions">
		<?= $HTML->link("Back", "/janitor/maintenance", array("class" => "button", "wrapper" => "li.back")) ?>
	</ul>

	<div class="all_items i:defaultList filters">
<?		if($all_items): ?>
		<ul class="items">
<?			foreach($all_items as $item): 
				$item = $IC->extendItem($item); ?>
			<li class="item items_id:<?= $item["id"] ?>">
				<h3><a href="/janitor/device/edit/<?= $item["item_id"] ?>"><?= $item["name"] ?></a></h3>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No devices without brand.</p>
<?		endif; ?>
	</div>

</div>
