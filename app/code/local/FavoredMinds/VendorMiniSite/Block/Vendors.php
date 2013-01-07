<?php
class FavoredMinds_VendorMiniSite_Block_Vendors extends Mage_Core_Block_Template
{
	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}
	

	public function getVendors(){
		$vendors = Mage::getModel('vendor/vendor')->getCollection();
		return $vendors;
	}


	
	protected function _beforeToHtml()
	{
		$toolbar = $this->getToolbarBlock();
		$collection = $this->getVendors();
		$toolbar->setCollection($collection);
		$this->setChild('toolbar', $toolbar);		 
		$this->getVendors()->load();
		return parent::_beforeToHtml();
	}

	
    public function getToolbarBlock()
    {        
        $block = $this->getLayout()->createBlock('vendorms/toolbar', microtime());
        return $block;
    }


    
     public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }
    
}
