<?php

class Luclong_Mixmatch_Model_Mysql4_Mixmatch_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mixmatch/mixmatch');
    }
}