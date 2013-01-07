<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Model_Statement_Status extends Varien_Object
{
    const VENDOR_STATUS_PAID	= 1;
    const VENDOR_STATUS_UNPAID	= 2;

    static public function getOptionArray()
    {
        return array(
            self::VENDOR_STATUS_PAID    => Mage::helper('vendor')->__('Paid'),
            self::VENDOR_STATUS_UNPAID   => Mage::helper('vendor')->__('Unpaid')
        );
    }
}