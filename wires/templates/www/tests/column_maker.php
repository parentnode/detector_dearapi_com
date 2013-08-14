<?php

?>
<style>
	div.c25 {
		background: #ffffff;
	}
	div.c100 p {
		text-align: center;
		padding: 0;
		background: #f2f2f2;
	}
	div.c25 p {
		text-align: center;
		min-height: 300px;
		padding: 0;
		background: #ffe5e5;
	}
</style>
<script>
function setPadding(input) {
	ps = document.getElementsByTagName("p");
	for(var i = 0; p = ps[i]; i++) {
		p.style.marginRight = input.value + "px";
		p.style.marginLeft = input.value + "px";
	}
}
</script>
<div class="c300">
	<label for="padding">padding:</label>
	<input type="text" class="text" value="" id="padding" name="padding" onkeyup="setPadding(this);" />
</div>
<div class="c300">
	<div class="c100">
		<p>c100</p>
		<div class="c25"><p>c25</p></div>
		<div class="c25"><p>c25</p></div>
		<div class="c25"><p>c25</p></div>
		<div class="c25"><p>c25</p></div>
	</div>
	<div class="c100">
		<p>c100</p>
		<div class="c25"><p>c25</p></div>
		<div class="c25"><p>c25</p></div>
		<div class="c25"><p>c25</p></div>
		<div class="c25"><p>c25</p></div>
	</div>
	<div class="c100">
		<p>c100</p>
		<div class="c25"><p>c25</p></div>
		<div class="c25"><p>c25</p></div>
		<div class="c25"><p>c25</p></div>
		<div class="c25"><p>c25</p></div>
	</div>
</div>
