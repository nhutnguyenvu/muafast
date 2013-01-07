<?php

class Luclong_Community_Model_Community extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('community/community');
    }
}