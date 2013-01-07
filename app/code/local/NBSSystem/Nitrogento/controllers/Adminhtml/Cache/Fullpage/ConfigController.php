<?php

class NBSSystem_Nitrogento_Adminhtml_Cache_Fullpage_ConfigController extends NBSSystem_Nitrogento_Controller_Adminhtml_Action
{
    public function indexAction()
    {
        if (!Mage::helper('nitrogento')->isCacheFullpageEnabled())
        {
            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('nitrogento')->__('
                Beware, this config needs Fullpage Cache activated here : 
                <a href="' . $this->getUrl('*/cache/index') . '">Activate Fullpage Cache</a> to work'));
        }
        
        $this->loadLayout();
        $this->_setActiveMenu('system/nitrogento');
        $this->_addContent($this->getLayout()->createBlock('nitrogento/adminhtml_cache_fullpage_config'));
        $this->_addContent($this->getLayout()->createBlock('nitrogento/adminhtml_cache_fullpage_cleaner'));
        $this->renderLayout();
    }
    
    public function editAction()
    {
        $cacheFullpageConfigId  = $this->getRequest()->getParam('config_id');
        $cacheFullpageConfig  = Mage::getModel('nitrogento/cache_fullpage_config')->load($cacheFullpageConfigId);
        
        if ($cacheFullpageConfig->getId() || $cacheFullpageConfigId == 0)
        {
            Mage::register('cache_fullpage_config', $cacheFullpageConfig);
            $this->loadLayout();
            $this->_setActiveMenu('system/cache');
            $this->_addContent($this->getLayout()->createBlock('nitrogento/adminhtml_cache_fullpage_config_edit'))
                 ->_addLeft($this->getLayout()->createBlock('nitrogento/adminhtml_cache_fullpage_config_edit_tabs'));
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
            
            Mage::getModel('nitrogento/cache_fullpage_config')->setId($this->getRequest()->getParam('config_id'))
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
                Mage::getModel('nitrogento/cache_fullpage_config')->setId($this->getRequest()->getParam('config_id'))->delete();
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
    
    public function cleanCacheFullpageObjectsAction()
    {
        try
        {
            Mage::app()->cleanCache(NBSSystem_Nitrogento_Helper_Data::CACHE_FULLPAGE_OBJECTS);
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nitrogento')->__('Cache Fullpage Objects were successfully deleted'));
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
            
            foreach ($postData["config_ids"] as $cacheFullpageConfigId)
            {
                Mage::getModel('nitrogento/cache_fullpage_config')->load($cacheFullpageConfigId)->setActivated(1)->save();
            }
            
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nitrogento')->__('Cache Fullpage was successfully enabled'));
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
            
            foreach ($postData["config_ids"] as $cacheFullpageConfigId)
            {
                $cacheFullpageConfig = Mage::getModel('nitrogento/cache_fullpage_config');
                $cacheFullpageConfig->load($cacheFullpageConfigId)->setActivated(0)->save();
                Mage::app()->cleanCache($cacheFullpageConfig->getFullActionName());
            }
            
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nitrogento')->__('Cache Fullpage was successfully disabled'));
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
            
            foreach ($postData["config_ids"] as $cacheFullpageConfigId)
            {
                $cacheFullpageConfig = Mage::getModel('nitrogento/cache_fullpage_config');
                $cacheFullpageConfig->load($cacheFullpageConfigId);
                Mage::app()->cleanCache($cacheFullpageConfig->getFullActionName());
                $cacheFullpageConfig->delete();
            }
            
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nitrogento')->__('Cache Fullpage was successfully deleted'));
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
            
            foreach ($postData["config_ids"] as $cacheFullpageConfigId)
            {
                Mage::app()->cleanCache(Mage::getModel('nitrogento/cache_fullpage_config')->load($cacheFullpageConfigId)->getFullActionName());
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
    
    public function activateIndexphpAction()
    {
        $indexPhpPath = BP . DS . 'index.php';
        $indexPhpContent = file_get_contents($indexPhpPath);
        
        if (!preg_match('/NBSSystem_Nitrogento_Main/', $indexPhpContent))
        {
            $indexPhpContent = str_replace(
                "Mage::run(",
                "(file_exists(BP . DS . 'app' . DS . 'code' . DS . 'local' . DS . 'NBSSystem' . DS . 'Nitrogento' . DS . 'Main.php')) ? NBSSystem_Nitrogento_Main::init()->renderPage() : false;" . PHP_EOL . PHP_EOL . "Mage::run(", 
                $indexPhpContent);
            file_put_contents($indexPhpPath, $indexPhpContent);
        }
        
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nitrogento')->__('Index php has been activated !!'));
        $this->_redirect('adminhtml/system_config/edit', array('section' => 'nitrogento'));
    }
}