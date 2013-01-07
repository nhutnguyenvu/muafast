<?php

class NBSSystem_Nitrogento_Model_Cache_Fullpage_Observer extends Varien_Object
{
    public function decidePutPageInCache($observer)
    {
        $response = Mage::app()->getResponse();
        
        if ($response->getHttpResponseCode() != '200'
         || !Mage::helper("nitrogento")->isCacheFullpageEnabled()
         || !Mage::getSingleton('nitrogento/cache_fullpage_cookie')->getNitrogentoCacheFullpage())
        {
            return;
        }
        
        $cacheFullpageConfig = Mage::getSingleton('nitrogento/cache_fullpage_config')->populateCurrentInfos();
        
        if ($cacheFullpageConfig->tryPageMatchWithCacheFullpageConfig())
        {
            $cacheHelper = Mage::helper($cacheFullpageConfig->getHelperClass());
            
            if ($cacheHelper->isPageCachable())
            {
                Mage::app()->saveCache(
                    $response->getBody(false),
                    $cacheHelper->buildCacheKey(),
                    $cacheHelper->buildCacheTags(),
                    $cacheFullpageConfig->getCacheLifetime()
                );
            }
        }
    }
    
    public function disableReportsProductDisplay($observer)
    {
        if (Mage::helper("nitrogento")->isCacheFullpageEnabled())
        {
            if (version_compare(Mage::getVersion(), '1.4.0', '>='))
            {
                $observer->getEvent()->getLayout()->getUpdate()->addHandle('nitrogento_disable_reports_product_display_v14');
            }
            else
            {
                $observer->getEvent()->getLayout()->getUpdate()->addHandle('nitrogento_disable_reports_product_display_v13');
            }
        }
    }
}