<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class FavoredMinds_Vendor_Block_Adminhtml_Sales_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form(array(
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                        )
        );

        $form->setUseContainer(true);
        $fieldset = $form->addFieldset('vendorsales_submit', array('legend' => Mage::helper('vendor')->__('Ghi chú về sản phẩm')));

        $_helper = Mage::app()->getHelper('vendor');
        if ($_helper->vendorIsLogged()) {
            $_vendor = $_helper->getVendorUserInfo($_helper->getVendorUserId());
        }
        $_order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('id'));
        $_items = $_order->getItemsCollection();
        $values = array();
        //echo count($_items);
        
        foreach ($_items as $_item):
            
            $product = Mage::getModel('catalog/product')->load($_item->getProductId());
            
            if ($_helper->vendorIsLogged()) {
                //we filter the output by vendor
                if ($product) { 
                    $manufacturer = $product->getManufacturer();
                    
                    if ($manufacturer == $_vendor['vendor_code']) {
                        if (!$product->isConfigurable()){
                            $values[] = array('value' => $_item->getId(), 'label' => $_item->getName());
                        }
                    }
                }
            } else {
                if (!$product->isConfigurable()) {
                    $values[] = array('value' => $_item->getId(), 'label' => $_item->getName());
                }
            }
            
        endforeach;
        
        $fieldset->addField('products', 'multiselect', array(
            'label' => Mage::helper('vendor')->__('For Products'),
            'name' => 'products',
            'class' => 'required-element',
            'values' => $values,
            'required' => true,
        ));
        /*
        $statuses = array();
        $statuses[] = array('value' => '', 'label' => 'Not changed');
        $statuses[] = array('value' => 'Pending', 'label' => 'Pending');
        $statuses[] = array('value' => 'Processing', 'label' => 'Processing');
        $statuses[] = array('value' => 'Complete', 'label' => 'Complete');
        $statuses[] = array('value' => 'On Hold', 'label' => 'On Hold');
        $statuses[] = array('value' => 'Cancelled', 'label' => 'Cancelled');
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('vendor')->__('Update Status'),
            'required' => false,
            'name' => 'status',
            'values' => $statuses,
        ));
        */

        $adjustments = array();
        $adjustments[] = array('value' => '', 'label' => 'Not changed');
        $adjustments[] = array('value' => 'Negative', 'label' => 'Negative');
        $adjustments[] = array('value' => 'Positive', 'label' => 'Positive');
        /*
        $fieldset->addField('adjustment_type', 'select', array(
            'label' => Mage::helper('vendor')->__('Adjustment'),
            'required' => false,
            'name' => 'adjustment_type',
            'values' => $adjustments,
        ));
        */
        /*
        $fieldset->addField('adjustment', 'text', array(
            'label' => Mage::helper('vendor')->__('Adjustment Amount'),
            'required' => false,
            'name' => 'adjustment',
        ));*/

        $fieldset->addField('comment_text', 'editor', array(
            'name' => 'comment_text',
            'label' => Mage::helper('vendor')->__('Comment'),
            'title' => Mage::helper('vendor')->__('Comment'),
            'wysiwyg' => false,
            'required' => true,
        ));
        /*
        $fieldset1 = $form->addFieldset('vendorsales_tracking', array('legend' => Mage::helper('vendor')->__('Add Tracking Information for selected products')));

        $fieldset1->addField('carrier', 'text', array(
            'label' => Mage::helper('vendor')->__('Shipping Carrier'),
            'required' => false,
            'name' => 'carrier',
        ));

        $fieldset1->addField('tracking', 'text', array(
            'label' => Mage::helper('vendor')->__('Tracking #'),
            'required' => false,
            'name' => 'tracking',
        ));
        */
        if (Mage::getSingleton('adminhtml/session')->getVendorsalesData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getVendorsalesData());
            Mage::getSingleton('adminhtml/session')->setVendorsalesData(null);
        } elseif (Mage::registry('vendorsales_data')) {
            $form->setValues(Mage::registry('vendorsales_data')->getData());
        }
        
        $this->setForm($form);

        return parent::_prepareForm();
    }

}