<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class Luclong_Tools_ImageController extends Mage_Core_Controller_Front_Action {

    
    public function removeAction(){

        ob_start();
        
        //$vendor_code = $this->getRequest()->getParam("vendor_code");

        $params = $this->getRequest()->getParam("vendor_code");

        $params = explode(",", $params);
        
        $helper = Mage::helper("tools");
        Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
        foreach ($params as $vendor_code){
            
            $productCollection = $helper->getProductListByVendor($vendor_code);
            
            if(count($productCollection) > 0){
                foreach ($productCollection as $product){

                    $_product = Mage::getModel('catalog/product')->load($product->getId());
                    $mediaApi = Mage::getModel("catalog/product_attribute_media_api");
                    try {
                        $id = $_product->getId();
                        $items = $mediaApi->items($id);
                        
                        
                        if(count($items) > 0){
                            foreach($items as $item) {
                                $path = Mage::getBaseDir('media')."/catalog/product".$item['file'];                                
                                $mediaApi->remove($id, $item['file']);
                                echo  "remove product ".$product->getId();
                            }
                        }
                        
                        
                    } catch (Exception $exception){
                        
                        Mage::log($exception->getMessage());
                        echo "Error: ".$id;
                        echo $exception->getMessage();
                        die;
                    }
                }
            }
        }
    }
    
}
