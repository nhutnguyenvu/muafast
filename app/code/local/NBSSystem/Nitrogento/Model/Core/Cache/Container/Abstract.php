<?php

abstract class NBSSystem_Nitrogento_Model_Core_Cache_Container_Abstract
{
    protected $_cache;
    protected $_useTwoLevels = false;
    
    protected function _getSimpleConfig()
    {
        return NBSSystem_Nitrogento_Helper_Data::getSimpleConfig();
    }
    
    public function getUseTwoLevels()
    {
        return $this->_useTwoLevels;
    }
    
    abstract public function getCache();
}