<?php

class Luclong_Marketing_Block_Adminhtml_Marketing_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'marketing';
        $this->_controller = 'adminhtml_marketing';
        
        $this->_updateButton('save', 'label', Mage::helper('marketing')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('marketing')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('marketing_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'marketing_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'marketing_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('marketing_data') && Mage::registry('marketing_data')->getId() ) {
            return Mage::helper('marketing')->__("Edit Item '%s' ", $this->htmlEscape(Mage::registry('marketing_data')->getTitle()));
        } else {
            return Mage::helper('marketing')->__('Add Item');
        }
    }
}