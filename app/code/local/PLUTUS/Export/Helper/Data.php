<?php
/**
* @copyright Amasty.
*/
class PLUTUS_Export_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
	 * Check is ajax request
	 */
	public static function isAjax() {
		if (! empty ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) && strtolower ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest') {
			return true;
		}
		return false;
	}


}