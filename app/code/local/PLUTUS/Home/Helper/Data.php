<?php
/**
* @copyright Amasty.
*/
class PLUTUS_Home_Helper_Data extends Mage_Core_Helper_Abstract
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
    
    public function getFeaturedProduct($page = 1, $limit = 4) {

        // some helpers
        $storeId = Mage::app()->getStore()->getId();
        // get all products that are marked as featured
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToSelect(array("name", "id", "image","small_image"));
        $collection->addAttributeToSelect('featured');
        $collection->addAttributeToSelect('price');
        $collection->addAttributeToSelect('special_price');
        $collection->addAttributeToSelect('color');
        $collection->addFieldToFilter(array(
            array('attribute' => 'featured', 'eq' => true),
        ));
        $collection->addAttributeToFilter('status', array("eq", 1));
        $collection->addAttributeToFilter('visibility', array("eq", 4));
        //$collection->setPage(1,$limit);

        if ($collection->count()) {
            return $collection;
        }
        return array();
        // if no products are currently featured, display some text\
    }


}