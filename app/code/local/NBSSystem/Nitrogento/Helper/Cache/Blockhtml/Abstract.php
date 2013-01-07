<?php

abstract class NBSSystem_Nitrogento_Helper_Cache_Blockhtml_Abstract extends Mage_Core_Helper_Abstract
{
    /**
     * Build the current cacheKey block
     *
     * @return string
     */
    abstract public function buildCacheKey($block);
    
    /**
     * Build the current cacheTags block
     *
     * @return array
     */
    abstract public function buildCacheTags($block);
    
    /**
     * Build a full base cacheKey
     *
     * @return string
     */
    public function buildFullBaseCacheKey($block)
    {
        $cacheBlockhtmlConfig = $block->getCacheBlockhtmlConfig();
        return $cacheBlockhtmlConfig->getData('store_id') . '_' . 
               $cacheBlockhtmlConfig->getData('customer_group_id') . '_' . 
               Mage::app()->getRequest()->getScheme() . '_' . 
               get_class($block) . '_' . 
               $block->getTemplate() . '_' . 
               NBSSystem_Nitrogento_Helper_Data::getDeviceKey();
    }
    
    /**
     * Build a simple base cacheKey
     *
     * @return string
     */
    public function buildSimpleBaseCacheKey($block)
    {
        $cacheBlockhtmlConfig = $block->getCacheBlockhtmlConfig();
        return get_class($block) . '_' . $block->getTemplate();
    }
    
    public function buildBaseCacheTags($block)
    {
        $cacheBlockhtmlConfig = $block->getCacheBlockhtmlConfig();
        return array(
            NBSSystem_Nitrogento_Helper_Data::BLOCK_HTML, 
            NBSSystem_Nitrogento_Helper_Data::CACHE_BLOCKHTML_OBJECTS, 
            get_class($block)
        );
    }
    
    public function isBlockCachable($block)
    {
        return true;
    }
}