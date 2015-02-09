<?php
global $action;
global $IC;
global $itemtype;
global $model;

$all_items = $model->searchForUniqueMatches();

//print_r($all_items);
?>
<div class="scene defaultList uniqueMatchList">
	<h1>Devices with unique match</h1>

	<div class="all_items i:uniqueMatchList filters"
		data-csrf-token="<?= session()->value("csrf") ?>"
		data-useragent-delete="<?= $this->validPath("/janitor/device/deleteUnidentified") ?>"
		data-useragent-details="<?= $this->validPath("/janitor/device/unidentifiedUseragentDetails") ?>"
		data-useragent-identify="<?= $this->validPath("/janitor/device/identifyUnidentifiedId") ?>"
		>
<?		if($all_items): ?>
		<ul class="items">
<?			foreach($all_items as $item): ?>
			<li class="item ua_id:<?= $item["id"] ?>">
				<h3><strong><?= $item["segment"] ?> (<?= $item["method"] ?>)</strong>, <?= $item["useragent"] ?></h3>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No devices.</p>
<?		endif; ?>
	</div>

</div>
