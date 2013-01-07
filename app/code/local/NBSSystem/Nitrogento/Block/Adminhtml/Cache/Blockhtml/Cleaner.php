<?php

class NBSSystem_Nitrogento_Block_Adminhtml_Cache_Blockhtml_Cleaner extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate("nitrogento/cache/blockhtml/cleaner.phtml");
    }
    
    public function getCleanCacheBlockhtmlObjectsUrl()
    {
        return $this->getUrl('*/*/cleanCacheBlockhtmlObjects');
    }
}