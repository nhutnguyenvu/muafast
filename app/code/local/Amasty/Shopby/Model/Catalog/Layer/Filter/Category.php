<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */
class Amasty_Shopby_Model_Catalog_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Category
{

	/**
     * Get data array for building category filter items
     *
     * @return array
     */
	    public function getCategory()
    {
     
			return $this->getLayer()->getCurrentCategory();
		
    }
	  protected function _getItemsData()
    {		
			$key = $this->getLayer()->getStateKey().'_SUBCATEGORIES';
			$data = $this->getLayer()->getAggregator()->getCacheData($key);
			$rootid  = $this->getLayer()->getCurrentStore()->getRootCategoryId();
			$vendor_id = Mage::app()->getRequest()->getParam("vendor_id");
			if($rootid == $this->getCategory()->getId()|| $vendor_id ||  Mage::app()->getRequest()->getParam("sp") ){
			
				$root =  Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($rootid);
				
				$categories = $root->getChildrenCategories();
				
				$this->getLayer()->getProductCollection()
						->addCountToCategories($categories);

				$data = array();
				$category_old =array();
			
				foreach ($categories as $category) {
				
					   $children = $category->getChildrenCategories();
					   if ($children && count($children)){

						   $this->getLayer()->getProductCollection()->addCountToCategories($children);
						   foreach ($children as $child){
								if($this->_categoryId==$child->getId()){$value = "";$cat="";}else{$value =$child->getId();$cat="&cat=".$value;}
								
									$is_ajax = Mage::app()->getRequest()->getParam("is_ajax");
									$data_old = Mage::getSingleton('core/session')->getDataCategory(); 
									if ($child->getIsActive() && (($is_ajax!=1&&$child->getProductCount())||($is_ajax==1&&in_array($child->getId(),$data_old)))) {
									if($vendor_id){
										$data[] = array(
											'label' => Mage::helper('core')->htmlEscape($child->getName()),
											'value' => $value,
											'count' => $child->getProductCount(),
											'url'=>str_replace("??","?",Mage::getUrl('shopby')."?vendor_id=".$vendor_id.$cat),
										);
										}else {
											$data[] = array(
												'label' => Mage::helper('core')->htmlEscape($child->getName()),
												'value' => $value,
												'count' => $child->getProductCount(),
												'url'=>str_replace("??","?",Mage::getUrl('shopby')."?sp=true".$cat),
											);
										
										}
										$category_old[]=$child->getId();
								}
						
						   }
					   }
					
				}
				
				Mage::getSingleton('core/session')->setDataCategory($category_old); 
				
			}else{
				

				if ($data === null) {
					$categoty   = $this->getCategory();
					/** @var $categoty Mage_Catalog_Model_Categeory */
					$categories = $categoty->getChildrenCategories();

					$this->getLayer()->getProductCollection()
						->addCountToCategories($categories);

					$data = array();
					$category_old =array();
				
					foreach ($categories as $category) {
						if($this->_categoryId==$category->getId()){
					 	   $value = "";
						   $cat="";
						}else{
						   $value =$category->getId();
						     $cat="?cat=".$value;
						}
						$is_ajax = Mage::app()->getRequest()->getParam("is_ajax");
						$data_old = Mage::getSingleton('core/session')->getDataCategory(); 
						if ($category->getIsActive() && (($is_ajax!=1&&$category->getProductCount())||($is_ajax==1&&in_array($category->getId(),$data_old))||(Mage::app()->getRequest()->getParam("p")&&in_array($category->getId(),$data_old) ))) {
							$data[] = array(
								'label' => Mage::helper('core')->htmlEscape($category->getName()),
								'value' => $value,
								'count' => $category->getProductCount(),
								'url'   =>str_replace("??","?",$this->getCategory()->getUrl().$cat),
							);
							$category_old[]=$category->getId();
						}
					}
				
					Mage::getSingleton('core/session')->setDataCategory($category_old); 
				}
				
			}
			
					$tags = $this->getLayer()->getStateTags();
					$this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
		return $data; 
    }
	 protected function _initItems()
    {
        if ('catalogsearch' == Mage::app()->getRequest()->getModuleName())
            return parent::_initItems();

        $data  = $this->_getItemsData();
        $items = array();
        foreach ($data as $itemData) {
            if (!$itemData)
                continue;

            $obj = new Varien_Object();
            $obj->setData($itemData);
             $obj->setUrl($itemData['url']);

            $items[] = $obj;
        }
        $this->_items = $items;
        return $this;
    }
 
    /**
     * Prepare category row
     * @param Mage_Catalog_Model_Category $category
     * @param unknown_type $id
     * @param unknown_type $isSelected
     * @param unknown_type $level
     * @param unknown_type $isFolded
     * @return array
     */
  
}