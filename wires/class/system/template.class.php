<?php
/**
* This class contains template loading functionality
*/
include_once("class/system/translation.class.php");

class Template extends Translation {

	public $response_column;
	private $target_id;
	public $container_id;
	public $template_object;

	/**
	* Get flow variables
	*/
	function __construct() {
		$this->response_column = getVar("response_column");
	}

	/**
	* Get response column type
	*
	* @return string Column classname
	*/
	function getResponseColumn() {
		return $this->response_column ? $this->response_column : "c300";
	}

	/**
	* Return reset target JavaScript action
	*
	* @param string $target Target to reset
	*/
	function getResetTargetScript($target) {
		print "<script>Util.Ajax.resetContainer('".$target."');</script>";
	}



	/**
	* Return "reload list" elements
	* Used when a view/edit/delete state is called on non-existant item.
	* Also used for regular delete requests.
	* Reloads list (which must then be outdated).
	*/
	function reloadList($column) {
		$this->getTargetTemplate($column, "container:item");
		$this->getLoadTargetScript("container:item_list", $this->url.'?page_status=list');

	}

	/**
	* Return "reload list, view element" elements
	* Used when an update/add state is called.
	* Reloads list, and view panel.
	*/
	function reloadListView($column, $id, $object, $url=false) {
		$this->getObject($object)->getItem($id);
		$this->getTemplate(($url ? $url : "html.view.php"), $object, $column, "container:item", false, true);
		$this->getLoadTargetScript("container:item_list", $this->url.'?page_status=list');
	}

	/**
	* Set url marker, later to be used to bookmark pages
	*
	*/
	function setUrlMarker($marker = false) {
		if($marker) {
			print '<script type="text/javascript">Util.Ajax.setUrlMarker("'.$marker.'");</script>';
		}
		else {
			print '<script type="text/javascript">Util.Ajax.resetUrlMarker();</script>';
		}
	}

	/**
	* Return load target JavaScript action
	*
	* @param string $target Target to load content into
	* @param string $url Url to load into container
	* @param string $parameters Parameters to send
	*/
	function getLoadTargetScript($target, $url, $parameters = "") {
		print "<script>Util.Ajax.loadContainer('".$url."', '".$target."', '".$parameters."');</script>";
	}

	/**
	* Return load target JavaScript action
	*
	* @param string $target Target to load content into
	* @param string $url Url to load into container
	* @param string $parameters Parameters to send
	*/
	function getLocationHrefScript($url) {
		print "<script>location.href = '".$url."'	;</script>";
	}

	/**
	* Get template object
	*
	* @return string Template object
	*/
	function getTemplateObject() {
		return $this->template_object ? $this->getObject($this->template_object) : "";
	}

	/**
	* Get template object name
	*
	* @return string Template object name
	*/
	function getTemplateObjectName() {
		return $this->template_object ? $this->template_object : "";
	}


	/**
	* Get target id
	*
	* @return string Target Id
	*/
	function getTargetId() {
		return $this->target_id ? $this->target_id : "";
	}

	/**
	* Get container id
	*
	* @return string Container Id
	*/
	function getContainerId() {
		return $this->container_id ? $this->container_id : "";
	}

	/**
	* Load external template
	*
	* @param string $template Path to template
	* @param string $template_object Class object to use in template
	* @param string $response_column Column type classname
	* @param string $container_id Id of wrapping container
	* @param string $target_id If template needs to link to other target
	* @param string $silent Get template without getting message (default loud)
	*/
	function getTemplate($template, $object, $response_column=false, $container_id=false, $target_id=false, $silent=false) {
		global $page;
		global $HTML;
		global $id;
		// when including template, page->status should be resat
		//$this->setStatus(false);
		$this->template_object = $object;
		$this->response_column = $response_column ? $response_column : $this->response_column;
		$this->container_id = $container_id;
		$this->target_id = $target_id;

		print (!$silent ? messageHandler()->getMessages("js") : '');

		if(file_exists(LOCAL_PATH."/templates/".$template)) {
			$file = LOCAL_PATH."/templates/".$template;
		}
		else if(defined("REGIONAL_PATH") && file_exists(REGIONAL_PATH."/templates/".$template)) {
			$file = REGIONAL_PATH."/templates/".$template;
		}
		else if(defined("GLOBAL_PATH") && file_exists(GLOBAL_PATH."/templates/".$template)) {
			$file = GLOBAL_PATH."/templates/".$template;
		}
		else if(file_exists(FRAMEWORK_PATH."/templates/".$template)) {
			$file = FRAMEWORK_PATH."/templates/".$template;
		}
		else {
			$file = FRAMEWORK_PATH."/templates/defaults/".$template;
		}
		$this->addTranslation($file);

//		$this->translater->__construct($file);
		include($file);
	}

	function getSnippet($snippet, $silent = false){
		print $snippet;
		print (!$silent ? messageHandler()->getMessages("js") : '');
		print $this->codeError();
	}
	/**
	* Load target template
	*
	* @param string $response_column Column type classname
	* @param string $container_id Id of wrapping container
	* @param string $loud Get message along with target template (default silent)
	*/
	function getTargetTemplate($response_column=false, $container_id=false, $loud=false) {
		global $HTML;
		// when including template, page->status should be resat
		//$this->setStatus(false);

		$this->response_column = $response_column;
		$this->container_id = $container_id;

		print ($loud ? messageHandler()->getMessages("js") : '');

		if(file_exists(LOCAL_PATH."/templates/html.target.php")) {
			include(LOCAL_PATH."/templates/html.target.php");
		}
		else if(defined("REGIONAL_PATH") && file_exists(REGIONAL_PATH."/templates/html.target.php")) {
			include(REGIONAL_PATH."/templates/html.target.php");
		}
		else if(defined("GLOBAL_PATH") && file_exists(GLOBAL_PATH."/templates/html.target.php")) {
			include(GLOBAL_PATH."/templates/html.target.php");
		}
		else if(file_exists(FRAMEWORK_PATH."/templates/html.target.php")) {
			include(FRAMEWORK_PATH."/templates/html.target.php");
		}
		else {
			include("templates/defaults/html.target.php");
		}
	}

	/**
	* TODO
	*/
	function designHeader() {
		$_ = '';
		if(strstr($this->getResponseColumn(), "border")) {
			$_ .= '<div class="cInner">';
		}
		return $_;
	}

	/**
	* TODO
	*/
	function designFooter() {
		$_ = '';
		if(strstr($this->getResponseColumn(), "border")) {
//			$_ .= $this->codeError();
			$_ .= '</div>';
		}
		return $_;
	}

}
?>