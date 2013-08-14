<div class="<?= $this->getResponseColumn() ?> init:form form:action:<?= $this->url ?>" id="<?= $this->getContainerId() ?>">
<?= $this->designHeader() ?>
<?
	$this->details(1);

	$this->vars = $this->getTemplateObject()->vars;
	$this->varnames = $this->getTemplateObject()->varnames;

//	$id = $this->getTemplateObject()->getQueryResult(0, "id");
	$this->vars["name"] = stringOr($this->vars["name"], $this->getTemplateObject()->getQueryResult(0, "name"));
//	$name = $this->getQueryResult(0, "name");
	$tags = $this->getTemplateObject()->getQueryResult(0, "tags");
	$this->vars["url"] = stringOr($this->vars["url"], $this->getTemplateObject()->getQueryResult(0, "url"));
//	$url = $this->getQueryResult(0, "url");
	$this->vars["classname"] = stringOr($this->vars["classname"], $this->getTemplateObject()->getQueryResult(0, "classname"));
//	$classname = $this->getQueryResult(0, "classname");
	$hidden = $this->getTemplateObject()->getQueryResult(0, "hidden");
//	$this->vars["hidden"] = stringOr($this->vars["hidden"], $this->getTemplateObject()->getQueryResult(0, "hidden"));

	$this->vars["sindex"] = stringOr($this->vars["sindex"], $this->getTemplateObject()->getQueryResult(0, "sindex"));

	$this->vars["type"] = stringOr($this->vars["type"], ($tags ? "tags" : ($this->vars["url"] ? "url" : "")));

	$navtype = array(
		$this->translate("Parent folder"),
		$this->translate("By tags"),
		$this->translate("By URL")
		);

	$navtype_options = array(
		"parent",
		"tags",
		"url"
	);

?>
	<?= $this->head($this->translate("Edit page item")) ?>

	<div class="ci50">
		<fieldset>

			<?= $this->inputHidden("id", $id) ?>
			<?= $this->inputHidden("page_status", "update") ?>
			<?= $this->input("name") ?>

			<div id="navType" class="<?= $this->vars["type"] ?>">

				<?= $this->label($this->varnames["type"]) ?>
				<?= $this->radio($navtype, "type", $navtype_options, $this->vars["type"], "CRrow", false, 'onchange="document.getElementById(\'navType\').className = this.value;"') ?>

				<?= $this->separator() ?>


				<div class="nav urlNav">
					<?= $this->input("url", false, "item_url") ?>
					<?= $this->select($this->varnames["page_list"], "page_list", array("id"=>$this->getTemplateObject()->pageList("file"), "values"=>$this->getTemplateObject()->pageList("values")), $this->vars["page_list"], array("", "-"), "Util.setValue('item_url', this.options[this.selectedIndex].value)") ?>
				</div>

				<div class="nav tagsNav">
					<?
						$tags_all = Page::getItems(UT_TAG);

		//				$conditions = $this->getConditions();
						$point_tags = $this->getTemplateObject()->getTags($tags);

						$tag_table = $this->table("incremental");
		//				$tag_table->setHeader(0, $this->varnames["conditions"]);
						$tag_table->setHeader(0, $this->varnames["tags"]);

						$tag_table->setColumnType(0, "select");

						$default_id[] = "";
						$default_value[] = "Select";

						$column_1[0] = "tags";

						$tag_ids = array_merge($default_id, $tags_all["id"]);
						$tag_values = array_merge($default_value, $tags_all["values"]);
						if($point_tags["id"]) {
							foreach($point_tags["id"] as $key => $id) {

								$column_1[1][] = $point_tags["id"][$key];;
								$column_1[2][] = $tag_ids;
								$column_1[3][] = $tag_values;
							}
						}
						else {

							$column_1[1] = false;
							$column_1[2] = $tag_ids;
							$column_1[3] = $tag_values;

						}

						$tag_table->setColumnValues($column_1);
						print $tag_table->build();
					?>
				</div>
			</div>
		</fieldset>
	</div>
	
	<div class="ci50">
		<fieldset>
			<?= $this->head("Details", 2)?>
			<?= $this->input("classname") ?>
			<?= $HTML->checkbox($this->varnames["hidden"], "hidden", stringOr($this->vars["hidden"], $hidden)) ?>
			<?= $this->separator() ?>
			<?= $this->p("This SEO index should uniquely identify the items it will hold. The value will be shown in the URL and it will factor in search engine optimizing.", "info") ?>
			<?= $this->input("sindex", "init:seoindex target:name") ?>
		</fieldset>
	</div>
	<div class="c">
		<fieldset>
			<?= $this->separator() ?>
			<?= $this->smartButton($this->translate("Update"), "update", "update", "fright key:s") ?>
			<?= $this->smartButton($this->translate("Cancel"), "view", "view", "fleft key:esc") ?>
		</fieldset>
	</div>
<?= $this->designFooter() ?>
</div>