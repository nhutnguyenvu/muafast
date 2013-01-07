<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Model_Status extends Varien_Object
{
    const VENDOR_STATUS_APPROVED	= 1;
    const VENDOR_STATUS_PENDING	= 0;

    static public function getOptionArray()
    {
        return array(
            self::VENDOR_STATUS_APPROVED    => Mage::helper('vendor')->__('Approved'),
            self::VENDOR_STATUS_PENDING   => Mage::helper('vendor')->__('Pending')
        );
    }
    static public function getOptionHS()
    {
        return array(
            0    => Mage::helper('vendor')->__('No'),
            1    => Mage::helper('vendor')->__("Yes")
        );
    }
}