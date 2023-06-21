<?php
global $action;
global $IC;
global $itemtype;
global $model;

$item_id = $action[1];
$item = $IC->getItem(array("id" => $item_id, "extend" => array("tags" => true, "mediae" => true)));
$item = $model->getUseragents($item);
$item = $model->getMarkersAndExceptions($item);

$this->pageTitle($item["name"]);
?>
<div class="scene defaultEdit <?= $itemtype ?>Edit">
	<h1>Edit <?= $itemtype ?></h1>
	<h2><?= strip_tags($item["name"]) ?></h2>

	<ul class="actions i:defaultEditActions item_id:<?= $item["item_id"] ?>"
		data-csrf-token="<?= session()->value("csrf") ?>"
		>
		<?= $HTML->link("List", "/janitor/".$itemtype."/list", array("class" => "button", "wrapper" => "li.cancel")) ?>
		<?= $HTML->link("Clone", "/janitor/".$itemtype."/cloneDevice/".$item["item_id"], array("class" => "button primary", "wrapper" => "li.clone.i:cloneDevice")) ?>

		<?= $HTML->oneButtonForm("Delete", "/janitor/".$itemtype."/delete/".$item["item_id"], array(
			"wrapper" => "li.delete",
			"success-location" => "/janitor/".$itemtype."/list"
		)); ?>
		<?//= $JML->deleteButton("Delete", "/janitor/".$itemtype."/delete/".$item["item_id"]) ?>
	</ul>

	<div class="status i:defaultEditStatus item_id:<?= $item["item_id"] ?>"
		data-csrf-token="<?= session()->value("csrf") ?>"
		>
		<ul class="actions">
			<?= $JML->statusButton("Enable", "Disable", "/janitor/".$itemtype."/status", $item) ?>
		</ul>
	</div>

	<div class="item i:defaultEdit">
		<h2>Device</h2>
		<?= $model->formStart("update/".$item["item_id"], array("class" => "labelstyle:inject")) ?>
			<fieldset>
				<?= $model->input("published_at", array("value" => date("Y-m", strtotime($item["published_at"])))) ?>
				<?= $model->input("name", array("value" => $item["name"])) ?>
				<?= $model->input("description", array("class" => "autoexpand", "value" => $item["description"])) ?>
			</fieldset>

			<?= $JML->editActions($item) ?>

		<?= $model->formEnd() ?>
	</div>


	<?= $JML->editTags($item) ?>



	<div class="markers i:editMarkers i:collapseHeader item_id:<?= $item["item_id"] ?>"
		data-csrf-token="<?= session()->value("csrf") ?>"
		data-marker-delete="<?= security()->validPath("/janitor/device/deleteMarker/".$item["item_id"]) ?>" 
		data-marker-update="<?= security()->validPath("/janitor/device/updateMarker/".$item["item_id"]) ?>" 
		>
		<h2>Markers</h2>
		<?= $model->formStart("addMarker/".$item["item_id"], array("class" => "labelstyle:inject")) ?>
			<fieldset>
				<?= $model->input("marker") ?>
			</fieldset>

			<ul class="actions">
				<?= $model->submit("Add marker", array("class" => "primary", "wrapper" => "li.save")) ?>
			</ul>
		<?= $model->formEnd() ?>

		<ul class="markers">
<?		if($item["markers"]): ?>
<?			foreach($item["markers"] as $index => $marker): ?>
			<li class="marker marker_id:<?= $marker["id"] ?>">
				<span class="marker"><?= $marker["marker"] ?></span>
			</li>
<?			endforeach; ?>
<?		endif; ?>
		</ul>
	</div>


	<div class="exceptions i:editExceptions i:collapseHeader item_id:<?= $item["item_id"] ?>"
		data-csrf-token="<?= session()->value("csrf") ?>"
		data-exception-delete="<?= security()->validPath("/janitor/device/deleteException/".$item["item_id"]) ?>" 
		data-exception-update="<?= security()->validPath("/janitor/device/updateException/".$item["item_id"]) ?>" 
		>
		<h2>Exceptions</h2>
		<?= $model->formStart("addException/".$item["item_id"], array("class" => "labelstyle:inject")) ?>
			<fieldset>
				<?= $model->input("exception") ?>
			</fieldset>

			<ul class="actions">
				<?= $model->submit("Add exception", array("class" => "primary", "wrapper" => "li.save")) ?>
			</ul>
		<?= $model->formEnd() ?>

		<ul class="exceptions">
<?		if($item["exceptions"]): ?>
<?			foreach($item["exceptions"] as $index => $exception): ?>
			<li class="exception exception_id:<?= $exception["id"] ?>">
				<span class="exception"><?= $exception["exception"] ?></span>
			</li>
<?			endforeach; ?>
<?		endif; ?>
		</ul>
	</div>


	<div class="testmarkers i:testMarkers i:collapseHeader item_id:<?= $item["item_id"] ?>"
		data-csrf-token="<?= session()->value("csrf") ?>"
		data-device-test="<?= security()->validPath("/janitor/device/testMarkers") ?>"
		data-device-edit="<?= security()->validPath("/janitor/device/edit") ?>"
		data-device-list="<?= security()->validPath("/janitor/device/list") ?>"
		data-device-merge="<?= security()->validPath("/janitor/device/mergeDevice") ?>"
		data-useragent-delete="<?= security()->validPath("/janitor/device/deleteUseragent/".$item["item_id"]) ?>" 
		data-useragent-move="<?= security()->validPath("/janitor/device/moveUseragentToDevice") ?>" 
		>
	</div>


	<div class="useragents i:editUseragents i:collapseHeader item_id:<?= $item["item_id"] ?>"
		data-csrf-token="<?= session()->value("csrf") ?>"
		data-useragent-delete="<?= security()->validPath("/janitor/device/deleteUseragent/".$item["item_id"]) ?>" 
		>
		<h2>Useragents</h2>
		<?= $model->formStart("addUseragent/".$item["item_id"], array("class" => "labelstyle:inject")) ?>
		<!--form action="/janitor/admin/items/<?= $itemtype ?>/<?= $item["item_id"] ?>/addUseragent" class="labelstyle:inject" method="post" enctype="multipart/form-data"-->
			<fieldset>
				<?= $model->input("useragent") ?>
			</fieldset>

			<ul class="actions">
				<?= $model->submit("Add useragent", array("class" => "primary", "wrapper" => "li.save")) ?>
			</ul>
		<?= $model->formEnd() ?>

		<ul class="useragents">
<?		if($item["useragents"]): ?>
<?			foreach($item["useragents"] as $index => $useragent): ?>
			<li class="useragent ua_id:<?= $useragent["id"] ?>">
				<?= $useragent["useragent"] ?>
			</li>
<?			endforeach; ?>
<?		endif; ?>
		</ul>
	</div>


	<div class="merge i:mergeDevices i:collapseHeader item_id:<?= $item["item_id"] ?>"
		data-csrf-token="<?= session()->value("csrf") ?>"
		data-device-list="<?= security()->validPath("/janitor/device/list") ?>"
		data-device-edit="<?= security()->validPath("/janitor/device/edit") ?>"
		data-device-merge="<?= security()->validPath("/janitor/device/mergeDevice") ?>"
		>
	</div>

</div>
