<?php

class FavoredMinds_VendorMiniSite_Block_Vendor extends Mage_Catalog_Block_Product_List
{
	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}

	public function getVendor(){
		$name = $this->getRequest()->getParam('name');
		$vendors = Mage::getModel('vendor/vendor')->getCollection();
		$vendors->addFieldToFilter('username', $name);
		return $vendors;
	}


	public function getProduts(){
		$_vendors=$this->getVendor();
		foreach ($_vendors as $_vendor){
			$vendorCode = $_vendor->getVendor_code();
			break;
		}
		$_pro_collection = Mage::getModel('catalog/product')->getCollection();
		$_pro_collection->addAttributeToFilter('manufacturer',$vendorCode);
		$this->prepareProductCollection($_pro_collection);
		return $_pro_collection;
	}

	protected function _beforeToHtml()
	{
		$toolbar = $this->getToolbarBlock();
		$collection = $this->getProduts();
		$toolbar->setCollection($collection);
		$this->setChild('toolbar4vendor', $toolbar);
		$this->getProduts()->load();
		return parent::_beforeToHtml();
	}


	public function getToolbarBlock()
	{
		$block = $this->getLayout()->createBlock('vendorms/toolbars', microtime());
		return $block;
	}



	public function getToolbarHtml()
	{
		return $this->getChildHtml('toolbar4vendor');
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
