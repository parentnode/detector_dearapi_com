<?php
global $action;
global $IC;
global $itemtype;
global $model;

$all_items = $model->listEmptyDevices();

//print_r($all_items);
?>
<div class="scene defaultList devicesWithoutUseragents">
	<h1>Devices without useragents</h1>

	<ul class="actions">
		<?= $HTML->link("Back", "/janitor/maintenance", array("class" => "button", "wrapper" => "li.back")) ?>
	</ul>

	<div class="all_items i:defaultList filters"
		data-csrf-token="<?= session()->value("csrf") ?>"
		>
<?		if($all_items): ?>
		<ul class="items">
<?			foreach($all_items as $item): ?>
			<li class="item item_id:<?= $item["item_id"] ?>">
				<h3><a href="/janitor/device/edit/<?= $item["item_id"] ?>"><?= $item["name"] ?></a></h3>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No empty devices.</p>
<?		endif; ?>
	</div>

</div>
