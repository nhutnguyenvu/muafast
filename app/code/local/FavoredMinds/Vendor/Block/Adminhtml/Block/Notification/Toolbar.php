<?php

	class FavoredMinds_Vendor_Block_Adminhtml_Block_Notification_Toolbar extends Mage_Adminhtml_Block_Notification_Toolbar
	{
		public function isShow()
		{
			$helper 	= Mage::app()->getHelper('vendor');
			if($helper->vendorIsLogged())
			{
				return false;
			}
			
			return parent::isShow();
		}

		public function isMessageWindowAvailable()
		{
			$helper 	= Mage::app()->getHelper('vendor');
			if($helper->vendorIsLogged())
			{
				return false;
			}
			
			return parent::isMessageWindowAvailable();
		}
	}
?>