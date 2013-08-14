<?php
$access_item = false;

if(isset($read_access) && $read_access) {
	return;
}

include_once($_SERVER["FRAMEWORK_PATH"]."/config/file_paths.php");
include_once("class/system/filesystem.class.php");

// control abuse but keeping an eye on imageproduction
$fileSystem = new FileSystem();
$fileSystem->mkdirr(BACKUP_FILE_PATH."log_image");
$requests = file(BACKUP_FILE_PATH."log_image/log");

// log continue if less than 20 requests
if(count($requests) < 20) {

	$ip = getenv("HTTP_X_FORWARDED_FOR") ? getenv("HTTP_X_FORWARDED_FOR") : getenv("REMOTE_ADDR");
	$fp = fopen(BACKUP_FILE_PATH."log_image/log", "a+");
	fwrite($fp, "$ip ".date("y-m-d H:i:s", time())." ".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."\n");
	fclose($fp);
	
}
// send report and reset log
else {

	require_once("include/phpmailer/class.phpmailer.php");

	$message = "";
	foreach($requests as $request) {
		$message .= $request;
	}

	$mail             = new PHPMailer();
	$mail->Subject    = "Image generation report: ".$_SERVER["HTTP_HOST"];

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
	$mail->AddAddress("martin@think.dk");

	$mail->Body = $message;

	// if report sending was successful, reset log
	if($mail->Send()) {
		$fp = fopen(BACKUP_FILE_PATH."log_image/log", "w");
		fclose($fp);
	}

}


// looking for jpg
if(substr($_SERVER["REQUEST_URI"], -4) == ".jpg") {
	include_once("class/system/image_tools.class.php");

	$imageTools = new ImageTools();
	// find image details
	// correct path /images/id/image
	if(preg_match_all("/\/([^\/]+)/i", $_SERVER["REQUEST_URI"], $matches)) {

		// did we get valid id
		if(isset($matches[1][1]) && is_numeric($matches[1][1])) {
			$id = $matches[1][1];
		}

		// did we get valid image
		if(isset($matches[1][2]) && $matches[1][2] && preg_match("/([0-9]*)x([0-9]*).jpg/i", $matches[1][2], $image)) {
			if($image[0] && (is_numeric($image[1]) || is_numeric($image[2]))) {
				$image_name = $image[0];
				$image_width = $image[1];
				$image_height = $image[2];
			}
		}

		// information ready to create image
		if($id && $image_name && file_exists(BACKUP_FILE_PATH.$id."/jpg")) {
			
			if(!file_exists(PUBLIC_FILE_PATH.$id)) {
				$fileSystem->mkdirr(PUBLIC_FILE_PATH.$id);
			}
			$imageTools->scaleImage(BACKUP_FILE_PATH.$id."/jpg", PUBLIC_FILE_PATH.$id."/", $image_width, $image_height, 2, $image_name);
			// redirect to image
			header("Location: /images/$id/$image_name");
			exit();
		}

	}
}

header("Location: /404");
exit();

?>
