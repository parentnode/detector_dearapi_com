<?php
global $action;
global $IC;
global $itemtype;
global $model;

$all_items = $model->deleteDupletUseragents();

//print_r($all_items);
?>
<div class="scene defaultList deleteDupletUseragents">
	<h1>Delete duplet useragents</h1>
	<h2>Will delete from both unidentified and identified – Heavy duty processing - don't exaggerate.</h2>

	<ul class="actions">
		<?= $HTML->link("Back", "/janitor/maintenance", array("class" => "button", "wrapper" => "li.back")) ?>
	</ul>

	<div class="all_items i:defaultList filters">
<?		if($all_items): ?>
		<ul class="items">
<?			foreach($all_items as $item): ?>
			<li class="item item_id:<?= $item["item_id"] ?>">
				<h3><?= $item["useragent"] ?><span class="type"><?= $item["type"] ?></span></h3>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No duplet useragents.</p>
<?		endif; ?>
	</div>

</div>
