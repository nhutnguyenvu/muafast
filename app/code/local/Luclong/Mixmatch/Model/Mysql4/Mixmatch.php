<?php

class Luclong_Mixmatch_Model_Mysql4_Mixmatch extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the mixmatch_id refers to the key field in your database table.
        $this->_init('mixmatch/mixmatch', 'mixmatch_id');
    }
}