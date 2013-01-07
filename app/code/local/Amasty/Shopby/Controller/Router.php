<?php
/**
 * @copyright  Copyright (c) 2009-2011 Amasty (http://www.amasty.com)
 */ 
class Amasty_Shopby_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function match(Zend_Controller_Request_Http $request) 
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        
        $pageId = $request->getPathInfo();
        // remove suffix if any
        $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
        if ($suffix && '/' != $suffix)
            $pageId = str_replace($suffix, '', $pageId);
        
        //add trailing slash 
        $pageId = trim($pageId, '/') . '/';
        
        $reservedKey = Mage::getStoreConfig('amshopby/seo/key') . '/';
        
        //check if we have reserved word in the url
        if (false === strpos($pageId, '/' . $reservedKey)){
            if (substr($pageId, 0, strlen($reservedKey)) != $reservedKey)
                return false;
        }
        else {
            $reservedKey = '/' . $reservedKey;
        }
        
        // get layered navigation params as string 
        list($cat, $params) = explode($reservedKey, $pageId, 2);
        $params = trim($params, '/');
        if ($params)
            $params = explode('/', $params);
        
        // remember for futire use in the helper
        if ($params){
            Mage::register('amshopby_current_params', $params);
        }  
         
        $cat = trim($cat, '/');
        if ($cat){ // normal category
            // if somebody has old urls in the cache.
            if (!Mage::getStoreConfig('amshopby/seo/urls'))
                return false;
                
            Varien_Autoload::registerScope('catalog');
            $cat = $cat . $suffix;
            $urlRewrite = Mage::getModel('core/url_rewrite')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByRequestPath($cat);
                
                
            if (!$urlRewrite->getId()){
                $store = $request->getParam('___from_store'); 
                $store = Mage::app()->getStore($store)->getId();  
                if (!$store){
                    return false;  
                }
                $urlRewrite = Mage::getModel('core/url_rewrite')
                    ->setStoreId($store)
                    ->loadByRequestPath($cat);                
            }
                
            if (!$urlRewrite->getId()){
                return false;
            }
            
            
            $request->setPathInfo($cat);
            $request->setModuleName('catalog');
            $request->setControllerName('category');
            $request->setActionName('view');
            $request->setParam('id', $urlRewrite->getCategoryId());

                
             $urlRewrite->rewrite($request);   
        }
        else { // root category
            $realModule = 'Amasty_Shopby';
            
            $request->setPathInfo(trim($reservedKey, '/'));
            $request->setModuleName('amshopby');
            $request->setRouteName('amshopby');  
            $request->setControllerName('index');
            $request->setActionName('index'); 
            $request->setControllerModule($realModule);
            
            $file = Mage::getModuleDir('controllers', $realModule) . DS . 'IndexController.php';
            include $file;
            
            //compatibility with 1.3
            $class = $realModule . '_IndexController';
            $controllerInstance = new $class($request, $this->getFront()->getResponse()); 
                          
            $request->setDispatched(true);
            $controllerInstance->dispatch('index');
        }
        
        return true;
    }
}