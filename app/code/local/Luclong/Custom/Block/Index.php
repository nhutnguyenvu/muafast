<?php   
class Luclong_Custom_Block_Index extends Mage_Catalog_Block_Product_List{
    
	
    public function getSpecialProductList(){

        
        $limit = Mage::getStoreConfig('catalog/frontend/grid_per_page');
		$page = $this->getRequest()->getParam('p');
        if(empty($page)){
            $page = 1;
        }
		
        $dateToday = date('m/d/y');
        $tomorrow = mktime(0, 0, 0, date('m'), date('d')+1, date('y'));
        $dateTomorrow = date('m/d/y', $tomorrow);
        
        $_productCollection = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('*') // select all attributes
                ->addAttributeToFilter('status',1) // select all attributes
                ->addAttributeToFilter('visibility',Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) // select all attributes
                ->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $dateToday))
                ->addAttributeToFilter('special_to_date', array('or'=> array(
                    0 => array('date' => true, 'from' => $dateTomorrow),
                    1 => array('is' => new Zend_Db_Expr('null')))
                ), 'left')
				->setOrder('entity_id','DESC');
        $_productCollection->setPageSize($limit);
        $_productCollection->setCurPage($page);
        
        return $_productCollection;
    }
     public function getSpecialProductCountpage(){

          $limit = Mage::getStoreConfig('catalog/frontend/grid_per_page');
        
        $dateToday = date('m/d/y');
        $tomorrow = mktime(0, 0, 0, date('m'), date('d')+1, date('y'));
        $dateTomorrow = date('m/d/y', $tomorrow);
        
        $_productCollection = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('*') // select all attributes
                ->addAttributeToFilter('status',1) // select all attributes
                ->addAttributeToFilter('visibility',Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) // select all attributes
                ->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $dateToday))
                ->addAttributeToFilter('special_to_date', array('or'=> array(
                    0 => array('date' => true, 'from' => $dateTomorrow),
                    1 => array('is' => new Zend_Db_Expr('null')))
                ), 'left');
      
        
        return ceil($_productCollection->count()/$limit);
    }
}
