<?php

$action = $this->actions();

$IC = new Item();
$itemtype = "device";
$model = $IC->typeObject($itemtype);

$item = $IC->getCompleteItem($action[1]);
$item_id = $item["item_id"];

?>
<div class="scene defaultEdit <?= $itemtype ?>Edit">
	<h1>Edit <?= $itemtype ?></h1>

	<ul class="actions">
		<li class="cancel"><a href="/admin/<?= $itemtype ?>/list" class="button">Back</a></li>
		<li class="clone i:cloneDevice"><a href="/admin/<?= $itemtype ?>/cloneDevice/<?= $item_id ?>" class="button primary">Clone</a></li>
	</ul>

	<div class="item i:defaultEdit">
		<form action="/admin/cms/update/<?= $item_id ?>" class="labelstyle:inject" method="post" enctype="multipart/form-data">

			<fieldset>
				<?= $model->input("published_at", array("value" => date("Y-m", strtotime($item["published_at"])))) ?>
				<?= $model->input("name", array("value" => $item["name"])) ?>
				<?= $model->input("description", array("class" => "autoexpand", "value" => $item["description"])) ?>
			</fieldset>

			<ul class="actions">
				<li class="cancel"><a href="/admin/<?= $itemtype ?>/list" class="button key:esc">Back</a></li>
				<li class="save"><input type="submit" value="Update" class="button primary key:s" /></li>
			</ul>

		</form>
	</div>

	<h2>Tags</h2>
	<div class="tags i:defaultTags item_id:<?= $item_id ?>">
		<form action="/admin/cms/update/<?= $item_id ?>" class="labelstyle:inject" method="post" enctype="multipart/form-data">
			<fieldset>
				<?= $model->input("tags") ?>
			</fieldset>

			<ul class="actions">
				<li class="save"><input type="submit" value="Add tag" class="button primary" /></li>
			</ul>
		</form>

		<ul class="tags">
<?		if($item["tags"]): ?>
<?			foreach($item["tags"] as $index => $tag): ?>
			<li class="tag">
				<span class="context"><?= $tag["context"] ?></span>:<span class="value"><?= $tag["value"] ?></span>
			</li>
<?			endforeach; ?>
<?		endif; ?>
		</ul>
	</div>

	<h2>Useragents</h2>
	<div class="useragents i:editUseragents item_id:<?= $item_id ?>">
		<form action="/admin/cms/<?= $itemtype ?>/<?= $item_id ?>/addUseragent" class="labelstyle:inject" method="post" enctype="multipart/form-data">
			<fieldset>
				<?= $model->input("useragent") ?>
			</fieldset>

			<ul class="actions">
				<li class="save"><input type="submit" value="Add useragent" class="button primary" /></li>
			</ul>

		</form>

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
