<?php
class Luclong_Mixmatch_Block_Adminhtml_Mixmatch extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_mixmatch';
    $this->_blockGroup = 'mixmatch';
    $this->_headerText = Mage::helper('mixmatch')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('mixmatch')->__('Add Item');
    parent::__construct();
  }
}