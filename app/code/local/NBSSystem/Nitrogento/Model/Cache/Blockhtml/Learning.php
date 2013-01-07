<?php

class NBSSystem_Nitrogento_Model_Cache_Blockhtml_Learning extends NBSSystem_Nitrogento_Model_Cache_Config
{
    public function __construct()
    {
        $this->_init('nitrogento/cache_blockhtml_learning', 'nitrogento/cache_blockhtml_learning_collection');
    }
    
    public function populateCurrentInfos()
    {
        $this->addData(array(
            "store_id" => Mage::app()->getStore()->getId()
        ));
    }
}