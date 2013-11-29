<?php
$action = $this->actions();

$tags = getPost("tags");
$search_string = getPost("search_string");

$IC = new Item();
$itemtype = "device";

// $tags, and possibly search_string
if($tags) {
	$all_items = $IC->getItems(array("itemtype" => $itemtype, "tags" => $tags, "order" => "status DESC"));
}
// search_string but no tags
else if($search_string) {
	$all_items = $IC->getItems(array("itemtype" => $itemtype, "order" => "status DESC"));
}
// no tags, no search string - show last
else {
	$all_items = $IC->getItems(array("itemtype" => $itemtype, "order" => "status DESC", "limit" => 50));
}

function searchFilter($item, $string) {
	
	if(preg_match("/".$string."/i", $item["name"])) {
		return true;
	}

	if(preg_match("/".$string."/i", $item["description"])) {
		return true;
	}

	foreach($item["useragents"] as $ua) {
		if(preg_match("/".$string."/i", $ua["useragent"])) {
			return true;
		}
	}
}


?>
<div class="scene i:defaultList defaultList <?= $itemtype ?>List">
	<h1>Devices</h1>

	<ul class="actions">
		<li class="new"><a href="/admin/<?= $itemtype ?>/new" class="button primary">New <?= $itemtype ?></a></li>
	</ul>

	<form class="options labelstyle:inject i:searchDevice" action="/admin/<?= $itemtype ?>/list" method="post" novalidate="novalidate">
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
				$item = $IC->getCompleteItem($item["id"]);

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
		<p>No devices.</p>
<?		endif; ?>
	</div>

</div>