<?php

class NBSSystem_Nitrogento_Adminhtml_Catalog_CleanerController extends NBSSystem_Nitrogento_Controller_Adminhtml_Action
{
    public function massCleanProductCacheAction()
    {
        $postData = $this->getRequest()->getPost();
        $toDeleteTags = array();
        
        foreach ($postData["entity_id"] as $productId)
        {
            $toDeleteTags[] = Mage_Catalog_Model_Product::CACHE_TAG  . "_" . $productId;
        }
        
        if (!empty($toDeleteTags))
        {
            Mage::app()->cleanCache($toDeleteTags);
        }
        
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nitrogento')->__('Cache successfully cleaned'));
        $this->_redirect('*/catalog_product/index');
    }
    
    public function cleanCategoryCacheAction()
    {
        $categoryId = (int) $this->getRequest()->getParam('id',false);
        
        Mage::app()->cleanCache(Mage_Catalog_Model_Category::CACHE_TAG . "_" . $categoryId);
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nitrogento')->__('Cache successfully cleaned'));
        
        $url = $this->getUrl('*/catalog_category/edit', array('_current' => true, 'id' => $categoryId));
        
        $this->getResponse()->setBody(
            '<script type="text/javascript">parent.updateContent("' . $url . '", {}, false);</script>');
    }
}