<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<!-- Martin Kaestel (c)+(p) 2006 //-->
	<!-- All material protected by copyrightlaws (as if you didnt know) //-->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>-- <?= $this->translate("Add file") ?> --</title>
	<style type="text/css">@import url(/css/include.css);</style>
</head>
<body onload="focus();">

<div class="<?= $this->getResponseColumn() ?> init:form aleft" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
	<fieldset>
		<form name="file" action="<?= $this->url ?>" method="post" enctype="multipart/form-data">
		<?= $HTML->head($this->translate("Add file")) ?>
		<?= $HTML->inputHidden("page_status", "save_file") ?>
		<?= $HTML->inputHidden("id", $id) ?>
		<?= $HTML->inputFile(false, "file") ?>
		<?= $HTML->button($this->translate("Save"), "save_file", false, "fright") ?>
		<?= $HTML->button($this->translate("Cancel"), false, "javascript:self.close()", "fleft") ?>
		</form>
	</fieldset>
<?= $this->designFooter() ?>
</div>

</body>
</html>