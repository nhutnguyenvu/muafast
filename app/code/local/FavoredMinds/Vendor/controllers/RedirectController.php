<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_RedirectController extends Mage_Core_Controller_Front_Action {

  public function _construct() {
    $helper			= Mage::app()->getHelper('vendor');
    if(!$helper->check()) {
      die("<script type='text/javascript'>window.location='".substr($_SERVER["REQUEST_URI"],0,strlen($_SERVER["REQUEST_URI"])-6)."';</script>");
    }
    parent::_construct();
  }

  protected function _initAction() {
    $this->loadLayout();
    return $this;
  }


  public function indexAction() {
    $this->loadLayout();

    $this->getLayout()->getBlock('head')->setTitle($this->__('Please whait while you are being redirected'));
    $this->renderLayout();
  }

}