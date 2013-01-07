<?php

	class FavoredMinds_Vendor_Block_Adminhtml_Block_Notification_Window extends Mage_Adminhtml_Block_Notification_Window
	{
		public function canShow()
		{
			$helper 	= Mage::app()->getHelper('vendor');
			if($helper->vendorIsLogged())
			{
				return false;
			}
			
			return parent::canShow();
		}
	}
?>