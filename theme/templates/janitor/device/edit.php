<?php
global $action;
global $IC;
global $itemtype;
global $model;

$item_id = $action[1];
$item = $IC->getItem(array("id" => $item_id, "extend" => array("tags" => true, "mediae" => true)));
?>
<div class="scene defaultEdit <?= $itemtype ?>Edit">
	<h1>Edit <?= $itemtype ?></h1>


	<ul class="actions i:defaultEditActions item_id:<?= $item["item_id"] ?>"
		data-csrf-token="<?= session()->value("csrf") ?>"
		>
		<?= $HTML->link("List", "/janitor/".$itemtype."/list", array("class" => "button", "wrapper" => "li.cancel")) ?>
		<?= $HTML->link("Clone", "/janitor/".$itemtype."/cloneDevice/".$item["item_id"], array("class" => "button primary", "wrapper" => "li.clone.i:cloneDevice")) ?>
		<?= $JML->deleteButton("Delete", "/janitor/".$itemtype."/delete/".$item["item_id"]) ?>
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


	<div class="useragents i:editUseragents item_id:<?= $item["item_id"] ?>"
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

</div>
