<div class="logview <?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url.($this->getTargetId() ? ' form:target:'.$this->getTargetId() : '') ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>

	<?= $this->head($this->translate("Log summary")) ?>
	<fieldset class="init:logview">
		<?= $HTML->inputHidden("log", getVar("log")) ?>
		<?= $HTML->inputHidden("page_status", "done") ?>
		<?
			$days = getVar("days");
			if($days) {

				$dates = array_keys($days);
				sort($dates);
				print $HTML->p("You have selected the period <strong>" . $dates[0] . "</strong> to <strong>" . $dates[count($dates)-1] . "</strong>.");


				$entries = 0;
				$ip_index = array();

				$log_index = array();

				// set timeout

				foreach($days as $day) {
					// get logfile
					$log_lines = file($day);

					foreach($log_lines as $log_line) {

						// parse log file

						// date, time, ip, session, details
						preg_match("/([0-9\-]{10}) ([0-9\:]{8}) ([0-9]?) ([0-9\.]+) ([^$]+)/", $log_line, $matches);
						list(, $date, $time, $user_id, $ip, $details) = $matches;
						$ip_index[$ip] = isset($ip_index[$ip]) ? $ip_index[$ip]+1 : 1;
						$log_index[] = array("date"=>$date, "time"=>$time, "user_id"=>$user_id, "ip"=>$ip, "details"=>$details);

//						print $status."<br>";
//						print $log_line."<br>";
					}

					$entries += count($log_lines);
				}

				print $HTML->head("IPs used to access system", 3);
				print '<div class="ips">';
				foreach($ip_index as $ip => $visits) {
					print '<div class="ip">';
						print '<span class="ip">'.$ip.' ('.$visits.')</span>';
					print '</div>';
				}
				print '</div>';

//				print_r($days);
				print $HTML->p("The selected period has <strong>$entries</strong> log entries.");
				print '<div class="entries">';

				foreach($log_index as $entry) {
					print '<div class="entry">';
						print '<span class="date">'.$entry["date"].'</span>';
						print '<span class="time">'.$entry["time"].'</span>';
						print '<span class="ip">'.$entry["ip"].'</span>';
						print '<span class="user_id">'.$entry["user_id"].'</span>';
						print '<span class="details">'.$entry["details"].'</span>';
					print '</div>';
				}
				print '</div>';

			}
			else {
				print $HTML->p("You did not select a period");
			}
		?>
		<?= $HTML->smartButton("Done", "done", "done", "key:esc") ?>
	</fieldset>
<?= $this->designFooter() ?>
</div>