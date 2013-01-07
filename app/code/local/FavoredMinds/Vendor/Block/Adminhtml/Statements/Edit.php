<?php
/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */

class FavoredMinds_Vendor_Block_Adminhtml_Statements_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
  public function __construct() {
    parent::__construct();
    $this->_objectId = 'vendor_statement_id';
    $this->_blockGroup = 'vendor';
    $this->_controller = 'adminhtml_statements';
    $this->_headerText = Mage::helper('vendor')->__('Vendor Sales Statements');

    $this->_updateButton('save', 'label', Mage::helper('vendor')->__('Save Statement'));
    $this->_updateButton('delete', 'label', Mage::helper('vendor')->__('Delete Statement'));

    $helper 		= Mage::app()->getHelper('vendor');
    if ($helper->vendorIsLogged()) {
      $this->_removeButton('save');
      $this->_removeButton('delete');
    }

    $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('rma_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'rma_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'rma_content');
                }
            }
        ";
  }

  public function getHeaderText() {
    if( Mage::registry('vendorstatements_data') && Mage::registry('vendorstatements_data')->getId() ) {
      return Mage::helper('vendor')->__("Edit Statement '%s'", $this->htmlEscape(Mage::registry('vendorstatements_data')->getStatementId()));
    } else {
      return Mage::helper('vendor')->__('Generate Vendor Sales Statements');
    }
  }
}