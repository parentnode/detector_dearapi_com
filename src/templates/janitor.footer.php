	</div>

	<div id="navigation">
		<ul class="navigation">
			<li class="content">
				<h3>Content</h3>
				<ul class="subjects">
					<?= $HTML->link("Devices", "/janitor/device/list", array("wrapper" => "li.device")) ?>
					<?= $HTML->link("Unidentified devices", "/janitor/device/unidentified", array("wrapper" => "li.unidentified")) ?>
					<?= $HTML->link("Generate script", "/janitor/generate", array("wrapper" => "li.generate")) ?>
					<?= $HTML->link("Maintenance", "/janitor/maintenance", array("wrapper" => "li.maintenance")) ?>
				</ul>
			</li>
			<li class="site">
				<h3>Site</h3>
				<ul class="subjects">
					<?= $HTML->link("Tags", "/janitor/admin/tag/list", array("wrapper" => "li.tags")) ?>
					<?= $HTML->link("Log", "/janitor/admin/log/list", array("wrapper" => "li.logs")) ?>
				</ul>
			</li>
			<li class="users">
				<h3>Users</h3>
				<ul class="subjects">
					<?= $HTML->link("Users", "/janitor/admin/user/list", array("wrapper" => "li.user")) ?>
					<?= $HTML->link("Profile", "/janitor/admin/profile", array("wrapper" => "li.profile")) ?>
				</ul>
			</li>
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