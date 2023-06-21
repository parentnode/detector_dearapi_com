	</div>

	<div id="navigation">
		<ul>
			<?= $HTML->link("Devices", "/janitor/device/list", array("wrapper" => "li.device")) ?>
			<?= $HTML->link("Unidentified devices", "/janitor/device/unidentified", array("wrapper" => "li.unidentified")) ?>
			<?= $HTML->link("Statistics", "/janitor/statistic", array("wrapper" => "li.statistic")) ?>

			<?= $HTML->link("Users", "/janitor/admin/user/list", array("wrapper" => "li.user")) ?>
			<?= $HTML->link("Tags", "/janitor/admin/tag/list", array("wrapper" => "li.tags")) ?>
		</ul>
	</div>

	<div id="footer">
		<ul class="servicenavigation">
			<li class="copyright">Janitor, Manipulator, Modulator - parentNode - Copyright 2014</li>
		</ul>
	</div>

</div>

</body>
</html>