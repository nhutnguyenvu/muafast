<?php

class Luclong_Mixmatch_Block_Adminhtml_Mixmatch_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('mixmatch_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('mixmatch')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('mixmatch')->__('Item Information'),
          'title'     => Mage::helper('mixmatch')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('mixmatch/adminhtml_mixmatch_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}