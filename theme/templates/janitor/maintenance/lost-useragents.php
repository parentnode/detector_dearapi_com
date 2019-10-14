<?php
global $action;
global $IC;
global $itemtype;
global $model;

$all_items = $model->listLostUseragents();

//print_r($all_items);
?>
<div class="scene defaultList lostUseragents">
	<h1>Lost useragents (useragents without device)</h1>

	<ul class="actions">
		<?= $HTML->link("Back", "/janitor/maintenance", array("class" => "button", "wrapper" => "li.back")) ?>
	</ul>

	<div class="all_items i:defaultList i:deleteLostUseragents filters"
		data-csrf-token="<?= session()->value("csrf") ?>"
		data-useragent-delete-lost="<?= $this->validPath("/janitor/device/deleteLostUseragents") ?>" 
		>
<?		if($all_items): ?>
		<ul class="items">
<?			foreach($all_items as $item): ?>
			<li class="item item_id:<?= $item["item_id"] ?>">
				<h3><?= $item["useragent"] ?> <span>(id: <?= $item["item_id"] ?>)</span></h3>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No lost useragents.</p>
<?		endif; ?>
	</div>

</div>
