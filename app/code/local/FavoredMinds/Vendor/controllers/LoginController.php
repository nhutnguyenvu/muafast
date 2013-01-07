<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_LoginController extends Mage_Core_Controller_Front_Action {
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
    $post	= $this->getRequest()->getPost();

    $username	= $post['login']["username"];
    $password	= $post['login']["password"];

    $vendorSession	= Mage::getSingleton('vendor/session');
    $adminSession	= Mage::getSingleton('admin/session');
    //$adminSession->setStore(0);

    /* @var $session Mage_Admin_Model_Session */
    //$request = Mage::app()->getRequest();
    //$user = $adminSession->getUser();

    /*
    if($user) {
      $user->reload();
    }
     *
     */
    //if (!$user || !$user->getId()) {
      $user			= $adminSession->login($username, $password); //, $request
      //$request->setPost('login', null);
    //}
    $adminSession->refreshAcl();
    if(!empty($user)) {
      if($user->username == $username) {
        $vendorSession->addData(array("logindata" => array("username" => $username, "password" => $password)));
        $this->_redirect("vendor/redirect");
      }
      else {
        $vendorSession->addException(new Exception, "Login failed, invalid role username!");
        $this->_redirect("vendor");
      }
    }
    else {
      $vendorSession->addException(new Exception, "Login failed!");
      $this->_redirect("vendor");
    }
    $this->loadLayout()->renderLayout();

  }

}