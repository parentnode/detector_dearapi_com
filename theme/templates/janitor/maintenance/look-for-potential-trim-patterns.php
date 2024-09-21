<?php
global $action;
global $IC;
global $itemtype;
global $model;

$result = $model->lookForPotentialTrimPatterns($action);
// debug([$result]);

?>
<div class="scene defaultList potentialPatterns i:potentialPatterns">
	<h1>Look for potential trim patterns</h1>
	<h2>Finding UA fragments that might be used as new trim-patterns from indexed and unidentified useragents</h2>

	<ul class="actions">
		<?= $HTML->link("Back", "/janitor/maintenance", array("class" => "button", "wrapper" => "li.back")) ?>
	</ul>

	<p>
		start_time: <?= $result["start_time"] ?><br />
		end_time: <?= $result["end_time"] ?><br />
	</p>

	<div class="all_items result i:defaultList filters">
<?		if($result && $result["items"]): ?>
		<ul class="items">
<?			foreach($result["items"] as $item): ?>
			<li class="item">
				<h3>
					<strong><?= $item["marker"] ?></strong>
				</h3>
				<div class="patterns">
					<h4>Patterns (<?= count($item["patterns"]) ?>)</h4>
					<p>
						<? foreach($item["patterns"] as $pattern): ?>
						<?= $pattern ?><br />
						<? endforeach; ?>
					</p>
				</div>
				<div class="uas">
					<h4>UserAgents (<?= count($item["useragents"]) ?>)</h4>
					<p>
						<? foreach($item["useragents"] as $useragent): ?>
						<?= $useragent ?><br />
						<? endforeach; ?>
					</p>
				</div>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No suggestions found.</p>
<?		endif; ?>
	</div>

</div>
