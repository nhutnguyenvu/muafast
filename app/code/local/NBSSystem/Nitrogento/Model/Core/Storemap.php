<?php

class NBSSystem_Nitrogento_Model_Core_Storemap
{
    const NITROGENTO_STOREMAP = 'nitrogento_storemap';
    
    protected $_cache;
    protected $_useTwoLevels;
    
    public function __construct($useCacheContainer = false)
    {
        $this->_cache = ($useCacheContainer) ? NBSSystem_Nitrogento_Helper_Data::getCacheContainer()->getCache() : Mage::app()->getCache();
        $this->_useTwoLevels = ($useCacheContainer) ? NBSSystem_Nitrogento_Helper_Data::getCacheContainer()->getUseTwoLevels() : false;
    }
    
    protected function _getCacheKey()
    {
        return NBSSystem_Nitrogento_Helper_Data::formatCacheKey(self::NITROGENTO_STOREMAP);
    }
    
    public function save($force = false)
    {
        if ($this->exists() && !$force)
        {
            return;
        }
        
        $storeMap = array();
        
        foreach (Mage::app()->getStores() as $store)
        {
            $storeMap[$store->getId()]['code'] = $store->getCode();
            $storeMap[$store->getId()]['secure_url'] = $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, true);
            $storeMap[$store->getId()]['unsecure_url'] = $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
            $storeMap[$store->getId()]['website_id'] = $store->getWebsite()->getId();
            $storeMap[$store->getId()]['default_currency_code'] = $store->getDefaultCurrencyCode();
            $storeMap[$store->getId()]['is_default'] = ($store->getWebsite()->getDefaultStore()->getId() == $store->getId());
        }
        
        $this->_cache->save(serialize($storeMap), $this->_getCacheKey());
    }
    
    public function load()
    {
        return unserialize(NBSSystem_Nitrogento_Helper_Data::loadFromCache($this->_getCacheKey(), $this->_cache, $this->_useTwoLevels));
    }
    
    public function delete()
    {
        $this->_cache->remove($this->_getCacheKey());
    }
    
    public function exists()
    {
        return $this->_cache->test($this->_getCacheKey());
    }
}