<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?> noPrint" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
<?
	$this->details(1);

	$this->vars = $this->getTemplateObject()->vars;
	$this->varnames = $this->getTemplateObject()->varnames;

	$navtype = array(
		$this->translate("Create a folder for structural purposes"),
		$this->translate("A tag-based page, where the content is selected with tags and ordered by you"),
		$this->translate("A link to a specific url, internal or external")
		);

	$navtype_options = array(
		"parent",
		"tags",
		"url"
	);

?>

	<?= $this->head($this->translate("New page/folder")) ?>
	<div class="ci50">
		<fieldset>
			<?= $this->inputHidden("page_status", "save") ?>
			<?= $this->p($this->translate("When creating a new page or folder it needs a name. Type the name below."), "info") ?>

			<?= $this->input("name") ?>
		
			<div id="navType" class="<?= stringOr($this->vars["type"], "tags") ?>">

				<?= $this->p($this->translate("Choose the type"), "info") ?>
				<?= $this->label($this->varnames["type"]) ?>
				<?= $this->radio($navtype, "type", $navtype_options, stringOr($this->vars["type"], "tags"), "CRcolumn", "tags", 'onchange="document.getElementById(\'navType\').className = this.value;"') ?>
				<?= $this->separator() ?>

				<div class="nav urlNav">

					<?= $this->p($this->translate("Type the URL. Include HTTP:// for external pages."), "info") ?>

					<?= $this->input("url", false, "item_url") ?>

					<?= $this->p($this->translate("Or choose a local page from the dropdown."), "info") ?>

					<?= $this->select($this->varnames["page_list"], "page_list", array("id"=>$this->getTemplateObject()->pageList("file"), "values"=>$this->getTemplateObject()->pageList("values")), $this->vars["page_list"], array("", "-"), "Util.setValue('item_url', this.options[this.selectedIndex].value)") ?>
				</div>

				<div class="nav tagsNav">
					<?
						$tags = Page::getItems(UT_TAG);
						if($tags) {

							$tag_table = $this->table("incremental");
							$tag_table->setHeader(0, $this->varnames["tags"]);
							$tag_table->setColumnType(0, "select");
							$default_id[] = "0";
							$default_value[] = "Select";
							$column_1[0] = "tags";
							$column_1[1] = false;

							$column_1[2] = array_merge($default_id, $tags["id"]);
							$column_1[3] = array_merge($default_value, $tags["values"]);

							$tag_table->setColumnValues($column_1);
							print $tag_table->build();
						}
						else {
							print $this->p("No tags available", "info");
						}
					?>
					<?= $this->p($this->translate("If you do not choose tag(s) the page will be created with a default identifier tag (like page:page_name)."), "info") ?>
				</div>

			</div>
		</fieldset>
	</div>

	<div class="ci50">
		<fieldset>
			<?= $this->input("classname") ?>
			<?= $HTML->checkbox($this->varnames["hidden"], "hidden") ?>
			<?= $this->separator() ?>
			<?= $this->p("The SEO index value should uniquely identify the items it will hold. The value will be shown in the URL and it will factor in search engine optimizing.", "info") ?>
			<?= $this->input("sindex", "init:seoindex target:name") ?>
		</fieldset>
	</div>
	<div class="c">
		<fieldset>
			<?= $this->separator() ?>
			<?= $HTML->smartButton($this->translate("Save"), "save", "save", "fright key:s") ?>
			<?= $HTML->smartButton($this->translate("Cancel"), "done", "done", "fleft key:esc") ?>
		</fieldset>
	</div>
<?= $this->designFooter() ?>
</div>