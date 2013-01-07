<?php
/**
 * @copyright  Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Shopby_Model_Source_Price extends Varien_Object
{
	public function toOptionArray()
	{
	    $hlp = Mage::helper('amshopby');
		return array(
			array('value' => 0, 'label' => $hlp->__('Default')),
			array('value' => 1, 'label' => $hlp->__('Dropdown')),
			array('value' => 2, 'label' => $hlp->__('From-To Only')),
			array('value' => 3, 'label' => $hlp->__('Slider')),
		);
	}
	
}