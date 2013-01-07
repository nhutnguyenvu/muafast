<?php

class Luclong_Community_Model_Mysql4_Community extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the community_id refers to the key field in your database table.
        $this->_init('community/community', 'community_id');
    }
}