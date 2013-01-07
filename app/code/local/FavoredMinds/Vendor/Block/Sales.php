<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Sales extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getSales()
     { 
        if (!$this->hasData('vendorsales')) {
            $this->setData('vendorsales', Mage::registry('vendorsales'));
        }
        return $this->getData('vendorsales');
        
    }
}