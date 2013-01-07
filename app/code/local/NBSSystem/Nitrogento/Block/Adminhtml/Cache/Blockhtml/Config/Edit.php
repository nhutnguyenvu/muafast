<?php

class NBSSystem_Nitrogento_Block_Adminhtml_Cache_Blockhtml_Config_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'config_id';
        $this->_blockGroup = 'nitrogento';
        $this->_controller = 'adminhtml_cache_blockhtml_config';
        $this->_headerText = '';
        parent::__construct();
        $this->_updateButton('save', 'label', Mage::helper('nitrogento')->__('Save Config'));
        $this->_updateButton('delete', 'label', Mage::helper('nitrogento')->__('Delete Config'));
    }
}