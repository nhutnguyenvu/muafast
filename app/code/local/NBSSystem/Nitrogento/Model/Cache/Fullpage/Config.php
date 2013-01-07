<?php

class NBSSystem_Nitrogento_Model_Cache_Fullpage_Config extends NBSSystem_Nitrogento_Model_Cache_Config
{
    public function __construct()
    {
        $this->_init('nitrogento/cache_fullpage_config', 'nitrogento/cache_fullpage_config_collection');
    }
    
    public function populateCurrentInfos()
    {
        $this->addData(array(
            "store_id" => Mage::app()->getStore()->getId(),
            "full_action_name" => Mage::app()->getFrontController()->getAction()->getFullActionName()
        ));
        
        return $this;
    }
    
    public function tryPageMatchWithCacheFullpageConfig()
    {
        $collection = $this->getConfigCollection();
        
        foreach ($collection as $cacheFullpageConfig) 
        {
            if ($cacheFullpageConfig["activated"] == 1
             && $cacheFullpageConfig["full_action_name"] == $this->getFullActionName()
             && (in_array($this->getStoreId(), $cacheFullpageConfig["store_id"]) || in_array(0, $cacheFullpageConfig["store_id"])))
            {
                $this->setHelperClass($cacheFullpageConfig["helper_class"]);
                $this->setCacheLifetime($cacheFullpageConfig["cache_lifetime"]);
                return true;
            }
        }
        
        return false;
    }
    
    public function setNonNullDatas($datas)
    {
        if (isset($datas['full_action_name']))
        {
            $this->setFullActionName($datas['full_action_name']);
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
} 