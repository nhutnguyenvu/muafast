<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class PLUTUS_Home_Block_Home extends Mage_Core_Block_Template {

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getThumbnailImageUrl($category) {

        $url = false;
        if ($image = $category->getThumbnail()) {
            $url = Mage::getBaseUrl('media') . 'catalog/category/' . $image;
        }
        return $url;
    }
    /*
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
       
    }
    */

    public static function isAjax() {
        if (!empty($_SERVER ['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER ['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }
        return false;
    }

}

?>