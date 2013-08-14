<div class="loglist <?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url.($this->getTargetId() ? ' form:target:'.$this->getTargetId() : '') ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>

	<?= $this->head($this->translate("Choose period")) ?>
	<fieldset class="init:loglist">
		<?= $HTML->inputHidden("log", getVar("log")) ?>
		<?= $HTML->inputHidden("page_status", "view") ?>
		<?
		
			$fs = new FileSystem();
			$raw_files = $fs->folderIterator(BACKUP_FILE_PATH . getVar("log"));
			$files = array();

			sort($raw_files);


			// index in groups
			foreach($raw_files as $file) {
				preg_match("/\/([0-9]{4})\/([0-9]{2})\/([0-9]{4}-[0-9]{2}-[0-9]{2})/", $file, $matches);
				list( ,$year, $month, $day) = $matches;

				$files[$year][$month][$day] = $file;
			}

			// get last year first
			krsort($files);


			print '<ul class="years">';

			foreach($files as $year => $months) {
				print '<li>';
					print '<h3>'.$year.'</h3>';

					print '<ul class="months">';
				 		foreach($months as $month => $days) {
				 			print '<li>';
								print '<h4>'.$month.'</h4>';

								print '<ul class="days">';
								foreach($days as $day => $file) {
									print '<li>';
										list($y, $m, $d) = explode("-", $day);
										print '<input type="checkbox" id="days_'.$day.'" value="'.$file.'" name="days['.$day.']"'. (isset($sel_days[$day]) ? ' checked="checked"' : '').' /><label for="days_'.$day.'">'.$d.'/'.$m.'</label>';
									print '</li>';
								}
								print '</ul>';

				 			print '</li>';
				 		}
					print '</ul>';

				print '</li>';
			}
			print '</ul>';

		?>

		<?= $HTML->smartButton("Show", "view", "view", "key:s") ?>
		<?= $HTML->smartButton("Back", "logs", "logs", "key:esc") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>