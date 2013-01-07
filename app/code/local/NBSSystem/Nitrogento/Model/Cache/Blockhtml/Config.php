<?php

class NBSSystem_Nitrogento_Model_Cache_Blockhtml_Config extends NBSSystem_Nitrogento_Model_Cache_Config
{
    public function __construct()
    {
        $this->_init('nitrogento/cache_blockhtml_config', 'nitrogento/cache_blockhtml_config_collection');
    }
    
    public function populateCurrentInfos()
    {
        $this->addData(array(
            "store_id" => Mage::app()->getStore()->getId(),
            "customer_group_id" => Mage::getSingleton('customer/session')->getCustomerGroupId()
        ));
        
        return $this;
    }
    
    public function populateDefaultInfos()
    {
        $this->addData(array(
            "store_id" => array(0),
            "activated" => 1
        ));
        
        return $this;
    }
    
    public function tryBlockMatchWithCacheBlockhtmlConfig($block)
    {
        $collection = $this->getConfigCollection();
        
        foreach ($collection as $cacheBlockhtmlConfig) 
        {
            if ($cacheBlockhtmlConfig["activated"] == 1
             && $cacheBlockhtmlConfig["block_class"] == get_class($block)
             && $cacheBlockhtmlConfig["block_template"] == $block->getTemplate()
             && (in_array($this->getStoreId(), $cacheBlockhtmlConfig["store_id"]) || in_array(0, $cacheBlockhtmlConfig["store_id"])))
            {
                $this->setHelperClass($cacheBlockhtmlConfig["helper_class"]);
                $this->setCacheLifetime($cacheBlockhtmlConfig["cache_lifetime"]);
                return true;
            }
        }
        
        return false;
    }
    
    public function setNonNullDatas($datas)
    {
        if (isset($datas['block_class']))
        {
            $this->setBlockClass($datas['block_class']);
        }
        
        if (isset($datas['block_template']))
        {
            $this->setBlockTemplate($datas['block_template']);
        }
        
        if (isset($datas['helper_class']))
        {
            $this->setHelperClass($datas['helper_class']);
        }
        
        if (isset($datas['store_id']))
        {
            $this->setStoreId($datas['store_id']);
        }
        
        if (isset($datas['friendly_entry']))
        {
            $this->setFriendlyEntry($datas['friendly_entry']);
        }
        
        return $this;
    }
    
    public function duplicate()
    {
        $newCacheBlockhtmlConfig = Mage::getModel('nitrogento/cache_blockhtml_config');
        $newCacheBlockhtmlConfig->addData($this->getData());
        $newCacheBlockhtmlConfig->unsId();
        return $newCacheBlockhtmlConfig;
    }
}