	</div>

	<div id="navigation">
		<ul>
			<?= $HTML->link("Devices", "/janitor/device/list", array("wrapper" => "li.device")) ?>
			<?= $HTML->link("Unidentified devices", "/janitor/device/unidentified", array("wrapper" => "li.unidentified")) ?>
			<?= $HTML->link("Generate script", "/janitor/generate", array("wrapper" => "li.generate")) ?>
			<?= $HTML->link("Maintenance", "/janitor/maintenance", array("wrapper" => "li.maintenance")) ?>

			<?= $HTML->link("Users", "/janitor/admin/user/list", array("wrapper" => "li.user")) ?>
			<?= $HTML->link("Tags", "/janitor/admin/tag/list", array("wrapper" => "li.tags")) ?>
		</ul>
	</div>

	<div id="footer">
		<ul class="servicenavigation">
			<li class="copyright">Copyright 2016, parentNode.dk</li>
		</ul>
	</div>

</div>

</body>
</html>