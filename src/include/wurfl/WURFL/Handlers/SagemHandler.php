<?php
/**
 * WURFL API
 *
 * LICENSE
 *
 * This file is released under the GNU General Public License. Refer to the
 * COPYING file distributed with this package.
 *
 * Copyright (c) 2008-2009, WURFL-Pro S.r.l., Rome, Italy
 * 
 *  
 *
 * @category   WURFL
 * @package    WURFL
 * @copyright  WURFL-PRO SRL, Rome, Italy
 * @license
 * @version    1.0.0
 */

/**
 * SagemUserAgentHandler
 * 
 *
 * @category   WURFL
 * @package    WURFL/UserAgentHandlers
 * @copyright  WURFL-PRO SRL, Rome, Italy
 * @license
 * @version    1.0.0
 */
class WURFL_Handlers_SagemHandler extends WURFL_Handlers_Handler {
	
	/**
	 * Intercept all UAs starting with "Sagem" or "SAGEM"
	 *
	 * @param unknown_type $userAgent
	 * @return unknown
	 */
	public function canHandle($userAgent) {
		return WURFL_Handlers_Utils::checkIfStartsWith($userAgent, "Sagem")
			|| WURFL_Handlers_Utils::checkIfStartsWith($userAgent, "SAGEM");
	}

	protected $prefix = "SAGEM";
}
?>