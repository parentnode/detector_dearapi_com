<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="da" lang="da">
<head>
	<!-- Martin Kaestel (c)+(p) 2006 //-->
	<!-- All material protected by copyrightlaws (as if you didnt know) //-->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="imagetoolbar" content="no" />
	<title>-- <?= SITE_NAME.(isset($this->trail[count($this->trail)-1]->name) ? " -- ".$this->trail[count($this->trail)-1]->name : "") ?> --</title>
	<style type="text/css">@import url(/css/include.css);</style>
	<script language="javascript" type="text/javascript" src="/js/include.js"></script>
	<!--[if lte IE 7]>
		<style type="text/css">@import url(/css/framework/ie.css);</style>
	<![endif]-->
</head>
<body>

<div id="page">
	<div id="header">
		<h1><?= SITE_NAME ." : ". (isset($this->trail[count($this->trail)-1]->name) ? $this->trail[count($this->trail)-1]->name : SITE_NAME) ?></h1>
		<div id="login">
			<? if(Session::getLogin()->getUserName()) { ?><span class="init:button fright" onclick="location.href='<?= $this->url ?>?page_status=logoff'"><?= $this->translate("Log Off") ?> (<?= Session::getLogin()->getUserName() ?>)</span><? } ?>
			<span class="init:button fright" onclick="location.href='/change_language/change_language.php?origin='+location.href+'&amp;language=<?= Session::getLanguageISO() == "en" ? "da" : "en" ?>'"><?= $this->translate("Language")." (".Session::getLanguageISO().")" ?></span>
		</div>
		<div id="trail">
			<div class="fright load" id="progress"><?= $this->translate("Loading") ?>...</div>
			<?php if(count($this->trail)){
				foreach($this->trail as $key){
					print "/ ".($key->url ? '<a href="'.$key->url.'">' : '').$key->name.($key->url ? '</a>' : '')." ";
				}
			}else{
				print "/ ";
			}?>
		</div>
	</div>
	<div id="nav">
		<ul class="init:list">
			<?php
				global $level;
				$level = 0;
				function createMenu($items) {
					global $page;
					global $HTML;
					global $level;
					$content_type = false;
					$_ = '';
					foreach($items as $item){
						//$more = $item->children ? 'more' : false;
						//$selected = $page->trail && count($page->trail) > $level && $item->id === $page->trail[$level]->id ? 'selected' : false;
						//print $item->url ."::".$page->trail."::".count($page->trail)."::".$level."<br>";
						$selected = $page->trail && count($page->trail) == $level+1 && $item->id === $page->trail[count($page->trail)-1]->id ? 'selected' : false;
//						$selected = $page->trail && count($page->trail) == $level+1 && $item["id"] === $page->trail[count($page->trail)-1]->id ? 'selected' : false;
//						$selected = $page->trail && count($page->trail) == $level+1 && $item["id"] === $page->trail[count($page->trail)-1]["id"] ? 'selected' : false;
						//$open = $more && $selected ? 'open' : false;


						if($item->children || $item->url) {
							$content_type = "text";
//						if(isset($item["children"]) || isset($item["url"])) {
							//$_ .= '<li'.$HTML->makeAttribute("class", $more, $selected, $open).'>';
							$_ .= '<li'.$HTML->makeAttribute("class", $selected).'>';

							$_ .= $item->url ? '<a href="'.$item->url.'">' : '';
//							$_ .= isset($item["url"]) ? '<a href="'.$item["url"].'">' : '';
							$_ .= $item->name;
//							$_ .= $item["name"];
							$_ .= $item->url ? '</a>' : '';
//							$_ .= isset($item["url"]) ? '</a>' : '';
							if($item->children) {
//							if(is_array($item["children"])) {
								$level++;
								$_ .= '<ul>';
								//.$HTML->makeAttribute("class", $open).'>';
								$_ .= createMenu($item->children);
//								$_ .= createMenu($item["children"]);
								$_ .= '</ul>';
								$level--;
							}
							$_ .= '</li>';
						}
//						else if($item["name"] == "----") {
						else if($item->name == "----" && $content_type != "separator") {
							$content_type = "separator";
							$_ .= '<li'.$HTML->makeAttribute("class", "separator").'></li>';
						}
					}
					return $_;
				}
				print createMenu(Session::getNavigation());

			?>
		</ul>
	</div>
	<div id="message"></div>
	<div id="content">
