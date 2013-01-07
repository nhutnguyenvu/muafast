<?php

class TTS_OnePageCheckout_Block_Shipping extends Mage_Checkout_Block_Onepage_Shipping
{
    public function getAddress()
    {
        if (is_null($this->_address))
        {
            if ($this->isCustomerLoggedIn())
            {
                $this->_address = $this->getQuote()->getShippingAddress();
            }
            else
            {
                $this->_address = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
            }
        }
        return $this->_address;
    }
}
