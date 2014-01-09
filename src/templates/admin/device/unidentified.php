<?php
$action = $this->actions();

$search = getPost("search");
$search_string = getPost("search_string");

// old search stored?
if(!$search && Session::value("unidentified_search")) {
	$search_string = Session::value("unidentified_search");
	$search = 1;
}


$IC = new Item();
$itemtype = "device";
$model = $IC->typeObject($itemtype);

if($search) {
	$all_items = $model->unidentifiedUseragents($search_string);
	Session::value("unidentified_search", $search_string);
}
else {
	$all_items = $model->unidentifiedUseragents();
}

?>
<div class="scene defaultList unidentifiedList">
	<h1>Unidentified useragents</h1>

	<form class="options labelstyle:inject i:searchUnidentified" action="/admin/<?= $itemtype ?>/unidentified" method="post" novalidate="novalidate">
		<?= $model->input("search", array("type" => "hidden", "value" => "true")) ?>
		<fieldset>
			<?= $model->input("search_string", array("type" => "string", "label" => "DB search (MySQL LIKE syntax)", "value" => $search_string)) ?>
		</fieldset>
		<ul class="actions">
			<li><input type="submit" value="Search" class="button" /></li>
		</ul>
	</form>

	<div class="stats">
		<p>A total of unidentified <?= pluralize(count($all_items), "useragent", "useragents")?> were returned by the server</p>
	</div>

	<div class="all_items i:unidentifiedList filters">
<?		if($all_items): ?>
		<ul class="items">
<?			foreach($all_items as $item): ?>
			<li class="item ua_id:<?= $item["id"] ?>">
				<h3><?= $item["useragent"] ?></h3>
<?
					
					
//				<ul class="info">
//					<li class="identified_as"><?= $item["identified_as"] ? ></li>
//					<li class="identified_at"><?= $item["identified_at"] ? ></li>
//					<li class="visits"><?= $item["visits"] ? ></li>
//					<li class="comment"><?= $item["comment"] ? ></li>
//				</ul>
//
//				<ul class="actions">
//					<li class="delete">
//						<form action="/admin/cms/delete/<?= $item["id"] ? >" class="i:formDefaultDelete" method="post" enctype="multipart/form-data">
//							<input type="submit" value="Delete" class="button delete" />
//						</form>
//					</li>
//				</ul>

?>
			 </li>
<?			endforeach; ?>
		</ul>
<?		else: ?>
		<p>No unidentified useragents.</p>
<?		endif; ?>
	</div>

</div>
