<?php
class Amasty_Shopby_Block_Adminhtml_Filter_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'filer_id';
        $this->_blockGroup = 'amshopby';
        $this->_controller = 'adminhtml_filter';
        
        parent::__construct();
        $this->_removeButton('reset'); 
    }

    public function getHeaderText()
    {
        return Mage::helper('amshopby')->__('Edit Filter Properties');
    }
}