<?php

/**
* Source Model to select API Mode
*/

class FavoredMinds_Vendor_Model_Adminhtml_Source_Sales {


	public function toOptionArray()
	{
		return array(
			array('value' => 'all_orders',   'label' => Mage::helper('adminhtml')->__('All Orders')),
			array('value' => 'completed_orders', 'label' => Mage::helper('adminhtml')->__('Completed Orders')),
			array('value' => 'vendor_completed_orders', 'label' => Mage::helper('adminhtml')->__('Vendor Completed Orders')),	
		);
	}
}
?>