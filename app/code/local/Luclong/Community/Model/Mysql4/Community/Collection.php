<?php

class Luclong_Community_Model_Mysql4_Community_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('community/community');
    }
}