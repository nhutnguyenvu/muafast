<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Adminhtml_Sales_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    
  public function __construct() {
    parent::__construct();

    $this->_objectId    = 'order_id';
    $this->_blockGroup = 'vendor';
    $this->_controller = 'adminhtml_sales';

    //$this->_updateButton('save', 'label', Mage::helper('vendor')->__('Update'));
    $this->_removeButton('save');
    $this->_removeButton('delete');
    $this->_removeButton('reset');
    //$this->setId('sales_order_view');
    $order = $this->getOrder();

    $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Update'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
            ), -100);
    $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('rma_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'rma_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'rma_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
  }
  public function getHeaderText() {
    if( Mage::registry('vendorsales_data') && Mage::registry('vendorsales_data')->getId() ) {
      return Mage::helper('vendor')->__("Order Update #%s", $this->htmlEscape(Mage::registry('vendorsales_data')->getIncrementId()));
    } else {
      return Mage::helper('vendor')->__('Order Update');
    }
  }
}