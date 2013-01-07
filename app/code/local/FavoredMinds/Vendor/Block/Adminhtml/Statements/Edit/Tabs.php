<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Adminhtml_Statements_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('statement_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('vendor')->__('Statement Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('vendor')->__('General Information'),
          'title'     => Mage::helper('vendor')->__('General Information'),
          'content'   => $this->getLayout()->createBlock('vendor/adminhtml_statements_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}