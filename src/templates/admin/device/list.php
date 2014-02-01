<?php
$action = $this->actions();

$tags = getPost("tags");
$search = getPost("search");
$search_string = getPost("search_string");


//print $search.",".$search_string.",".$tags."<br>";

// if not new search - check for stored search values
if(!$search) {
	if(Session::value("device_search")) {
		$search_string = Session::value("device_search");
		$search = 1;
	}
	if(Session::value("device_search_tags")) {
		$tags = Session::value("device_search_tags");
		$search = 1;
	}
}

//print $search.",".$search_string.",".$tags."<br>";


$IC = new Item();
$itemtype = "device";
$model = $IC->typeObject($itemtype);


if($search && ($search_string || $tags)) {
	$search_parameters = array();

	// save search
	if($search_string) {
		$search_parameters["search_string"] = $search_string;
		Session::value("device_search", $search_string);
	}
	else {
		Session::reset("device_search");
	}
	if($tags) {
		$search_parameters["tags"] = $tags;
		Session::value("device_search_tags", $tags);
	}
	else {
		Session::reset("device_search_tags");
	}
	
	$all_items = $model->searchDevices($search_parameters);
}
else {
	$all_items = $IC->getItems(array("itemtype" => $itemtype, "order" => "modified_at DESC", "limit" => 50));

	Session::reset("device_search");
	Session::reset("device_search_tags");
}

?>
<div class="scene defaultList <?= $itemtype ?>List">
	<h1>Devices</h1>

	<ul class="actions">
		<li class="new"><a href="/admin/<?= $itemtype ?>/new" class="button primary">New <?= $itemtype ?></a></li>
	</ul>

	<form class="options labelstyle:inject i:searchDevice" action="/admin/<?= $itemtype ?>/list" method="post" novalidate="novalidate">
		<?= $model->input("search", array("type" => "hidden", "value" => "true")) ?>
		<fieldset>
			<?= $model->input("search_string", array("type" => "string", "label" => "Global search (regular expression)", "value" => $search_string)) ?>
		</fieldset>
		<ul class="actions">
			<li><input type="submit" value="Search" class="button" /></li>
		</ul>
<? 		if($tags): ?>
		<div class="tags">
			<ul class="tags">
<? 			$tags = explode(";", $tags);
			foreach($tags as $tag):
				list($context, $value) = explode(":", $tag);
				 ?>
				<li class="tag"><span class="context"><?= $context ?></span><span class="value"><?= $value ?></span></li>
<?			endforeach; ?>
			</ul>
		</div>
<?		endif; ?>
	</form>

	<div class="all_items i:defaultList taggable filters">
<?		if($all_items): ?>
		<ul class="items">
<?			foreach($all_items as $item): 
				$item = $IC->extendItem($item); ?>
			<li class="item item_id:<?= $item["id"] ?>">
				<h3><?= $item["name"] ?></h3>

<?				if($item["tags"]): ?>
				<ul class="tags">
<?					foreach($item["tags"] as $tag): ?>
					<li class="<?= $tag["context"] ?>"><span class="context"><?= $tag["context"] ?></span>:<span class="value"><?= $tag["value"] ?></span></li>
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
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No devices.</p>
<?		endif; ?>
	</div>

</div>
