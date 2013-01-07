<?php

class NBSSystem_Nitrogento_Block_Adminhtml_Cache_Fullpage_Cleaner extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate("nitrogento/cache/fullpage/cleaner.phtml");
    }
    
    public function getCleanCacheFullpageObjectsUrl()
    {
        return $this->getUrl('*/*/cleanCacheFullpageObjects');
    }
}