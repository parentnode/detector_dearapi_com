<?php
global $action;
global $IC;
global $itemtype;
global $model;

$all_items = $model->purgeUseragentRegex($action);

//print_r($all_items);
?>
<div class="scene defaultList purgeUseragentRegex i:purgeUseragentRegex">
	<h1>purge Useragent Regex</h1>
	<h2>Removing trim-patterns from indexed and unidentified useragents</h2>

<?		if($all_items): ?>
	<p class="note system_warning">This must be repeated until no more results show up. Purging all at once, will have too much impact on the server.</p>
	<p>Please be careful to look through the list to check that purging did not overdo it.</p>
<?		endif; ?>

	<div class="all_items i:defaultList filters">
<?		if($all_items): ?>
		<ul class="items">
<?			foreach($all_items as $item): ?>
			<li class="item ua_id:<?= $item["id"] ?>">
				<h3 class="<?= $item["status"] ?>" title="<?= $item["sql"] ?>">
					<strong><?= $item["status_text"] ?></strong>
					<span class="<?= superNormalize($item["type"]) ?>"><?= $item["type"] ?></span>
				</h3>
				<div class="uas">
					<h4><strong>BEFORE:</strong><br /><?= $item["useragent"] ?></h4>
					<h4><strong>TRIMMED:</strong><br /><?= $item["trimmed_useragent"] ?></h4>
					<h4><strong>DIFF:</strong><br /><?= $item["diff_useragent"] ?></h4>
				</div>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No useragents matching.</p>
<?		endif; ?>
	</div>

</div>
