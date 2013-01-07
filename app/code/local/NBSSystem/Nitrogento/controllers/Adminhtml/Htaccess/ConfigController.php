<?php

class NBSSystem_Nitrogento_Adminhtml_Htaccess_ConfigController extends NBSSystem_Nitrogento_Controller_Adminhtml_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/nitrogento');
        $this->_addContent($this->getLayout()->createBlock('nitrogento/adminhtml_htaccess_config'));
        $this->renderLayout();
    }
    
    public function saveHtaccessConfigAction()
    {
        $postData = $this->getRequest()->getPost();
        
        try
        {
            if (isset($postData['activate']) && isset($postData['activate']['deflate']))
            {
                Mage::getModel('nitrogento/htaccess_config')->activateHtaccessConfig(NBSSystem_Nitrogento_Model_Htaccess_Config::BEGIN_NITROGENTO_DEFLATE);
            }
            else
            {
                Mage::getModel('nitrogento/htaccess_config')->desactivateHtaccessConfig(NBSSystem_Nitrogento_Model_Htaccess_Config::BEGIN_NITROGENTO_DEFLATE);
            }
            
            if (isset($postData['activate']) && isset($postData['activate']['expire']))
            {
                Mage::getModel('nitrogento/htaccess_config')->desactivateHtaccessConfig(NBSSystem_Nitrogento_Model_Htaccess_Config::BEGIN_NITROGENTO_EXPIRE);
                Mage::getModel('nitrogento/htaccess_config')->activateHtaccessConfig(NBSSystem_Nitrogento_Model_Htaccess_Config::BEGIN_NITROGENTO_EXPIRE, $postData['time_expire_configs']);
            }
            else
            {
                Mage::getModel('nitrogento/htaccess_config')->desactivateHtaccessConfig(NBSSystem_Nitrogento_Model_Htaccess_Config::BEGIN_NITROGENTO_EXPIRE);
            }
            
            if (isset($postData['activate']) && isset($postData['activate']['etags']))
            {
                Mage::getModel('nitrogento/htaccess_config')->activateHtaccessConfig(NBSSystem_Nitrogento_Model_Htaccess_Config::BEGIN_NITROGENTO_ETAGS);
            }
            else
            {
                Mage::getModel('nitrogento/htaccess_config')->desactivateHtaccessConfig(NBSSystem_Nitrogento_Model_Htaccess_Config::BEGIN_NITROGENTO_ETAGS);
            }
            
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nitrogento')->__('Htaccess Config successfully saved !!'));
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        
        $this->_redirect('*/*/index');
    }
}