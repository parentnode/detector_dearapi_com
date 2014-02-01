<?php

$action = $this->actions();

$IC = new Item();

$tag = $IC->getTags(array("tag_id" => $action[1]));

?>
<div class="scene defaultEdit tagEdit">
	<h1>Edit tag</h1>

	<ul class="actions">a
		<li class="cancel"><a href="/admin/tag/list" class="button">Back</a></li>
	</ul>

	<div class="item">
		<form action="/admin/cms/tag/update/<?= $tag["id"] ?>" class="i:formDefaultEdit labelstyle:inject" method="post" enctype="multipart/form-data">

			<fieldset>
				<div class="field string required">
					<label for="input_context">Tag context</label>
					<input type="text" name="context" id="input_context" value="<?= $tag["context"] ?>" />
					<div class="help">
						<div class="hint">Tag context is the scope/category/relation of the tag</div>
						<div class="error">Tag context is always required</div>
					</div>
				</div>
				<div class="field string required">
					<label for="input_value">Tag value</label>
					<input type="text" name="value" id="input_value" value="<?= $tag["value"] ?>" />
					<div class="help">
						<div class="hint">Tag value is the actual value of the tag</div>
						<div class="error">Tag context is always required</div>
					</div>
				</div>

				<div class="field text autoexpand">
					<label for="input_description">Optional description</label>
					<textarea id="input_description" name="description"><?= $tag["description"] ?></textarea>
					<div class="help">
						<div class="hint">If tag requires any kind of explanation, write it here</div>
					</div>
				</div>				
			</fieldset>

			<ul class="actions">
				<li class="cancel"><a href="/admin/tag/list" class="button key:esc">Back</a></li>
				<li class="save"><input type="submit" value="Update" class="button primary key:s" /></li>
			</ul>

		</form>
	</div>

	<h2>Items with tag</h2>
	<div class="tag_items">
		<ul class="tag_items">
		<? foreach($tag["items"] as $item):
		$item = $IC->getCompleteItem($item["item_id"]);
		 ?>
			<li>
				<dl>
					<dt class="name">Name</dt>
					<dd class="name"><?= $item["name"] ?></dd>
					<dt class="itemtype">Itemtype</dt>
					<dd class="itemtype"><?= $item["itemtype"] ?></dd>
					<dt class="status">Status</dt>
					<dd class="status"><?= $item["status"] ? "enabled" : "disabled" ?></dd>
				</dl>
			</li>
		<? endforeach; ?>
		</ul>
	</div>

</div>