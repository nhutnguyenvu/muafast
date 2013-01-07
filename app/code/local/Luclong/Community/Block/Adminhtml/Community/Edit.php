<?php

class Luclong_Community_Block_Adminhtml_Community_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'community';
        $this->_controller = 'adminhtml_community';
        
        $this->_updateButton('save', 'label', Mage::helper('community')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('community')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('community_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'community_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'community_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('community_data') && Mage::registry('community_data')->getId() ) {
            return Mage::helper('community')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('community_data')->getTitle()));
        } else {
            return Mage::helper('community')->__('Add Item');
        }
    }
}