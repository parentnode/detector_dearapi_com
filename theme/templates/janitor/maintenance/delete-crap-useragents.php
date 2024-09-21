<?php
global $action;
global $IC;
global $itemtype;
global $model;

$result = $model->deleteCrapUseragents();

//print_r($result);
?>
<div class="scene defaultList deleteDupletUseragents">
	<h1>Delete crap useragents</h1>
	<h2>Will delete from both unidentified and identified – Heavy duty processing - don't exaggerate.</h2>

	<ul class="actions">
		<?= $HTML->link("Back", "/janitor/maintenance", array("class" => "button", "wrapper" => "li.back")) ?>
	</ul>

	<p><strong>THIS HAS NOT BEEN IMPLEMENTED YET</strong></p>

	<p>
		Finding useragents that clearly have been manipulated and thus are not meaningful to keep for reference.
	</p>
	<p>
		identified_deleted: <?= $result["identified_deleted"] ?><br />
		unidentified_deleted: <?= $result["unidentified_deleted"] ?><br />

		start_time: <?= $result["start_time"] ?><br />
		end_time: <?= $result["end_time"] ?><br />
	</p>

	<div class="all_items i:defaultList filters">
<?		if($result && $result["items"]): ?>
		<ul class="items">
<?			foreach($result["items"] as $item): ?>
			<li class="item">
				<h3><?= $item["useragent"] ?> (<?= $item["no_deleted"] ?> deleted)<span class="type"><?= $item["type"] ?></span></h3>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No known crap useragents.</p>
<?		endif; ?>
	</div>

</div>
