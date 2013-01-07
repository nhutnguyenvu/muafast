<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Shopby_Model_Value extends Mage_Core_Model_Abstract
{
    public function _construct()
    {    
        $this->_init('amshopby/value');
    }
    
    public function getFilterId()
    {
        return $this->getResource()->getFilterId($this->getOptionId());
    }    
}