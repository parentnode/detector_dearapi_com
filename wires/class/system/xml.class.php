<?php
/**
* This file contains generel XML functionality
*
*/
class XML {

	private $xml_parser;
	private $xml;
	private $i;

	/**
	* Parses specified xml file and returns array with xmlfile content
	*
	* @param string $file Path to xml file
	* @return array
	*/
	function parse($file) {
		$this->xml_parser = xml_parser_create();
		xml_parser_set_option($this->xml_parser, XML_OPTION_CASE_FOLDING, true);
		xml_parser_set_option($this->xml_parser, XML_OPTION_SKIP_WHITE, 1);
		$data = file_get_contents($file);
		xml_parse_into_struct($this->xml_parser, $data, &$this->xml);
		xml_parser_free($this->xml_parser);
		$this->i = 0;
		$structure = $this->index();
		return $structure;
	}

	/**
	* Indexes xml content into structured array (recursive)
	*
	* @param integer $i Current index
	* @return array
	*/
	function index() {
		if(isset($this->xml[$this->i])) {
			
			if($this->xml[$this->i]["type"] == "open") {

				$identifier = $this->xml[$this->i]["tag"];
				$structure[$identifier]["attributes"] = isset($this->xml[$this->i]["attributes"]) ? $this->xml[$this->i]["attributes"] : '';
				$structure[$identifier]["value"] = isset($this->xml[$this->i]["value"]) ? $this->xml[$this->i]["value"] : '';
				$this->i++;
				while($child = $this->index()) {
					$structure[$identifier]["children"][] = $child;
				}
				return $structure;

			}
			else if($this->xml[$this->i]["type"] == "complete") {

				$structure[$this->xml[$this->i]["tag"]]["attributes"] = isset($this->xml[$this->i]["attributes"]) ? $this->xml[$this->i]["attributes"] : '';
				$structure[$this->xml[$this->i]["tag"]]["value"] = isset($this->xml[$this->i]["value"]) ? $this->xml[$this->i]["value"] : '';
				$this->i++;
				return $structure;

			}
			else if($this->xml[$this->i]["type"] == "close") {

				$this->i++;
				return false;
			}

		}

		return isset($structure) ? $structure : false;
	}

}

$XML = new XML();
?>