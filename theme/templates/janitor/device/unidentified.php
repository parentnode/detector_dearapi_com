<?php
global $action;
global $IC;
global $itemtype;
global $model;


$search = getPost("search");
$search_string = getPost("search_string");

$test_marker = getPost("test_marker");
$device_id = getPost("device_id");

$crossreference_marker = getPost("crossreference_marker");


// old search stored?
if(!$search && session()->value("unidentified_search")) {
	$search_string = session()->value("unidentified_search");
	$search = 1;
}

if($crossreference_marker && $device_id) {
	$all_items = $model->crossreferenceMarkersOnUnidentified($device_id);
}
else if($test_marker && $device_id) {
	$all_items = $model->testMarkersOnUnidentified($device_id);
}
else if($search) {
	$all_items = $model->unidentifiedUseragents($search_string);
	session()->value("unidentified_search", $search_string);
	$this->pageTitle($search_string);
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
			<?= $model->input("search_string", array("type" => "string", "label" => "DB search (MySQL REGEX syntax)", "value" => $search_string)) ?>
		</fieldset>
		<ul class="actions">
			<?= $model->submit("Search", array("wrapper" => "li.search")) ?>
		</ul>
	<?= $model->formEnd() ?>


	<div class="testmarkers i:testMarkersOnUnidentified i:collapseHeader"
		data-csrf-token="<?= session()->value("csrf") ?>"
		data-device-get="<?= $this->validPath("/janitor/device/getDevicesWithPatterns") ?>"
		data-device-test="<?= $this->validPath("/janitor/device/unidentified") ?>"
		>
	</div>

	<div class="testmarkers crossreference i:crossreferenceUnidentified i:collapseHeader"
		data-csrf-token="<?= session()->value("csrf") ?>"
		data-device-get="<?= $this->validPath("/janitor/device/getDevicesWithPatterns") ?>"
		data-device-test="<?= $this->validPath("/janitor/device/unidentified") ?>"
		>
	</div>


	<div class="stats">
		<? if($all_items): ?>
		<p>A total of <?= pluralize(count($all_items), "unidentified useragent", "unidentified useragents")?> were returned by the server</p>
		<? else: ?>
		<p>No devices returned.</p>
		<? endif; ?>
	</div>

	<div class="all_items i:unidentifiedList filters"
		data-csrf-token="<?= session()->value("csrf") ?>"
		data-useragent-delete="<?= $this->validPath("/janitor/device/deleteUnidentified") ?>"
		data-useragent-details="<?= $this->validPath("/janitor/device/unidentifiedUseragentDetails") ?>"
		data-useragent-identify="<?= $this->validPath("/janitor/device/identifyUnidentifiedId") ?>"
		data-useragent-add="<?= $this->validPath("/janitor/device/addUnidentifiedToDevice") ?>"
		data-device-clone="<?= $this->validPath("/janitor/device/cloneDevice") ?>"
		data-device-list="<?= $this->validPath("/janitor/device/list") ?>"
	>
<?		if($all_items): ?>
		<ul class="items">
<?			foreach($all_items as $item): ?>
			<li class="item ua_id:<?= $item["id"] ?>">
				<h3><?= stringOr($item["useragent"], "&nbsp;") ?></h3>

<? 				if(isset($item["matches"])): ?>
				<h4 class="matches<?= count($item["matches"]) > 1 ? " system_warning" : "" ?>"><em><?= $item["marker"] ?></em> matches <span>(<?= count($item["matches"]) ?>)</span>:</h4>
				<ul class="matches">
<? 					foreach($item["matches"] as $match): ?>
					<li><em><?= $match["name"] ?></em>, <?= $match["useragent"] ?></li>
<? 					endforeach; ?>
					<li class="note">This is not a complete list of matching useragents</li>
				</ul>
<? 				endif; ?>

<? 				if(isset($item["mismatches"])): ?>
				<h4 class="mismatches">Also found in these segments:</h4>
				<ul class="mismatches">
<? 					foreach($item["mismatches"] as $segment => $mismatch): ?>
					<li>
						<h5><?= $segment ?></h5>
						<ul>
<? 						foreach($mismatch as $match): ?>
							<li><em><?= $match["name"] ?></em>, <?= $match["useragent"] ?></em></li>
<? 						endforeach; ?>
							<li class="note">This is not a complete list of mismatching useragents</li>
						</ul>
					</li>
<? 					endforeach; ?>
				</ul>
<? 				endif; ?>
			</li>

<? 				if(isset($item["unid"])): ?>
<? 					foreach($item["unid"] as $unid): ?>
			<li class="item secondary ua_id:<?= $unid["id"] ?>">
				<h3><?= stringOr($unid["useragent"], "&nbsp;") ?></h3>
			</li>
<? 					endforeach; ?>
<? 				endif; ?>


<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No unidentified useragents.</p>
		<ul class="items"></ul>
<?		endif; ?>
	</div>

</div>
