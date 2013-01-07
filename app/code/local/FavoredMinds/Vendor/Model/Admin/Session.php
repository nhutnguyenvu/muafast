<?php
/**
* qikDropShipper Extension
*
* @category   FavoredMinds
* @package    Vendor
* @author     FavoredMinds Solution (geedubya15@gmail.com)
* @copyright  Copyright (c) 2010 FavoredMinds.com (http://www.favoredminds.com)
*/

class FavoredMinds_Vendor_Model_Admin_Session extends Mage_Admin_Model_Session {
  // allow vendors to access their pages
  public function isAllowed($resource, $privilege=null) {
    $helper 		= Mage::app()->getHelper('vendor');
    $vendorlogged	= $helper->vendorIsLogged();
    $check			= $helper->check();

    if(!$check && !empty($vendorlogged)) {
      return false;
    }

    if (empty($vendorlogged)){
      return parent::isAllowed($resource, $privilege);
    }

    /*

    if(($resource == "admin/catalog" || $resource == "admin/catalog/products") && $helper->vendorIsLogged() === true) {
      return $check;
    }

    if(!$check && strpos($resource,"favoredminds") !== false) {
      return false;
    }
     * 
     */

    return parent::isAllowed($resource, $privilege);
  }
}
