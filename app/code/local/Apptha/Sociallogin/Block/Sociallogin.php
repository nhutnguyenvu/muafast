<?php

/**
 * @name         :  Apptha One Step Checkout
 * @version      :  1.0
 * @since        :  Magento 1.5
 * @author       :  Prabhu Mano
 * @copyright    :  Copyright (C) 2011 Powered by Apptha
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  July 26 2012
 *
 * */
?>
<?php
class Apptha_Sociallogin_Block_Sociallogin extends Mage_Core_Block_Template
{
    
    
    
	public function _prepareLayout()
    {
		
            if(Mage::getStoreConfig('sociallogin/general/enable_sociallogin')==1 && !Mage::helper('customer')->isLoggedIn())
			if(is_object($this->getLayout()->getBlock('head')))
            {	
				$this->getLayout()->getBlock('head')->addJs('sociallogin/soc.js');
            }
            
            return parent::_prepareLayout();
    }
    
     public function getSociallogin()     
     { 
        if (!$this->hasData('sociallogin')) {
            $this->setData('sociallogin', Mage::registry('sociallogin'));
        }
        return $this->getData('sociallogin');
        
    }
}