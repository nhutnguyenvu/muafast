<?php

class NBSSystem_Nitrogento_Adminhtml_Cache_Blockhtml_ConfigController extends NBSSystem_Nitrogento_Controller_Adminhtml_Action
{
    public function indexAction()
    {
        if (!Mage::helper('nitrogento')->isCacheBlockhtmlEnabled())
        {
            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('nitrogento')->__('
                Beware, this config needs Blockhtml Cache activated here : 
                <a href="' . $this->getUrl('*/cache/index') . '">Activate Blockhtml Cache</a> to work'));
        }
        
        $this->loadLayout();
        $this->_setActiveMenu('system/nitrogento');
        $this->_addContent($this->getLayout()->createBlock('nitrogento/adminhtml_cache_blockhtml_config'));
        
        if (Mage::getStoreConfig('nitrogento/cache_blockhtml/learning_mode'))
        {
            $this->_addContent($this->getLayout()->createBlock('nitrogento/adminhtml_cache_blockhtml_learning'));
        }
        
        $this->_addContent($this->getLayout()->createBlock('nitrogento/adminhtml_cache_blockhtml_cleaner'));
        $this->renderLayout();
    }
    
    public function editAction()
    {
        $cacheBlockhtmlConfigId  = $this->getRequest()->getParam('config_id');
        $cacheBlockhtmlConfig  = Mage::getModel('nitrogento/cache_blockhtml_config')->load($cacheBlockhtmlConfigId);
        
        if (!$cacheBlockhtmlConfigId)
        {
            $cacheBlockhtmlConfig->populateDefaultInfos();
        }
        
        if ($cacheBlockhtmlConfig->getId() || $cacheBlockhtmlConfigId == 0)
        {
            Mage::register('cache_blockhtml_config', $cacheBlockhtmlConfig);
            $this->loadLayout();
            $this->_setActiveMenu('system/cache');
            $this->_addContent($this->getLayout()->createBlock('nitrogento/adminhtml_cache_blockhtml_config_edit'))
                 ->_addLeft($this->getLayout()->createBlock('nitrogento/adminhtml_cache_blockhtml_config_edit_tabs'));
            $this->renderLayout();
        }
        else
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('nitrogento')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
    
    public function saveAction()
    {
        try
        {
            $postData = $this->getRequest()->getPost();
            
            Mage::getModel('nitrogento/cache_blockhtml_config')->setId($this->getRequest()->getParam('config_id'))
                ->setNonNullDatas($postData)
                ->setCacheLifetime($postData['cache_lifetime'])
                ->setActivated(isset($postData['activated']))
                ->save();
            
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nitrogento')->__('Item was successfully saved'));
            
            $this->_redirect('*/*/');
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('config_id' => $this->getRequest()->getParam('config_id')));
        }
    }
    
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('config_id') > 0)
        {
            try
            {
                Mage::getModel('nitrogento/cache_blockhtml_config')->setId($this->getRequest()->getParam('config_id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nitrogento')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            }
            catch (Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('config_id' => $this->getRequest()->getParam('config_id')));
            }
        }
    }
    
    public function cleanCacheBlockhtmlObjectsAction()
    {
        try 
        {
            Mage::app()->cleanCache(NBSSystem_Nitrogento_Helper_Data::CACHE_BLOCKHTML_OBJECTS);
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nitrogento')->__('Cache Blockhtml Objects were successfully deleted'));
        }
        catch (Exception $e) 
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        
        $this->_redirect('*/*/');
    }
    
    public function massEnableAction()
    {
        try
        {
            $postData = $this->getRequest()->getPost();
            
            foreach ($postData["config_ids"] as $cacheBlockhtmlConfigId)
            {
                Mage::getModel('nitrogento/cache_blockhtml_config')->load($cacheBlockhtmlConfigId)->setActivated(1)->save();
            }
            
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nitrogento')->__('Cache Blockhtml was successfully enabled'));
            $this->_redirect('*/*/');
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/', $this->getRequest()->getPost());
        }
    }
    
    public function massDisableAction()
    {
        try
        {
            $postData = $this->getRequest()->getPost();
            
            foreach ($postData["config_ids"] as $cacheBlockhtmlConfigId)
            {
                $cacheBlockhtmlConfig = Mage::getModel('nitrogento/cache_blockhtml_config');
                $cacheBlockhtmlConfig->load($cacheBlockhtmlConfigId)->setActivated(0)->save();
                Mage::app()->cleanCache($cacheBlockhtmlConfig->getBlockClass());
            }
            
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nitrogento')->__('Cache Blockhtml was successfully disabled'));
            $this->_redirect('*/*/');
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/', $this->getRequest()->getPost());
        }
    }
    
    public function massDeleteAction()
    {
        try
        {
            $postData = $this->getRequest()->getPost();
            
            foreach ($postData["config_ids"] as $cacheBlockhtmlConfigId)
            {
                $cacheBlockhtmlConfig = Mage::getModel('nitrogento/cache_blockhtml_config');
                $cacheBlockhtmlConfig->load($cacheBlockhtmlConfigId);
                Mage::app()->cleanCache($cacheBlockhtmlConfig->getBlockClass());
                $cacheBlockhtmlConfig->delete();
            }
            
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nitrogento')->__('Cache Blockhtml was successfully deleted'));
            $this->_redirect('*/*/');
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/', $this->getRequest()->getPost());
        }
    }
    
    public function massRefreshAction()
    {
        try
        {
            $postData = $this->getRequest()->getPost();
            
            foreach ($postData["config_ids"] as $cacheBlockhtmlConfigId)
            {
                Mage::app()->cleanCache(Mage::getModel('nitrogento/cache_blockhtml_config')->load($cacheBlockhtmlConfigId)->getBlockClass());
            }
            
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nitrogento')->__('Cache config Item(s) was successfully refreshed'));
            $this->_redirect('*/*/');
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/', $this->getRequest()->getPost());
        }
    }
    
    public function massPutInStaticCacheConfigAction()
    {
        $this->massPutInCacheConfig("static");
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nitrogento')->__('Learning Blocks were successfully set in Cache Config'));
        $this->_redirect('*/*/');
    }
    
    public function massPutInDynamicCacheConfigAction()
    {
        $this->massPutInCacheConfig("dynamic");
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nitrogento')->__('Learning Blocks were successfully set in Cache Config'));
        $this->_redirect('*/*/');
    }
    
    private function massPutInCacheConfig($mode)
    {
        $postData = $this->getRequest()->getPost();
        
        foreach ($postData["learning_ids"] as $cacheBlockhtmlLearningId)
        {
            $cacheBlockhtmlLearning = Mage::getSingleton('nitrogento/cache_blockhtml_learning')->load($cacheBlockhtmlLearningId);
            $cacheBlockhtmlConfig = Mage::getModel('nitrogento/cache_blockhtml_config');
            
            $cacheBlockhtmlConfig->setBlockClass($cacheBlockhtmlLearning->getBlockClass())
                ->setBlockTemplate($cacheBlockhtmlLearning->getBlockTemplate())
                ->setStoreId(array($cacheBlockhtmlLearning->getStoreId()))
                ->setHelperClass('nitrogento/cache_blockhtml_generic_' . $mode . '_impl')
                ->setActivated(0)
                ->save();
        }
    }
}