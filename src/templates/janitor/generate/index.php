<?
global $action;
global $IC;
global $itemtype;
global $model;

$script = $model->createDetectionScript();
?>

<div class="scene generate i:generate">
	<h1>Generate detection script</h1>

	<div class="script">
		<code><?= $script ?></code>
	</div>

	<?= $model->formStart("writeDetectionScript", array("class" => "labelstyle:inject")) ?>
		<ul class="actions">
			<?= $model->submit("Write script", array("class" => "primary", "wrapper" => "li.write")) ?>
		</ul>
	<?= $model->formEnd() ?>

</div>