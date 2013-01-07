<?php

class NBSSystem_Nitrogento_Helper_Cache_Blockhtml_Generic_Dynamic_Impl extends NBSSystem_Nitrogento_Helper_Cache_Blockhtml_Abstract
{
    public function buildCacheKey($block)
    {
        return parent::buildFullBaseCacheKey($block) . NBSSystem_Nitrogento_Helper_Data::formatCacheKey(NBSSystem_Nitrogento_Helper_Url::getCurrentUrl(NBSSystem_Nitrogento_Helper_Data::getQueryStringFilters()));
    }
    
    public function buildCacheTags($block)
    {
        return parent::buildBaseCacheTags($block);
    }
}