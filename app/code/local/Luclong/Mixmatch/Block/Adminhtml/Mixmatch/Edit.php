<?php

class Luclong_Mixmatch_Block_Adminhtml_Mixmatch_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'mixmatch';
        $this->_controller = 'adminhtml_mixmatch';
        
        $this->_updateButton('save', 'label', Mage::helper('mixmatch')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('mixmatch')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('mixmatch_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'mixmatch_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'mixmatch_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('mixmatch_data') && Mage::registry('mixmatch_data')->getId() ) {
            return Mage::helper('mixmatch')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('mixmatch_data')->getTitle()));
        } else {
            return Mage::helper('mixmatch')->__('Add Item');
        }
    }
}