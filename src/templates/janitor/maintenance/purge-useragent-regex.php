<?php
global $action;
global $IC;
global $itemtype;
global $model;

$all_items = $model->purgeUseragentRegex($action);

//print_r($all_items);
?>
<div class="scene defaultList purgeUseragentRegex">
	<h1>purge Useragent Regex</h1>
	<h2>Removing trim-patterns from existing useragents</h2>

	<div class="all_items i:defaultList filters">
<?		if($all_items): ?>
		<ul class="items">
<?			foreach($all_items as $item): ?>
			<li class="item items_id:<?= $item["id"] ?>">
				<h3><strong><?= $item["status"] ?>:</strong><br /><?= $item["useragent"] ?></h3>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No useragents matching.</p>
<?		endif; ?>
	</div>

</div>
