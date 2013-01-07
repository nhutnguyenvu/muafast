<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Adminhtml_Vendors extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_vendors';
    $this->_blockGroup = 'vendor';
    $this->_headerText = Mage::helper('vendor')->__('Vendors');
    $this->_addButtonLabel = Mage::helper('vendor')->__('Create New Vendor');
    parent::__construct();
  }
}