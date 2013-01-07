<?php

class NBSSystem_Nitrogento_Model_Mysql4_Cache_Blockhtml_Learning_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('nitrogento/cache_blockhtml_learning');
    }
    
    public function addLearningFieldsToFilter($parent)
    {
        return $this
            ->addFieldToFilter('store_id', $parent->getStoreId())
            ->addFieldToFilter('block_class', $parent->getBlockClass())
            ->addFieldToFilter('block_template', $parent->getBlockTemplate());
    }    
}