<?php

class NBSSystem_Nitrogento_Helper_Cache_Blockhtml_Generic_Static_Impl extends NBSSystem_Nitrogento_Helper_Cache_Blockhtml_Abstract
{
    public function buildCacheKey($block)
    {
        return parent::buildFullBaseCacheKey($block);
    }
    
    public function buildCacheTags($block)
    {
        return parent::buildBaseCacheTags($block);
    }
}