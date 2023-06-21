<div class="logview devices <?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url.($this->getTargetId() ? ' form:target:'.$this->getTargetId() : '') ?>" id="<?= $this->getContainerId() ?>">
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

				$fs = new FileSystem();
				$entries = 0;
				$ip_index = array();

				$logs = array();
				$log_index = array();
				$log_index["UA IDENTIFIED"] = array();
				$log_index["UA UNIQUE"] = array();
				$log_index["UA GUESSED"] = array();
				$log_index["UA UNIDENTIFIED"] = array();


				// due to memory overconsumption we need to store data in files instead of arrays
				// progress: saving data in files for log_index and logs
				// TODO: more flexible writer and reader function, instead of hardcoded file pointers as below
				// TODO: save top_devices_details info in file
				// TODO: read files for output section (it still uses arrays)

				$fs->mkdirr(BACKUP_FILE_PATH."cache");
				$cache_logs = fopen(BACKUP_FILE_PATH."cache/logs", "w+");
				$cache_log_index["UA IDENTIFIED"] = fopen(BACKUP_FILE_PATH."cache/ua_identified", "w+");
				$cache_log_index["UA UNIQUE"] = fopen(BACKUP_FILE_PATH."cache/ua_unique", "w+");
				$cache_log_index["UA GUESSED"] = fopen(BACKUP_FILE_PATH."cache/ua_guessed", "w+");
				$cache_log_index["UA UNIDENTIFIED"] = fopen(BACKUP_FILE_PATH."cache/ua_unidentified", "w+");

				// fwrite($fp, $log."\n");
				// fclose($fp);


				$top_devices = array();
				$top_devices_names = array();
				$top_devices_details = array();


				// TODO: set max timeout
				set_time_limit(0);

				foreach($days as $day) {
					// get logfile
//					print $day."<br>";
					$log_lines = file($day);
					$i = 0;

					print count($log_lines)."<br>";
					foreach($log_lines as $log_line) {

						// print "start process:" . ($i++) . "<br>";
						// print $log_line ."<br>";
						// parse log file

						//2012-05-01 00:05:07  109.202.140.211 fa605c58187990c4494adeb97f7f3429 UA IDENTIFIED; UA: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1); IDENTIFIED: Internet Explorer 6, Microsoft (5488)
						//2012-05-01 00:48:22  109.202.140.211 c578d488897b85577bb88168cae69949 UA UNIQUE; UA: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.162 Safari/535.19; IDENTIFIED: Chrome 18, Google (6561)
						//2012-05-01 03:42:43  81.7.189.37 14d3068ca0a16cee80419f9f64bfba15 UA GUESSED (space); UA: Mozilla/5.0 (compatible; MJ12bot/v1.4.3; http://www.majestic12.co.uk/bot.php?+); IDENTIFIED: U9, Motorola (455); PARTIAL:Mozilla/5.0 (compatible;
						//2012-04-30 04:35:13  81.7.189.54 fefca4ae53ccfe2836a5b98df60d70c3 UA GUESSED (known parts); UA: Mozilla/5.0 (Linux; U; Android 2.3.5; da-se; HTC_DesireS_S510e Build/GRJ90) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1; IDENTIFIED: Desire S (Saga, S510e), Android 2.2+, HTC (6510); PARTIAL:Mozilla/5.0 (Linux; U;%;% HTC_DesireS_S510e Build/GRJ90) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1
						//2012-04-30 03:16:07  81.7.189.37 c1e5e902798efe6ccc22e597781f87c4 UA GUESSED (/); UA: Mozilla/4.05 [en]; IDENTIFIED: BlackBerry 8100, RIM (63); PARTIAL:Mozilla

						// date, time, $user_id, ip, session, status, details
						if(preg_match("/([0-9\-]{10}) ([0-9\:]{8}) ([0-9]?) ([0-9\.]+) ([a-z0-9]+) ([^;]+); ([^$]+)/", $log_line, $matches)) {

							// print "device log<br>";

							list(, $date, $time, $user_id, $ip, $session, $status, $details) = $matches;
							$ip_index[$ip] = isset($ip_index[$ip]) ? $ip_index[$ip]+1 : 1;

							// getting top 200
							if(preg_match("/GUESSED/", $status)) {
								if(preg_match("/IDENTIFIED: ([^$]+) \(([\d]+)\); PARTIAL\:([^$]+)/", $details, $top_device)) {
									list(, $device, $id, $partial) = $top_device;
//									print $device.", ".$id.", ".$partial."<br>";

									if(!isset($top_devices[$id])) {
										$top_devices[$id] = 0;
									}
									$top_devices[$id]++;
									$top_devices_names[$id][0] = $device;
									$top_devices_details[$id][] = $details;
								}
							}
							else if($status == "UA UNIQUE" || $status == "UA IDENTIFIED") {
								if(preg_match("/IDENTIFIED: ([^$]+) \(([\d]+)\)/", $details, $top_device)) {
									list(, $device, $id) = $top_device;
//									print $device.", ".$id."<br>";

									if(!isset($top_devices[$id])) {
										$top_devices[$id] = 0;
									}
									$top_devices[$id]++;
									$top_devices_names[$id][0] = $device;
									$top_devices_details[$id][] = $details;
								}
							}
							else if($status == "UA UNIDENTIFIED") {
								if(!isset($top_devices["unknown"])) {
									$top_devices["unknown"] = 0;
								}
								$top_devices["unknown"]++;
								$top_devices_names["unknown"][0] = $details;
								$top_devices_details["unknown"][] = $details;
							}


							// split guesses
							if(preg_match("/GUESSED ([^\;]+)/", $status, $guess)) {
								$log_index["UA GUESSED"][$guess[1]][] = array("date"=>$date, "time"=>$time, "user_id"=>$user_id, "ip"=>$ip, "session"=>$session, "status"=>$status, "details"=>$details);

//								fwrite($cache_log_index["UA GUESSED"], json_encode(array("guess"=>$guess[1], "date"=>$date, "time"=>$time, "user_id"=>$user_id, "ip"=>$ip, "session"=>$session, "status"=>$status, "details"=>$details))."\n");
							}
							else {
								$log_index[$status][] = array("date"=>$date, "time"=>$time, "user_id"=>$user_id, "ip"=>$ip, "session"=>$session, "status"=>$status, "details"=>$details);

//								fwrite($cache_log_index[$status], json_encode(array("date"=>$date, "time"=>$time, "user_id"=>$user_id, "ip"=>$ip, "session"=>$session, "status"=>$status, "details"=>$details))."\n");
							}

							if(count($days) == 1) {
								$logs[] = array("date"=>$date, "time"=>$time, "user_id"=>$user_id, "ip"=>$ip, "session"=>$session, "status"=>$status, "details"=>$details);

//								fwrite($cache_logs, json_encode(array("date"=>$date, "time"=>$time, "user_id"=>$user_id, "ip"=>$ip, "session"=>$session, "status"=>$status, "details"=>$details))."\n");
							}
						}
						// try basic format
						else if(preg_match("/([0-9\-]{10}) ([0-9\:]{8}) ([0-9]?) ([0-9\.]+) ([^$]+)/", $log_line, $matches)) {

							// print "default log<br>";

							list(, $date, $time, $user_id, $ip, $details) = $matches;
							$ip_index[$ip] = isset($ip_index[$ip]) ? $ip_index[$ip]+1 : 1;

							if(count($days) == 1) {
								$logs[] = array("date"=>$date, "time"=>$time, "user_id"=>$user_id, "ip"=>$ip, "session"=>"", "status"=>"","details"=>$details);

//								fwrite($cache_logs, json_encode(array("date"=>$date, "time"=>$time, "user_id"=>$user_id, "ip"=>$ip, "session"=>$session, "status"=>$status, "details"=>$details))."\n");
							}
						}

//						print "done:" . $status."<br>";
					}


					$entries += count($log_lines);

				}

//				print_r($days);
				print $HTML->p("The selected period has <strong>$entries</strong> log entries.");

				print '<dl>';


				print '<dt>IDENTIFIED</dt>';
				print '<dd>';
					print '<span class="count">'.count($log_index["UA IDENTIFIED"]).'</span>';
					print '<span class="percent">('.round((count($log_index["UA IDENTIFIED"])/$entries)*100, 2).'%)</span>';
				print '</dd>';

				print '<dt>UNIQUE</dt>';
				print '<dd>';
					print '<span class="count">'.count($log_index["UA UNIQUE"]).'</span>';
					print '<span class="percent">('.round((count($log_index["UA UNIQUE"])/$entries)*100, 2).'%)</span>';
				print '</dd>';


				// guesses (variations of guesses)
				foreach($log_index["UA GUESSED"] as $variation => $entry) {
					print '<dt>GUESSED '.$variation.'</dt>';
					print '<dd>';
						print '<span class="count">'.count($log_index["UA GUESSED"][$variation]).'</span>';
						print '<span class="percent">('.round((count($log_index["UA GUESSED"][$variation])/$entries)*100, 2).'%)</span>';
					print '</dd>';
				}

				print '<dt>UNIDENTIFIED</dt>';
				print '<dd>';
					print '<span class="count">'.count($log_index["UA UNIDENTIFIED"]).'</span>';
					print '<span class="percent">('.round((count($log_index["UA UNIDENTIFIED"])/$entries)*100, 2).'%)</span>';
				print '</dd>';

				// make sure we get any faultly indexed stuff out
				foreach($log_index as $index => $value) {
					if(!preg_match("/UA IDENTIFIED|UA UNIQUE|UA GUESSED|UA UNIDENTIFIED/", $index)) {
						print '<dt>'.$index.'</dt>';
						print '<dd>';
							print '<span class="count">'.count($log_index[$index]).'</span>';
							print '<span class="percent">('.round((count($log_index[$index])/$entries)*100, 2).'%)</span>';
						print '</dd>';
					}
				}
				print '</dl>';

				print '<div class="c init:expandable id:unidentified">';
					print $HTML->head("Unidentified devices", 2);
					print '<div class="unidentified">';
					if($log_index["UA UNIDENTIFIED"]) {
						foreach($log_index["UA UNIDENTIFIED"] as $entry) {
							print '<div class="entry">';
								print '<span class="date">'.$entry["date"].'</span>';
								print '<span class="time">'.$entry["time"].'</span>';
								print '<span class="ip">'.$entry["ip"].'</span>';
								print '<span class="user_id">'.$entry["user_id"].'</span>';
								print '<span class="details">'.$entry["details"].'</span>';
							print '</div>';
						}
					}
					print '</div>';
				print '</div>';

				print '<div class="c init:expandable id:top200">';
					print $HTML->head("Top 200 devices", 2);
					print '<div class="top200">';

//					print_r($top_devices);
					$total = count($top_devices) > 100 ? 100 : count($top_devices);
					for($i = 0; $i < $total; $i++) {
						$highest_id = false;
						$highest_count = 0;
						foreach($top_devices as $index => $count) {
//							print $count ;
							if($count > $highest_count) {
								$highest_count = $count;
								$highest_id = $index;
							}
						}
//						print $highest_id."<br>";
						print '<div class="entry">';
							print '<span class="position">'.($i+1).'</span>';
							print '<span class="count">'.$top_devices[$highest_id].'</span>';
							print '<span class="percent">'.round(($top_devices[$highest_id]/$entries)*100, 2).'%</span>';
							print '<span class="device">'.$top_devices_names[$highest_id][0].'</span>';
							print '<div class="details">';
								foreach($top_devices_details[$highest_id] as $detail) {
									print '<div class="detail">'.$detail.'</div>';
								}
							print '</div>';
						print '</div>';

//						print $top_devices[$highest_id];
						$top_devices[$highest_id] = 0;
						//unset($top_devices[$highest_id]);

					}
//					print_r($top_devices);

					print '</div>';
				print '</div>';


				print '<div class="c init:expandable id:ips">';
					print $HTML->head("IPs used to access system", 2);
					print '<div class="ips">';
					foreach($ip_index as $ip => $visits) {
						print '<div class="ip">';
							print '<span class="ip">'.$ip.' ('.$visits.')</span>';
						print '</div>';
					}
					print '</div>';
				print '</div>';

				// needs to be modified - maybe I want the entries to be next to the groups of statuses but I also want the ordered list in full?

				print '<div class="c init:expandable id:entries">';
					print $HTML->head("All log entries", 2);

					print '<div class="entries">';

					if(count($days) == 1) {

						foreach($logs as $entry) {
							print '<div class="entry">';
								print '<span class="date">'.$entry["date"].'</span>';
								print '<span class="time">'.$entry["time"].'</span>';
								print '<span class="ip">'.$entry["ip"].'</span>';
								print '<span class="status '.strtolower($entry["status"]).'">'.$entry["status"].'</span>';
								print '<span class="user_id">'.$entry["user_id"].'</span>';
								print '<span class="session">'.$entry["session"].'</span>';
								print '<span class="details">'.$entry["details"].'</span>';
							print '</div>';
						}
					}
					else {
						print $HTML->p("Log entries only shown on single day summaries");
					}
					print '</div>';
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