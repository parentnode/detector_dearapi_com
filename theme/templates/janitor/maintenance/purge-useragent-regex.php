<?php
global $action;
global $IC;
global $itemtype;
global $model;

$result = $model->purgeUseragentRegex($action);

//print_r($result);
?>
<div class="scene defaultList purgeUseragentRegex i:purgeUseragentRegex">
	<h1>purge Useragent Regex</h1>
	<h2>Removing trim-patterns from indexed and unidentified useragents</h2>

	<ul class="actions">
		<?= $HTML->link("Back", "/janitor/maintenance", array("class" => "button", "wrapper" => "li.back")) ?>
	</ul>



<?		if($result && $result["items"]): ?>
	<p class="note system_warning">This must be repeated until no more results show up. Purging all at once, will have too much impact on the server.</p>
	<p>Please be careful to look through the list to check that purging did not overdo it.</p>
	<p>
		identified_deleted: <?= $result["identified_deleted"] ?><br />
		unidentified_deleted: <?= $result["unidentified_deleted"] ?><br />
		identified_updated: <?= $result["identified_updated"] ?><br />
		unidentified_updated: <?= $result["unidentified_updated"] ?><br /><br />

		start_time: <?= $result["start_time"] ?><br />
		end_time: <?= $result["end_time"] ?><br />
	</p>

<?		endif; ?>

	<div class="all_items result i:defaultList filters">
<?		if($result && $result["items"]): ?>
		<ul class="items">
<?			foreach($result["items"] as $item): ?>
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
