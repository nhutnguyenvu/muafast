<?php

class NBSSystem_Nitrogento_Block_Adminhtml_Htaccess_Config extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
    	parent::__construct();
        $this->setTemplate("nitrogento/htaccess/config.phtml");
    }
    
    public function getSaveHtaccessConfigUrl()
    {
        return $this->getUrl('*/*/saveHtaccessConfig');
    }
    
    public function isActivatedHtaccessConfig($config)
    {
        switch ($config)
        {
            case ('expire'):
                return Mage::getModel('nitrogento/htaccess_config')->isActivatedHtaccessConfig(NBSSystem_Nitrogento_Model_Htaccess_Config::BEGIN_NITROGENTO_EXPIRE);
            case ('deflate'):
                return Mage::getModel('nitrogento/htaccess_config')->isActivatedHtaccessConfig(NBSSystem_Nitrogento_Model_Htaccess_Config::BEGIN_NITROGENTO_DEFLATE);
            case ('etags'):
                return Mage::getModel('nitrogento/htaccess_config')->isActivatedHtaccessConfig(NBSSystem_Nitrogento_Model_Htaccess_Config::BEGIN_NITROGENTO_ETAGS);
            default:
                return false;
        }
    }
    
    protected function _prepareLayout()
    {
        $this->setChild('config_time_expire',
            $this->getLayout()->createBlock('nitrogento/adminhtml_htaccess_config_time_expire')
        );
        return parent::_prepareLayout();
    }
}