<?php
/**
* @package framework
*/
/**
* 
*/

/**
* This class implements HTTP functionallity
*/
class HTTP {
	
	/**
	* Post a request using HTTP
	* The wait_for parameter determines if the function should wait for and return a response from the host.  
	*
	* @param $string host Host to request
	* @param $string port Port prort to request
	* @param $string url URL to request
	* @param $string content Content to post
	* @param $string wait_for Optional parameter, if true functions return when host reply, otherwise return immediately after request has been sent (default false)
	* @return string|void Returns the response of the host if wait_for is ste to true eles void is returned
	*/
	function post($host, $path, $content) {
		// Generate the request header

		$request =
		"POST $path HTTP/1.1\n".
		"Host: $host\n".
		"Content-Type: application/x-www-form-urlencoded\n".
		"Content-Length: ".strlen($content)."\n\n".
		$content."\n";

		// Open the connection to the host
		$socket = fsockopen($host);
		fputs($socket, $request);

		if($socket) {
			
		$result = "";
		while (!feof($socket)) { 
			$result .= fgets($socket, 128);
		}
		fclose($socket);
		}
		return $result;
	}
}
?>