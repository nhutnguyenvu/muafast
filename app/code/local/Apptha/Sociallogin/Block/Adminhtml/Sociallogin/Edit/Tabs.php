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

class Apptha_Sociallogin_Block_Adminhtml_Sociallogin_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('sociallogin_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('sociallogin')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('sociallogin')->__('Item Information'),
          'title'     => Mage::helper('sociallogin')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('sociallogin/adminhtml_sociallogin_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}