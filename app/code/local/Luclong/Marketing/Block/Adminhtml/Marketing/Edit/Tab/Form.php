<?php

class Luclong_Marketing_Block_Adminhtml_Marketing_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  public function ArrayUsername(){
    $data = Mage::registry('marketing_data');
    $user_id = $data['user_id'];
    $customer = Mage::getModel("customer/customer")->load($user_id);
    $first = $customer->getFirstname();
    $last =  $customer->getLastname();
    $name = $first.' '.$last ;
    return '<b>'.$name.'</b>'; 
  }
  
  public function ArrayEmail(){
    $data = Mage::registry('marketing_data');
    $user_id = $data['user_id'];
    $email = Mage::getModel("customer/customer")->load($user_id)->getEmail();
    return '<b>'.$email.'</b>'; 
  }  
    
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('marketing_form', array('legend'=>Mage::helper('marketing')->__('Item information')));
      
      $fieldset->addField('', 'label', array(
          'label'     => Mage::helper('marketing')->__('Username'),
          'after_element_html' => $this->ArrayUsername(),
      ));

      $fieldset->addField('1', 'label', array(
          'label'     => Mage::helper('marketing')->__('Email'),
          'after_element_html' => $this->ArrayEmail(),
      ));
      
      $fieldset->addField('count_like', 'label', array(
          'label'     => Mage::helper('marketing')->__('Số like'),
          //'after_element_html' => $this->ArrayEmail(),
            'name'      => 'count_like',
      ));
      
      $fieldset->addField('face_id', 'label', array(
          'label'     => Mage::helper('marketing')->__('Face_id'),
          //'class'     => '',
          //'required'  => false,
          'name'      => 'face_id',
      ));
       $fieldset->addField('user_id', 'hidden', array(
            'required'  => false,
            'name'      => 'user_id',
      ));
      $fieldset->addField('photo', 'hidden', array(
            'required'  => false,
            'name'      => 'photo-hidden',
      ));
      $fieldset->addType('extended_label','Luclong_Marketing_Lib_Varien_Data_Form_Element_ExtendedLabel');
        $fieldset->addField('mycustom_element', 'extended_label', array(
            'name'          => 'mycustom_element',
            'required'      => false,
            //'value'     => $this->getLastEventLabel($lastEvent),
      ));
      $fieldset->addField('marketing_photo', 'file', array(
            'label'     => Mage::helper('marketing')->__('Photo'),
            'required'  => false,
           // 'value'  => 'Upload',
            'name'      => 'photo',
            'after_element_html' => '<br/><i>Up ảnh tối đa <b>2M</b></i>'
      ));
      
      $fieldset->addField('description', 'editor', array(
          'name'      => 'description',
          'label'     => Mage::helper('marketing')->__('Description'),
          'title'     => Mage::helper('marketing')->__('Description'),
          'style'     => 'width:300px; height:200px;',
          'wysiwyg'   => false,
          'required'  => false,
      ));
      
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('marketing')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('marketing')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('marketing')->__('Disabled'),
              ),
			  array(
                  'value'     => 3,
                  'label'     => Mage::helper('marketing')->__('Not Accept'),
              ),
          ),
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getMarketingData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getMarketingData());
          Mage::getSingleton('adminhtml/session')->setMarketingData(null);
      } elseif ( Mage::registry('marketing_data') ) {
          $form->setValues(Mage::registry('marketing_data')->getData());
      }
      return parent::_prepareForm();
  }
}