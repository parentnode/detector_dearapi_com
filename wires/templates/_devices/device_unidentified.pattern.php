<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url.($this->getTargetId() ? ' form:target:'.$this->getTargetId() : '') ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
<?
	$this->vars = $this->getTemplateObject()->vars;
	$this->varnames = $this->getTemplateObject()->varnames;

	$items = $this->getTemplateObject()->getUnidentifiedDevicesByPattern($this->vars["pattern"]);

?>

	<?= $this->head($this->translate("Unidentified devices")) ?>
	<fieldset>
		<?= $this->inputHidden("id", "") ?>
		<?= $this->inputHidden("page_status", "pattern") ?>

		<?= $this->smartButton("Back to the Complete list", "list", "list") ?>

		<?= $this->separator() ?>

		<div class="c">
			<div class="c">
				<?= $this->input("pattern") ?>
				<?= $this->smartButton("Match", "pattern", "pattern") ?>
			</div>
			<div class="c">
				<h3>Presets</h3>
				<ul class="presets init:devicepresets">
					<li class="brand:Mozilla">mac%firefox/2</li>
					<li class="brand:Mozilla">mac%firefox/3.0</li>
					<li class="brand:Mozilla">mac%firefox/3.5</li>
					<li class="brand:Mozilla">mac%firefox/3.6</li>
					<li class="brand:Mozilla">windows nt%firefox/2</li>
					<li class="brand:Mozilla">linux i686%firefox/2</li>
					<li class="brand:Mozilla">linux i686%firefox/3.0</li>
					<li class="brand:Mozilla">linux i686%firefox/3.5</li>
					<li class="brand:Mozilla">linux i686%firefox/3.6</li>

					<li class="separator"></li>

					<li class="brand:Google">mac%chrome/6%safari</li>
					<li class="brand:Google">mac%chrome/7%safari</li>
					<li class="brand:Google">mac%chrome/8%safari</li>
					<li class="brand:Google">mac%chrome/9%safari</li>
					<li class="brand:Google">mac%chrome/10%safari</li>
					<li class="brand:Google">windows nt%chrome/5</li>
					<li class="brand:Google">windows nt%chrome/6</li>
					<li class="brand:Google">windows nt%chrome/7</li>
					<li class="brand:Google">windows nt%chrome/8</li>
					<li class="brand:Google">windows nt%chrome/9</li>
					<li class="brand:Google">windows nt%chrome/10</li>

					<li class="separator"></li>

					<li class="brand:Microsoft">MSIE 9</li>

					<li class="separator"></li>

					<li class="brand:Apple">iphone;%os 4%version/4</li>
					<li class="brand:Apple">iphone;%os 4%version/5</li>
					<li class="brand:Apple">iphone;%os 3%version/4</li>

					<li class="brand:Apple">ipod;%os 4%version/4</li>
					<li class="brand:Apple">ipod;%os 4%version/5</li>
					<li class="brand:Apple">ipod;%os 3%version/4</li>

					<li class="brand:Apple">ipad;%os 3%version/4</li>
					<li class="brand:Apple">ipad;%os 4%version/5</li>

					<li class="brand:Apple">Macintosh%version/5%safari</li>
					<li class="brand:Apple">Macintosh%version/4.0.5%safari</li>
					<li class="brand:Apple">Macintosh%version/4%safari</li>
					<li class="brand:Apple">Macintosh%version/3%safari</li>

					<li class="brand:Apple">Windows NT%version/5%safari</li>
					<li class="brand:Apple">Windows NT%version/4.0.5%safari</li>
					<li class="brand:Apple">Windows NT%version/4%safari</li>
					<li class="brand:Apple">Windows NT%version/3%safari</li>

					<li class="separator"></li>

					<li class="brand:Opera">Opera/9.80 (Windows NT%version/11</li>
					<li class="brand:Opera">Opera/9.80 (Windows NT%version/10</li>
				</ul>
			</div>
		</div>
		<?= $this->p($this->translate("Matches:".count($items["id"]))) ?>
		<?

			$table = $HTML->table();

//			$table->setHeader(0, $this->translate("Useragents"), "");
			$table->setColumnType(0, "checkbox");
			$table->setHeader(1, "", "max search");

			if($items) {
				foreach($items["id"] as $key => $id) {
//					$ids[] = $id;
//					$status[] = "view_unidentified";
					
					$input[0][] = "useragent[".$id."]";
					if($this->vars["useragent"] && isset($this->vars["useragent"][$id]) && $this->vars["useragent"][$id]) {
						$input[1][] =  1;
					}
					else {
						$input[1][] =  0;
					}
					
				}

//				if(Session::getLogin()->validatePage("view_unidentified")) {
//					$table->setRowStatus($status);
//				}
//				$table->setRowId($items["id"]);
//				$table->setRowClasses($classes);

				$table->setColumnValues($input, $items["useragent"]);
				$table->setColumnClass(1, "max");
				print $table->build();

				?>

				<div class="c">
					<?= $HTML->head("Select device for these useragents", 2) ?>
					<?= $this->p("Make sure you have selected only useragents matching this device", "info") ?>
					<fieldset>
						<?= $HTML->select($this->translate("Select brand"), "brand_id", Generic::getItems(UT_BRA, false, "name"), stringOr(getVar("brand_id")), array("", "-"), "Util.Ajax.submitContainer('container:item');") ?>

						<?= getVar("brand_id") ? $HTML->select($this->translate("Select model"), "device_id", $this->getTemplateObject()->getModels(getVar("brand_id")), stringOr(getVar("device_id")), array("", "-"), "Util.Ajax.submitContainer('container:item');") : "" ?>
						<?= getVar("device_id") ? $HTML->smartButton($this->translate("Select"), "add_by_pattern", "add_by_pattern") : "" ?>
					</fieldset>
				</div>

				<?

			}
			else {
				print $this->p("No unidentified devices");
			}
		?>

	</fieldset>
<?= $this->designFooter() ?>
</div>