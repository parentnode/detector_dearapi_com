<?php
/**
* This file contains generel Soap functionality
*
*/
class Soap {

	/**
	* Make soap connection and return handle
	*
	* @param String $url soap service url
	* @return SoapClient
	*/
	function makeSoapConnection($url) {

		ini_set("soap.wsdl_cache_enabled", "0");
		ini_set("default_socket_timeout", "10");
		ini_set("error_reporting", E_ERROR);

		try {
			$client = new SoapClient($url, array('trace' => 1, 'exceptions' => true));
		}
		catch (SoapFault $fault) {

			Page::addLog("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})");
			return false;
		}

		Page::addLog("Soap connection: $url");
		return $client;
	}

}

?>