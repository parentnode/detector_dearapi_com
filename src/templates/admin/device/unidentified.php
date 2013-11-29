<?php
$action = $this->actions();


$IC = new Item();
$itemtype = "device";
$model = $IC->typeObject($itemtype);

$all_items = $model->unidentifiedUseragents();

print_r($all_items);
?>
<div class="scene i:defaultList defaultList unidentifiedList">
	<h1>Unidentified useragents</h1>

	<form class="options labelstyle:inject" action="/admin/<?= $itemtype ?>/list" method="post" novalidate="novalidate">
		<fieldset>
			<div class="field string">
				<label>Global search (regular expression)</label>
				<input type="text" name="search_string" class="default" value="<?= $search_string ?>" />
			</div>
		</fieldset>
		<ul class="actions">
			<li><input type="submit" value="Search" class="button" /></li>
		</ul>
	</form>

	<div class="all_items">
<?		if($all_items): ?>
		<ul class="items taggable searchable">
<?			foreach($all_items as $item): 
//				$item = $IC->getCompleteItem($item["id"]);

				// search result?
				if(!$search_string || searchFilter($item, $search_string)) {
				 ?>
			<li class="item item_id:<?= $item["id"] ?>">
				<h3><?= $item["name"] ?></h3>

<?				if($item["tags"]): ?>
				<ul class="tags">
<?					foreach($item["tags"] as $tag): ?>
<?//						if($tag["context"] == "category"): ?>
					<li><span class="context"><?= $tag["context"] ?></span>:<span class="value"><?= $tag["value"] ?></span></li>
<?//						endif; ?>
<?					endforeach; ?>
				</ul>
<?				endif; ?>

				<ul class="actions">
					<li class="edit"><a href="/admin/<?= $itemtype ?>/edit/<?= $item["id"] ?>" class="button">Edit</a></li>
					<li class="delete">
						<form action="/admin/cms/delete/<?= $item["id"] ?>" class="i:formDefaultDelete" method="post" enctype="multipart/form-data">
							<input type="submit" value="Delete" class="button delete" />
						</form>
					</li>
					<li class="status">
						<form action="/admin/cms/<?= ($item["status"] == 1 ? "disable" : "enable") ?>/<?= $item["id"] ?>" class="i:formDefaultStatus" method="post" enctype="multipart/form-data">
							<input type="submit" value="<?= ($item["status"] == 1 ? "Disable" : "Enable") ?>" class="button status" />
						</form>
					</li>
				</ul>
			 </li>
<?				
				}


			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No unidentified useragents.</p>
<?		endif; ?>
	</div>

</div>
