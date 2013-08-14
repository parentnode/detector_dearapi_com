<?php
$access_item = false;

if(isset($read_access) && $read_access) {
	return;
}

$page_status = isset($_POST["page_status"]) ? $_POST["page_status"] : false;

$username = isset($_POST["username"]) ? $_POST["username"] : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";

if($page_status == "login") {
	if($username && $password) {
		include_once($_SERVER["FRAMEWORK_PATH"]."/class/system/page.class.php");
		$login_error = '<p class="error">Unknown login!</p>';
	}
	else {
		$login_error = $username ? '' : '<p class="error">Username?</p>';
		$login_error .= $password ? '' : '<p class="error">Password?</p>';
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Martin Kaestel/think.dk (c)+(p) 2006-2010 //-->
	<!-- All material protected by copyrightlaws (as if you didnt know) //-->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="language" content="en" />
	<title>-- login --</title>
	<style type="text/css">@import url(/css/login.css);</style>
</head>
<body onload="document.login.username.focus();">

<div id="page">
	<div id="login">

		<form name="login" action="index.php" method="post">
			<fieldset>
				<p>All visits to this page are logged and unauthorized attempts to access will be prosecuted to the full extent of the law.</p>
				<?= isset($login_error) ? $login_error : '' ?>
				<input type="hidden" name="page_status" value="login" class="hidden" />

				<label for="username">Username:</label>
				<input type="text" name="username" id="username" value="<?= $username ?>" />

				<label for="password">Password:</label>
				<input type="password" name="password" id="password" value="<?= $password ?>" />

				<input type="submit" class="button submit" value="Login" />
			</fieldset>
		</form>

	</div>
</div>

</body>
</html>