<?php

class NBSSystem_Nitrogento_Block_Adminhtml_Cache_Fullpage_Config_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('nitrogento')->__('Cache Fullpage Config'));
    }
    
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('nitrogento')->__('General Config'),
            'title'     => Mage::helper('nitrogento')->__('General Config'),
            'content'   => $this->getLayout()->createBlock('nitrogento/adminhtml_cache_fullpage_config_edit_tab_form')->toHtml(),
        ));
       
        return parent::_beforeToHtml();
    }
}