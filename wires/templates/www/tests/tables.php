<?php

$names = array("test[1]", "test[2]", "test[3]", "test[4]", "test[5]", "test[6]");
$selected = array(0,1,0,1,0,0);

?>
<div class="c300 border">
	<? $this->response_column = "c300 border"; ?>
	<?= $this->designHeader(); ?>
		<?= $HTML->head("Table 1") ?>
		<?php
		$table = $HTML->table();
		$table->setHeader(1, "Lorem", "max sortby");
		$table->setHeader(2, "Search", "search");

		$column_0[0] = $names;
		$column_0[1] = $selected;

		$column_1 = array("Lorem", "ipsum", "opion ipsu", "Lore option", "ipsu", "kap");
		$column_2 = array("Lorem", "ipsumt", "option ipsu", "Lore option", "ipsu", "kap");

		$table->setColumnType(0, "checkbox");
		$table->setColumnValues($column_0, $column_1, $column_2);
		?>
		<?= $table->build() ?>
	<?= $this->designFooter(); ?>
</div>

<div class="c300 border">
	<? $this->response_column = "c300 border"; ?>
	<?= $this->designHeader(); ?>
		<?= $HTML->head("Table 2") ?>
		<?php
		$table = $HTML->table();
		$table->setHeader(1, "Lorem", "sortby max");
		$table->setHeader(2, "Search", "search");

		// column 0 (checkbox [0] = name, [1] = checked 0|1)
		$column_0[0] = $names;
		$column_0[1] = $selected;

		// column 1
		$column_1 = array("Lorem", "ipsum", "opion ipsu", "Lore option", "ipsu", "kap");

		// column 2 (select [0] = name, [1] = selected false|index, [2] = option_values, [3] = option_texts)
		$column_2[0] = array("selectthis[1]", "selectthis[2]", "selectthis[3]", "selectthis[4]", "selectthis[5]", "selectthis[6]");
		$column_2[1] = array(0,1,2,false,5,3);

		// generic selects
		$column_2[2] = array("0", "1", "2", "3", "4", "5", "6");
		$column_2[3] = array("op0", "op1", "op2", "op3", "op4", "op5", "op6");

		// individual selects
/*
		$option_values[0] = array("0", "1", "2", "3", "4", "5", "6");
		$option_texts[0] = array("0-op0", "0-op1", "0-op2", "0-op3", "0-op4", "0-op5", "0-op6");
		$option_values[1] = array("0", "1", "2", "3", "4", "5", "6");
		$option_texts[1] = array("1-op0", "1-op1", "1-op2", "1-op3", "1-op4", "1-op5", "1-op6");
		$option_values[2] = array("0", "1", "2", "3", "4", "5", "6");
		$option_texts[2] = array("2-op0", "2-op1", "2-op2", "2-op3", "2-op4", "2-op5", "2-op6");
		$option_values[3] = array("0", "1", "2", "3", "4", "5", "6");
		$option_texts[3] = array("3-op0", "3-op1", "3-op2", "3-op3", "3-op4", "3-op5", "3-op6");
		$option_values[4] = array("0", "1", "2", "3", "4", "5", "6");
		$option_texts[4] = array("4-op0", "4-op1", "4-op2", "4-op3", "4-op4", "4-op5", "4-op6");
		$option_values[5] = array("0", "1", "2", "3", "4", "5", "6");
		$option_texts[5] = array("5-op0", "5-op1", "5-op2", "5-op3", "5-op4", "5-op5", "5-op6");
		$column_2[2] = $option_values;
		$column_2[3] = $option_texts;
*/

		$table->setColumnType(0, "checkbox");
		$table->setColumnType(2, "select");
		$table->setColumnValues($column_0, $column_1, $column_2);
		?>
		<?= $table->build() ?>
	<?= $this->designFooter(); ?>
</div>

<div class="c300 border">
	<? $this->response_column = "c300 border"; ?>
	<?= $this->designHeader(); ?>
		<?= $HTML->head("Table 3") ?>
		<?php
		$table = $HTML->table("incremental");
		$table->setHeader(0, "Quantity");
		$table->setHeader(1, "Units");
		$table->setHeader(2, "Note", "max");

		// column 0
		$column_0[0] = "quantity";
		$column_0[1] = array("");

		// column 1 (select [0] = name, [1] = selected false|index, [2] = option_values, [3] = option_texts)
		$column_1[0] = "units";
		$column_1[1] = array(false);
		$column_1[2] = array("-", "1", "2", "3");
		$column_1[3] = array("-", "dåse", "ml", "cl");

		$column_2[0] = "note";
		$column_2[1] = array("");

		$table->setColumnType(0, "input");
		$table->setColumnClass(0, "w50");

		$table->setColumnType(1, "select");

		$table->setColumnType(2, "input");
		$table->setColumnClass(2, "max");

		$table->setColumnValues($column_0, $column_1, $column_2);
		?>
		<?= $table->build() ?>
	<?= $this->designFooter(); ?>
</div>

<div class="c300">
	<div class="c150 border">
		<? $this->response_column = "c150 border"; ?>
		<?= $this->designHeader(); ?>
			<?= $HTML->head("Table 4") ?>
			<?php
			for($i=0;$i<7;$i++){
				$checked[0][] = "c";
				$checked[1][] = 0;
			
				$select[0][] = "name".$i;
				$select[1][] = 2;
				$select[2][] = array("option1","option2","option3");
				$select[3][] = array("option1","option2","option3");
			}
			$checked[1][0] = 1;
			$checked[1][2] = 1;				
		
			$text = "lorem ipsum";
		
			$table = $HTML->table();
			$table->setHeader(0, $text, "aleft");
		
			$column_1 = array($text,$text,$text,$text,$text,$text,$text);
		
			$table->setColumnType(0, "checkbox");
			$table->setColumnType(2, "select");
			$table->setColumnValues($checked,$column_1,$select,$column_1);
			?>
			<?= $table->build() ?>
		<?= $this->designFooter(); ?>
	</div>
	<div class="c150 border">
		<? $this->response_column = "c150 border"; ?>
		<?= $this->designHeader(); ?>
			<?= $HTML->head("Table 5") ?>
			<?php
			$table = $HTML->table();
			$table->setHeader(1, "Lorem", "max sortby");
			$table->setHeader(2, "Search", "search");

			$column_0[0] = $names;
			$column_0[1] = $selected;

			$column_1 = array("Lorem", "ipsum", "opion ipsu", "Lore option", "ipsu", "kap");
			$column_2 = array("Lorem", "ipsumt", "option ipsu", "Lore option", "ipsu", "kap");

			$table->setColumnType(0, "checkbox");
			$table->setColumnValues($column_0, $column_1, $column_2);
			?>
			<?= $table->build() ?>
		<?= $this->designFooter(); ?>
	</div>
</div>

<div class="c300">
	<div class="c150 border">
		<? $this->response_column = "c75 border"; ?>
		<?= $this->designHeader(); ?>
			<?= $HTML->head("Table 6") ?>
			<?php
			$table = $HTML->table();
			$table->setHeader(0, "Lorem", "max");
			$table->setHeader(1, "ipsum", "acenter");
		
			$text = "ipsum";
			$column_0 = array($text,$text,$text,$text,$text,$text,$text,$text,$text,$text,$text,$text,$text,$text,$text,$text,$text);
			$column_1 = array(0,1,2,3,4,5,6,4,5,6,7,8,6,6,0,1,0);
		
			$table->setColumnType(0,"indent");
			$table->setColumnIndent(0, $column_1);
			$table->setColumnClass(1, "acenter");
			$table->setColumnValues($column_0, $column_1);
			?>
			<?= $table->build() ?>

			<?= $HTML->head("Table 7") ?>
			<?php
			$table = $HTML->table("incremental");
			$table->setHeader(0, "Quantity");
			$table->setHeader(1, "Units");
			$table->setHeader(2, "Note", "max");

			// column 0
			$column_0[0] = "quantity";
			$column_0[1] = array("");

			// column 1 (select [0] = name, [1] = selected false|index, [2] = option_values, [3] = option_texts)
			$column_1[0] = "units";
			$column_1[1] = array(false);
			$column_1[2] = array("-", "1", "2", "3");
			$column_1[3] = array("-", "dåse", "ml", "cl");

			$column_2[0] = "note";
			$column_2[1] = array("");

			$table->setColumnType(0, "input");
			$table->setColumnClass(0, "w50");

			$table->setColumnType(1, "select");

			$table->setColumnType(2, "input");
			$table->setColumnClass(2, "max");

			$table->setColumnValues($column_0, $column_1, $column_2);
			?>
			<?= $table->build() ?>
		<?= $this->designFooter(); ?>
	</div>
	<div class="c75">
		<div class="c75 border">
			<? $this->response_column = "c75 border"; ?>
			<?= $this->designHeader(); ?>
				<?= $HTML->head("Table 8") ?>
				<?php
				$table = $HTML->table();
				$table->setHeader(0, "Search", "search");
				$column_0 = array("KLorem1", "ipsum2", "jopion ipsu3", "J. Lore option4", "i. psu5", "I. kap6");
				$table->setColumnValues($column_0);
				?>
				<?= $table->build() ?>
			<?= $this->designFooter(); ?>
		</div>

		<div class="c75 border">
			<? $this->response_column = "c75 border"; ?>
			<?= $this->designHeader(); ?>
				<?= $HTML->head("Table 9") ?>
				<?php
				$table = $HTML->table();
				$table->setHeader(0, "Ipsum");
				$column_0 = array("KLorem1", "ipsum2", "jopion ipsu3", "J. Lore option4", "i. psu5", "I. kap6");
				$table->setColumnValues($column_0);
				?>
				<?= $table->build() ?>
			<?= $this->designFooter(); ?>
		</div>
	</div>
	<div class="c75 border">
		<? $this->response_column = "c75 border"; ?>
		<?= $this->designHeader(); ?>
			<?= $HTML->head("Table 10") ?>
			<?php
			for($i=0;$i<7;$i++){
				$select[0][] = "name".$i;
				$select[1][] = 2;
				$select[2][] = array("option1","option2","option3");
				$select[3][] = array("option1","option2","option3");
			}
			$select[1][3] = 1;
			$select[1][6] = 0;

			$table = $HTML->table();
			$table->setHeader(0, $text, "aleft");

			$table->setColumnType(0, "select");
			$table->setColumnValues($select);
			?>
			<?= $table->build() ?>
		<?= $this->designFooter(); ?>
	</div>
</div>
<div class="c300 border">
	<? $this->response_column = "c300 border"; ?>
	<?= $this->designHeader(); ?>
	<?= $HTML->head("Commodo congue luptatum") ?>
	<div class="c225">
		<?= $HTML->head("Table 11") ?>
		<?php
		$table = $HTML->table();
		$table->setHeader(1, "Lorem", "sortby max");
		$table->setHeader(2, "Search", "search");

		// column 0 (checkbox [0] = name, [1] = checked 0|1)
		$column_0[0] = $names;
		$column_0[1] = $selected;

		// column 1
		$column_1 = array("Lorem", "ipsum", "opion ipsu", "Lore option", "ipsu", "kap");

		// column 2 (select [0] = name, [1] = selected false|index, [2] = option_values, [3] = option_texts)
		$column_2[0] = array("selectthis[1]", "selectthis[2]", "selectthis[3]", "selectthis[4]", "selectthis[5]", "selectthis[6]");
		$column_2[1] = array(0,1,2,false,5,3);

		// generic selects
		$column_2[2] = array("0", "1", "2", "3", "4", "5", "6");
		$column_2[3] = array("op0", "op1", "op2", "op3", "op4", "op5", "op6");

		// individual selects
/*
		$option_values[0] = array("0", "1", "2", "3", "4", "5", "6");
		$option_texts[0] = array("0-op0", "0-op1", "0-op2", "0-op3", "0-op4", "0-op5", "0-op6");
		$option_values[1] = array("0", "1", "2", "3", "4", "5", "6");
		$option_texts[1] = array("1-op0", "1-op1", "1-op2", "1-op3", "1-op4", "1-op5", "1-op6");
		$option_values[2] = array("0", "1", "2", "3", "4", "5", "6");
		$option_texts[2] = array("2-op0", "2-op1", "2-op2", "2-op3", "2-op4", "2-op5", "2-op6");
		$option_values[3] = array("0", "1", "2", "3", "4", "5", "6");
		$option_texts[3] = array("3-op0", "3-op1", "3-op2", "3-op3", "3-op4", "3-op5", "3-op6");
		$option_values[4] = array("0", "1", "2", "3", "4", "5", "6");
		$option_texts[4] = array("4-op0", "4-op1", "4-op2", "4-op3", "4-op4", "4-op5", "4-op6");
		$option_values[5] = array("0", "1", "2", "3", "4", "5", "6");
		$option_texts[5] = array("5-op0", "5-op1", "5-op2", "5-op3", "5-op4", "5-op5", "5-op6");
		$column_2[2] = $option_values;
		$column_2[3] = $option_texts;
*/

		$table->setColumnType(0, "checkbox");
		$table->setColumnType(2, "select");
		$table->setColumnValues($column_0, $column_1, $column_2);
		?>
		<?= $table->build() ?>
	</div>
	<div class="c75">
		<?= $HTML->head("Table 12") ?>
		<?php
		$table = $HTML->table("indented sort");
		$table->setHeader(0,"","acenter");
		$table->setHeader(1,"div:form f11", "max");
		
		$table->setColumnType(0, "checkbox");
		
		$column1_0[1] = array(0,1,0,1,0,1);
		$column1_0[0] = array("check_1", "check_2", "check_3", "check_4", "check_5", "check_6");
		
		$indent = array(0,1,2,1,2,3);
		$table->setColumnType(1,"indent");
		$table->setColumnIndent(1, $indent);
		
		$table->setColumnValues($column1_0, $column1_0[0]);
		?>
		<?= $table->build() ?>
	</div>
	<?= $this->designFooter(); ?>
</div>

<div class="c300">
	<div class="c100 border">
		<? $this->response_column = "c100 border"; ?>
		<?= $this->designHeader(); ?>
			<?= $HTML->head("Table 13") ?>
			<?php
			$table = $HTML->table();
			$table->setHeader(0,"Lorem ipsum", "acenter");
			$table->setHeader(1,"Lorem ipsum", "acenter");
		
			$column_0 = array("martin ipsum","Lorem ipsum");
			$column_1 = array("Lorem ipsum", "Lorem ipsum");
			$table->setColumnValues($column_0, $column_1);
			?>
			<?= $table->build() ?>
		<?= $this->designFooter(); ?>
	</div>
	<div class="c200 border">
		<? $this->response_column = "c200 border"; ?>
		<?= $this->designHeader(); ?>
			<?= $HTML->head("Table 14") ?>
			<?php
			$checked = array();
			$select = array();
			for($i = 0; $i < 7; $i++) {
				$checked[0][] = "c";
				$checked[1][] = 0;
		
				$select[0][] = "name".$i;
				$select[1][] = 2;
				$select[2][] = array(0, 1, 2);
				$select[3][] = array("option1","option2","option3");
			}
			$checked[1][0] = 1;
			$checked[1][2] = 1;

			$text = "lorem ipsum";

			$table = $HTML->table();
			$table->setHeader(1, $text, "aleft");
	
			$column_1 = array($text,$text,$text,$text,$text,$text,$text);
	
			$table->setColumnType(0, "checkbox");
			$table->setColumnType(2, "select");
			$table->setColumnValues($checked,$column_1,$select,$column_1);
			?>
			<?= $table->build() ?>
		<?= $this->designFooter(); ?>
	</div>
</div>


<div class="c300">
	<div class="c100 border">
		<? $this->response_column = "c100 border"; ?>
		<?= $this->designHeader(); ?>
			<?= $HTML->head("Table 15") ?>
			<?php
			$table = $HTML->table();
			$table->setHeader(0,"Linked items", "max");
		
			$column_0 = array("lorem ipsum", "Lorem ipsum");
			$column_1 = array("Lorem ipsum", "Lorem ipsum");
		
			$links = array("content_providers.php?page_status=view&amp;id=10","content_providers.php?page_status=view&amp;id=10");
			$table->setRowStatus($links);
			$table->setColumnValues($column_0, $column_1);
			?>
			<?= $table->build() ?>
		<?= $this->designFooter(); ?>
	</div>
	<div class="c200 border">
		<? $this->response_column = "c200 border"; ?>
		<?= $this->designHeader(); ?>
			<?= $HTML->head("Table 16") ?>
			<?php
			$table = $HTML->table("incremental");
			$table->setHeader(0, "Quantity");
			$table->setHeader(1, "Units");
			$table->setHeader(2, "Note", "max");

			// column 0
			$column_0[0] = "quantity";
			$column_0[1] = array("");

			// column 1 (select [0] = name, [1] = selected false|index, [2] = option_values, [3] = option_texts)
			$column_1[0] = "units";
			$column_1[1] = array(false);
			$column_1[2] = array("-", "1", "2", "3");
			$column_1[3] = array("-", "dåse", "ml", "cl");

			$column_2[0] = "note";
			$column_2[1] = array("");

			$table->setColumnType(0, "input");
			$table->setColumnClass(0, "w50");

			$table->setColumnType(1, "select");

			$table->setColumnType(2, "input");
			$table->setColumnClass(2, "max");

			$table->setColumnValues($column_0, $column_1, $column_2);
			?>
			<?= $table->build() ?>
		<?= $this->designFooter(); ?>
	</div>
</div>