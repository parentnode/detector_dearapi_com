<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url.($this->getTargetId() ? ' form:target:'.$this->getTargetId() : '') ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>

	<?= $this->head($this->translate("Choose log")) ?>
	<fieldset>
		<?= $HTML->inputHidden("page_status", "list") ?>
		<label for="log">Choose log</label>
		<select name="log" id="log">
			<option value="">--- choose log ---</option>
		<?
			$handle = opendir(BACKUP_FILE_PATH);
			while($file = readdir($handle)) {
				if(strpos($file, "log") !== false) {
					print '<option value="'.$file.'">'.$file.'</option>';
				}
			}
		?>
		</select>
		<?= $HTML->smartButton("Select", "list", "list", "key:s") ?>
	</fieldset>

<?= $this->designFooter() ?>
</div>