<?php
/**
* @copyright Amasty.
*/ 
class Amasty_Shopby_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		/*if($this->getRequest()->getParam('isAjax')==1&&$this->getRequest()->getParam('vendor_id')){
		 $html['page'] = $this->getRequest()->getParam('p');
		 $html['product'] = $this->getLayout()->createBlock('vendor/vendor_renderer_vendor')->setTemplate('vendor/productlist_scoll.phtml')->toHtml();
		 $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($html));
		 return true;
		 }else
				if($this->getRequest()->getParam('isAjax')==1){
		
					 $html['page'] = $this->getRequest()->getParam('p');
					 $html['product'] = $this->getLayout()->createBlock('catalog/product_list')->setTemplate('catalog/product/list_scoll.phtml')->toHtml();
					 $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($html));
					 return true;
					 }
		 
		 }*/
        // init category
		if($categoryId = Mage::app()->getRequest()->getParam("cat")){
		 
		 }else{
			$categoryId = (int) Mage::app()->getStore()->getRootCategoryId();
			if (!$categoryId) {
				$this->_forward('noRoute'); 
				return;
			}
		}
        $category = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($categoryId);
            
        Mage::register('current_category', $category); 
        Mage::getSingleton('catalog/session')->setLastVisitedCategoryId($category->getId());  
		
		
        // need to prepare layer params
        try {
            Mage::dispatchEvent('catalog_controller_category_init_after', 
                array('category' => $category, 'controller_action' => $this));
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return;
        } 
		// by khanh.phan
          	if($this->getRequest()->getParam('isAjax')==1){
			  $this->loadLayout();
	          Mage::getSingleton('catalogsearch/advanced')->addFilters($this->getRequest()->getQuery());
			  //$response = $this->getLayout()->getBlock('search_result_list')->toHtml();
					 $html['page'] = $this->getRequest()->getParam('p');
					 $html['product'] = $this->getLayout()->getBlock('search_result_list')->toHtml();
					 $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($html));
					 return true;
					 }
        // observer can change value
        if (!$category->getId()){
            $this->_forward('noRoute'); 
            return;
        }     
            
        $this->loadLayout();
        $this->renderLayout();
    }

}