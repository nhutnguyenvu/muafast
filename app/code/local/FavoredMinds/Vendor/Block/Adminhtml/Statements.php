<?php
/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */

class FavoredMinds_Vendor_Block_Adminhtml_Statements extends Mage_Adminhtml_Block_Widget_Grid_Container {
    
  public function __construct() {
    $this->_controller = 'adminhtml_statements';
    $this->_blockGroup = 'vendor';
    $this->_headerText = Mage::helper('vendor')->__('Vendor Sales Statements');
    $helper 		= Mage::app()->getHelper('vendor');
    $vendorIsLogged	= $helper->vendorIsLogged();
    if (!$vendorIsLogged) {
      $this->_addButton('generate_statements', array(
              'label'    => Mage::helper('vendor')->__('Generate Vendor Sales Statements'),
              'onclick'  => 'setLocation(\'' . $this->getGenerateUrl() . '\')',
      ));
    }
    parent::__construct();
    $this->_removeButton('add');
  }

  public function getGenerateUrl() {
    return $this->getUrl('*/*/generate');
  }

}