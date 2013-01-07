<?php

class TTS_OnePageCheckout_Block_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    public function getAddress()
    {
        if (empty($this->_address))
        {
            if ($this->isCustomerLoggedIn())
            {
                $address = $this->getQuote()->getShippingAddress();
            }
            else
            {
                $address = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
            }
            $address->implodeStreetAddress();
            $address->setCollectShippingRates(true);
            $address->collectShippingRates();
            $address->setCollectShippingRates(true);
            $this->_address = $address;
        }
        return $this->_address;
    }
}
