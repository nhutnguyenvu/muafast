<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_RssController extends Mage_Core_Controller_Front_Action {
  public function _construct() {
    $helper			= Mage::app()->getHelper('vendor');
    if(!$helper->check()) {
      die("<script type='text/javascript'>window.location='".substr($_SERVER["REQUEST_URI"],0,strlen($_SERVER["REQUEST_URI"])-6)."';</script>");
    }
    parent::_construct();
  }

  public function ordersAction() {
    $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
    $this->loadLayout(false);
    $this->renderLayout();
  }

  public function newvendorsAction() {
    $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
    $this->loadLayout(false);
    $this->renderLayout();
  }

  public function statementsAction() {
    $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
    $this->loadLayout(false);
    $this->renderLayout();
  }

  /**
   * Controller predispatch method to change area for some specific action.
   *
   * @return Mage_Rss_OrderController
   */
  public function preDispatch() {
    if ($this->getRequest()->getActionName() == 'orders') {
      $this->_currentArea = 'adminhtml';
      Mage::helper('rss')->authAdmin('favoredminds_vendor/vendorsales');
    }
    if ($this->getRequest()->getActionName() == 'newvendors') {
      $this->_currentArea = 'adminhtml';
      Mage::helper('rss')->authAdmin('favoredminds_vendor/viewvendors');
    }
    if ($this->getRequest()->getActionName() == 'statements') {
      $this->_currentArea = 'adminhtml';
      Mage::helper('rss')->authAdmin('favoredminds_vendor/statements');
    }
    return parent::preDispatch();
  }
}
