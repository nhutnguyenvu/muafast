<?php
class Luclong_Community_Block_Adminhtml_Community extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_community';
    $this->_blockGroup = 'community';
    $this->_headerText = Mage::helper('community')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('community')->__('Add Item');
    parent::__construct();
  }
}