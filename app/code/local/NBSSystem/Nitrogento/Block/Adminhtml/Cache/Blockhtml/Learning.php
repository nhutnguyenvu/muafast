<?php

class NBSSystem_Nitrogento_Block_Adminhtml_Cache_Blockhtml_Learning extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_cache_blockhtml_learning';
        $this->_blockGroup = 'nitrogento';
        $this->_headerText = Mage::helper('nitrogento')->__('Cache Blockhtml Learning');
        parent::__construct();
        $this->removeButton('add');
    }
}