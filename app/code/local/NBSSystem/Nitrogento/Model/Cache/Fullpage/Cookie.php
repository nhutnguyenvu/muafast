<?php

class NBSSystem_Nitrogento_Model_Cache_Fullpage_Cookie extends Varien_Object
{
    public function getNitrogentoCacheFullpage($reload = false)
    {
        if ($reload || !$this->hasData('nitrogento_cache_fullpage'))
        {
            $this->setData('nitrogento_cache_fullpage', Mage::helper("nitrogento")->isCacheFullpageEnabled()
            &&  Mage::getSingleton('core/session')->getMessages()->count() == 0
            &&  Mage::getSingleton('catalog/session')->getMessages()->count() == 0
            &&  Mage::getSingleton('review/session')->getMessages()->count() == 0
            &&  Mage::getSingleton('customer/session')->getMessages()->count() == 0
            &&  Mage::getSingleton('checkout/session')->getMessages()->count() == 0
            && !Mage::getSingleton('customer/session')->isLoggedIn()
            &&  Mage::getSingleton('checkout/cart')->getItemsCount() == 0
            &&  Mage::helper('catalog/product_compare')->getItemCount() == 0
            );
        }
        
        return (int) $this->getData('nitrogento_cache_fullpage');
    }
    
    public function setNitrogentoCacheFullpage()
    {
        $this->getNitrogentoCacheFullpage(true);
    }
    
    public function sendCookie()
    {
        $cookie = Mage::getModel('core/cookie');
        $cookie->set('nitrogento_cache_fullpage', $this->getNitrogentoCacheFullpage(true));
        $cookie->set('nitrogento_last_store', Mage::app()->getStore()->getCode());
    }
}