<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart controller
 */
require_once 'Mage/Checkout/controllers/CartController.php';

class PLUTUS_Home_Checkout_CartController extends Mage_Checkout_CartController
{
    public function addmultipleAction() {
        
        $productIds = $this->getRequest()->getParam('products');
        if (!is_array($productIds)) {
            $this->_goBack();
            return;
        }
        $cart = Mage::getModel("checkout/cart");
        //$cart->addProductsByIds($productIds);
        $qtyList = array();
        foreach ($productIds as $key=>$productId) {
            
            try {
                if(empty($productId)){
                    continue; 
                }
                $qty = $this->getRequest()->getParam('qty' . $productId, 0);               
                $qtyList[$key] = intval($qty);
                $productId  = intval($productId);
                if ($qtyList[$key] <= 0)
                    continue; // nothing to add
                
                $cart->addProduct($productId,$qtyList[$key]);
                
                
                $product = Mage::getModel("catalog/product")->load($productId);

                $message = $this->__('%s was successfully added to your shopping cart.', $product->getName());
                Mage::getSingleton('checkout/session')->addSuccess($message);
                
            } catch (Mage_Core_Exception $e) {
                if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                    Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
                } else {
                    Mage::getSingleton('checkout/session')->addError($e->getMessage());
                }
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addException($e, $this->__('Can not add item to shopping cart'));
            }
           
        }
        $cart->save(); 
        
        $this->_goBack();
    }
  
    


}
