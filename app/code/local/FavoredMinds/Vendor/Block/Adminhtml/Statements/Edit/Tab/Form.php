<?php
/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */

class FavoredMinds_Vendor_Block_Adminhtml_Statements_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
  protected function _prepareForm() {
    $statement = Mage::registry('vendorstataments_data');

    $form = new Varien_Data_Form();
    $this->setForm($form);
    $fieldset = $form->addFieldset('vendor_form', array('legend'=>Mage::helper('vendor')->__('Statement information')));

    $fieldset->addField('statement_id', 'text', array(
            'label'     => Mage::helper('vendor')->__('Statement Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'statement_id',
    ));

    /*
    $helper 		= Mage::app()->getHelper('vendor');
    if (!$helper->vendorIsLogged()) {
      $fieldset->addField('vendor_id', 'select', array(
              'label'     => Mage::helper('vendor')->__('Vendor'),
              'name'      => 'status',
              'values'    => array(
                      array(
                              'value'     => 1,
                              'label'     => Mage::helper('vendor')->__('Paid'),
                      ),

                      array(
                              'value'     => 2,
                              'label'     => Mage::helper('vendor')->__('Unpaid'),
                      ),
              ),
      ));
    }
     *
     */


    $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('vendor')->__('Status'),
            'name'      => 'status',
            'values'    => array(
                    array(
                            'value'     => 1,
                            'label'     => Mage::helper('vendor')->__('Paid'),
                    ),

                    array(
                            'value'     => 2,
                            'label'     => Mage::helper('vendor')->__('Unpaid'),
                    ),
            ),
    ));

    $fieldset->addField('total_comission', 'text', array(
            'label'     => Mage::helper('vendor')->__('Total Store Commission'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'total_comission',
    ));

    $fieldset->addField('total_payout', 'text', array(
            'label'     => Mage::helper('vendor')->__('Total Vendor Payout'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'total_payout',
    ));
    /*
    $fieldset->addField('orders_data', 'editor', array(
            'name'      => 'orders_data',
            'label'     => Mage::helper('vendor')->__('Order Information'),
            'title'     => Mage::helper('vendor')->__('Order Information'),
            'style'     => 'width:700px; height:500px;',
            'wysiwyg'   => false,
            'required'  => true,
    ));
    */
    if ( Mage::getSingleton('adminhtml/session')->getVendorData() ) {
      $form->setValues(Mage::getSingleton('adminhtml/session')->getVendorData());
      Mage::getSingleton('adminhtml/session')->getVendorData(null);
    } elseif ( Mage::registry('vendorstatements_data') ) {
      $form->setValues(Mage::registry('vendorstatements_data')->getData());
    }
    return parent::_prepareForm();
  }
}