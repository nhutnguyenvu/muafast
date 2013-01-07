<?php

class NBSSystem_Nitrogento_Model_Cache_Observer
{
    public function cleanNitrogentoCache($observer)
    {
        if (in_array(Mage::app()->getRequest()->getParam('section'), array('general', 'web', 'design', 'currency', 'nitrogento')))
        {
            Mage::app()->cleanCache(
                NBSSystem_Nitrogento_Helper_Data::CACHE_FULLPAGE_OBJECTS, 
                NBSSystem_Nitrogento_Helper_Data::CACHE_BLOCKHTML_OBJECTS, 
                Mage_Core_Model_Store::CACHE_TAG, 
                Mage_Cms_Model_Block::CACHE_TAG
            );
        }
    }
    
    public function cleanOldCacheEntries()
    {
        Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_OLD);
    }
    
    public function cleanCmsPageCache($observer)
    {
        if (version_compare(Mage::getVersion(), '1.4.0', '<'))
        {
            Mage::app()->cleanCache(array('cms_page'));
        }
    }
    
    public function cleanCategoryCache($observer)
    {
        if (version_compare(Mage::getVersion(), '1.4.0', '<'))
        {
            $category = $observer->getEvent()->getDataObject();
            Mage::app()->cleanCache(Mage_Catalog_Model_Category::CACHE_TAG . "_" . $category->getId());
        }
    }
    
    public function cleanProductCategoriesCache($observer)
    {
        if (version_compare(Mage::getVersion(), '1.4.0', '<'))
        {
            $product = $observer->getEvent()->getDataObject();
            $this->_cleanProductsCategoriesCache(array($product->getId()));
        }
    }
    
    public function cleanStockItemCache($observer)
    {
        $item = $observer->getEvent()->getItem();
        
        $currentContext = new Varien_Object(array('item' => $item, 'can_clean_stock_item_cache' => true));
        NBSSystem_Nitrogento_Main::getInstance()->dispatchEvent('nitrogento_before_clean_stock_item_cache', array('data_object' => $currentContext));
        
        if (!$currentContext->getCanCleanStockItemCache() || Mage::registry('disable_clean_stock_item_cache') === true)
        {
            return;
        }
        
        if ($item->getStockStatusChangedAutomaticallyFlag() || $item->dataHasChangedFor('is_in_stock'))
        {
            $this->_cleanProductsCategoriesCache(array($item->getProductId()));
        }
    }
    
    public function cleanOrderItemsCache($observer)
    {
        $order = $observer->getEvent()->getOrder();
        if (Mage::getStoreConfig('nitrogento/cache/clean_cache_each_order'))
        {
            $productsIds = array();
            foreach ($order->getAllItems() as $item)
            {
                $productsIds[] = $item->getProductId();
            }
            
            $this->_cleanProductsCategoriesCache($productsIds);
        }
    }
    
    protected function _cleanProductsCategoriesCache($productsIds)
    {
        if (empty($productsIds))
        {
            return;
        }
        
        $cacheTagsToDelete = array();
        foreach ($productsIds as $productId)
        {
            $cacheTagsToDelete[] = Mage_Catalog_Model_Product::CACHE_TAG . "_" . $productId;
        }
               
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $select = $read->select()->from($resource->getTableName('catalog_category_product'), 'category_id')->where('product_id IN (?)', $productsIds)->group('category_id');
        
        $categoriesIds = $read->fetchCol($select);
        foreach ($categoriesIds as $categoryId)
        {
            $cacheTagsToDelete[] = Mage_Catalog_Model_Category::CACHE_TAG . "_" . $categoryId;
        }
        
        Mage::app()->cleanCache($cacheTagsToDelete);
    }
    
    public function validateLicenceKey($observer)
    {
        if (Mage::app()->getRequest()->getParam('section') == 'nitrogento')
        {
            Mage::helper('nitrogento')->validateLicenceKey();
        }
    }
    
    public function addProductDeleteCacheMassAction($observer)
    {
        // Retrieve current block
        $currentBlock = $observer->getBlock();
        if ($currentBlock instanceof Mage_Adminhtml_Block_Catalog_Product_Grid)
        {
            $currentBlock->getMassactionBlock()->addItem('cleanCache', array(
                'label' => Mage::helper('nitrogento')->__('Clean Product Cache'),
                'url'   => $currentBlock->getUrl('*/catalog_cleaner/massCleanProductCache')
            ));
        }
    }
    
    public function addCategoryDeleteCacheAction($observer)
    {
        // Retrieve current block
        $currentBlock = $observer->getBlock();
        if ($currentBlock instanceof Mage_Adminhtml_Block_Catalog_Category_Edit_Form)
        {
            $currentBlock->addAdditionalButton("cleanerCategoryCache", array(
                'label'     => Mage::helper('catalog')->__('Clean Category Cache'),
                'onclick'   => "cleanCategoryCache('" . $currentBlock->getUrl('*/catalog_cleaner/cleanCategoryCache', array('_current'=>true)). "')",
            ));
            
            $currentBlock->getLayout()->getBlock('js')->append($currentBlock->getLayout()->createBlock('nitrogento/adminhtml_cache_common_category_cleaner'));
        }
    }
    
    public function disableCatalogSessionMemorizeParams()
    {
        Mage::getSingleton('catalog/session')->setParamsMemorizeDisabled(true);
    }
}