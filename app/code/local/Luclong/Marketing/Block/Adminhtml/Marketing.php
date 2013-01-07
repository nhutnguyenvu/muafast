<?php
class Luclong_Marketing_Block_Adminhtml_Marketing extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_marketing';
    $this->_blockGroup = 'marketing';
    $this->_headerText = Mage::helper('marketing')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('marketing')->__('Add Item');
    parent::__construct();
    $this->_removeButton('add');
  }
}