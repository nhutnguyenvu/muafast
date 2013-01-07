<?php

class Luclong_Generating_Adminhtml_GenerateController extends Mage_Adminhtml_Controller_Action
{
    
    protected function _initAction() {
        
        $this->loadLayout()
            ->_setActiveMenu('generating/generating')
            ->_addBreadcrumb(Mage::helper('generating')->__('Generate'), Mage::helper('generating')->__('Generating'));
        return $this;
	}
	
   
 
    
    public function createAction(){
        
        $featureProduct = Mage::helper("home")->getFeaturedProduct();
        
        $path = "generating/home/feature.phtml";
        
        $pahtphs = Mage::getBaseDir("design")."/frontend/muafast/default/template/home/generating/feature.phtml";
        
     
        $magento_block = Mage::getSingleton('core/layout');

        
        $productsHtml = $magento_block->createBlock('core/template');
        
        $productsHtml->setTemplate($path);
        
        $html = $productsHtml ->toHTML();
        
        file_put_contents($pahtphs, $html);
        
        exit();
        

    }
   
}