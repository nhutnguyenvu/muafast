<?php

abstract class NBSSystem_Nitrogento_Model_Mysql4_Cache_Config_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) 
        {
            $item->setStoreId(unserialize($item->getStoreId()));
        }
        
        return parent::_afterLoad();
    }
}