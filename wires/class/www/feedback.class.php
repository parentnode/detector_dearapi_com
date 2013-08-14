<?php
/**
* @package framework.feedback
*/

/**
* This file contains device feedback functionality. Optimally this will transform into a uniform feedback method 
* 
* 
*/
class Feedback extends translation {


	public $varnames;
	public $vars;

	/**
	* Init, set varnames, validation rules
	* @return void
	*/
	function __construct() {
		$this->addTranslation(__FILE__);

		$this->varnames["feedback_id"] = "";
		$this->varnames["feedback"] = $this->translate("Feedback");
		$this->varnames["comment"] = $this->translate("Comment");
		$this->varnames["rating"] = $this->translate("Rating");
		$this->vars = getVars($this->varnames);
	}


	
	/**
	* Save new item, based on submitted values
	*
	* @return bool
	* @uses Message
	*/
	function saveFeedback() {

		$vars = "DEFAULT";
		$vars .= ",'".$_SERVER["HTTP_USER_AGENT"]."'";
		$vars .= ",'".Session::getDevice("segment")."'";

		$vars .= ",'".$this->vars['feedback_id']."'";
		$vars .= ",".($this->vars['feedback'] ? $this->vars['feedback'] : 0);
		$vars .= ",'".$this->vars['comment']."'";
		$vars .= ",'".$this->vars['rating']."'";

		$vars .= ", CURRENT_TIMESTAMP";
		$vars .= ", '".Session::getLogin()->getUserName()."'";

		if($this->sql("INSERT INTO ".UT_FEE." VALUES($vars)")) {
			messageHandler()->addStatusMessage("Item saved");
			return true;
		}
		else {
			print($this->dbError());
			messageHandler()->addErrorMessage($this->dbError());
			return false;
		}
	}

}

?>