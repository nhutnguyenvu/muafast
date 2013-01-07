<?php

class Luclong_Marketing_Model_Mysql4_Marketing extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the marketing_id refers to the key field in your database table.
        $this->_init('marketing/marketing', 'marketing_id');
    }
}