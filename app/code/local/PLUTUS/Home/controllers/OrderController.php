<?php

/**
 * qikDropShipper Extension
 *
 * @category   FavoredMinds
 * @package    Vendor
 * @author     FavoredMinds Solution (geedubya15@gmail.com)
 * @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
 */
class PLUTUS_Home_OrderController extends Mage_Core_Controller_Front_Action {
    
     public function buildAction(){
         
         $is_ajax = Mage::helper("home")->isAjax();
         if($is_ajax==true){
            $sku = $this->getRequest()->getParam("sku");
            $this->loadLayout ( 'build_order' );
            $result ['result'] = $this->getLayout ()->createBlock ( 'core/template' )
                                                    ->setData( 'sku',$sku )
                                                    ->setTemplate("home/productinfo.phtml")
                                                    ->toHtml ();
            $result ['error'] = 0;
            $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
            return;
         }
         else{
            $this->loadLayout();
            $this->renderLayout();
         }
     }
}