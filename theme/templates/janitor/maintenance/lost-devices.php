<?php
global $action;
global $IC;
global $itemtype;
global $model;

$all_items = $model->listLostDevices();

//print_r($all_items);
?>
<div class="scene defaultList lostDevices">
	<h1>Lost devices (items without device)</h1>

	<ul class="actions">
		<?= $HTML->link("Back", "/janitor/maintenance", array("class" => "button", "wrapper" => "li.back")) ?>
	</ul>

	<div class="all_items i:defaultList i:deleteLostDevices filters"
		data-csrf-token="<?= session()->value("csrf") ?>"
		data-devices-delete-lost="<?= security()->validPath("/janitor/device/deleteLostDevices") ?>" 
		>
<?		if($all_items): ?>
		<ul class="items">
<?			foreach($all_items as $item): ?>
			<li class="item item_id:<?= $item["id"] ?>">
				<h3>Sindex: <?= $item["sindex"] ?>, Modified at: <?= $item["modified_at"] ?> <span>(id: <?= $item["id"] ?>)</span></h3>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No lost device items.</p>
<?		endif; ?>
	</div>

</div>
