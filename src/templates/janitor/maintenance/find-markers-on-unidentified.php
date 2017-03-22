<?php
global $action;
global $IC;
global $itemtype;
global $model;

$all_items = false;

if(count($action) == 2) {
	$all_items = $model->findMarkersOnUnidentified($action[1]);
}

$patterns = $model->getDevicesWithPatterns("bla");

?>
<div class="scene defaultList findMarkersOnUnidentified unidentifiedList">
	<h1>Find markers on unidentified</h1>
	<h2>Ordered by number of occurences - to help reduce UA's faster</h2>

	<div class="testmarkers">
		<div class="filter">
			<ul class="markers">
			<? foreach($patterns as $pattern): ?>
				<li class="marker"><a href="/janitor/maintenance/find-markers-on-unidentified/<?= $pattern["id"] ?>"><?= $pattern["name"] ?></a></li>
			<? endforeach; ?>
			</ul>
		</div>
	</div>

	<div class="all_items i:defaultList filters">
<?		if($all_items): ?>
		<ul class="items">
<?			foreach($all_items as $marker => $count): ?>
			<li class="item items_id:<?= $item["id"] ?>">
				<h3><strong><?= $marker ?>:</strong> <?= $count ?></h3>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No markers found.</p>
<?		endif; ?>
	</div>

</div>
