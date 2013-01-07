<?php
/**
* @copyright Amasty.
*/
class Luclong_Tools_Helper_Data extends Mage_Core_Helper_Abstract
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
    
    public function getProductListByVendor($vendor_code){
        
        $productCollection = Mage::getModel("catalog/product")
                                ->getCollection()->addAttributeToSelect(array("name"))
                                ->addAttributeToFilter("manufacturer", $vendor_code);
        return $productCollection;
    }


}