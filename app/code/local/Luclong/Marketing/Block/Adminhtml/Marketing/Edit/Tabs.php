<?php

class Luclong_Marketing_Block_Adminhtml_Marketing_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('marketing_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('marketing')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('marketing')->__('Item Information'),
          'title'     => Mage::helper('marketing')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('marketing/adminhtml_marketing_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}