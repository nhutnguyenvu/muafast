<?php
class Luclong_Custom_IndexController extends Mage_Core_Controller_Front_Action{
    
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Lục Long"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("lục long", array(
                "label" => $this->__("Lục Long"),
                "title" => $this->__("Lục Long")
		   ));

      $this->renderLayout(); 
	  
    }
    public function specialproductAction(){
		if($this->getRequest()->getParam('isAjax')==1){
		 
				 $html['page'] = $this->getRequest()->getParam('p');
				 $html['product'] = $this->getLayout()->createBlock('custom/index')->setTemplate('custom/specialproduct_scoll.phtml')->toHtml();
				 $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($html));
				 return true;
				 }
        $this->loadLayout();
        $this->renderLayout();
    }

}