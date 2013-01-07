<?php

class NBSSystem_Nitrogento_Helper_Cache_Fullpage_Catalog_Product_View_Impl extends NBSSystem_Nitrogento_Helper_Cache_Fullpage_Abstract
{
    public function buildCacheTags()
    {
        $cacheTags = parent::buildBaseCacheTags();
        
        if ($currentProduct = Mage::registry('current_product'))
        {
            $cacheTags[] = Mage_Catalog_Model_Product::CACHE_TAG  . "_" . $currentProduct->getId();
        }
        
        return $cacheTags;
    }
}