<?php

class Luclong_Marketing_Model_Marketing extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('marketing/marketing');
    }
}