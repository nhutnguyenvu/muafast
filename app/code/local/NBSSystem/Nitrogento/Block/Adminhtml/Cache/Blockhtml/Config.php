<?php

class NBSSystem_Nitrogento_Block_Adminhtml_Cache_Blockhtml_Config extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
    	$this->_controller = 'adminhtml_cache_blockhtml_config';
        $this->_blockGroup = 'nitrogento';
        $this->_headerText = Mage::helper('nitrogento')->__('Cache Blockhtml Config');
        $this->_addButtonLabel = Mage::helper('nitrogento')->__('Add Config');
        parent::__construct();
    }
}