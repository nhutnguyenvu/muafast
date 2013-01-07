<?php
/**
 * @copyright  Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Shopby_Model_Source_Canonical extends Varien_Object
{
	public function toOptionArray()
	{
	    $hlp = Mage::helper('amshopby');
		return array(
			array('value' => 0, 'label' => $hlp->__('Just Url Key')),
			array('value' => 1, 'label' => $hlp->__('Current URL')),
			array('value' => 2, 'label' => $hlp->__('First Attribute Value')),
		);
	}
}