<?php
global $action;
global $IC;
global $itemtype;
global $model;


$search = getPost("search");
$search_string = getPost("search_string");

// old search stored?
if(!$search && session()->value("unidentified_search")) {
	$search_string = session()->value("unidentified_search");
	$search = 1;
}


if($search) {
	$all_items = $model->unidentifiedUseragents($search_string);
	session()->value("unidentified_search", $search_string);
}
else {
	$all_items = $model->unidentifiedUseragents();
}

?>
<div class="scene defaultList unidentifiedList">
	<h1>Unidentified useragents</h1>

	<?= $model->formStart("/janitor/".$itemtype."/unidentified", array("class" => "options i:searchUnidentified labelstyle:inject")) ?>
		<?= $model->input("search", array("type" => "hidden", "value" => "true")) ?>
		<fieldset>
			<?= $model->input("search_string", array("type" => "string", "label" => "DB search (MySQL LIKE syntax)", "value" => $search_string)) ?>
		</fieldset>
		<ul class="actions">
			<?= $model->submit("Search", array("wrapper" => "li.search")) ?>
		</ul>
	<?= $model->formEnd() ?>

	<div class="stats">
		<p>A total of unidentified <?= pluralize(count($all_items), "useragent", "useragents")?> were returned by the server</p>
	</div>

	<div class="all_items i:unidentifiedList filters"
		data-csrf-token="<?= session()->value("csrf") ?>"
		data-useragent-delete="<?= security()->validPath("/janitor/device/deleteUnidentified") ?>"
		data-useragent-details="<?= security()->validPath("/janitor/device/unidentifiedUseragentDetails") ?>"
		data-useragent-identify="<?= security()->validPath("/janitor/device/identifyUnidentifiedId") ?>"
		data-useragent-add="<?= security()->validPath("/janitor/device/addUnidentifiedToDevice") ?>"
		data-device-clone="<?= security()->validPath("/janitor/device/cloneDevice") ?>"
		data-device-list="<?= security()->validPath("/janitor/device/list") ?>"
	>
<?		if($all_items): ?>
		<ul class="items">
<?			foreach($all_items as $item): ?>
			<li class="item ua_id:<?= $item["id"] ?>"><h3><?= $item["useragent"] ?></h3></li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No unidentified useragents.</p>
<?		endif; ?>
	</div>

</div>
