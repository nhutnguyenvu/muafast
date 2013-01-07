<?php
class FavoredMinds_VendorMiniSite_Model_Data 
{
   public function getNumOfProduts($vendorCode){
    $_pro_collection = Mage::getModel('catalog/product')->getCollection();   
    $_pro_collection->addAttributeToFilter('manufacturer',$vendorCode);
    $this->prepareProductCollection($_pro_collection);
    return $_pro_collection->count();
   }
   
   public function prepareProductCollection($collection)
    {
        $collection
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents();
            

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        return $this;
    }
}