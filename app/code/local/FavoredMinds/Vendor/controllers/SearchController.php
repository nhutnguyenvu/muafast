<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class FavoredMinds_Vendor_SearchController extends Mage_Core_Controller_Front_Action {

    public function ajaxresultAction(){
	
	if($this->getRequest()->getParam('isAjax') == 1){
			
		$this->loadLayout();
		$response = $this->getLayout()->getBlock('layer_vendorlist')->toHtml();
		$response = Zend_Json::encode($response);
		$this->getResponse()->setBody($response);
      
		}
   
    }

}