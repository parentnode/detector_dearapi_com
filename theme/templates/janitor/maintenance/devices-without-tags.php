<?php
global $action;
global $IC;
global $itemtype;
global $model;

$all_items = $model->listDevicesWithoutTags();

//print_r($all_items);
?>
<div class="scene defaultList devicesWithoutTags">
	<h1>Devices without tags</h1>

	<div class="all_items i:defaultList filters"
		data-csrf-token="<?= session()->value("csrf") ?>"
		data-devices-delete-lost="<?= security()->validPath("/janitor/device/deleteLostDevices") ?>" 
		>
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
		<p>No devices without tags.</p>
<?		endif; ?>
	</div>

</div>
