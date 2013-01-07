<?php
class Luclong_Community_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {	
		$this->loadLayout();     
		$this->renderLayout();
    }
    
   	public function ajaxAction(){
        $this->loadLayout();
		$response = $this->getLayout()->getBlock('ajax.community')->toHtml();
	 	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }
}