<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>

	<h1>Users</h1>
	<p>The function is still in development</p>

	<fieldset>
		<?= $HTML->inputHidden("page_status", "done") ?>
		<?= $HTML->smartButton($this->translate("Back"), "done", "done", "fleft key:esc") ?>
	</fieldset>

<?= $this->designFooter() ?>
</div>