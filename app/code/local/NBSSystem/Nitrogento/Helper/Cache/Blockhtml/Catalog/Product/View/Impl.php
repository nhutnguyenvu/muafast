<?php

class NBSSystem_Nitrogento_Helper_Cache_Blockhtml_Catalog_Product_View_Impl extends NBSSystem_Nitrogento_Helper_Cache_Blockhtml_Abstract
{
    public function buildCacheKey($block)
    {
        $cacheKey = parent::buildFullBaseCacheKey($block);
        $cacheKey .= '_' . Mage::app()->getStore()->getCurrentCurrencyCode();
        
        if ($currentProduct = Mage::registry('current_product'))
        {
            $cacheKey .= '_' . $currentProduct->getId();
        }
        
        return $cacheKey;
    }
    
    public function buildCacheTags($block)
    {
        $cacheTags = parent::buildBaseCacheTags($block);
        
        if ($currentProduct = Mage::registry('current_product'))
        {
            $cacheTags[] = Mage_Catalog_Model_Product::CACHE_TAG  . "_" . $currentProduct->getId();
        }
        
        return $cacheTags;
    }
    
    public function isBlockCachable($block)
    {
        $messageHtml = $block->getMessagesBlock()->getGroupedHtml();
        return empty($messageHtml);
    }
}