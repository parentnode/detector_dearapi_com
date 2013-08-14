<?php


function collectNotification($collection, $message) {

	$collection_path = BACKUP_FILE_PATH."notifications/";
	if(!file_exists($collection_path)) {
		FileSystem::mkdirr($collection_path);
	}

	$collection_file = BACKUP_FILE_PATH."notifications/".$collection;

	if(file_exists($collection_file)) {
		$notifications = file($collection_file);
	}
	else {
		$notifications = array();
	}

//	$string = print_r($_SERVER, true);

//	print BACKUP_FILE_PATH."notifications/".$collection;

	// log continue if less than 500 requests
	if(count($notifications) < 500) {

		$ip = getenv("HTTP_X_FORWARDED_FOR") ? getenv("HTTP_X_FORWARDED_FOR") : getenv("REMOTE_ADDR");
		$fp = fopen(BACKUP_FILE_PATH."notifications/".$collection, "a+");
		fwrite($fp, "$ip ".date("y-m-d H:i:s", time())." ".$message."\n");
//		fwrite($fp, "$ip ".date("y-m-d H:i:s", time())." ".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."\n");
		fclose($fp);

	}
	// send report and reset collection
	else {

		$message = "";
		foreach($notifications as $notification) {
			// reformat before sending

			$notification = preg_replace("/\; REFERRER\:/", ";\nREFERRER:", $notification);
			$notification = preg_replace("/\; USERAGENT\:/", ";\nUSERAGENT:", $notification);
			$notification = preg_replace("/\; IDENTIFIED\:/", ";\nIDENTIFIED:", $notification);

			$message .= $notification."\n";
		}

		if(notifier("NOTIFICATION: $collection", $message)) {
			$fp = fopen($collection_file, "w");
			fclose($fp);
		}

	}

}


function notifier($subject, $message, $recipients="martin@think.dk") {
	
	require_once("include/phpmailer/class.phpmailer.php");

	$mail             = new PHPMailer();
	$mail->Subject    = $subject;

	//$mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)

	$mail->CharSet    = "UTF-8";
	$mail->IsSMTP();

	$mail->SMTPAuth   = true;
	$mail->SMTPSecure = "ssl";
	$mail->Host       = "smtp.gmail.com";
	$mail->Port       = 465;
	$mail->Username   = "mailer@think.dk";
	$mail->Password   = "mi8y6td";

	$mail->SetFrom('mailer@think.dk', 'Think Postmaster');
	if(is_array($recipients)) {
		foreach($recipients as $recipient) {
			$mail->AddAddress($recipient);
		}
	}
	else {
		$mail->AddAddress($recipients);
	}

	$mail->Body = $message;

	return $mail->Send();

}

?>