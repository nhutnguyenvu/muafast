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

class Apptha_Sociallogin_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('sociallogin')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('sociallogin')->__('Disabled')
        );
    }
    public function toOptionArray()
  {
    return array(
      array('value' => 0, 'label' => Mage::helper('sociallogin')->__('Header')),
      array('value' => 1, 'label' => Mage::helper('sociallogin')->__('Inner Pages')),
         
    );
  }
}