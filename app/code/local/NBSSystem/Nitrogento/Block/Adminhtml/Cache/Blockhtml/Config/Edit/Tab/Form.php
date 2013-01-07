<?php

class NBSSystem_Nitrogento_Block_Adminhtml_Cache_Blockhtml_Config_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('config_form', array('legend'=>Mage::helper('nitrogento')->__('General Config')));
        
        $fieldset->addField('block_class', 'text', array(
            'label'     => Mage::helper('nitrogento')->__('Block Class'),
            'required'  => true,
            'name'      => 'block_class',
        ));
        
        $fieldset->addField('block_template', 'text', array(
            'label'     => Mage::helper('nitrogento')->__('Block Template'),
            'required'  => true,
            'name'      => 'block_template',
        ));
        
        $fieldset->addField('friendly_entry', 'text', array(
            'label'     => Mage::helper('nitrogento')->__('Friendly Entry'),
            'name'      => 'friendly_entry',
        ));
        
        $fieldset->addField('helper_class', 'text', array(
            'label'     => Mage::helper('nitrogento')->__('Helper Class'),
            'required'  => true,
            'name'      => 'helper_class',
        ));
        
        $fieldset->addField('cache_lifetime', 'text', array(
            'label'     => Mage::helper('nitrogento')->__('Cache Lifetime'),
            'required'  => true,
            'name'      => 'cache_lifetime',
        ));
        
        $fieldset->addField('store_id', 'multiselect', array(
            'name'      => 'store_id[]',
            'label'     => Mage::helper('cms')->__('Store View'),
            'title'     => Mage::helper('cms')->__('Store View'),
            'required'  => true,
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true)
        ));
        
        $fieldset->addField('activated', 'checkbox', array(
            'label'     => Mage::helper('nitrogento')->__('Activated ?'),
            'name'      => 'activated',
            'checked'   => Mage::registry('cache_blockhtml_config')->getActivated()
        ));
        
        $form->setValues(Mage::registry('cache_blockhtml_config')->getData());
        
        return parent::_prepareForm();
    }
}