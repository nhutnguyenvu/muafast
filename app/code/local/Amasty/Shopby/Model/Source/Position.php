<?php
/**
 * @copyright  Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Shopby_Model_Source_Position extends Varien_Object
{
	public function toOptionArray()
	{
	    $hlp = Mage::helper('amshopby');
		return array(
			array('value' => 'left', 'label' => $hlp->__('Left')),
			array('value' => 'top',  'label' => $hlp->__('Top')),
		);
	}
	
}