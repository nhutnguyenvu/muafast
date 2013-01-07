<?php
class Luclong_Mixmatch_Block_Mixmatch extends Mage_Core_Block_Template
{	
	
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getMixmatch()     
     { 
        if (!$this->hasData('mixmatch')) {
            $this->setData('mixmatch', Mage::registry('mixmatch'));
        }
        return $this->getData('mixmatch');
        
    }
	public function getCategoryProduct(){
		$categories = Mage::getModel('catalog/category')->getCollection()
	->addAttributeToSelect('name')
	->addFieldToFilter('level',3)
	->addFieldToFilter('is_active',1);

		return $categories;	
	
	}
	public function countproductbycategory($category){
		$collection = Mage::getModel('catalog/product')->getCollection()
					->addCategoryFilter($category)
					->addAttributeToFilter("mixnmatch",array('neq'=>""));
		Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
		return $collection->count(); 
	}
	public function countproductbyvendor($vendor_id){
		$collection = Mage::getModel('catalog/product')->getCollection()
					->addAttributeToFilter("manufacturer",$vendor_id)
					->addAttributeToFilter("mixnmatch",array('neq'=>""));
		Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
		return $collection->count(); 
	}
	public function getTags(){
		$_tags = Mage::getModel('tag/tag')->getCollection()
                  ->addFieldToFilter('status',1) ;
		$_tags->getSelect()->joinRight( array('table_alias'=>Mage::getSingleton('core/resource')->getTableName('tag_summary')), 'main_table.tag_id = table_alias.tag_id AND table_alias.products >0 AND table_alias.store_id = '.Mage::app()->getStore()->getId(), array('table_alias.products'));		  
		return 	$_tags;	  
	}
	public function getThumbnailImageUrl($category){
	
		$url = false;
        if ($image = Mage::getModel('catalog/category')->load($category->getId())->getThumbnail()) {
            $url = Mage::getBaseUrl('media').'catalog/category/'.$image;
        }
        return $url;
	}
	public function getProductbyTag($tasid){
		$collection = Mage::getResourceModel('tag/product_collection')
                    ->addAttributeToSelect('sku')
                    ->addAttributeToSelect('name')
					->addAttributeToFilter("mixnmatch",array('neq'=>""))
                    ->addTagFilter($tasid)->getFirstItem();
				
		return $collection;
	}
	public function getProductCollection(){
		if($tag = $this->getRequest()->getParam('tag')){
			$collection = Mage::getResourceModel('tag/product_collection')
						->addAttributeToSelect("image")
						->addAttributeToSelect("small_image")
						->addTagFilter($tag);
						
			//return 	$collection;		
		}else{
			$collection = Mage::getModel('catalog/product')->getCollection()
				->addAttributeToSelect("image")
				->addAttributeToSelect("small_image");
			if($_categoryid = $this->getRequest()->getParam('cat')){	
			  $category = Mage::getModel('catalog/category')->load($_categoryid);
			  $collection->addCategoryFilter($category);
			}
			if($vendor= $this->getRequest()->getParam('vendor')){	
			  $collection ->addAttributeToFilter('manufacturer',$vendor);
			}
			if($name= $this->getRequest()->getParam('string')){	
				$arr_name = explode(" ", $name);
				$arr_filter= array();
				foreach($arr_name as $item){
					if($item != ""){
					$arr_filter[]=array('attribute'=>'name','like'=>"%$item%");
					}
				}
			  $collection->addAttributeToFilter($arr_filter);
			}
		}
		$collection->addAttributeToFilter("mixnmatch",array('neq'=>""));
		Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
		return $collection;
	}
	public function getLoadedProductCollection(){
        $page_number = $this->getRequest()->getParam('p');
        if(empty($page_number)){
            $page_number = 1;
        }

                $page_element = 16;
				$collection = $this->getProductCollection();
                $collection->setPageSize($page_element);
                $collection->setCurPage($page_number);
            
        return $collection;
    }
	public function getProductcount(){
	   return $this->getProductCollection()->count();
	}
	public function countpage(){
		return intval(ceil($this->getProductcount()/16));
	}
	 public function getAdditionalData($product,array $excludeAttr = array())
    {
        $data = array();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
//            if ($attribute->getIsVisibleOnFront() && $attribute->getIsUserDefined() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
            if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
                $value = $attribute->getFrontend()->getValue($product);

                if (!$product->hasData($attribute->getAttributeCode())) {
                    $value = Mage::helper('catalog')->__('N/A');
                } elseif ((string)$value == '') {
                    $value = Mage::helper('catalog')->__('No');
                } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = Mage::app()->getStore()->convertPrice($value, true);
                }

                if (is_string($value) && strlen($value)) {
                    $data[$attribute->getAttributeCode()] = array(
                        'label' => $attribute->getStoreLabel(),
                        'value' => $value,
                        'code'  => $attribute->getAttributeCode()
                    );
                }
            }
        }
        return $data;
    }
}