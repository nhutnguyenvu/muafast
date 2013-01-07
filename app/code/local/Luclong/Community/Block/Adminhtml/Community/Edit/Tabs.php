<?php

class Luclong_Community_Block_Adminhtml_Community_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('community_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('community')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('community')->__('Item Information'),
          'title'     => Mage::helper('community')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('community/adminhtml_community_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}