<?php

class NBSSystem_Nitrogento_Helper_Cache_Blockhtml_Catalog_Product_List_Impl extends NBSSystem_Nitrogento_Helper_Cache_Blockhtml_Abstract
{
    public function buildCacheKey($block)
    {
        $cacheKey = parent::buildFullBaseCacheKey($block) . "_" . NBSSystem_Nitrogento_Helper_Data::formatCacheKey(NBSSystem_Nitrogento_Helper_Url::getRequestUri());
        $cacheKey .= '_' . Mage::app()->getStore()->getCurrentCurrencyCode();
        
        if ($currentCategory = Mage::registry('current_category'))
        {
            $cacheKey .= '_' . $currentCategory->getId();
        }
        
        if ($categoryId = $block->getCategoryId())
        {
            $cacheKey .= '_' . $categoryId;
        }
        
        return $cacheKey;
    }
    
    public function buildCacheTags($block)
    {
        $cacheTags = parent::buildBaseCacheTags($block);
        
        if ($currentCategory = Mage::registry('current_category'))
        {
            $cacheTags[] = Mage_Catalog_Model_Category::CACHE_TAG . "_" . $currentCategory->getId();
        }
        
        if ($categoryId = $block->getCategoryId())
        {
            $cacheTags[] = Mage_Catalog_Model_Category::CACHE_TAG . "_" . $categoryId;
        }
        
        return array_unique($cacheTags);
    }   
}