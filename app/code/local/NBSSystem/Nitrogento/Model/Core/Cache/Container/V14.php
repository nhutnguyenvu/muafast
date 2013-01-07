<?php

class NBSSystem_Nitrogento_Model_Core_Cache_Container_V14 extends NBSSystem_Nitrogento_Model_Core_Cache_Container_Abstract
{
    public function getCache()
    {
        if (!$this->_cache)
        {
            $options = $this->_getSimpleConfig()->getNode('global/cache');
            
            if ($options) 
            {
                $options = $options->asArray();
            } 
            else 
            {
                $options = array();
            }
            
            $cacheInstance = new NBSSystem_Nitrogento_Model_Core_Cache($options);
            $this->_cache = $cacheInstance->getFrontend();
            $this->_useTwoLevels = $cacheInstance->getUseTwoLevels();
        }
        
        return $this->_cache;
    }
}