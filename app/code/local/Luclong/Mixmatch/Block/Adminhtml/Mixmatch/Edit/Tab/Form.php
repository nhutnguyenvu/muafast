<?php

class Luclong_Mixmatch_Block_Adminhtml_Mixmatch_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('mixmatch_form', array('legend'=>Mage::helper('mixmatch')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('mixmatch')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('mixmatch')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('mixmatch')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('mixmatch')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('mixmatch')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('mixmatch')->__('Content'),
          'title'     => Mage::helper('mixmatch')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getMixmatchData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getMixmatchData());
          Mage::getSingleton('adminhtml/session')->setMixmatchData(null);
      } elseif ( Mage::registry('mixmatch_data') ) {
          $form->setValues(Mage::registry('mixmatch_data')->getData());
      }
      return parent::_prepareForm();
  }
}