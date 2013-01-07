<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Model_Quote_Address_Rate extends Mage_Sales_Model_Quote_Address_Rate
{
	// overwrite default shipping price with custom shipping price that includes vendor shipping price set for products
    public function importShippingRate(Mage_Shipping_Model_Rate_Result_Abstract $rate)
    {
        if ($rate instanceof Mage_Shipping_Model_Rate_Result_Error) {
            $this
                ->setCode($rate->getCarrier().'_error')
                ->setCarrier($rate->getCarrier())
                ->setCarrierTitle($rate->getCarrierTitle())
                ->setErrorMessage($rate->getErrorMessage())
            ;
        } elseif ($rate instanceof Mage_Shipping_Model_Rate_Result_Method) {
			
			$this
                ->setCode($rate->getCarrier().'_'.$rate->getMethod())
                ->setCarrier($rate->getCarrier())
                ->setCarrierTitle($rate->getCarrierTitle())
                ->setMethod($rate->getMethod())
                ->setMethodTitle($rate->getMethodTitle())
                ->setMethodDescription($rate->getMethodDescription())
                //->setPrice($rate->getPrice())
            ;
			
			$onepage		= Mage::getSingleton('checkout/type_onepage');
			
			$shipping_price	= 0;
			$totalproducts	= 0;
			$totalproducts2	= 0;
			
			
			// calculate shipping cost for all products in order
			foreach($onepage->getQuote()->getItemsCollection()->getItems() as $item)
			{
				$totalproducts++;
				$_product		= Mage::getModel('catalog/product')->load($item->getProduct()->getId());
				$shipping_cost	= $_product->getData("shipping_cost");
				
				if($shipping_cost*1>0)
				{
					$shipping_price	+= $shipping_cost*1*($item->getData("qty"));
					$totalproducts2++;
				}
			}
			
			if($shipping_price>0)
			{
				$price				= $rate->getPrice();
				$itemShippingPrice	= $price/$totalproducts;
				$price				= round($shipping_price+($itemShippingPrice*($totalproducts-$totalproducts2)),2);
				
				$this->setPrice($price);
			}
			else
			{
				$this->setPrice($rate->getPrice());
			}
			
        }
        return $this;
    }
}