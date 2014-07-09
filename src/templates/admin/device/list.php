<?php
global $action;
global $IC;
global $itemtype;
global $model;


$tags = getPost("tags");
$search = getPost("search");
$search_string = getPost("search_string");


//print $search.",".$search_string.",".$tags."<br>";

// if not new search - check for stored search values
if(!$search) {
	if(session()->value("device_search")) {
		$search_string = session()->value("device_search");
		$search = 1;
	}
	if(session()->value("device_search_tags")) {
		$tags = session()->value("device_search_tags");
		$search = 1;
	}
}

//print $search.",".$search_string.",".$tags."<br>";



if($search && ($search_string || $tags)) {
	$search_parameters = array();

	// save search
	if($search_string) {
		$search_parameters["search_string"] = $search_string;
		session()->value("device_search", $search_string);
	}
	else {
		session()->reset("device_search");
	}
	if($tags) {
		$search_parameters["tags"] = $tags;
		session()->value("device_search_tags", $tags);
	}
	else {
		session()->reset("device_search_tags");
	}
	
	$all_items = $model->searchDevices($search_parameters);
}
else {
	$all_items = $IC->getItems(array("itemtype" => $itemtype, "order" => "modified_at DESC", "limit" => 50));

	session()->reset("device_search");
	session()->reset("device_search_tags");
}

?>
<div class="scene defaultList <?= $itemtype ?>List">
	<h1>Devices</h1>

	<ul class="actions">
		<?= $HTML->link("New ".$itemtype, "/admin/".$itemtype."/new", array("class" => "button primary key:n", "wrapper" => "li.new")) ?>
	</ul>

	<?= $model->formStart("/admin/".$itemtype."/list", array("class" => "options i:searchDevice labelstyle:inject")) ?>
		<?= $model->input("search", array("type" => "hidden", "value" => "true")) ?>
		<fieldset>
			<?= $model->input("search_string", array("type" => "string", "label" => "Global search (regular expression)", "value" => $search_string)) ?>
		</fieldset>
		<ul class="actions">
			<?= $model->submit("Search", array("wrapper" => "li.search")) ?>
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
	<?= $model->formEnd() ?>

	<div class="all_items i:defaultList taggable filters">
<?		if($all_items): ?>
		<ul class="items">
<?			foreach($all_items as $item): 
				$item = $IC->extendItem($item, array("tags" => true)); ?>
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
					<?= $HTML->link("Edit", "/admin/".$itemtype."/edit/".$item["id"], array("class" => "button", "wrapper" => "li.edit")) ?>
					<?= $HTML->deleteButton("Delete", "/admin/cms/delete/".$item["id"], array("js" => true)) ?>
					<?= $HTML->statusButton("Enable", "Disable", "/admin/cms/status", $item, array("js" => true)) ?>
				</ul>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No devices.</p>
<?		endif; ?>
	</div>

</div>
