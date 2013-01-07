<?php

abstract class NBSSystem_Nitrogento_Model_Mysql4_Cache_Config extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (in_array(0, $object->getStoreId()))
        {
            $object->setStoreId(serialize(array(0)));
        }
        else
        {
            $object->setStoreId(serialize($object->getStoreId()));
        }
        
        return parent::_beforeSave($object);
    }
    
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $object->setStoreId(unserialize($object->getStoreId()));
        return parent::_afterLoad($object);
    }
}