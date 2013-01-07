<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class FavoredMinds_Vendor_Model_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage {

    // Overwrite saveOrder from Mage_Checkout_Model_Type_Onepage
    // in order to add automatic e-mail sending & automatic payments
    public function saveOrder() {

        
        $this->validateOrder();
        $billing = $this->getQuote()->getBillingAddress();
        if (!$this->getQuote()->isVirtual()) {
            $shipping = $this->getQuote()->getShippingAddress();
        }
        $method = $this->getQuote()->getCheckoutMethod();
        
        if(empty($method)){
            $method = Mage_Sales_Model_Quote::CHECKOUT_METHOD_GUEST;
        }
        
        switch ($method) {
            
            case Mage_Sales_Model_Quote::CHECKOUT_METHOD_GUEST:
                
                if (!$this->getQuote()->isAllowedGuestCheckout()) {
                    Mage::throwException(Mage::helper('checkout')->__('Sorry, guest checkout is not enabled. Please try again or contact store owner.'));
                }
                $this->getQuote()->setCustomerId(null)
                        ->setCustomerEmail($billing->getEmail())
                        ->setCustomerIsGuest(true)
                        ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
                break;

            case Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER:
                $customer = Mage::getModel('customer/customer');
                /* @var $customer Mage_Customer_Model_Customer */

                $customerBilling = $billing->exportCustomerAddress();
                $customer->addAddress($customerBilling);

                if (!$this->getQuote()->isVirtual() && !$shipping->getSameAsBilling()) {
                    $customerShipping = $shipping->exportCustomerAddress();
                    $customer->addAddress($customerShipping);
                }

                if ($this->getQuote()->getCustomerDob() && !$billing->getCustomerDob()) {
                    $billing->setCustomerDob($this->getQuote()->getCustomerDob());
                }

                if ($this->getQuote()->getCustomerTaxvat() && !$billing->getCustomerTaxvat()) {
                    $billing->setCustomerTaxvat($this->getQuote()->getCustomerTaxvat());
                }

                Mage::helper('core')->copyFieldset('checkout_onepage_billing', 'to_customer', $billing, $customer);

                $customer->setPassword($customer->decryptPassword($this->getQuote()->getPasswordHash()));
                $customer->setPasswordHash($customer->hashPassword($customer->getPassword()));

                $this->getQuote()->setCustomer($customer);
                break;

            default:
                $customer = Mage::getSingleton('customer/session')->getCustomer();

                if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
                    $customerBilling = $billing->exportCustomerAddress();
                    $customer->addAddress($customerBilling);
                }
                if (!$this->getQuote()->isVirtual() &&
                        ((!$shipping->getCustomerId() && !$shipping->getSameAsBilling()) ||
                        (!$shipping->getSameAsBilling() && $shipping->getSaveInAddressBook()))) {

                    $customerShipping = $shipping->exportCustomerAddress();
                    $customer->addAddress($customerShipping);
                }
                $customer->setSavedFromQuote(true);
                $customer->save();

                $changed = false;
                if (isset($customerBilling) && !$customer->getDefaultBilling()) {
                    $customer->setDefaultBilling($customerBilling->getId());
                    $changed = true;
                }
                if (!$this->getQuote()->isVirtual() && isset($customerBilling) && !$customer->getDefaultShipping() && $shipping->getSameAsBilling()) {
                    $customer->setDefaultShipping($customerBilling->getId());
                    $changed = true;
                } elseif (!$this->getQuote()->isVirtual() && isset($customerShipping) && !$customer->getDefaultShipping()) {
                    $customer->setDefaultShipping($customerShipping->getId());
                    $changed = true;
                }

                if ($changed) {
                    $customer->save();
                }
        }

        $this->getQuote()->reserveOrderId();
        $convertQuote = Mage::getModel('sales/convert_quote');
        /* @var $convertQuote Mage_Sales_Model_Convert_Quote */
        //$order = Mage::getModel('sales/order');
        if ($this->getQuote()->isVirtual()) {
            $order = $convertQuote->addressToOrder($billing);
        } else {
            $order = $convertQuote->addressToOrder($shipping);
        }
        /* @var $order Mage_Sales_Model_Order */
        $order->setBillingAddress($convertQuote->addressToOrderAddress($billing));

        if (!$this->getQuote()->isVirtual()) {
            $order->setShippingAddress($convertQuote->addressToOrderAddress($shipping));
        }

        $order->setPayment($convertQuote->paymentToOrderPayment($this->getQuote()->getPayment()));

        foreach ($this->getQuote()->getAllItems() as $item) {
            $orderItem = $convertQuote->itemToOrderItem($item);
            if ($item->getParentItem()) {
                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
            }
            $order->addItem($orderItem);
        }

        /**
         * We can use configuration data for declare new order status
         */
        Mage::dispatchEvent('checkout_type_onepage_save_order', array('order' => $order, 'quote' => $this->getQuote()));

        // check again, if customer exists
        if ($this->getQuote()->getCheckoutMethod() == Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER) {
            if ($this->_customerEmailExists($customer->getEmail(), Mage::app()->getWebsite()->getId())) {
                Mage::throwException(Mage::helper('checkout')->__('There is already a customer registered using this email address'));
            }
        }
        $order->place();


        if ($this->getQuote()->getCheckoutMethod() == Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER) {
            $customer->save();
            $customerBillingId = $customerBilling->getId();
            if (!$this->getQuote()->isVirtual()) {
                $customerShippingId = isset($customerShipping) ? $customerShipping->getId() : $customerBillingId;
                $customer->setDefaultShipping($customerShippingId);
            }
            $customer->setDefaultBilling($customerBillingId);
            $customer->save();

            $this->getQuote()->setCustomerId($customer->getId());

            $order->setCustomerId($customer->getId());
            Mage::helper('core')->copyFieldset('customer_account', 'to_order', $customer, $order);

            $billing->setCustomerId($customer->getId())->setCustomerAddressId($customerBillingId);
            if (!$this->getQuote()->isVirtual()) {
                $shipping->setCustomerId($customer->getId())->setCustomerAddressId($customerShippingId);
            }

            if ($customer->isConfirmationRequired()) {
                $customer->sendNewAccountEmail('confirmation');
            } else {
                $customer->sendNewAccountEmail();
            }
        }

        /**
         * a flag to set that there will be redirect to third party after confirmation
         * eg: paypal standard ipn
         */
        $redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
        if (!$redirectUrl) {
            $order->setEmailSent(true);
        }

        $order->save();


        // SEND AUTOMATIC PAYMENT
        $helper = Mage::app()->getHelper('vendor');
        $items = $order->getItemsCollection();
        $payments = array();
        $mails = array();
        $order_id = $order->getId();

        $_products = array();
        $_items = array();

        foreach ($items->getItems() as $item) {
            if (!$item->getParentItem()) {
                //mail('mi@orientsol.com', 'Order: ', $item->getProduct()->getId());
                //$itemData	= $item->toArray();
                array_push($_items, $item); // save all items into this array for further use
                $itemData = $item->toArray();
                $productId = $itemData["product_id"];

                // load the product, get the manufacturer (vendor) and then get the commission for vendor to calculate amount
                if (!isset($_products[$productId])) {
                    $_product = Mage::getModel('catalog/product')->load($productId);
                    $_products[$productId] = $_product;
                } else {
                    $_product = $_products[$productId];
                }

                $manufacturer = $_product->getAttributeText("manufacturer");

                $vendor_id = $helper->getVendorByManufacturer($manufacturer);
                if ($vendor_id) {
                    $productData = $_product->toArray();
                    $productData["qty_ordered"] = $itemData["qty_ordered"];
                    $vendorData = $helper->getVendorUserInfo($vendor_id);
                }
                if ($_product->getId()) {

                    if (isset($itemData["product_type"]) && $itemData["product_type"] == 'configurable') {
                        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                        $item_id = $itemData["item_id"];
                        $table = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
                        $resultqry = $write->query("select product_id from `$table` where parent_item_id = '" . $item_id . "';");
                        $lineqry = $resultqry->fetch();

                        if (isset($lineqry["product_id"]) && ($lineqry["product_id"] != '')) {
                            $_product = Mage::getModel('catalog/product')->load($lineqry["product_id"]);
                        }
                    }
                    $oldQty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getQty();
                    $qty = (float) $itemData["qty_ordered"] ? (float) $itemData["qty_ordered"] : 1;
                    $newQty = $oldQty - $qty;
                    
                    if ($newQty > 0) {

                        try{
                            $_product->setStockData(array('qty' => $newQty));
                        }
                        catch(Exception $ex){
                            echo $ex->getMessage();
                        }
                        
                    } else {
                             
                        try{
                            $_product->setStockData(array('qty' => $newQty, 'is_in_stock' => 1));
                        }
                        
                        catch(Exception $ex){
                            //echo $ex->getMessage();
                        }


                    }
                    try{
                        $_product->save();
                    }
                    catch(Exception $ex){
                           // echo $ex->getMessage();
                    }
                }
            }
        }
        //exit;
//mail('mi@orientsol.com', 'Items: ', count($_items));
        // send automatic emails to vendors
        foreach ($_items as $itemData) {
            $productId = $itemData["product_id"];

            // load the product, get the manufacturer (vendor) and then get the commission for vendor to calculate amount
            if (!isset($_products[$productId])) {
                $_product = Mage::getModel('catalog/product')->load($productId);
                $_products[$productId] = $_product;
            } else {
                $_product = $_products[$productId];
            }
            $manufacturer = $_product->getAttributeText("manufacturer");

            $vendor_id = $helper->getVendorByManufacturer($manufacturer);
            if ($vendor_id) {
                $vendorData = $helper->getVendorUserInfo($vendor_id);

                if (!isset($mails[$vendor_id])) {
                    $mails[$vendor_id] = array();
                    $mails[$vendor_id]["order_id"] = $order->getIncrementId();
                    $mails[$vendor_id]["order"] = array();
                    $mails[$vendor_id]["order"]["orderData"] = $order;
                    $mails[$vendor_id]["order"]["items"] = array();
                    $mails[$vendor_id]["order"]["total"] = 0;
                    $mails[$vendor_id]["order"]["total_shipping"] = 0;
                    $mails[$vendor_id]["vendorData"] = $vendorData;
                }

                $commission = 100 - $vendorData["commission"] * 1;

                $productData = $_product->toArray();
                
                $productData["qty_ordered"] = $itemData["qty_ordered"];
                $productData["price"] = $itemData["price_incl_tax"];

                $productData["sku"] = $itemData["sku"];
                $productData["name"] = $itemData["name"];

                if (isset($itemData["product_type"]) && $itemData["product_type"] == 'configurable') {
                    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                    $item_id = $itemData["item_id"];
                    $table = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
                    $resultqry = $write->query("select name from `$table` where parent_item_id = '" . $item_id . "';");
                    $lineqry = $resultqry->fetch();

                    if (isset($lineqry["name"]) && ($lineqry["name"] != '')) {
                        $productData["name"] .= "<br/>" . $lineqry["name"];
                    }
                }
                $productData["shipping_cost"] = 0;
                $productData["vendor_amount"] = round(
                        ($productData["qty_ordered"] * $productData["shipping_cost"]) +
                        ($productData["qty_ordered"] * (($productData["price"] / 100) * $commission)   // 60% of price
                        ), 2);

                array_push($mails[$vendor_id]["order"]["items"], $productData);
                $mails[$vendor_id]["order"]["total"] += $productData["vendor_amount"] * 1;
                $mails[$vendor_id]["order"]["total_shipping"] += ($productData["shipping_cost"] * 1) * ($productData["qty_ordered"] * 1);
            }
        }

        //send emails

        foreach ($mails as $vendor_id => $mailData) {
            
           
            $helper->sendVendorMail($vendor_id, $mailData, $order);
        }


        Mage::dispatchEvent('checkout_type_onepage_save_order_after', array('order' => $order, 'quote' => $this->getQuote()));

        /**
         * need to have somelogic to set order as new status to make sure order is not finished yet
         * quote will be still active when we send the customer to paypal
         */
        $orderId = $order->getIncrementId();
        $this->getCheckout()->setLastQuoteId($this->getQuote()->getId());
        $this->getCheckout()->setLastOrderId($order->getId());
        $this->getCheckout()->setLastRealOrderId($order->getIncrementId());
        $this->getCheckout()->setRedirectUrl($redirectUrl);

        /**
         * we only want to send to customer about new order when there is no redirect to third party
         */
        if (!$redirectUrl) {
            $order->sendNewOrderEmail();
        }

        if ($this->getQuote()->getCheckoutMethod(true) == Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER
                && !Mage::getSingleton('customer/session')->isLoggedIn()) {
            /**
             * we need to save quote here to have it saved with Customer Id.
             * so when loginById() executes checkout/session method loadCustomerQuote
             * it would not create new quotes and merge it with old one.
             */
            $this->getQuote()->save();
            if ($customer->isConfirmationRequired()) {
                Mage::getSingleton('checkout/session')->addSuccess(Mage::helper('customer')->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())
                        ));
            } else {
                Mage::getSingleton('customer/session')->loginById($customer->getId());
            }
        }

        //Setting this one more time like control flag that we haves saved order
        //Must be checkout on success page to show it or not.
        $this->getCheckout()->setLastSuccessQuoteId($this->getQuote()->getId());

        $this->getQuote()->setIsActive(false);
        $this->getQuote()->save();

        return $this;
    }

}
