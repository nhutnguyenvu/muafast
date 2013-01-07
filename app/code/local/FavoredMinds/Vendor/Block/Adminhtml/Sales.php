<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Adminhtml_Sales extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_sales';
    $this->_blockGroup = 'vendor';
    $this->_headerText = Mage::helper('vendor')->__('Danh sách đơn hàng');
    parent::__construct();
    $this->_removeButton('add');
  }
}