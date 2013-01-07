<?php

class NBSSystem_Nitrogento_Block_Adminhtml_Htaccess_Config_Time_Expire extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate("nitrogento/htaccess/config.time.expire.phtml");
    }
    
    public function getTimeExpireOptions()
    {
        return array('0 seconds' => $this->__('0 seconds'), '2 hours' => $this->__('2 hours'), '1 day' => $this->__('1 day'), '1 week' => $this->__('1 week'), '1 month' => $this->__('1 month'), '1 year' => $this->__('1 year'));
    }
    
    public function getTimeExpireConfigs()
    {
        return Mage::getModel('nitrogento/htaccess_config')->getTimeExpireConfigs();
    }
}