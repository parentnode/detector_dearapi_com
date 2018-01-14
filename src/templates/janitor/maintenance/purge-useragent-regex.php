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
	<h2>Removing trim-patterns from existing useragents</h2>

	<div class="all_items i:defaultList filters">
<?		if($all_items): ?>
		<ul class="items">
<?			foreach($all_items as $item): ?>
			<li class="item ua_id:<?= $item["id"] ?>">
				<h3 class="<?= superNormalize($item["status"]) ?>" title="<?= $item["sql"] ?>"><strong><?= $item["status"] ?></strong></h3>
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
