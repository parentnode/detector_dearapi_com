<?php
/**
* This file contains Task summary maintenance functionality
*/

/**
* TaskSummary
*/
class TaskSummary extends Translation { 

	private $tasks = array();

	/**
	* Init, set varnames, validation rules
	*/
	function __construct() {
		// initiate helpers before calling View construct
		$this->addTranslation(__FILE__);
	}

	/**
	*
	*/
	function addTask($name, $link, $count, $warning_level=false) {
		if($count) {
			$task->name = $name;
			$task->link = $link;
			$task->count = $count;
			$task->warning = $warning_level !== false && $count >= $warning_level ? true : false;
			$this->tasks[] = $task;
		}
	}

	/**
	*
	*/
	function getTaskSummary() {
		global $HTML;
		$_ = '';
		foreach($this->tasks as $key) {
			$_ .= $HTML->p('<a href="'.$key->link.'">'.$key->name.'</a>');
		}
		return $_;
	}

}

?>