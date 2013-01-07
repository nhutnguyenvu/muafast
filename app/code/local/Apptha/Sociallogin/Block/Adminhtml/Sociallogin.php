<?php

/**
 * @name         :  Apptha One Step Checkout
 * @version      :  1.0
 * @since        :  Magento 1.5
 * @author       :  Prabhu Mano
 * @copyright    :  Copyright (C) 2011 Powered by Apptha
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  July 26 2012
 *
 * */
?>
<?php
class Apptha_Sociallogin_Block_Adminhtml_Sociallogin extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_sociallogin';
    $this->_blockGroup = 'sociallogin';
    $this->_headerText = Mage::helper('sociallogin')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('sociallogin')->__('Add Item');
    parent::__construct();
  }
}