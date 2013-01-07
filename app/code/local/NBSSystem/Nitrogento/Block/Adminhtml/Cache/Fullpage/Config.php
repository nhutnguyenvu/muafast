<?php

class NBSSystem_Nitrogento_Block_Adminhtml_Cache_Fullpage_Config extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
    	$this->_controller = 'adminhtml_cache_fullpage_config';
        $this->_blockGroup = 'nitrogento';
        $this->_headerText = Mage::helper('nitrogento')->__('Cache Fullpage Config');
        $this->_addButtonLabel = Mage::helper('nitrogento')->__('Add Config');
        parent::__construct();
    }
}