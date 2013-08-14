<div class="c300 border">
	<fieldset>
		<?= $HTML->head("Sortable list") ?>
		<table class="list init:list arrange list:arrange">
		<tr>
			<th class="content">Lorem ipsum</th>
			<th class="search"><input type="text" name="list:search" value="search" /></th>
		</tr>
		<tr class="list0">
			<td>1 Lorem ipsum</td>
			<td>1 dolor sit amet  amet dolor sit amet dolor sit amet</td>
		</tr>
		<tr class="list1">
			<td>2 consectetuer</td>
			<td>2 adipiscing</td>
		</tr>
		<tr class="list0">
			<td>3 Lorem ipsum</td>
			<td>3 dolor sit amet</td>
		</tr>
		<tr class="list1">
			<td>4 consectetuer</td>
			<td>4 commodo</td>
		</tr>
		<tr class="list0">
			<td>5 facilisis</td>
			<td>5 dolor sit amet</td>
		</tr>
		<tr class="list1">
			<td>6 consectetuer</td>
			<td>6 eleifend</td>
		</tr>
		</table>
		<button class="arrange:save save:/test.php target:container:item_list fright disabled" type="button" disabled="disabled">Save</button>
	</fieldset>
</div>

<div class="c300">
	<div class="c150 border">
		<fieldset>
			<?= $HTML->head("Sortable list 2") ?>
			<table class="list init:list arrange list:arrange">
			<tr>
				<th class="selectall"><input type="checkbox" class="checkbox" name="list:selectall" value="" /></th>
				<th class="content">Lorem ipsum</th>
				<th></th>
			</tr>
			<tr class="list0 id:1">
				<td><input type="checkbox" class="checkbox" name="c" value="1" checked="checked" /></td>
				<td>Lorem ipsum 1</td>
				<td>Lorem ipsum 1</td>
			</tr>
			<tr class="list1 id:2">
				<td><input type="checkbox" class="checkbox" name="c" value="1" /></td>
				<td>Lorem ipsum 2</td>
				<td>Lorem ipsum 2</td>
			</tr>
			<tr class="list0 id:3">
				<td><input type="checkbox" class="checkbox" name="c" value="1" checked="checked" /></td>
				<td>Lorem ipsum 3</td>
				<td>Lorem ipsum 3</td>
			</tr>
			<tr class="list1 id:4">
				<td><input type="checkbox" class="checkbox" name="c" value="1" /></td>
				<td>Lorem ipsum 4</td>
				<td>Lorem ipsum 4</td>
			</tr>
			<tr class="list0 id:5">
				<td><input type="checkbox" class="checkbox" name="c" value="1" /></td>
				<td>Lorem ipsum 5</td>
				<td>Lorem ipsum 5</td>
			</tr>
			<tr class="list1 id:6">
				<td><input type="checkbox" class="checkbox" name="c" value="1" /></td>
				<td>Lorem ipsum 6</td>
				<td>Lorem ipsum 6</td>
			</tr>
			</table>
			<button class="arrange:save save:/test.php target:container:item_list fright disabled" type="button" disabled="disabled">Save</button>
		</fieldset>
	</div>
</div>

<div class="c300">
	<div class="c100 border">
		<fieldset>
			<?= $HTML->head("Sortable list") ?>
			<table class="list init:list arrange list:arrange">
			<tr>
				<th>Lorem ipsum 1</th>
				<th>Lorem ipsum 1</th>
			</tr>
			<tr class="list0">
				<td>Lorem ipsum 2</td>
				<td>Lorem ipsum 2</td>
			</tr>
			<tr class="list1">
				<td>Lorem ipsum 3</td>
				<td>Lorem ipsum 3</td>
			</tr>
			<tr class="list0">
				<td>Lorem ipsum 4</td>
				<td>Lorem ipsum 4</td>
			</tr>
			<tr class="list1">
				<td>Lorem ipsum 5</td>
				<td>Lorem ipsum 5</td>
			</tr>
			<tr class="list0">
				<td>Lorem ipsum 6</td>
				<td>Lorem ipsum 6</td>
			</tr>
			<tr class="list1">
				<td>Lorem ipsum 7</td>
				<td>Lorem ipsum 7</td>
			</tr>
			</table>
			<button class="arrange:save save:/test.php target:container:item_list fright disabled" type="button" disabled="disabled">Save</button>
		</fieldset>
	</div>
</div>

<div class="c300 border">
	<fieldset>
		<?= $HTML->head("Sortable list") ?>
		<table class="list init:list arrange list:arrange">
		<tr>
			<th class="content">Item title</th>
			<th>Unlt. access</th>
		</tr>
		<tr class="list0 id:1">
			<td>index:1</td>
			<td>no</td>
		</tr>
		<tr class="list1 id:2">
			<td class="indent_1">index:2</td>
			<td>yes</td>
		</tr>
		<tr class="list0 id:3">
			<td>index:3</td>
			<td>yes</td>
		</tr>
		<tr class="list1 disabled id:4">
			<td class="indent_1">index:4</td>
			<td>yes</td>
		</tr>
		<tr class="list0 id:5">
			<td class="indent_0">index:5</td>
			<td>no</td>
		</tr>
		<tr class="list1 id:6">
			<td class="indent_1">index:6</td>
			<td>no</td>
		</tr>
		<tr class="list0 id:7">
			<td class="indent_2">index:7</td>
			<td>no</td>
		</tr>
		</table>
		<button class="arrange:save save:/test.php target:container:item_list fright disabled" type="button" disabled="disabled">Save</button>
	</fieldset>
</div>