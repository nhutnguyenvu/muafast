<?php

abstract class NBSSystem_Nitrogento_Helper_Cache_Fullpage_Abstract extends Mage_Core_Helper_Abstract
{
    abstract public function buildCacheTags();
    
    public function buildBaseCacheTags()
    {
        return array(
            Mage_Catalog_Model_Category::CACHE_TAG, 
            Mage_Core_Model_Store::CACHE_TAG, 
            NBSSystem_Nitrogento_Helper_Data::FULLPAGE, 
            NBSSystem_Nitrogento_Helper_Data::CACHE_FULLPAGE_OBJECTS, 
            Mage::app()->getFrontController()->getAction()->getFullActionName()
        );
    }
    
    public function buildCacheKey($storeCode = null, $currencyCode = null, $defaultCurrencyCode = null)
    {
        $storeCode = (!is_null($storeCode)) ? $storeCode : Mage::app()->getStore()->getCode();
        $currencyCode = (!is_null($currencyCode)) ? $currencyCode : Mage::app()->getStore()->getCurrentCurrencyCode();
        $defaultCurrencyCode = (!is_null($defaultCurrencyCode)) ? $defaultCurrencyCode : Mage::app()->getStore()->getDefaultCurrencyCode();
        $url = NBSSystem_Nitrogento_Helper_Url::getCurrentUrl(NBSSystem_Nitrogento_Helper_Data::getQueryStringFilters());
        
        $cacheKey = $storeCode . '_' . NBSSystem_Nitrogento_Helper_Data::getDeviceKey() . '_' . $url;
        $cacheKey = ($defaultCurrencyCode == $currencyCode) ? $cacheKey : $currencyCode . '_' . $cacheKey;
        
        $currentContext = new Varien_Object(array('cache_key' => $cacheKey, 'url' => $url, 'store_code' => $storeCode, 'currency_code' => $currencyCode));
        NBSSystem_Nitrogento_Main::getInstance()->dispatchEvent('nitrogento_before_build_cache_fullpage_key', array('data_object' => $currentContext));
        return NBSSystem_Nitrogento_Helper_Data::formatCacheKey($currentContext->getCacheKey());
    }
    
    public function isPageCachable()
    {
        return true;
    }
}