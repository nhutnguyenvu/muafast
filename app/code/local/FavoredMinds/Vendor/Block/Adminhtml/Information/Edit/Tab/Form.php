<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Block_Adminhtml_Information_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
	protected function _prepareForm() {
		$vendor = Mage::registry('vendor_data');
	
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('vendor_form', array('legend'=>Mage::helper('vendor')->__('General Information')));
		if ($vendor->getId()) {
			$fieldset->addField('username', 'text', array(
				'label'     => Mage::helper('vendor')->__('Username'),
				'class'     => 'required-entry',
				'style'     => 'background-color:#D6D6D6',
				'required'  => true,
				'readonly' => 'readonly',
				'name'      => 'username',
			));
		} else {
			$fieldset->addField('username', 'text', array(
				'label'     => Mage::helper('vendor')->__('Username'),
				'class'     => 'required-entry',
				'required'  => true,
				'name'      => 'username',
			));
		}
  $fieldset->addField('logo', 'image', array(
            'label'     => Mage::helper('vendor')->__('Logo'),
            'required'  => false,
            'name'      => 'filename',
    ));


    $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('vendor')->__('Status'),
            'name'      => 'status',
            'values'    => array(
                    array(
                            'value'     => 1,
                            'label'     => Mage::helper('vendor')->__('Approved'),
                    ),

                    array(
                            'value'     => 0,
                            'label'     => Mage::helper('vendor')->__('Pending'),
                    ),
            ),
    ));

    $fieldset->addField('email', 'text', array(
            'label'     => Mage::helper('vendor')->__('Email'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'email',
    ));

    $fieldset->addField('firstname', 'text', array(
            'label'     => Mage::helper('vendor')->__('First Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'firstname',
    ));

    $fieldset->addField('lastname', 'text', array(
            'label'     => Mage::helper('vendor')->__('Last Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'lastname',
    ));

    $fieldset->addField('address', 'text', array(
            'label'     => Mage::helper('vendor')->__('Address'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'address',
    ));

    $fieldset->addField('city', 'text', array(
            'label'     => Mage::helper('vendor')->__('City'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'city',
    ));

    $fieldset->addField('state', 'text', array(
            'label'     => Mage::helper('vendor')->__('State'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'state',
    ));

    $fieldset->addField('zip', 'text', array(
            'label'     => Mage::helper('vendor')->__('Zip'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'zip',
    ));

    $fieldset->addField('country', 'text', array(
            'label'     => Mage::helper('vendor')->__('Country'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'country',
    ));

    $fieldset->addField('url', 'text', array(
            'label'     => Mage::helper('vendor')->__('URL'),
            'required'  => false,
            'name'      => 'url',
    ));

    $fieldset->addField('salesperweek', 'text', array(
            'label'     => Mage::helper('vendor')->__('Sales per Week'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'salesperweek',
    ));

    $fieldset->addField('company_name', 'text', array(
            'label'     => Mage::helper('vendor')->__('Vendor Company Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'company_name',
    ));

    if (Mage::getStoreConfig('vendor/general/sku_prefix') == 1) {
      $fieldset->addField('sku_prefix', 'text', array(
              'label'     => Mage::helper('vendor')->__('sku_prefix'),
              'required'  => false,
              'name'      => 'sku_prefix',
			  'style'     => 'background-color:#D6D6D6',
			  'readonly' => 'readonly',
      ));
    }

    $fieldset->addField('aboutcompany', 'editor', array(
            'name'      => 'aboutcompany',
            'label'     => Mage::helper('vendor')->__('About your Company'),
            'title'     => Mage::helper('vendor')->__('About your Company'),
            'style'     => 'width:700px; height:500px;',
            'wysiwyg'   => false,
            'required'  => true,
    ));

    $fieldset->addField('paypal', 'text', array(
            'label'     => Mage::helper('vendor')->__('Paypal Email'),
            'name'      => 'paypal',
    ));

    $fieldset->addField('commission', 'text', array(
            'label'     => Mage::helper('vendor')->__('Commission'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'commission',
			'style'     => 'background-color:#D6D6D6',
			'readonly' => 'readonly',
    ));

    if ($vendor->getId()) {
      // add password management fieldset
      $newFieldset = $form->addFieldset(
              'password_fieldset',
              array('legend'=>Mage::helper('customer')->__('Password Management'))
      );
      $newFieldset->addField('unencrypted_pass', 'text', array(
              'label'     => Mage::helper('vendor')->__('Current Password'),
              'name'      => 'unencrypted_pass',
      ));

      // New customer password
      $field = $newFieldset->addField('new_password', 'text',
              array(
              'label' => Mage::helper('vendor')->__('New Password'),
              'name'  => 'new_password',
              'class' => 'validate-new-password'
              )
      );

      $field = $newFieldset->addField('new_password_confirmation', 'text',
              array(
              'label' => Mage::helper('vendor')->__('Confirm New Password'),
              'name'  => 'new_password_confirmation',
              'class' => 'validate-new-password'
              )
      );
    }
    else {
      $newFieldset = $form->addFieldset(
              'password_fieldset',
              array('legend'=>Mage::helper('customer')->__('Password Management'))
      );
      $field = $newFieldset->addField('unencrypted_pass', 'text',
              array(
              'label' => Mage::helper('vendor')->__('Password'),
              'class' => 'input-text required-entry validate-password',
              'name'  => 'unencrypted_pass',
              'required' => true
              )
      );
    }

    if ( Mage::getSingleton('adminhtml/session')->getVendorData() ) {
      $form->setValues(Mage::getSingleton('adminhtml/session')->getVendorData());
      Mage::getSingleton('adminhtml/session')->getVendorData(null);
    } elseif ( Mage::registry('vendor_data') ) {
      $form->setValues(Mage::registry('vendor_data')->getData());
    }
    return parent::_prepareForm();
  }
}
