<?php

class Luclong_Community_Block_Adminhtml_Community_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  public function ArrayProductName(){
      $data = Mage::registry('community_data');
      $product_id = $data['product_id'];
      $name_product = Mage::getModel('catalog/product')->load($product_id);
      return $row = $name_product->getName();  
  }  
    
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('community_form', array('legend'=>Mage::helper('community')->__('Item information')));
     
      $fieldset->addField('', 'label', array(
          'label'     => Mage::helper('community')->__('Sản phẩm'),
          //'name'      => 'product_id',
          'after_element_html' => $this->ArrayProductName(),
      ));
      
      $fieldset->addField('like', 'text', array(
          'label'     => Mage::helper('community')->__('Số Like'),
          'class'     => '',
          'required'  => false,
          'name'      => 'like',
      ));
      
       $fieldset->addField('comment', 'text', array(
          'label'     => Mage::helper('community')->__('Số Comment'),
          'class'     => '',
          'required'  => false,
          'name'      => 'comment',
      ));
      
      $fieldset->addField('share', 'text', array(
          'label'     => Mage::helper('community')->__('Số Share'),
          'class'     => '',
          'required'  => false,
          'name'      => 'share',
      ));
      
      $fieldset->addField('buy', 'text', array(
          'label'     => Mage::helper('community')->__('Số Buy'),
          'class'     => '',
          'required'  => false,
          'name'      => 'buy',
      ));

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('community')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('community')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('community')->__('Disabled'),
              ),
          ),
      ));
     
     
      if ( Mage::getSingleton('adminhtml/session')->getCommunityData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCommunityData());
          Mage::getSingleton('adminhtml/session')->setCommunityData(null);
      } elseif ( Mage::registry('community_data') ) {
          $form->setValues(Mage::registry('community_data')->getData());
      }
      return parent::_prepareForm();
  }
}