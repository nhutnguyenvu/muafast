<?php

class NBSSystem_Nitrogento_Model_Optimization_Observer
{
    public function observeHtml($observer)
    {
        $controllerAction = $observer->getEvent()->getControllerAction();
        $bodyHtml = $controllerAction->getResponse()->getBody();
        
        $modelCdn = Mage::getModel('nitrogento/optimization_cdn');
        
        if(!empty($bodyHtml) && Mage::getStoreConfig('nitrogento/optimization_cdn/enabled'))
        {
            $bodyHtml = $modelCdn->cdnHtml($bodyHtml);
        }
        
        $controllerAction->getResponse()->clearBody();
        $controllerAction->getResponse()->appendBody($bodyHtml);
    }
    
    public function addSpriteCss($observer)
    {
        $design = Mage::getDesign();
        
        if (file_exists($design->getSkinBaseDir() . DS . 'css' . DS . 'sprite.css')
         || file_exists($design->getSkinBaseDir(array('_theme' => 'default')) . DS . 'css' . DS . 'sprite.css'))
        {
            $observer->getEvent()->getLayout()->getUpdate()->addHandle('add_sprite_css');
        }
    }
}