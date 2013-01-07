<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Model_Observer {

  /**
   * Set logged in vendor for new products
   *
   * @param Varien_Object $observer
   */
  public function catalog_product_new_action($observer) {
    //add the maximum limit of the products
    

    
    $resource = Mage::getSingleton('core/resource');
    $read = $resource->getConnection('catalog_read');
    $catalog_product_entity_int = $resource->getTableName("catalog_product_entity_int");
    $eav_attribute = $resource->getTableName('eav_attribute');
    
    $helper 		= Mage::app()->getHelper('vendor');
    $manufacturer	= $helper->getManufacturerOption($helper->getVendorUserId());
    $manufacturer = $manufacturer['value'];
    
    
    //if this is a vendor lock the manufacturer attribute onloy to that vendor
    
    if($helper->getVendorUserId())
    {
    $event=$observer->getEvent();
    $event->getProduct()->setManufacturer($manufacturer);
    $event->getProduct()->lockAttribute("manufacturer");
    }
    
    
    $result		= $read->query("select count(*) as total from $catalog_product_entity_int cpei, $eav_attribute ea where ea.attribute_code = 'manufacturer' and ea.attribute_id = cpei.attribute_id and cpei.value ='$manufacturer';");
    $products		= $result->fetch();
    $limit = Mage::getStoreConfig('vendor/general/vendorproductlimit');
    if ((int)$limit > 0) {
      if ($limit <= $products['total']) {
        //add the error message
        Mage::getSingleton('adminhtml/session')->addError($helper->__("You have reached the maximum products number"));

        //redirect to the listing page
        $response = Mage::app()->getResponse()
                ->setHeader("Location", Mage::helper('adminhtml')->getUrl("*/*/"))
                ->sendHeaders();
      }
    }
  }

  /**
   * Set logged in vendor for new products
   *
   * @param Varien_Object $observer
   */
  public function catalog_product_save_before($observer) {
    //add the maximum limit of the products
    $resource = Mage::getSingleton('core/resource');
    $read = $resource->getConnection('catalog_read');
    $catalog_product_entity_int = $resource->getTableName("catalog_product_entity_int");
    $eav_attribute = $resource->getTableName('eav_attribute');

    $helper 		= Mage::app()->getHelper('vendor');
    $manufacturer	= $helper->getManufacturerOption($helper->getVendorUserId());
    $manufacturer = $manufacturer['value'];

    $result		= $read->query("select count(*) as total from $catalog_product_entity_int cpei, $eav_attribute ea where ea.attribute_code = 'manufacturer' and ea.attribute_id = cpei.attribute_id and cpei.value ='$manufacturer';");
    $products		= $result->fetch();
    $limit = Mage::getStoreConfig('vendor/general/vendorproductlimit');
    if ((int)$limit > 0) {
      if ($limit <= $products['total']) {
        //add the error message
        Mage::getSingleton('adminhtml/session')->addError($helper->__("You have reached the maximum products number"));

        //redirect to the listing page
        $response = Mage::app()->getResponse()
                ->setHeader("Location", Mage::helper('adminhtml')->getUrl("*/*/"))
                ->sendHeaders();
      }
    }
    $helper 		= Mage::app()->getHelper('vendor');
    if ($helper->vendorIsLogged()) {
      $product = $observer->getEvent()->getProduct();
      $vendor = $helper->getVendorUserInfo($helper->getVendorUserId());
      $product->setManufacturer($vendor['vendor_code']);
      //check if we need to set the SKU prefix
      if (Mage::getStoreConfig('vendor/general/sku_prefix') == 1) {
        //check the sku
        $sku = $product->getSKU();
        if ((string)$vendor['sku_prefix'] != ''){
            if (strpos($sku, $vendor['sku_prefix']) === false){
              //we surely add sku prefix
              $product->setSku($vendor['sku_prefix'] . '_' .$sku);
            } else {
              if (strpos($sku, $vendor['sku_prefix']) > 0){
                //we add it, since the substring is not at the beginning
                $product->setSku($vendor['sku_prefix'] . '_' .$sku);
              }
            }
        }
      }
      //set the status to disable
      if (Mage::getStoreConfig('vendor/general/autoapproveproducts') == 1) {
        $product->setStatus(1);
      } else {
        $product->setStatus(2);
      }
    }
  }

  public function catalog_product_collection_load_before($observer) {
    //add the maximum limit of the products
    $resource = Mage::getSingleton('core/resource');
    $read = $resource->getConnection('catalog_read');
    $catalog_product_entity_int = $resource->getTableName("catalog_product_entity_int");
    $eav_attribute = $resource->getTableName('eav_attribute');

    $helper 		= Mage::app()->getHelper('vendor');
    $manufacturer	= $helper->getManufacturerOption($helper->getVendorUserId());
    $manufacturer = $manufacturer['value'];

    $result		= $read->query("select count(*) as total from $catalog_product_entity_int cpei, $eav_attribute ea where ea.attribute_code = 'manufacturer' and ea.attribute_id = cpei.attribute_id and cpei.value ='$manufacturer';");
    $products		= $result->fetch();
    $limit = Mage::getStoreConfig('vendor/general/vendorproductlimit');
    if ((int)$limit > 0) {
      if ($limit <= $products['total']) {
        //add the error message
        Mage::getSingleton('adminhtml/session')->addError($helper->__("You have reached the maximum products number"));
      }
    }

    $observer->getEvent()->getCollection()->addAttributeToSelect('manufacturer');
    $helper 		= Mage::app()->getHelper('vendor');
    if ($helper->vendorIsLogged()) {
      //we filter the output by vendor
      $vendor = $helper->getVendorUserInfo($helper->getVendorUserId());
      $observer->getEvent()->getCollection()->addAttributeToFilter('manufacturer', $vendor['vendor_code']);
    }
  }

  /**
   * Update stock status for product collection if augmented stock status is used
   *
   * @param mixed $observer
   */
  public function catalog_product_collection_load_after($observer) {
  }

}
?>
