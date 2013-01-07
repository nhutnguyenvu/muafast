<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class FavoredMinds_Vendor_Block_Catalog_Product_Renderer_Vendor extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    // Holds an associative array with vendor id and the associated label
    private static $_vendors = array(); // "singleton"

    public static function getVendorsArray() {
        // Make sure the static property is only populated once
        if (count(self::$_vendors) == 0) {
            $vendor = new FavoredMinds_Vendor_Model_Vendor();

            $helper = Mage::app()->getHelper('vendor');
            if ($helper->vendorIsLogged()) {

                //we filter the output by vendor
                $vendorinfo = $helper->getVendorUserInfo($helper->getVendorUserId());
                $vendor->getCollection()->addAttributeToFilter('vendor_code', $vendorinfo['vendor_code']);
                $vendors = $vendor->getCollection()->toOptionHash();
            } else {

                $vendors = $vendor->getCollection()->toOptionHash();
            }
            self::$_vendors = $vendors;
        }

        return self::$_vendors;
    }

    public static function getMannu() {
        $_product = Mage::getModel('catalog/product');

        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($_product->getResource()->getTypeId())
                ->addFieldToFilter('attribute_code', 'manufacturer');

        $attribute = $attributes->getFirstItem()->setEntity($_product->getResource());
        $manufacturers = $attribute->getSource()->getAllOptions(false);
        //return $manufacturers[0]; 

        $manu = array();
        foreach ($manufacturers as $i => $v) {
            $manu[$v['value']] = $v['label'];
        }
        return $manu;
    }

}