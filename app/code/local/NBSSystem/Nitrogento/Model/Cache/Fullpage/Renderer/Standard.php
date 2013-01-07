<?php

class NBSSystem_Nitrogento_Model_Cache_Fullpage_Renderer_Standard extends NBSSystem_Nitrogento_Model_Cache_Fullpage_Renderer_Abstract
{
    protected $_store;
    protected $_cacheContainer;
    
    public function __construct($store)
    {
        $this->_store = $store;
        $this->_cacheContainer = NBSSystem_Nitrogento_Helper_Data::getCacheContainer();
    }
    
    protected function _loadPageContent()
    {
        $helper = new NBSSystem_Nitrogento_Helper_Cache_Fullpage_Generic_Impl();
        return NBSSystem_Nitrogento_Helper_Data::loadFromCache(
            $helper->buildCacheKey($this->_store['code'], $this->_retrieveCurrencyCode(), $this->_store['default_currency_code']),
            $this->_cacheContainer->getCache(),
            $this->_cacheContainer->getUseTwoLevels()
        );
    }
    
    private function _retrieveCurrencyCode()
    {
        $defaultCurrencyCode = $this->_store['default_currency_code'];
        $currencyCode = $defaultCurrencyCode;
        
        if (isset($_COOKIE) && isset($_COOKIE['currency']))
        {
            $currencyCode = $_COOKIE['currency'];
        }
        
        return $currencyCode;
    }
    
    public function renderPage()
    {
        Varien_Profiler::start('nitrogento_cache_fullpage_load_from_cache');
        $pageContent = $this->_loadPageContent();
        Varien_Profiler::stop('nitrogento_cache_fullpage_load_from_cache');
        
        if ($pageContent)
        {
            header('Content-Type: text/html; charset=UTF-8');
            echo $pageContent;
            Varien_Profiler::stop('nitrogento_cache_fullpage_render_page');
            exit();
        }
    }
}