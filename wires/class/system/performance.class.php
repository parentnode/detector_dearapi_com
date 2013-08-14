<?php
/**
* This file contains generel Performance tracking functionality
*
* Usage:
* $perf = new Performance();
* 
* Set new subject/track
* $perf->mark("stringOr", true);
*
* Track process
* $perf->mark("step 1");
* $perf->mark("step 2");
*
* Get result
* $perf->result();
*
*/
class Performance {

	private $subjects;
	private $current_subject;

	
	function __construct() {
		$this->subjects = array();
		$this->current_subject = 0;
	}
	
	function mark($label, $new_subject=false) {
		$timestamp = microtime();

		if($new_subject) {
			$this->current_subject = $label;
		}

		$this->subjects[$this->current_subject]["timestamp"][] = $timestamp;
		$this->subjects[$this->current_subject]["label"][] = $label;
	}


	function result($reset=false) {
		bcscale(6);
		
		$_ = '';
		$_ .= '<div class="performanceMarks">';
		$_ .= '<h2>Performance marks</h2>';

		foreach($this->subjects as $subject => $marks) {

			$pre_usec = 0;
			$pre_sec = 0;

			$_ .= '<h3>'.$subject.'</h3>';

			foreach($marks["timestamp"] as $mark => $timestamp) {
		    	list($usec, $sec) = explode(" ", $timestamp);

				if($pre_usec != 0) {
					$_ .= bcadd(bcsub($usec, $pre_usec), bcsub($sec, $pre_sec)).":".$marks["label"][$mark]."<br>";
				}

				$pre_usec = $usec;
				$pre_sec = $sec;
			}
		}

		if($reset) {
			$this->subjects = array();
			$this->current_subject = 0;
		}

		$_ .= '</div>';

		return $_;
	}
}


?>