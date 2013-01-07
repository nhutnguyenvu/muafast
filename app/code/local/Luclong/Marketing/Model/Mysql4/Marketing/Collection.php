<?php

class Luclong_Marketing_Model_Mysql4_Marketing_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('marketing/marketing');
    }
}